<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\DodFormCreated;

class Form extends Model
{
    protected $fillable = ['*'];

    protected $dates = ['signed_at', 'viewed_at'];

    public function customer()
    {
    	return $this->belongsTo(Customer::class);
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public static function createDod($attributes)
    {

    	$form = new static;
    	$form->user_id = \Auth::user()->id;
    	$form->customer_id = $attributes['customer_id'];
    	$form->access_code = str_random(40);
    	$form->server_ip = $_SERVER['SERVER_ADDR'];
    	$form->type = 'dod';
    	$form->data = json_encode([ 'deposits'	=>	$attributes['deposits'] ]);

    	$form->save();

    	event(new DodFormCreated($form, $attributes["email"]));

    	return $form;
    }

    public function depositsWithCC()
    {
    	$depositsData = json_decode($this->data)->deposits;

    	$deposits = collect([]);

    	foreach ($depositsData as $deposit) {
    		$dbDeposit = Deposit::findOrFail($deposit->id);
    		$dbDeposit->last4 = $deposit->last4;
    		$deposits->push($dbDeposit);
    	}

    	return $deposits;
    }

    public function deposits()
    {
    	$depositsData = json_decode($this->data)->deposits;

    	$depositsIds = [];

    	foreach ($depositsData as $deposit) {
    		array_push($depositsIds, $deposit->id);
    	}

    	return Deposit::findOrFail($depositsIds);
    }
}
