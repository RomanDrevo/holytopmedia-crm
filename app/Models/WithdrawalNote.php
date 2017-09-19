<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
class WithdrawalNote extends Model
{
    protected $fillable = ["user_id", "withdrawal_id", "content", "broker_id"];

    protected $table = 'withdrawal_notes';

    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}