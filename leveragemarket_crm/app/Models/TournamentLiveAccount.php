<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentLiveAccount extends Model
{
    use HasFactory;
    protected $table='tournament_liveaccount';
    public $timestamps = false;
    protected $fillable = [
        'tournament_id',
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
        'ib1'
    ];
    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type', 'ac_group');
    }
}
