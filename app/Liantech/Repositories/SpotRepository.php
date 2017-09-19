<?php
namespace App\Liantech\Repositories;

use App\Models\Broker;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Withdrawal;
use App\Models\Setting;
use Carbon\Carbon;
use Config;
use DB;
use Pusher;
use App\Liantech\Traits\NotificationsTrait;

/**
 * Fetch and updating data from spot CRM replica DB
 */
class SpotRepository
{
    use NotificationsTrait;

    public static $MAX_RECORDS = 150;

    protected $currenciesSymbols;

    public function __construct()
    {
        $this->currencies_symbols = Config::get("liantech.currencies_symbols");
    }

    public function update()
    {
        $this->updateDeposits();
        $this->updateWithdrawals();
    }

    private function updateDeposits()
    {
        $brokers = Broker::where("platform", "spot")->get();
        foreach ($brokers as $broker) {

            $customersIds = Customer::byBroker($broker->id)->pluck("customer_crm_id")->toArray();


            try {
                $lastDeposit = Deposit::where("broker_id", $broker->id)->orderBy('deposits_crm_id', 'desc')->first();
                $lastId = !is_null($lastDeposit) ? $lastDeposit->deposits_crm_id : 0;
                \Log::info("last ID: " . $lastId . " " . $broker->name);
                $deposits = DB::connection('spot_db_' . $broker->name)->table('customer_deposits')->where('id', '>', $lastId)->take(self::$MAX_RECORDS)->get();

                \Log::info("Deposits count: " . $deposits->count() . " " . $broker->name);

                foreach ($deposits as $deposit) {

                    if (!in_array($deposit->customerId, $customersIds)) {
                        $this->createCustomer($deposit->customerId, $broker);
                        $customersIds[] = $deposit->customerId;
                    }

                    if ($deposit->cancellationTime == '0000-00-00 00:00:00')
                        $deposit->cancellationTime = null;

                    if ($deposit->confirmTime == '0000-00-00 00:00:00')
                        $deposit->confirmTime = null;
                    $deposit->assigned_at = $deposit->confirmTime;
                    $deposit->deposits_crm_id = $deposit->id;
                    $deposit->broker_id = $broker->id;

                    try {
                        $db_deposit = Deposit::create((array)$deposit);
                    } catch (\Exception $e) {
                        \Log::error("Error creating deposit: " . json_encode($deposit));
                        continue;
                    }

                    $this->updateDeposit($db_deposit, $broker);
                }

                if ($deposits->count()) {
                    $this->updateStats($broker);
                }
            } catch (\Exception $e) {
                \Log::error("Log from cron message: " . $e->getMessage());
            }
        }

    }


    /**
     * Automatically assign the deposit to the employee
     * that assigned to this customer during the
     * transaction
     *
     * @param  Deposit $deposit
     * @param  Eloquent Model $newDeposit
     * @return boolean
     */
    private function updateDeposit($deposit, $broker)
    {
        $employee = $deposit->receptionEmployeeId == 0 ? null : Employee::where('employee_crm_id', $deposit->receptionEmployeeId)->where('broker_id', $deposit->broker->id)->first();

        if (is_null($employee))
            $employee = $this->getAssignedEmployee($deposit);

        if (!is_null($employee) && isset($employee->id)) {
            $deposit->receptionEmployeeId = $employee->employee_crm_id;
            $deposit->table_id = $employee->table_id;

            if ($employee->table)
                $deposit->deposit_type = $employee->table->type;
        }
        $verified = $this->isDepositVerified($deposit);
        $deposit->is_verified = $verified;

        $deposit->save();

        // We trigger the notification function with all data
        // necessary to push a new notification to the
        // Notifier App so it can be shown on screens
        // only in case that it's not a selfie and brlongs to table + not Bonus/Qiwi

        $madeByEmployeeWithTable = ($deposit->receptionEmployeeId != 0 && !is_null($deposit->table));

        if ($madeByEmployeeWithTable && !in_array($deposit->paymentMethod, ['Bonus', 'Qiwi'])) {

            $employee = Employee::where("broker_id", $broker->id)->where("employee_crm_id", $deposit->receptionEmployeeId)->first();

            if (is_null($employee))
                return true;

            $data = [
                "name" => $employee->name,
                "desk" => $employee->table ? $employee->table->name : "Unknown",
                "deskType" => $employee->table ? $employee->table->type : "Unknown",
                "time" => Carbon::now("Asia/Jerusalem")->format("H:i"),
                "amount" => number_format(intval($deposit->amount)),
                "amountType" => $this->currencies_symbols[$deposit->currency],
                "amountUSD" => number_format(intval($deposit->amount * $deposit->rateUSD)),
                "intAmount" => intval($deposit->amount),
                "photo" => $employee->image,
                "animation" => "animated tada"
            ];

            $this->notifyNewDeposit($broker, $data);

        }

        return true;
    }

