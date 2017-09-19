<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
class DepositNote extends Model
{
    protected $fillable = ["user_id", "deposit_id", "content", "broker_id"];

    protected $table = 'deposit_notes';

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}