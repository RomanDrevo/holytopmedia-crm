<?php

namespace App\Liantech\Repositories;

use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Broker;
use App\Models\Goal;
use App\Liantech\Classes\Pusher;
use App\Models\Setting;
use App\Models\Withdrawal;
use Carbon\Carbon;
use DB;
/**
* 	Main functions for employees
*/
class EmployeesRepository
{
	
    /**
     * Update all internal employees from spot_option replica DB
     *     
     * @return boolean
     */
	public function updateFromDB()
	{
        $brokers = Broker::where("platform", "spot")->get();


        foreach ($brokers as $broker) {
            $employees = DB::connection('spot_db_' . $broker->name)
                ->table('users')
                ->where('id', '!=', 0)
                ->get();

            foreach ($employees as $employee) {
                $emp = Employee::where("broker_id", $broker->id)->where('employee_crm_id', $employee->id)->first();
//                if( !$emp && $employee->status == "activated" ){
//
//                    //Create a new employee
//                    $emp = new Employee;
//                    $emp->employee_crm_id = $employee->id;
//                    $emp->broker_id = $broker->id;
//                    $emp->name = $employee->firstName.' '.$employee->lastName;
//                    $emp->active = true;
//                    $emp->save();
//
//                }

                if( !$emp ){

                    //Create a new employee
                    $emp = new Employee;
                    $emp->employee_crm_id = $employee->id;
                    $emp->broker_id = $broker->id;
                    $emp->name = $employee->firstName.' '.$employee->lastName;
                    $emp->active = true;
                    $emp->save();

                }

                else{
                    if($emp && $employee){
                        if($emp->active && $employee->status != "activated"){
                            $emp->active = false;
                            $emp->save();
                        }
                        else if(!$emp->active && $employee->status = "activated"){
                            $emp->active = true;
                            $emp->save();
                        }
                    }
                }
            }


        }
        return true;

	}

    /**
     * Main entry for fluent updates (Deposits & Employees)
     * 
     * @return boolean
     */
    public function checkForUpdates()
    {     

        $isUpdated = false;

        /**
         * Check if a new deposit is available and
         * assign it to the employee or
         * leave as Selfie
         */
        $newDeposits = $this->checkForDeposits();

        if(count($newDeposits) > 0)
            $isUpdated = true;


        /**
         * Check if a new withdrawal is available and
         * remove it from the employee or
         * leave as Selfie
         */
        $newWithdrawals = $this->checkForWithdrawals();
        if(count($newWithdrawals) > 0)
            $isUpdated = true;

        $this->notifyScoreboard($isUpdated);

        return true;
    }

