<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransfer extends Model
{
    use HasFactory;
    protected $table = 'wallet_totransfer';
    protected $fillable = [
        'wallet_from',
        'wallet_to',
		'transfer_currency',
        'wallet_balance',
        'transfer_amount',
        'transfer_date',
        'transfer_note',
        'status',
        'created_at',
        'updated_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'email','email');
    }
}