    public function createCustomer($id, $broker)
    {
        $customer = DB::connection('spot_db_' . $broker->name)->table('customers')->where('id', $id)->first();
        $customer->regTime = $customer->regTime != '0000-00-00 00:00:00' ? $customer->regTime : null;
        $customer->firstDepositDate = $customer->firstDepositDate != '0000-00-00 00:00:00' ? $customer->firstDepositDate : null;
        $customer->lastDepositDate = $customer->lastDepositDate != '0000-00-00 00:00:00' ? $customer->lastDepositDate : null;
        $customer->lastWithdrawalDate = $customer->lastWithdrawalDate != '0000-00-00 00:00:00' ? $customer->lastWithdrawalDate : null;

        $localCustomer = new Customer((array)$customer);
        $localCustomer->broker_id = $broker->id;
        $localCustomer->customer_crm_id = $customer->id;
        $localCustomer->lastLoginDate = null;
        $localCustomer->save();
    }

    /**
     * Check if the deposit is verified by testing the proccessor type
     * and if not check if the customer itself is verified
     *
     * @param  Deposit $deposit
     * @return boolean
     */
    public function isDepositVerified($deposit)
    {
        $autoAuthorizedProccessors = ["Processing3D", "Fibonatix1", "Inatec1"];

        if ($deposit->paymentMethod == "Wire")
            return true;

        if (in_array($deposit->clearedBy, $autoAuthorizedProccessors))
            return true;

        return $this->isCustomerVerified($deposit->customerId, $deposit->broker);
    }

    /**
     * Get customer by ID and check if he is fully verified
     *
     * @param  Int $customer_id
     * @param $broker
     * @return boolean
     */
    public function isCustomerVerified($customer_id, $broker)
    {
        $customer = DB::connection('spot_db_' . $broker->name)
            ->table('customers')
            ->where('id', $customer_id)
            ->first();

        if (is_null($customer))
            return false;

        return $customer->verification == "Full";

    }

    /**
     * Get the assigned employee that incharge
     * of the given customer
     *
     * @param  Deposit $deposit
     * @return Employee $employee
     */
    private function getAssignedEmployee($deposit)
    {
        $customer = DB::connection('spot_db_' . $deposit->broker->name)
            ->table("customers")
            ->where("id", $deposit->customerId)
            ->first();

        if (!$customer || !$customer->employeeInChargeId)
            return false;

        return Employee::where('employee_crm_id', $customer->employeeInChargeId)->where('broker_id', $deposit->broker->id)->first();
    }


