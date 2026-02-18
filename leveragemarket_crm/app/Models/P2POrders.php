<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2POrders extends Model
{
    protected $table="p2p_orders";
	protected $fillable = [
        'orderId',
        'userid',
        'email',
        'merchantaccid',
        'orderprice',
        'orderpayamount',
        'orderpaycurrency',
        'orderreceiveamount',
        'orderreceivecurrency',
        'orderpaymentmethod',
        'orderpaymentproof'
    ];
}

