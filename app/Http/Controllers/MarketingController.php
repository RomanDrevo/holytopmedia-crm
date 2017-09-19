<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendSmsRequest;
use App\Http\Requests\SendTestSms;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Twilio\Rest\Client;
use App\Models\Customer;


use Config;
use App\Liantech\Helpers\Textlocal;
use Maatwebsite\Excel\Facades\Excel;


class MarketingController extends Controller
{
    public function index(Request $request)
    {

        $query = "SELECT CA.id, CA.name, COUNT(*) as registrations, COUNT(CASE WHEN C.lastDepositDate > '2014-01-01 00:00:00' then 1 ELSE NULL END ) as depositors, CD.totalDeposits
                    FROM customers C
                    JOIN campaigns CA ON C.campaignId = CA.id
                    JOIN (
                        SELECT SUM(amountUSD) as totalDeposits
                        FROM customer_deposits
                        WHERE paymentMethod = 'Credit Card'
                            OR paymentMethod = 'Wire'
                        )
                     CD
                    
                    GROUP BY C.campaignId";

        $campaigns = collect(\DB::connection("spot_db_" . \Auth::user()->broker->name)->select(\DB::raw($query)));

        return view('pages.marketing.reports.index', compact('campaigns'));
    }


    public function getCampaign(Request $request)
    {

        if (!$request) {
            return response([
                "message" => "Something went wrong!"
            ], 403);
        }

        if (!$request->campaignID) {
            return response([
                "message" => "Campaign ID is required!"
            ], 403);
        }


        $campaignID = $request->campaignID;
        $start_date = ($request->start_date) ? Carbon::parse($request->start_date)->startOfDay()->format("Y-m-d H:m:i") : Carbon::now()->startOfYear()->format("Y-m-d H:m:i");
        $end_date = ($request->end_date) ? Carbon::parse($request->end_date)->startOfDay()->format("Y-m-d H:m:i") : Carbon::now()->format("Y-m-d H:m:i");

        $query = "SELECT CA.id, CA.name, COUNT(*) as registrations, COUNT(CASE WHEN C.lastDepositDate > '2014-01-01 00:00:00' then 1 ELSE NULL END ) as depositors, CD.totalDeposits
                    FROM customers C
                    JOIN campaigns CA ON C.campaignId = CA.id
                    JOIN (
                        SELECT SUM(amountUSD) as totalDeposits
                        FROM customer_deposits
                        WHERE campaignId = $campaignID
                        AND (
                            paymentMethod = 'Credit Card'
                            OR paymentMethod = 'Wire'
                        )
                        AND (
                            status = 'approved'
                        )
                    ) CD
                    WHERE C.regTime > '$start_date'
                    AND C.regTime < '$end_date'
                    AND CA.id = $campaignID
                    GROUP BY C.campaignId";

        $campaign = collect(\DB::connection("spot_db_" . \Auth::user()->broker->name)->select(\DB::raw($query)));


        if (count($campaign) != 0) {
            $totalCustomers = $campaign[0]->registrations;
            $totalDeposits = $campaign[0]->totalDeposits;
            $averageDeposit = $totalDeposits / $totalCustomers;

            $campaignInfo = [
                "name" => $campaign[0]->name,
                "id" => $campaign[0]->id,
                "total_registrations" => $campaign[0]->registrations,
                "depositors" => $campaign[0]->depositors,
                "total_deposits" => round($campaign[0]->totalDeposits),
                "player_value" => round($averageDeposit)
            ];

            return response([
                "data" => $campaignInfo,
                "message" => "OK"
            ], 200);
        } else {
            return response([
                "data" => [],
                "message" => "This campaign has no traffic in this year."
            ], 200);
        }
    }


    public function verifyEmails()
    {
        $customers = Customer::byBroker()->limit(100)->get();
        $verifiedEmails = [];
        $badEmails = [];
        $client = new \Kickbox\Client('cd098ba3e2ae4452e128cfb7afe980de41b74e4f47fe4cc3123ed1b998108192');
        $kickbox = $client->kickbox();

        foreach ($customers as $customer) {
            $email = $customer["email"];
            $response = $kickbox->verify($email);

            if ($response->body["result"] != "deliverable") {
                array_push($badEmails, $customer);
            } else {
                array_push($verifiedEmails, $customer);
            }
        }

        dd($verifiedEmails);
//        dd($badEmails);
    }

    public function sendSms()
    {
        return view('pages.marketing.send-sms');
    }

