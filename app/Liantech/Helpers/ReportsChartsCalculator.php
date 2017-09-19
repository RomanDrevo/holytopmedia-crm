<?php
namespace App\Liantech\Helpers;

use App\Models\Table;
use App\Models\Withdrawal;
use Carbon\Carbon;
use App\Models\Deposit;
use App\Models\Setting;
use Illuminate\Http\Request;
use DB;
use Symfony\Component\Console\Helper\TableCell;

class ReportsChartsCalculator
{
    protected $settings;
    protected $countries_codes;

    public function __construct()
    {
        $this->settings = $this->sortSettings();
        $this->countries_codes = \Config::get('liantech.countries_codes');
    }


    public function cCvsWirePieChart(Request $request)
    {
        $start = Carbon::parse($request->startDate)->format("Y-m-d H:i:s");
        $end = Carbon::parse($request->endDate)->format("Y-m-d H:i:s");

        $deposits = Deposit::byBroker()
            ->where('paymentMethod', 'Credit Card')
            ->orWhere('paymentMethod', 'Wire')
            ->dateBetween($start, $end)
            ->where(function ($query) use ($request) {
                if ($request->tableId) {
                    $query->where('table_id', $request->tableId);
                }
            })->get();

        $cc = 0;
        $wire = 0;
        if (!$deposits->count()) {
            return false;
        }
        foreach ($deposits as $deposit) {
            if ($deposit->paymentMethod == 'Credit Card') {
                $cc++;
            }
            if ($deposit->paymentMethod == 'Wire') {
                $wire++;
            }
        }

        $total = $deposits->count();

        $cc_perc = round(($cc / $total) * 100, 2); //get % and return 2 digits after floating point
        $wire_perc = round(($wire / $total) * 100, 2);
        return array(
            'title' => 'CC vs Wire between ' . Carbon::parse($request->startDate)->format("d M Y") . ' and ' . Carbon::parse($request->endDate)->format("d M Y"),
            'series' => [
                'type' => 'pie',
                'name' => 'Payment Methods',
                'data' => array(
                    ['name' => 'Credit Card', 'y' => $cc_perc, 'count' => number_format($cc) . ' deposits'],
                    ['name' => 'Wire', 'y' => $wire_perc, 'count' => number_format($wire) . ' deposits']
                )
            ]

        );
    }

    public function monthlyGoalGuageChart()
    {
        $settings = Setting::getArrayOfSettings();
        $startOfMonth = Carbon::now()->startOfMonth();
        $monthlyDeposits = Deposit::byBroker()
            ->where('assigned_at', '>=', $startOfMonth)
            ->where('paymentMethod', '!=', "Bonus")
            ->get();
        return array(
            'goal' => $settings['monthly_goal'],
            'total' => $monthlyDeposits->sum('amount')
        );
    }

    /**
     * @param $request
     * @return \Illuminate\Support\Collection
     */
    public function customersByCampaigns($request)
    {
        $start = Carbon::parse($request->startDate)->format("Y-m-d H:i:s");
        $end = Carbon::parse($request->endDate)->format("Y-m-d H:i:s");

        $query = "SELECT C.campaignId, CA.name, COUNT(C.id) as customers_count, COUNT(CASE WHEN C.lastDepositDate > :last_deposit_date then 1 ELSE NULL END ) as depositors
        FROM customers C
        JOIN campaigns CA ON CA.id = C.campaignId
        WHERE C.regTime > :start_time
        AND C.regTime < :end_time
        GROUP BY C.campaignId";

        return $campaigns = collect(\DB::connection("spot_db_" . \Auth::user()->broker->name)->select(DB::raw($query), ["last_deposit_date" => $start, "start_time" => $start, "end_time" => $end]));
    }

