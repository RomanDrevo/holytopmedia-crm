<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerGetDodRequest;
use App\Http\Requests\CustomerSignedDodRequest;
use App\Http\Requests;
use Jenssegers\Agent\Agent;
use App\Form;
use App\File;
use App\Customer;
use Carbon\Carbon;

class FormsController extends Controller
{

	public function __construct()
    {

        $this->middleware('auth', ['only' => [
            'createDod'
        ]]);

    }

    public function createDod(Request $request)
    {
    	$form = Form::createDod([
    		'customer_id'	=>	$request->customer_id,
    		'deposits'		=>	$request->deposits,
    		'email'			=>	$request->email,
    	]);

    	if(!$form)
    		return redirect('back')->with('error', 'Could not create DOD please try again.');

    	return redirect("customers/{$request->customer_id}")->with('success', 'Dod has been send successfully to this customer');
    }

    public function getDod(CustomerGetDodRequest $request)
    {
    	$customer = Customer::find($request->customer_id);
    	$form = Form::find($request->form_id);
    	if( is_null($customer) || is_null($form) || $form->access_code != $request->access_code)
    		abort(403, 'Unauthorized action');

    	$deposits = $form->depositsWithCC();

    	$form->user_ip_on_view = \Request::ip();
    	$form->viewed_at = Carbon::now();
    	$form->save();

    	return view('pdf.dod', compact('customer', 'form', 'deposits'));
    }

    public function storeDod(CustomerSignedDodRequest $request)
    {

    	$customer = Customer::find($request->customer_id);
    	$form = Form::find($request->form_id);

    	if( is_null($customer) || is_null($form) || $form->access_code != $request->access_code)
    		abort(403, 'Unauthorized action');


    	$form->user_ip_on_sign = \Request::ip();
    	$form->signed_at = Carbon::now();
    	$form->completed = true;
    	$form->access_code = "";
    	$form->save();

    	$this->storePdfFromImage($request, $customer, $form);

		//return $pdf->stream();

    	return redirect('success/dod');

    }

    public function thankYouDod()
    {
    	return view('success.thank-you-dod');
    }



    private function storePdfFromImage($request, $customer, $form)
    {

		$requestData = $request->all();

		$pdf = \PDF::loadView('pdf.dod-authorize', compact('requestData', 'customer', 'form', 'agent'));

    	$filename = $customer->id .'_' . str_random(5) . '_dod.pdf';

    	$pdf->save(public_path('documents/dod/') . $filename);

		$agent = new Agent();

		$file = new File;
		$file->customer_id = $customer->id;
		$file->form_id = $form->id;
		$file->name = $filename;
		$file->type = 'dod';
		$file->signed_computer_data = json_encode([
    		"os"		=>	$agent->platform(),
    		"browser"	=>	$agent->browser(),
    		"ip"		=>	$request->ip()
    	]);
		$file->is_local = true;
		$file->save();
    }
}
