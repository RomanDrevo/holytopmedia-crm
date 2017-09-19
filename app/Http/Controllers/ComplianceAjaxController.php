<?php

namespace App\Http\Controllers;

//use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;

use App\Models\Deposit;
use App\Models\File;
use App\Models\Form;
use App\Http\Requests;
use App\Liantech\Repositories\CustomersRepository;


use App\Http\Requests\CreateSplitRequest;
use App\Http\Requests\RemoveSplitRequest;
use App\Liantech\Classes\Pusher;

use App\Models\DepositNote;
use App\Models\Employee;
use App\Models\Split;
use App\Models\Table;
use Carbon\Carbon;
use Gate;



class ComplianceAjaxController extends Controller
{
    public function getDeposits(Request $request)
    {
    	if($request->has('deposit_ids') && is_array(json_decode($request->deposit_ids)) ){
    		$deposits = Deposit::find(json_decode($request->deposit_ids));
    		return $deposits;
    	}

    	return 'error';
    }

    public function toggleDeposit(Request $request)
    {
//        if(!$request->has("deposit_id"))
//            return "error";

        if(!$request->has("deposit_id"))
            return "error";

        $deposit = Deposit::find($request->deposit_id);
        if(is_null($deposit))
            return response("error", 403);

        if(Gate::denies('compliance_approve_deposit')){
            return response([
                "message" => "You don't have permission to edit deposits!"
            ], 403);
        }

        $deposit->approved = !$deposit->approved;
        $deposit->save();

        return response([
            "deposit"   =>  $deposit
        ],200);
    }

    public function approveDenyFile(Request $request)
    {
    	if(!$request->file_id || !is_numeric($request->file_id) || !$request->action || !in_array($request->action, ["approve", "deny"]))
    		return "error";

    	$file = File::find($request->file_id);

    	if(!$file)
    		return "error";

    	$form = Form::find($file->form_id);



    	$file->approved = ($request->action == "approve") ? true : false;
    	$file->save();

    	foreach ($form->deposits() as $deposit) {
    		$deposit->approved = $file->approved;
    		$deposit->save();
    	}


    	return 'ok';
    }

    public function getFileComments(Request $request)
    {
    	if(!$request->file_id || !is_numeric($request->file_id))
    		return "error";

    	$file = File::find($request->file_id);

    	if(!$file)
    		return "error";

    	return $file->comments;
    }

    public function addCommentToCustomer(Request $request){

    }
}