    public function depositsAndNetByCampaignBarChart($request)
    {
        $start = Carbon::parse($request->startDate)->format("Y-m-d H:i:s");
        $end = Carbon::parse($request->endDate)->format("Y-m-d H:i:s");

        $deposits = Deposit::with('campaign')
            ->byBroker()
            ->whereBetween('assigned_at', [$start, $end])
            ->where('paymentMethod', '!=', 'Bonus')
            ->where('approved', 0)
            ->get();

        $withdrawals = Withdrawal::with('campaign')
            ->byBroker()
            ->whereBetween('confirmTime', [$start, $end])
            ->where('paymentMethod', '!=', 'Bonus')
            ->where('approved', 0)
            ->get();

        $graphTotal = [];
        foreach ($deposits as $deposit) {
            if (isset($graphTotal[$deposit->campaignId])) {
                $graphTotal[$deposit->campaignId]['total_deposits'] += $deposit["amount"] * floatval($this->settings[$deposit->currency]);

            } else {
                $graphTotal[$deposit->campaignId]['total_deposits'] = $deposit["amount"] * floatval($this->settings[$deposit->currency]);
                $graphTotal[$deposit->campaignId]['campaign_name'] = $deposit->campaign->name;
            }
            $graphTotal[$deposit->campaignId]['net'] = $graphTotal[$deposit->campaignId]['total_deposits'];
        }

        foreach ($withdrawals as $withdrawal) {
            if (isset($graphTotal[$withdrawal->campaignId])) {
                $graphTotal[$withdrawal->campaignId]['net'] -= $withdrawal["amount"] * floatval($this->settings[$withdrawal->currency]);
            }
        }


        $graphTotalCollection = (collect($graphTotal))->sortByDesc('total_deposits')->values();
        $campaignsWithMostTotalDeposits = $graphTotalCollection->splice(0, 7);
        $others = $graphTotalCollection;
        $othersTotal = $others->sum('total_deposits');
        $othersNet = $others->sum('net');

        return array(
            //get names of 7 most popular campaigns and push others as a last category
            'categories' => $campaignsWithMostTotalDeposits->pluck('campaign_name')->push('others'),
            'title' => 'Total Deposit/WD/Net per campaign ' . Carbon::parse($request->startDate)->format("d M Y") . ' and ' . Carbon::parse($request->endDate)->format("d M Y"),
            'series' => array(
                //get array of total deposits from 7 most polular campaigns + push total/net of others to the end of collection
                ['name' => 'Total Deposits', 'data' => $campaignsWithMostTotalDeposits->pluck('total_deposits')->push($othersTotal)],
                ['name' => 'Net', 'data' => $campaignsWithMostTotalDeposits->pluck('net')->push($othersNet)]
            )
        );
    }

    public function depositsAndNetByCountryBarChart($request)
    {
        $start = Carbon::parse($request->startDate)->format("Y-m-d H:i:s");
        $end = Carbon::parse($request->endDate)->format("Y-m-d H:i:s");

        $deposits = Deposit::with('customer')
            ->byBroker()
            ->whereBetween('assigned_at', [$start, $end])
            ->where('paymentMethod', '!=', 'Bonus')
            ->where('approved', 0)
            ->get();

        $withdrawals = Withdrawal::with('customer')
            ->byBroker()
            ->whereBetween('confirmTime', [$start, $end])
            ->where('paymentMethod', '!=', 'Bonus')
            ->where('approved', 0)
            ->get();
        $graphTotal = [];

        foreach ($deposits as $deposit) {
            $customer = $deposit->customer;
            if (is_null($customer))
                continue;

            $country_id = $customer->Country;
            if (isset($graphTotal[$country_id])) {
                $graphTotal[$country_id]['total_deposits'] += $deposit["amount"] * floatval($this->settings[$deposit->currency]);

            } else {
                $graphTotal[$country_id]['total_deposits'] = $deposit["amount"] * floatval($this->settings[$deposit->currency]);
                $graphTotal[$country_id]['country_name'] = $this->countries_codes[$country_id];
            }
            $graphTotal[$country_id]['net'] = $graphTotal[$country_id]['total_deposits'];
        }

        foreach ($withdrawals as $withdrawal) {
            $customer = $withdrawal->customer;
            if (is_null($customer))
                continue;

            $country_id = $customer->Country;

            if (isset($graphTotal[$withdrawal->customer->Country]) && $country_id) {
                $graphTotal[$country_id]['net'] -= $withdrawal["amount"] * floatval($this->settings[$withdrawal->currency]);
            }
        }

        $graphTotalCollection = (collect($graphTotal))->sortByDesc('total_deposits')->values();

        $countiesWithMostTotalDeposits = $graphTotalCollection->splice(0, 7);
        $others = $graphTotalCollection;
        $othersTotal = $others->sum('total_deposits');
        $othersNet = $others->sum('net');

        return array(
            //get names of 7 most popular countries and push others as a last category
            'categories' => $countiesWithMostTotalDeposits->pluck('country_name')->push('others'),
            'title' => 'Total Deposit/WD/Net per country ' . Carbon::parse($request->startDate)->format("d M Y") . ' and ' . Carbon::parse($request->endDate)->format("d M Y"),
            'series' => array(
                //get array of total deposits from 7 most polular counties + push total/net of others to the end of collection
                ['name' => 'Total Deposits', 'data' => $countiesWithMostTotalDeposits->pluck('total_deposits')->push($othersTotal)],
                ['name' => 'Net', 'data' => $countiesWithMostTotalDeposits->pluck('net')->push($othersNet)]
            )
        );
    }


