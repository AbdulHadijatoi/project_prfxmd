<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2POrdershistory extends Model
{
    protected $table="p2p_orderhistory";
	protected $fillable = [
        'orderid',
        'orderremarks',
        'status'
    ];
}

