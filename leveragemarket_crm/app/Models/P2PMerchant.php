<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2PMerchant extends Model
{
    protected $table = 'p2p_merchantacc';

    protected $fillable = [
        'wanttype',
        'cryptoval',
        'currency_code',
        'pricetype',
        'quoteprice',
        'total_amount',
        'min_limit',
        'max_limit',
        'time_limit',
        'tags',
        'remarks',
        'autoreply',
        'transferstatus',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
}