    /**
     * @param Request array
     * @return  array
     */
    public function customersByCampaignsBarChart($request)
    {
        $campaigns = $this->customersByCampaigns($request);
        $campaign_names = $campaigns->pluck("name")->toArray();

        $depositors = $campaigns->pluck("depositors")->toArray();
        $not_depositors = array();
        foreach ($campaigns as $campaign) {
            array_push($not_depositors, ($campaign->customers_count - $campaign->depositors));
        }
        return array(
            'categories' => $campaign_names,
            'title' => 'Customers grouped by Campaigns between ' . Carbon::parse($request->startDate)->format("d M Y") . ' and ' . Carbon::parse($request->endDate)->format("d M Y"),
            'series' => [
                ['name' => 'Deposited', 'data' => $depositors, 'stack' => 'customers'],
                ['name' => 'Not Deposited', 'data' => $not_depositors, 'stack' => 'customers']
            ]
        );
    }

    public function customersByCampaignPieChart($request)
    {
        $campaigns = $this->customersByCampaigns($request)->sortByDesc('customers_count')->values();
        $total_count = $total_left = $campaigns->sum('customers_count');
        $total_percents = 0;

        foreach ($campaigns as $campaign) {
            $percents = round(($campaign->customers_count / $total_count) * 100, 2);
            $total_percents = $total_percents + $percents;
            $total_left = $total_left - $campaign->customers_count;

            $series['name'] = 'Count';

            if ($total_percents >= 90) {
                $series['data'][] = array(
                    'name' => 'Others',
                    'y' => 100 - $total_percents,
                    'count' => number_format($total_left) . ' customers'
                );
                break;
            }

            $series['data'][] = array(
                'name' => $campaign->name,
                'y' => $percents,
                'count' => number_format($campaign->customers_count) . ' customers'
            );

        }
        $series['colorByPoint'] = true;
        return [
            'title' => 'Conversion per campaign ' . Carbon::parse($request->startDate)->format("d M Y") . ' and ' . Carbon::parse($request->endDate)->format("d M Y"),
            'series' => $series
        ];
    }

    public function customersByCountries($request)
    {
        $start = Carbon::parse($request->startDate)->format("Y-m-d H:i:s");
        $end = Carbon::parse($request->endDate)->format("Y-m-d H:i:s");
        $query = "SELECT C.Country as countryId, COUNT(C.id) as customers_count, COUNT(CASE WHEN C.lastDepositDate > :last_deposit_date then 1 ELSE NULL END ) as depositors
        FROM customers C
        WHERE C.regTime > :start_time
        AND C.regTime < :end_time
        GROUP BY C.Country";

        return $countries = collect(\DB::connection("spot_db_" . \Auth::user()->broker->name)->select(DB::raw($query), ["last_deposit_date" => $start, "start_time" => $start, "end_time" => $end]));
    }


