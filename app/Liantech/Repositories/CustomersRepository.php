<?php

namespace App\Liantech\Repositories;

use App\Models\Customer;
use App\Models\Broker;
use App\Models\CustomerComment;
use App\Models\UserReport;
use App\Liantech\Classes\SpotAPI;
use Carbon\Carbon;
use Liantech\Spot;
use Validator;
use DB;
use Illuminate\Http\Request;
use Gate;

/**
 * Main repository for customers
 */
class CustomersRepository
{
    public static $MAX_RECORDS = 500;

    public function updateFromDB()
    {
        $brokers = Broker::where("platform", "spot")->get();

        foreach ($brokers as $broker) {
            try {
                $lastCustomer = Customer::where("broker_id", $broker->id)->orderBy('customer_crm_id', 'desc')->first();
                $lastId = !is_null($lastCustomer) ? $lastCustomer->customer_crm_id : 0;
                $customers = DB::connection('spot_db_' . $broker->name)->table('customers')->where('lastDepositDate', ">=", Carbon::now()->subYears(3))->where('id', '>', $lastId)->take(self::$MAX_RECORDS)->get();

                $customersIds = $customers->pluck('id')->toArray();

                $existsCustomers = Customer::where("broker_id", $broker->id)->whereIn('customer_crm_id', $customersIds)->get();

                foreach ($customers as $customer) {

                    $res = $existsCustomers->search(function ($item, $key) use ($customer) {
                        return $item->customer_crm_id == $customer->id;
                    });

                    if ($res === false) {
                        $customer->regTime = $customer->regTime != '0000-00-00 00:00:00' ? $customer->regTime : null;
                        $customer->firstDepositDate = $customer->firstDepositDate != '0000-00-00 00:00:00' ? $customer->firstDepositDate : null;
                        $customer->lastDepositDate = $customer->lastDepositDate != '0000-00-00 00:00:00' ? $customer->lastDepositDate : null;
                        $customer->lastWithdrawalDate = $customer->lastWithdrawalDate != '0000-00-00 00:00:00' ? $customer->lastWithdrawalDate : null;
                        $this->create($customer, $broker);
                    }
                }

            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }
    }

    public function updateExistingRecords()
    {
        $brokers = Broker::where("platform", "spot")->get();
        foreach ($brokers as $broker) {
            try {
                $customers = Customer::byBroker($broker->id)->where("regTime", ">=", Carbon::now()->subYear())->get();
                $customersData = [];
                foreach ($customers as $customer) {
                    $customersData[$customer->customer_crm_id] = [
                        "FirstName" => $customer->FirstName,
                        "LastName" => $customer->LastName,
                        "Phone" => $customer->Phone,
                        "email" => $customer->email,
                    ];
                }

                $remoteCustomers = \DB::connection('spot_db_' . $broker->name)->table("customers")->whereIn("id", array_keys($customersData))->get();
                foreach ($remoteCustomers as $remCust) {
                    $customer = null;
                    if ($remCust->FirstName != $customersData[$remCust->id]["FirstName"]) {
                        $customer = Customer::byBroker($broker->id)->where("customer_crm_id", $remCust->id)->first();
                        $customer->FirstName = $remCust->FirstName;
                    }

                    if ($remCust->LastName != $customersData[$remCust->id]["LastName"]) {
                        if (is_null($customer))
                            $customer = Customer::byBroker($broker->id)->where("customer_crm_id", $remCust->id)->first();

                        $customer->LastName = $remCust->LastName;
                    }

                    if ($remCust->Phone != $customersData[$remCust->id]["Phone"]) {
                        if (is_null($customer))
                            $customer = Customer::byBroker($broker->id)->where("customer_crm_id", $remCust->id)->first();

                        $customer->Phone = $remCust->Phone;
                    }

                    if ($remCust->email != $customersData[$remCust->id]["email"]) {
                        if (is_null($customer))
                            $customer = Customer::byBroker($broker->id)->where("customer_crm_id", $remCust->id)->first();

                        $customer->email = $remCust->email;
                    }

                    if (!is_null($customer)) {
                        $customer->save();
                    }

                }

            } catch (\Exception $e) {
                \Log::error("Log from cron message: " . $e->getMessage());
            }

        }

    }

    private function create($customer, Broker $broker)
    {
        $localCustomer = new Customer((array)$customer);
        $localCustomer->broker_id = $broker->id;
        $localCustomer->customer_crm_id = $customer->id;
        $localCustomer->lastLoginDate = null;
        $localCustomer->save();
    }

    public function addComment($customer, $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required'
        ]);