    private function updateWithdrawals()
    {
        $brokers = Broker::where("platform", "spot")->get();
        try {
            foreach ($brokers as $broker) {
                $lastWithdrawal = Withdrawal::where("broker_id", $broker->id)->orderBy('withdrawals_crm_id', 'desc')->first();
                $lastId = !is_null($lastWithdrawal) ? $lastWithdrawal->withdrawals_crm_id : 0;
                $withdrawals = \DB::connection('spot_db_' . $broker->name)->table('withdrawals')->where('id', '>', $lastId)->take(self::$MAX_RECORDS)->get();

                foreach ($withdrawals as $withdrawal) {
                    if ($withdrawal->confirmTime == '0000-00-00 00:00:00')
                        $withdrawal->confirmTime = null;

                    if ($withdrawal->cancellationTime == '0000-00-00 00:00:00')
                        $withdrawal->cancellationTime = null;

                    //if currency isn't set, get deposit of the customer and set withdrawal's currency according to deposit's currency
                    if (!$withdrawal->currency) {
                        $deposit = Deposit::byBroker()
                            ->where('customerId', $withdrawal->customerId)
                            ->where('paymentMethod', '!=', 'Bonus')
                            ->where('assigned_at', '>', Carbon::now()->subMonths(6))
                            ->orderBy('id', 'desc')
                            ->first();
                        $withdrawal->currency = $deposit->currency;
                    }
                    $withdrawal->withdrawals_crm_id = $withdrawal->id;
                    $withdrawal->broker_id = $broker->id;

                    try {
                        $db_withdrawal = Withdrawal::create((array)$withdrawal);
                    } catch (\Exception $e) {
                        \Log::error("Error creating withdrawal: " . json_encode($withdrawal));
                        continue;
                    }

                    $this->updateWithdrawal($db_withdrawal);
                }
            }
        } catch (\Exception $e) {
            \Log::error("Log from cron message: " . $e->getMessage());
        }


    }

    /**
     * Automatically assign the withdrawal to the employee
     *
     * @param  Withdrawal $withdrawal
     * @param  Eloquent Model $newWithdrawal
     * @return boolean
     */
    private function updateWithdrawal($withdrawal)
    {
        //$employee = Employee::find($withdrawal->receptionEmployeeId);

        $employee = Employee::where('employee_crm_id', $withdrawal->receptionEmployeeId)->where('broker_id', $withdrawal->broker->id)->first();
        if (!is_null($employee)) {
            $withdrawal->table_id = $employee->table_id;

            if ($employee->table)
                $withdrawal->withdrawal_type = $employee->table->type;
        }

        //reset withdrawal receptionEmployeeId to 0, after savint table id and table type
        $withdrawal->receptionEmployeeId = 0;


        $withdrawal->save();

        return true;
    }

    public function getStats(Broker $broker)
    {
        $setting = DB::table('settings')->select('option_value')->where('option_name', 'monthly_goal')->first();
        $monthlyGoal = $setting->option_value;
        $todayDeposits = Deposit::byBroker($broker->id)
            ->where('assigned_at', '>=', Carbon::create()->startOfDay())
            ->where('paymentMethod', '!=', "Bonus")
            ->get();

        $monthlyDeposits = Deposit::byBroker($broker->id)
            ->where('assigned_at', '>=', Carbon::create()->startOfMonth())
            ->where('paymentMethod', '!=', "Bonus")
            ->get();


        return [
            "todayFtd" => $todayDeposits->where('deposit_type', '=', 1)->count(),
            "monthlyFtd" => $monthlyDeposits->where('deposit_type', '=', 1)->count(),
            "todayDeposits" => '$' . number_format($this->calculateAmountUSD($todayDeposits)),
            "totalDeposits" => '$' . number_format($this->calculateAmountUSD($monthlyDeposits)),
            "monthGoal" => '$' . number_format($monthlyGoal)
        ];
    }


    public function calculateAmountUSD($deposits)
    {
        $currenciesSettings = Setting::whereIn('pretty_name', ['GBP', 'USD', 'NIS', 'EUR'])->get();
        $settingsArr = array();

        foreach ($currenciesSettings as $singleSetting) {
            $settingsArr[$singleSetting->pretty_name] = $singleSetting->option_value;
        }

        $total = 0;
        foreach ($deposits as $deposit) {
            $total += $deposit->amount * floatval($settingsArr[$deposit->currency]);// * $deposit->rateUSD;
        }
        return $total;
    }

