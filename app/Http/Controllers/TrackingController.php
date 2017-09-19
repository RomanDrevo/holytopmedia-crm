<?php

namespace App\Http\Controllers;

use App\Models\EmailCustomer;
use App\Models\EmailList;
use App\Liantech\Classes\SpotAPI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Vinkla\Pusher\Facades\Pusher;

class TrackingController extends Controller
{
    /**
     * Save the current user in the database and redirect to the destination
     *         
     * @param  Request $request
     * @return Redirect
     */
    public function recordClick(Request $request)
    {
        $this->validate($request, [
            "list_id" => "required|exists:lists,id",
            "email" => "required|email",
        ]);

        $list = EmailList::find($request->list_id);
        $spot = new SpotAPI( env('SPOT_' . strtoupper($list->broker->url_name) . '_USERNAME'), env('SPOT_' . strtoupper($list->broker->url_name) . '_PASSWORD') );
        $customer = $spot->getCustomerByEmail($request->email);

        if(isset($customer["email"]) && !empty($customer["email"])){
            return $this->saveCustomer($customer, $list, $request);
        }
        return redirect()->back();
    }

    /**
     * If the user exists, we save it to our database.
     * 
     * @param  Array $customer 
     * @param  Request $request  
     * @return Redirect           
     */
    private function saveCustomer($customer, $list, $request)
    {
        $this->notifyUser($customer, $list);
        $isExists = EmailCustomer::where('customer_id', $customer["id"])->where('list_id', $list->id)->first();
        if( !is_null($isExists)){
            $isExists->updated_at = Carbon::now();
            $isExists->save();
            return $this->redirectWithParameters( $list->target_url, $request );
        }

        $emailCustomer = new EmailCustomer;
        $emailCustomer->list_id = $list->id;
        $emailCustomer->customer_id = $customer["id"];
        $emailCustomer->name = $customer["FirstName"] . " " . $customer["LastName"];
        $emailCustomer->employee_id = $customer["employeeInChargeId"];
        $emailCustomer->employee_name = $customer["employeeInChargeName"];
        $emailCustomer->save();

        return $this->redirectWithParameters( $list->target_url, $request);
    }

    private function redirectWithParameters($url, $request)
    {
        return redirect( $url . "?" . http_build_query($request->all()) );
    }

    private function notifyUser($customer, $list)
    {
        $data = [
            "success" => true, 
            "username" => $customer["FirstName"] . " " . $customer["LastName"], 
            "listname" => $list->name
        ];
        Pusher::trigger('list_' . $list->id, 'customer_clicked', $data);
    }

}
