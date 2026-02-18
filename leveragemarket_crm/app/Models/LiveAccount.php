<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveAccount extends Model
{
    use HasFactory;
    protected $table = 'liveaccount';
    public $timestamps = false;
    protected $fillable = [
        'balance',
        'credit',
        'MarginFree',
        'MarginLevel',
        'equity',
        'email',
        'name',
        'trade_id',
        'account_type',
        'leverage',
        'currency',
        'trader_pwd',
        'invester_pwd',
        'phone_pwd',
        'ib1',
        'ib2',
        'ib3',
        'ib4',
        'ib5',
        'ib6',
        'ib7',
        'ib8',
        'ib9',
        'ib10',
        'ib11',
        'ib12',
        'ib13',
        'ib14',
        'ib15'
    ];
    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type', 'ac_index');
    }
}