    //check in spot if there was changes in withdarwal status und if so update status and confrim time
    public function updateWithdrawalsStatus()
    {
        $brokers = Broker::where("platform", "spot")->get();
        foreach ($brokers as $broker) {
            try {
                $withdrawals = Withdrawal::byBroker($broker->id)
                    ->where("confirmTime", ">=", Carbon::now()->subMonths(6))
                    ->orWhereNull("confirmTime")
                    ->get();
                $withdrawalsData = [];
                foreach ($withdrawals as $withdrawal) {
                    $withdrawalsData[$withdrawal->withdrawals_crm_id] = [
                        'status' => $withdrawal->status,
                        'confirmTime' => $withdrawal->confirmTime
                    ];

                }
                $remoteWithdrawals = \DB::connection('spot_db_' . $broker->name)->table("withdrawals")->whereIn("id", array_keys($withdrawalsData))->get();
                foreach ($remoteWithdrawals as $remoteWithdrawal) {
                    $localWithdrawal = null;

                    if ($remoteWithdrawal->status != $withdrawalsData[$remoteWithdrawal->id]["status"]) {
                        $localWithdrawal = Withdrawal::byBroker($broker->id)->where("withdrawals_crm_id", $remoteWithdrawal->id)->first();
                        $localWithdrawal->status = $remoteWithdrawal->status;
                    }

                    if($remoteWithdrawal->confirmTime == "0000-00-00 00:00:00") continue;

                    if ($remoteWithdrawal->confirmTime != $withdrawalsData[$remoteWithdrawal->id]["confirmTime"] || is_null($withdrawalsData[$remoteWithdrawal->id]["confirmTime"])) {
                        if (is_null($localWithdrawal)) {
                            $localWithdrawal = Withdrawal::byBroker($broker->id)->where("withdrawals_crm_id", $remoteWithdrawal->id)->first();
                        }
                        $localWithdrawal->confirmTime = $remoteWithdrawal->confirmTime;
                    }
                    if (!is_null($localWithdrawal)) {
                        $localWithdrawal->save();
                    }

                }

            } catch (\Exception $e) {
                \Log::error("Log from cron message: " . $e->getMessage());
            }

        }
    }

    //check in spot if there was changes in deposits status und if so update status and confrim time
    public function updatesDepositsStatus()
    {
        $brokers = Broker::where("platform", "spot")->get();
        foreach ($brokers as $broker) {
            try {
                $deposits = Deposit::byBroker($broker->id)
                    ->where("confirmTime", ">=", Carbon::now()->subMonths(6))
                    ->orWhereNull("confirmTime")
                    ->get();

                $depositsData = [];
                foreach ($deposits as $deposit) {
                    $depositsData[$deposit->deposits_crm_id] = [
                        'status' => $deposit->status,
                        'confirmTime' => $deposit->confirmTime
                    ];
                }
                $remoteDeposits = \DB::connection('spot_db_' . $broker->name)->table("customer_deposits")->whereIn("id", array_keys($depositsData))->get();
                foreach ($remoteDeposits as $remoteDeposit) {
                    $localDeposit = null;

                    // check if status is the same and update if needed
                    if ($remoteDeposit->status != $depositsData[$remoteDeposit->id]["status"]) {
                        $localDeposit = Deposit::byBroker($broker->id)->where("deposits_crm_id", $remoteDeposit->id)->first();
                        $localDeposit->status = $remoteDeposit->status;
                    }

                    if($remoteDeposit->confirmTime == "0000-00-00 00:00:00") continue;

                    //check confirm time
                    if ($remoteDeposit->confirmTime != $depositsData[$remoteDeposit->id]["confirmTime"]) {
                        if (is_null($localDeposit)) {
                            $localDeposit = Deposit::byBroker($broker->id)->where("deposits_crm_id", $remoteDeposit->id)->first();
                        }
                        //set also new assigned at time if it wasn't set by admin (if it's not the same as confirm time)
                        if ($localDeposit->assigned_at == $localDeposit->confirmTime || is_null($localDeposit->assigned_at)) {
                            $localDeposit->assigned_at = $remoteDeposit->confirmTime;
                        }
                        $localDeposit->confirmTime = $remoteDeposit->confirmTime;
                    }
                    if (!is_null($localDeposit)) {
                        $localDeposit->save();
                    }

                }

            } catch (\Exception $e) {
                \Log::error("Log from cron message: " . $e->getMessage());
            }

        }
    }
}