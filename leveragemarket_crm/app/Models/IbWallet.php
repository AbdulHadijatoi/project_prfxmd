<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IbWallet extends Model
{
    use HasFactory;
    protected $table = 'ib_wallet';
    protected $fillable = [
        'ib_wallet',
        'ib_withdraw',
        'email',
        'trade_id',
        'order_id',
		'comission_per_lot',
        'remark',
        'ib_level',
        'admin_id'
    ];
}
