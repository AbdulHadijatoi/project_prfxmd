<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeDeposits extends Model
{
    use HasFactory;
    protected $table = 'trade_deposit';
    public $timestamps = false;
    protected $fillable = [
        'email',
        'trade_id',
        'deposit_amount',
        'bonus_amount',
        'bonus_trans_id',
        'deposit_type',
        'deposit_from',
        'deposit_proof',
        'Status',
        'deposted_date',
        'bankwire_details',
        'created_by',
        'AdminRemark',
        'Js_Admin_Remark_Date',
        'admin_email',
        'payment_log_id',
        'usdt_wallet_qr',
        'usdt_wallet_id',
        'deposit_currency',
        'deposit_currency_amount',
        'deposit_currency_in_usd',
        'adj_amount',
        'deposit_account_details'
    ];
    public function liveAccount()
    {
        return $this->hasOne(LiveAccount::class, 'trade_id', 'trade_id');
    }
}
