<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Liantech\Helpers\CustomersUploader;
class CustomersUploaderController extends Controller
{

    public function createCustomers()
    {
        return view('pages.sales.uploader.create-customers');
    }

    public function editCustomers()
    {
        return view('pages.sales.uploader.edit-customers');
    }


    public function createAndUpload(Request $request)
    {
        $this->validate($request, [
            "customers_file" => "required"
        ]);


        $file = $request->file("customers_file");
        $broker = strtoupper(\Auth::user()->broker->name);

        $brokerName = str_replace('_', '', strtolower($broker));

        $uploader= (new CustomersUploader($file, "add"))->setClient(env('SPOT_'. $broker .'_USERNAME'), env('SPOT_'. $broker .'_PASSWORD'), $brokerName);

        $customers = $uploader->getCustomers();
        if($customers) {
            $uploader->createCustomers($customers);
        }
    }


    public function editAndUpload(Request $request)
    {
        $this->validate($request, [
            "customers_file" => "required"
        ]);


        $file = $request->file("customers_file");
        $broker = strtoupper(\Auth::user()->broker->name);

        $brokerName = str_replace('_', '', strtolower($broker));

        $uploader= (new CustomersUploader($file, "edit"))->setClient(env('SPOT_'. $broker .'_USERNAME'), env('SPOT_'. $broker .'_PASSWORD'), $brokerName);

        $customers = $uploader->getEditedCustomers();
        if($customers) {
            $uploader->editCustomers($customers);
        }
    }

}
