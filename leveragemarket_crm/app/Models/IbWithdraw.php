<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IbWithdraw extends Model
{
    use HasFactory;
    protected $table = 'ib1_withdraw';
    protected $fillable = [
        'email',
        'withdraw_type',
        'withdraw_amount',
        'amount_in_other_currency',
        'adjustment_inr',
        'withdraw_to',
        'withdrawal_currency',
        'client_bank'
    ];
	public $timestamps = false;
	
	public function user()
    {
        return $this->belongsTo(User::class, 'email','email');
    }
}
