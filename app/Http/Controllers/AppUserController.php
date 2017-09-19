<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Liantech\Classes\SMSRules\NewDepositors;
use App\Models\Broker;
use App\Models\Department;
use App\Permission;
use App\User;
use Illuminate\Http\Request;

class AppUserController extends Controller
{

    public function sendSMS(Request $request)
    {    
        $customers = (new NewDepositors(Carbon::now()->subDays(7)))->get();
    }


    public function index(Request $request)
    {
        $q = \Request::has("query") ? \Request::get("query") : "";

        $field = \Request::has("field") ? \Request::get("field") : "id";

        $users = User::ByBroker()->where($field, "LIKE", "%$q%")
            ->orderBy("id", "desc")
            ->paginate(25);

        return view('pages.admin.users', compact('users'));
    }

    public function show($id)
    {
        $u = User::with('permissions', 'department', 'broker')->findOrFail($id);
        $departments = Department::with('permissions')->get();
        $brokers = Broker::all();
        return view('pages.admin.user', compact('u', 'departments', 'brokers'));
    }

    public function create()
    {
        $departments = Department::with('permissions')->get();
        $permissions = Permission::all();
        $brokers = Broker::all();

        return view('pages.admin.create', compact('departments', 'brokers', 'permissions'));
    }


    public function getDepartments()
    {
        $departments = Department::with('permissions')->get();
        return $departments;
    }

    public function getBrokers()
    {
        $departments = Broker::all();
        return $departments;
    }

    public function store(StoreUserRequest $request)
    {

        $department = Department::where('name', $request->department_name)->first();
        if(is_null($department)) {
            return 'error';
        }
        $user = User::create([
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'department_id' => $department->id,
            'broker_id' => $request->broker_id
        ]);

        $user->permissions()->sync($request->permissions);
        return 'ok';
    }

    public function update(UpdateUserRequest $request)
    {
        $department = Department::where('name', $request->department_name)->first();

        $user = User::find($request->id);
        if(is_null($user)) {
            return 'error';
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->department_id = $department->id;
        $user->broker_id = $request->broker_id;
        
        if(!empty($request->password)){
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $user->permissions()->sync($request->permissions);

        return 'ok';

    }

    public function destroy($id)
    {
        $u = User::findOrFail($id);
        $u->delete();
        return redirect()->back();
    }


}