    public function sendTo(SendSmsRequest $request)
    {
        $subject = $request->subject;
        $body = $request->smsbody;
        $type = $request->type;
        isset($request->from) ? $from = Carbon::parse($request->from) : $from = Carbon::now()->startOfMonth();

        $client = new Client(env('TWILLIO_ACCOUNT_CID'), env('TWILLIO_TOKEN'));

        //if message body contains {phone} word replace it by phone relevant to customer Country and send
        if (str_contains($body, "{phone}")) {
            //get array of phones by countries only for a relevant broker
            $internationalPhones = Config::get('liantech.phones_by_countries')[\Auth::user()->broker->id];
            $countiesWithCustomers = $this->sortCustomersByCountries($type, $from, $internationalPhones);
            foreach ($countiesWithCustomers as $countryCode => $customers) {
                if (empty($customers))
                    continue;
                $message = str_replace("{phone}", $internationalPhones[$countryCode], $body);
                $phones = app()->make($type)->getPhones($customers);
                foreach ($phones as $phone) {
                    $client->account->messages->create(
                    // the number we are sending to - Any phone number
                        $phone,
                        array('from' => env('TWILLIO_NUMBER'), 'body' => $message)
                    );
                }
            }
        } else {
            //if not contains - send the same message to all customers without filtering
            $customers = app()->make($type)->getCustomers($from);
            $phones = app()->make($type)->getPhones($customers);
            foreach ($phones as $phone) {
                try{
                    $client->account->messages->create(
                    // the number we are sending to - Any phone number
                        $phone,
                        array('from' => env('TWILLIO_NUMBER'), 'body' => $body)
                    );
                }catch(\Exception $e){
                    continue;
                }
            }
        }
        return response('ok', 200);
    }

    public function sendTestSms(SendTestSms $request)
    {
        $body = $request->smsbody;
        if (str_contains($body, "{phone}")) {
            $body = str_replace("{phone}", '(+44) 203-608-6720', $body);
        }
        $client = new Client(env('TWILLIO_ACCOUNT_CID'), env('TWILLIO_TOKEN'));
        $client->messages->create(
            $request->testNumber,
            array(
                'from' => env('TWILLIO_NUMBER'),
                'body'=> $body
            )
        );
        return response('ok', 200);

    }

    private function sortCustomersByCountries($type, $from, $internationalPhones)
    {
        $countriesArr = array();

        //build array with countries codes are keys, values are empty arrays
        foreach ($internationalPhones as $country => $phone) {
            $countriesArr[$country] = [];
        }
        $customers = app()->make($type)->getCustomers($from);
        //fill an array with customers
        foreach ($customers as $customer) {
            if (isset($countriesArr[$customer->countryId])) {
                $countriesArr[$customer->countryId][] = $customer;
            } else {
                $countriesArr["225"][] = $customer;
            }
        }
        return $countriesArr;
    }

    public function checkPhonesCount(Request $request)
    {
        if (!isset($request->type)) {
            return response('Please, set a rule', 503);
        }
        $type = $request->type;
        isset($request->from) ? $from = Carbon::parse($request->from) : $from = Carbon::now()->startOfMonth();

        $customers = app()->make($type)->getCustomers($from);
        return count($customers);
    }


    public function downloadToSVC(Request $request)
    {
        Excel::create('Target_customers', function ($excel) use ($request) {
            $excel->setTitle('Target_customers');
            $excel->sheet('Target_customers', function ($sheet) use ($request) {


                $allCustomers = array();
                $type = $request->type;
                isset($request->from) ? $from = Carbon::parse($request->from) : $from = Carbon::now()->startOfMonth();
                $customers = app()->make($type)->getCustomers($from);

                foreach ($customers as $customer) {
                    isset($customer->depositsCount) ? $count = $customer->depositsCount : $count = null;

                    $oneCustomer = array(
                        'ID' => $customer->customer_id,
                        'Email' => $customer->customer_email,
                        'Phone' => $customer->customer_phone,
                        'CellPhone' => $customer->customer_cellphone,
                        'Sales Status' => $customer->sales_status,
                        'Country' => $customer->country_name,
                        'Deposit Count' => $count,
                        'Custom Status' => $customer->custom_sales_status,

                    );
                    //push the deposit to the big array
                    array_push($allCustomers, $oneCustomer);
                }
                $sheet->fromArray($allCustomers);

            });

        })->export('xls');
    }
}