    /**
     * Notify the scoreboard for new updates
     * @param  boolean $isUpdated
     */
    private function notifyScoreboard($isUpdated = false)
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY', ''), 
            env('PUSHER_APP_SECRET', ''), 
            env('PUSHER_APP_ID', '')
        );

        $pusher->trigger('scoreboard_channel', 'needs_to_update', ["is_updated" => $isUpdated] );
    }


    /**
     * Check if a new deposit has been assign to an employee
     * 
     * @return boolean
     */
    protected function checkForDeposits(){

        $thisMonth = Carbon::now()->startOfMonth();

        /**
         * Get all deposits from Spot DB
         * 
         * @var Collection
         */
        $brokers = Broker::where("platform", "spot")->get();

        foreach ($brokers as $broker) {
            $deposits = DB::connection("spot_db_" . $broker->name)
                ->table("customer_deposits")
                ->where('status', 'approved')
                ->where('confirmTime', '>=', $thisMonth->format("Y-m-d H:i:s"))
                ->where(function($table){
                    $table->where('paymentMethod', 'Credit Card');
                    $table->orWhere('paymentMethod', 'Wire');
                })
                ->get();

            /**
             * Array to store deposits ID's
             * @var array
             */
            $employeesDepositsIds = array();


            /**
             * Set an array for all the deposits id's for the
             * find method to fetch from our DB the existing
             */
            foreach ($deposits as $deposit) {
                array_push($employeesDepositsIds, $deposit->id);
            }


            /**
             * All existing deposits on our DB
             * @var Collection
             */
            //$existsDeposits = Deposit::find($employeesDepositsIds);
            $existsDeposits = Deposit::where("broker_id", $broker->id)->whereIn('deposits_crm_id', $employeesDepositsIds)->get();


            /**
             * Will store all the new deposits that occured
             *
             * @var array
             */
            $newDepositsIds = array();

            /**
             * Get all the settings available
             * @var App\Setting
             */
            $allSettings = Setting::all();

            $settings = array();

            foreach ($allSettings as $singleSetting) {
                $settings[$singleSetting->pretty_name] = $singleSetting->option_value;
            }

            /**
             * Loop through all deposits and check if a new deposit has happened
             */
            foreach ($deposits as $deposit) {

                $res = $existsDeposits->search(function ($item, $key) use ($deposit) {
                    return $item->deposits_crm_id == $deposit->id;
                });

                if($res === false){
                    $this->createNewDeposit( $deposit, $settings, $broker);
                    array_push($newDepositsIds, $deposit->id);
                }

            }

            return count($newDepositsIds) == 0 ? false : $newDepositsIds;
        }


    }

    /**
     * Check if a new withdrawal has been occured
     * 
     * @return boolean
     */
    protected function checkForWithdrawals(){

        $thisMonth = Carbon::now()->startOfMonth();

        /**
         * Get all deposits from Spot DB
         * 
         * @var Collection
         */
        $brokers = Broker::where("platform", "spot")->get();

        foreach ($brokers as $broker) {
            $withdrawals = DB::connection("spot_db_" . $broker->name)
                ->table("withdrawals")
                ->where('status', 'approved')
                ->where('confirmTime', '>=', $thisMonth->format("Y-m-d H:i:s"))
                ->where(function($table){
                    $table->where('paymentMethod', 'Credit Card');
                    $table->orWhere('paymentMethod', 'Wire');
                    $table->orWhere('paymentMethod', 'chargeBack');
                })
                ->get();


            /**
             * Array to store withdrawals ID's
             * @var array
             */
            $employeesWithdrawalsIds = array();


            /**
             * Set an array for all the withdrawals id's for the
             * 'find' method to fetch from our DB the existing
             */
            foreach ($withdrawals as $withdrawal) {
                array_push($employeesWithdrawalsIds, $withdrawal->id);
            }


            /**
             * All existing withdrawals on our DB
             * @var Collection
             */
            //$existsWithdrawals = Withdrawal::find($employeesWithdrawalsIds);
            $existsWithdrawals = Withdrawal::where("broker_id", $broker->id)->whereIn('withdrawals_crm_id', $employeesWithdrawalsIds)->get();



            /**
             * Will store all the new withdrawals that occured
             *
             * @var array
             */
            $newWithdrawalsIds = array();

            /**
             * Get all the settings available
             * @var App\Setting
             */
            $allSettings = Setting::all();

            $settings = array();

            foreach ($allSettings as $singleSetting) {
                $settings[$singleSetting->pretty_name] = $singleSetting->option_value;
            }

            /**
             * Loop through all withdrawals and check if a new withdrawal has happened
             */
            foreach ($withdrawals as $withdrawal) {

                $res = $existsWithdrawals->search(function ($item, $key) use ($withdrawal) {
                    return $item->withdrawals_crm_id == $withdrawal->id;
                });

                if($res === false){
                    $this->createNewWithdrawal( $withdrawal, $settings, $broker );
                    array_push($newWithdrawalsIds, $withdrawal->id);
                }

            }

            return count($newWithdrawalsIds) == 0 ? false : $newWithdrawalsIds;
        }


    }


    /**
     * Create a new deposit record on DB from 
     * Spot DB customer_deposit record
     * 
     * @param  Deposit $deposit
     * @return boolean
     */
    public function createNewDeposit($deposit, $settings, $broker)
    {
        $newDeposit = new Deposit;
        $newDeposit->deposits_crm_id = $deposit->id;
        $newDeposit->broker_id = $broker->id;
        $newDeposit->employee_id = 0;
        $newDeposit->customer_id = $deposit->customerId;
        $newDeposit->transaction_id = $deposit->transactionID;
        $newDeposit->payment_method = $deposit->paymentMethod;
        $newDeposit->cleared_by = $deposit->clearedBy;
        $newDeposit->amount = $deposit->amount;
        $newDeposit->usd_amount = $deposit->currency == "USD" ? $deposit->amount : ($deposit->amount * floatval($settings[$deposit->currency]) );
        $newDeposit->currency = $deposit->currency;
        $newDeposit->confirm_time = $deposit->confirmTime;
        $newDeposit->assigned_at = $deposit->confirmTime;
        $newDeposit->is_verified = $this->isDepositVerified($deposit);

        if($deposit->receptionEmployeeId != 0)
            return $this->updateAssignedDeposit($deposit, $newDeposit);
        
        return $this->updateNotAssignedDeposit($deposit, $newDeposit);
    }

    /**
     * Create a new withdrawal record on DB from 
     * Spot DB customer_deposit record
     * 
     * @param  Withdrawal $withdrawal
     * @return boolean
     */
    public function createNewWithdrawal($withdrawal, $settings, $broker)
    {
        $newWithdrawal = new Withdrawal;
        $newWithdrawal->withdrawals_crm_id = $withdrawal->id;
        $newWithdrawal->broker_id = $broker->id;
        $newWithdrawal->employee_id = 0;
        $newWithdrawal->customer_id = $withdrawal->customerId;
        $newWithdrawal->transaction_id = $withdrawal->transactionID;
        $newWithdrawal->payment_method = $withdrawal->paymentMethod;
        $newWithdrawal->cleared_by = $withdrawal->clearedBy;
        $newWithdrawal->amount = $withdrawal->amount;
        $newWithdrawal->usd_amount = $withdrawal->currency == "USD" ? $withdrawal->amount : ($withdrawal->amount * floatval($settings[$withdrawal->currency]) );
        $newWithdrawal->currency = $withdrawal->currency;
        $newWithdrawal->confirm_time = $withdrawal->confirmTime;
        $newWithdrawal->is_verified = $this->isWithdrawalVerified($withdrawal);

        if($withdrawal->receptionEmployeeId != 0)
            return $this->updateAssignedWithdrawal($withdrawal, $newWithdrawal);
        
        return $this->updateNotAssignedWithdrawal($withdrawal, $newWithdrawal);
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

        if($deposit->paymentMethod == "Wire")
            return true;

        if( in_array($deposit->clearedBy, $autoAuthorizedProccessors))
            return true;

        return $this->isCustomerVerified($deposit->customerId, $deposit->broker);
    }

    /**
     * Check if the withdrawal is verified by testing 
     * if the customer itself is verified
     * 
     * @param  Withdrawal $withdrawal
     * @return boolean
     */
    public function isWithdrawalVerified($withdrawal)
    {
        if($this->isCustomerVerified($withdrawal->customerId, $withdrawal->broker))
            return true;


        return false;
    }

    /**
     * Get customer by ID and check if he is fully verified
     * 
     * @param  Int  $customer_id
     * @return boolean
     */
    public function isCustomerVerified($customer_id, $broker)
    {
        $customer = DB::connection('spot_db_' . $broker->name)
            ->table('customers')
            ->where('id', $customer_id)
            ->first();

        if(is_null($customer))
            return false;

        return $customer->verification == "Full";
            
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
    private function updateAssignedDeposit($deposit, $newDeposit)
    {
        //$employee = Employee::find($deposit->receptionEmployeeId);
        $employee = Employee::where('broker_id', $deposit->broker->id)->where('employee_crm_id', $deposit->receptionEmployeeId)->first();
        if( !is_null($employee) ){
            $newDeposit->employee_id = $employee->employee_crm_id;
            $newDeposit->table_id = $employee->table_id;
            
            if($employee->table)
                $newDeposit->type = $employee->table->type;
        }
        $newDeposit->save();

        // We trigger the notification function with all data
        // necessary to push a new notification to the 
        // Notifier App so it can be shown on screens
        // only in case that it's not a selfie
        if( $newDeposit->employee_id != 0 ){
            $this->notifyNewDeposit([
                "employee"      =>  $newDeposit->employee,
                "table"         =>  $newDeposit->employee->table,
                "currency"      =>  $newDeposit->currency,
                "amount_plain"  =>  $newDeposit->amount,
                "amount"        =>  number_format($newDeposit->amount),
                "created_at"    =>  $newDeposit->assigned_at->setTimezone('Asia/Jerusalem')->format("H:i"),
            ]);
        }

        return true;
    }

    /**
     * Create a new Pusher instance to send a new
     * notification to the scoreboard platform
     * 
     * @param  array  $data
     * @return boolean
     */
    public function notifyNewDeposit($data = [])
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY', ''), 
            env('PUSHER_APP_SECRET', ''), 
            env('PUSHER_APP_ID', '')
        );

        $pusher->trigger('roiteks_playground_channel', 'new_deposit', $data );

        return true;
    }

    /**
     * If the deposit doesn't have any employee assigned
     * to it during the transaction, we try to look
     * for the employee that assigned to the customer
     * or a communication with the customer that
     * has the words "KYC" (know your client)
     * in the subject line
     * 
     * @param  Deposit $deposit
     * @param  Eloquent Model $newDeposit 
     * @return boolean
     */
    private function updateNotAssignedDeposit($deposit, $newDeposit)
    {

        $employee = $this->getAssignedEmployee($deposit);

        if(!$employee)
            $employee = $this->getEmployeeByCommunication($deposit);
        

        if( !is_null($employee) && isset($employee->id) ){
            $newDeposit->employee_id = $employee->employee_crm_id;
            $newDeposit->table_id = $employee->table_id;

            if($employee->table)
                $newDeposit->type = $employee->table->type;
        }

        $newDeposit->save();

        return true;
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
        $customer = DB::connection('spot_db_' .  $deposit->broker->name)
            ->table("customers")
            ->where("id", $deposit->customerId)
            ->first();

        if(!$customer || !$customer->employeeInChargeId)
            return false;

        //return Employee::find($customer->employeeInChargeId);
        return Employee::where('broker_id', $deposit->broker->id)->where('employee_crm_id', $customer->employeeInChargeId)->first();
    }

    /**
     * Looking for a kyc in the communication subject
     * 
     * @param  Deposit $deposit
     * @return Employee $employee
     */
    private function getEmployeeByCommunication($deposit)
    {
        $comments = DB::connection('spot_db_' .  $deposit->broker->name)
            ->table("customer_communications")
            ->where("customerId", $deposit->customerId)
            ->where('subject', 'LIKE', '%kyc%')
            ->orderBy("id", "desc")
            ->get();

        if( count($comments) > 0 )
            //return Employee::find($comments[0]->creatorId);
            return Employee::where('broker_id', $deposit->broker->id)->where('employee_crm_id', $comments[0]->creatorId)->first();

            
        return false;
    }


    /**
     * Automatically assign the withdrawal to the employee
     * 
     * @param  Withdrawal $withdrawal
     * @param  Eloquent Model $newWithdrawal 
     * @return boolean
     */
    private function updateAssignedWithdrawal($withdrawal, $newWithdrawal)
    {
        //$employee = Employee::find($withdrawal->receptionEmployeeId);
        $employee =  Employee::where('broker_id', $withdrawal->broker->id)->where('employee_crm_id', $withdrawal->receptionEmployeeId)->first();
        if( !is_null($employee) ){
            $newWithdrawal->table_id = $employee->table_id;

            if($employee->table)
                $newWithdrawal->type = $employee->table->type;
        }
        $newWithdrawal->save();

        return true;
    }

    /**
     * If the withdrawal doesn't have any employee assigned
     * to it during the transaction, we try to look
     * for existing deposits and see if we can
     * track the current employee
     * 
     * @param  Withdrawal $withdrawal
     * @param  Eloquent Model $newWithdrawal 
     * @return boolean
     */
    private function updateNotAssignedWithdrawal($withdrawal, $newWithdrawal)
    {
        $deposits = Deposit::where("customer_id", $withdrawal->customerId)->orderBy('id', 'desc')->get();

        /**
         * Determine if this withrawal belongs to a table manager
         */
        foreach ($deposits as $deposit) {
            if($deposit->table_id != 0){
                $newWithdrawal->table_id = $deposit->table_id;
                break;
            }
        }
        $newWithdrawal->save();

        return true;
    }

}






