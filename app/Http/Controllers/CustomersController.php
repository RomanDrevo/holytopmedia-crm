<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Customer;
use App\Models\CustomerComment;
use App\Models\Bonus;
use App\Models\Deposit;
use App\Models\Alert;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateCustomerRequest;
use App\Liantech\Repositories\CustomersRepository;
use App\Liantech\Repositories\FilesRepository;
use App\Liantech\Repositories\DepositsRepository;
use App\Liantech\Repositories\WithdrawalsRepository;
use Config;
use PhpParser\Comment;

class CustomersController extends Controller
{

	protected $customersRepo;

	function __construct()
	{
		$this->customersRepo = new CustomersRepository;
	}


	public function index (){
        $countries = collect(\Config::get('liantech.countries_codes'));
        return view('pages.compliance.customers', compact('countries'));
    }
	public function getCustomers(Request $request)
    {
    	$customers = $this->customersRepo->allDepositors($request);
        return $customers;

    }


    public function show($customer_id)
    {

        $customer = Customer::byBroker()->with("alerts")->where('customer_crm_id', $customer_id)->first();


        if(is_null($customer))
            return redirect()->back()->with("error", "Customer with the ID: " . $customer_id ." does not exists");

        $documents = $this->customersRepo->getDocuments($customer);

        $files = File::where('customer_id', $customer->customer_crm_id)->get();

        $countries = \Config::get('liantech.countries_codes');

        $currencies = \Config::get('liantech.currencies_symbols');

        $totalDeposits = $customer->deposits->sum('amount');

        $totalCCdeposits = $customer->deposits->sum(function ($deposit){
            if($deposit->paymentMethod == "Credit Card"){
                return $deposit->amount;
            }
        });

        return view('pages.compliance.customer', compact('customer', 'documents', 'files', 'countries', 'totalDeposits', 'totalCCdeposits', 'currencies', 'alerts'));
    }



    public function getCustomerInfo($customer_id)
    {

        $customer = Customer::byBroker()->with("alerts", "comments.user", "withdrawals", "deposits")->where('customer_crm_id', $customer_id)->first();


        if(is_null($customer))
            return redirect()->back()->with("error", "Customer with the ID: " . $customer_id ." does not exists");

        $documents = $this->customersRepo->getDocuments($customer);

        $files = File::where('customer_id', $customer->customer_crm_id)->get();

        $countries = \Config::get('liantech.countries_codes');

        $currencies = \Config::get('liantech.currencies_symbols');

        $totalDeposits = $customer->deposits->sum('amount');

        $totalCCdeposits = $customer->deposits->sum(function ($deposit){
            if($deposit->paymentMethod == "Credit Card"){
                return $deposit->amount;
            }
        });

        $bonuses = Deposit::byBroker()->where('customerId', $customer->id)->where("paymentMethod", "Bonus")->get();

        $withdrawals = Withdrawal::where('customerId', $customer->id)->get();

        $data = [
            'documents' => $documents,
            'files' => $files,
            'countries' => $countries,
            'totalDeposits' => $totalDeposits,
            'totalCCdeposits' => $totalCCdeposits,
            'currencies' => $currencies,
            'customer' => $customer,
            'bonuses' => $bonuses,
            'withdrawals' => $withdrawals
        ];

        return response([
            "customerInfo"   =>  $data
        ],200);
    }

    public function update(UpdateCustomerRequest $request, $customer_id){

        $customer = Customer::byBroker()->where('customer_crm_id', $customer_id)->first();

        if( !$this->customersRepo->update($customer, $request)){
            return redirect()->back()->with('error', 'Something went wrong, please try again later');
        }

        return redirect()->back();
    }

    public function editFileComments(Request $request)
    {
        if(!$request->file_id || !is_numeric($request->file_id))
            return redirect()->back()->with('error', 'No file found on the request!');

        $file = File::findOrFail($request->file_id);

        $file->comments = $request->comments;
        $file->save();

        return redirect()->back()->with('success', 'The comments on filename: '. $file->name .' has been updated successfully');
    }

    public function addCommentToCustomer(Request $request, $customer_id){

        $customer = Customer::byBroker()->where('customer_crm_id', $customer_id)->first();

        $this->customersRepo->addComment($customer, $request);
    }

    public function addDepositNote(Request $request)
    {
        if(isset($request->deposit_id) && isset($request->deposit_note) && $request->deposit_note) {
            $note = DepositNote::create([
                'user_id' => \Auth::user()->id,
                'deposit_id' => $request->deposit_id,
                'content' => $request->deposit_note
            ]);
            return $note;
        }
        return 'Missing parameters';

    }
}