        if ($validator->fails())
            return false;

        if ($customer->comments->count() == 0) {
            UserReport::addFirstCommentToUser();
        }

        $comment = new CustomerComment;
        $comment->customer_id = $customer->id;
        $comment->user_id = \Auth::user()->id;
        $comment->content = $request->content;
        $comment->save();

        return true;
    }

    public function update($customer, $request)
    {

        $customer = Customer::where("id", $request->customer_id)->first();

        if (!empty($request->content)) {
            if (!$this->addComment($customer, $request))
                return false;
        }

        if($request->has("secondary_phone") && $request->secondary_phone != ""){

            $customer->secondary_phone = $request->secondary_phone;
        }


        if($request->has("secondary_email") && $request->secondary_email != ""){

            $customer->secondary_email = $request->secondary_email;
        }


        $customer->save();




        if(Gate::allows('compliance_approve_verification')){
            if ($customer->verification != $request->verification) {
                
                $broker_cresentials = strtoupper($customer->broker->name);
                $broker_name = str_replace('_', '', $customer->broker->name);
                $spot = new Spot(env('SPOT_' . $broker_cresentials . '_USERNAME'),
                    env('SPOT_' . $broker_cresentials . '_PASSWORD'), $broker_name);

                $result = $spot->callWithModuleAndCommand('Customer', 'edit', [
                    'customerId' => $customer->customer_crm_id,
                    'verification' => $request->verification
                ]);

                if ($result['data']['operation_status'] == 'successful') {
                    $customer->verification = $request->verification;
                    $customer->save();

                    return true;
                }
                return false;

            }
        }else{

//            return redirect()->back()->with('Error', 'You do not have a permission to edit verification status!');
            return redirect()->back()->withErrors(['Error', 'You do not have a permission to edit verification status!']);
        }




        return true;
    }

    public function getDocuments(Customer $customer)
    {

        return \DB::connection('spot_db_' . $customer->broker->name)
            ->table('customer_verification_documents')
            ->where('customerId', $customer->customer_crm_id)
            ->get();
    }

    public function all($request)
    {
        $q = \Request::has("query") ? \Request::get("query") : "";

        $field = \Request::has("field") ? \Request::get("field") : "id";

        return Customer::where($field, "LIKE", "%$q%")
            ->ByBroker()
            ->orderBy("id", "desc")
            ->paginate(25);
    }




    public function allDepositors($request)
    {
        $q = \Request::has("query") ? \Request::get("query") : "";

        $field = \Request::has("field") ? \Request::get("field") : "id";

        return Customer::whereNotNull("lastDepositDate")
            ->ByBroker()
            ->where(function ($query) use ($request) {
                if ($request->search) {
                    if ($request->by == "customer_crm_id") {
                        $query->where("customer_crm_id", "LIKE", "%{$request->search}%");
                    } else if($request->by == "FirstName"){
                        $query->where('FirstName', 'LIKE', '%' . $request->search . '%');
                    }
                    else{
                        $query->where('LastName', 'LIKE', '%' . $request->search . '%');
                    }
                }
            })
            ->orderBy("id", "desc")
            ->paginate(25);
    }

    public function getCountryByCode($country_code)
    {
        return \Config::get('liantech.countries_codes')[$country_code];
    }

    public function getCampaignById($campaign_id)
    {
        return \DB::connection('spot_db_' . \Auth::user()->broker->name)
            ->table('campaigns')
            ->where('id', $campaign_id)
            ->first();
    }
}
