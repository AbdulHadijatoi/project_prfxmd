<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crypto extends Model
{
    protected $fillable = [
        'symbol',
        'name',
        'minprice',
        'maxprice',
        'defaultprice',
        'icon',
		'status',
    ];
	
	public function latestHistory()
	{
		return $this->hasOne(Cryptohistory::class, 'cryptoid')
					->latestOfMany();
	}
}