    public function customersByCountriesPieChart($request)
    {
        $countries = $this->customersByCountries($request)->sortByDesc('customers_count')->values();
        $total_count = $total_left = $countries->sum('customers_count');

        $total_percents = 0;
        foreach ($countries as $country) {
            $country_name = $this->countries_codes[$country->countryId];
            $percents = round(($country->customers_count / $total_count) * 100, 2);
            $total_percents = $total_percents + $percents;
            $total_left = $total_left - $country->customers_count;

            $series['name'] = 'Count';
            if ($total_percents >= 90) {
                $series['data'][] = array(
                    'name' => 'Others',
                    'y' => 100 - $total_percents,
                    'count' => number_format($total_left) . ' customers'
                );
                break;
            }
            $series['type'] = 'pie';
            $series['data'][] = array(
                'name' => $country_name,
                'y' => $percents,
                'count' => number_format($country->customers_count) . ' customers'
            );
        }
        $series['colorByPoint'] = true;
        return [
            'title' => 'Conversion per country ' . Carbon::parse($request->startDate)->format("d M Y") . ' and ' . Carbon::parse($request->endDate)->format("d M Y"),
            'series' => $series
        ];
    }


    public function depositsByCurrency($request)
    {
        $start = Carbon::parse($request->startDate)->format("Y-m-d H:i:s");
        $end = Carbon::parse($request->endDate)->format("Y-m-d H:i:s");
        return $deposits = \DB::table('deposits')
            ->select(DB::raw('COUNT(deposits.id) as depositsCount'), 'deposits.currency')
            ->where('paymentMethod', '!=', 'Bonus')
            ->where('broker_id', \Auth::user()->broker->id)
            ->whereBetween("deposits.assigned_at", [$start, $end])
            ->where(function ($query) use ($request) {
                if ($request->tableId) {
                    $query->where('table_id', $request->tableId);
                }
            })
            ->groupBy('deposits.currency')
            ->get();
    }

    public function depositsByCurrenciesPieChart($request)
    {
        $deposits = $this->depositsByCurrency($request);
        $total_count = $deposits->sum('depositsCount');
        $series = array();
        $series['name'] = 'Count';
        $series['type'] = 'pie';
        foreach ($deposits as $deposit) {

            $percents = round(($deposit->depositsCount / $total_count) * 100, 2);
            $series['data'][] = array(
                'name' => $deposit->currency,
                'y' => $percents,
                'count' => number_format($deposit->depositsCount) . ' deposits'
            );
        }
        return [
            'title' => 'Currency Breakdown ' . Carbon::parse($request->startDate)->format("d M Y") . ' and ' . Carbon::parse($request->endDate)->format("d M Y"),
            'series' => $series
        ];
    }

    public function getUpsaleData($start, $end)
    {
        $deposits = Deposit::byBroker()
            ->with('customer')
            ->whereHas('employee')
            ->whereNotIn('paymentMethod', ['Bonus', 'Qiwi', 'AlertPay'])
            ->where("assigned_at", "<=", $end)
            ->where("assigned_at", ">=", $start)
            ->orderBy('assigned_at', 'asc')
            ->get();

        $depositsByCustomerIds = array();
        foreach ($deposits as $deposit) {
            if ($deposit->customer && $deposit->customer->firstDepositDate < $start) {
                continue;
            }
            $depositsByCustomerIds[$deposit->customerId][] = $deposit;
        }
        $upsaleRows = array();
        array_push($upsaleRows, ['ID', 'Broker', 'Customer ID', 'Amount', 'Currency', 'Transaction ID', 'Employee', 'Table Manager', 'Deposit Date', 'Upsale Date']);

        foreach ($depositsByCustomerIds as $id) {
            if (count($id) > 1) {
                foreach ($id as $index => $deposit) {
                    if ($index == 0) {
                        $firstDepositDate = Carbon::parse($deposit->assigned_at)->format('d-m-Y H:i:s');
                        continue;
                    }
                    $employee = $deposit->employee->name;
                    !is_null($deposit->table) ? $manager = $deposit->table->manager->name : $manager = '';

                    $upsaleDate = Carbon::parse($deposit->assigned_at)->format('d-m-Y H:i:s');
                    $upsaleRows[] = [$deposit->id, $deposit->broker->name, $deposit->customerId, number_format($deposit->amount),
                        $deposit->currency, $deposit->transactionID, $employee, $manager, $firstDepositDate, $upsaleDate
                    ];
                }
            }
        }
        return $upsaleRows;
    }


    private static function sortSettings()
    {
        $allSettings = Setting::all();
        $settings = array();

        foreach ($allSettings as $singleSetting) {
            $settings[$singleSetting->pretty_name] = $singleSetting->option_value;
        }
        return $settings;
    }

}