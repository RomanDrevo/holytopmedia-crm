<?php

namespace App\Http\Controllers;


use App\Models\Broker;
use App\Models\EmailCustomer;
use App\Models\EmailList;
use Illuminate\Http\Request;

class EmailsListsController extends Controller
{
    public function index()
    {
        $lists = EmailList::where('user_id', auth()->user()->id)->latest()->get();

        return view('pages.marketing.lists.index', compact('lists'));
    }

    public function show($id)
    {
        $list = EmailList::with("broker")->where("id", $id)->first();

        return view('pages.marketing.lists.show', compact('list'));
    }

    public function create()
    {
        $brokers = Broker::all();

        return view('pages.marketing.lists.create', compact('brokers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "name"          =>  "required",
            "broker"        =>  "required|exists:brokers,id",
            "target_url"    =>  "required",
        ]);

        $list = new EmailList;
        $list->name = $request->name;
        $list->user_id = auth()->user()->id;
        $list->broker_id = $request->broker;
        $list->target_url = $request->target_url;
        $list->save();

        return redirect('/marketing/lists')->with('success', 'List with the ID: ' . $list->id . ' has been created succeffully');
    }

    public function getCustomers($id)
    {
        $customers = EmailList::findOrFail($id)->customers;

        return $customers;
    }

    public function customerClicked($list_id, $customer_id)
    {
        $customer = EmailCustomer::where('list_id', $list_id)->where('customer_id', $customer_id)->first();
        if(is_null($customer))
            return "error";

        $customer->clicked = true;
        $customer->save();
        return "ok";
    }
}
