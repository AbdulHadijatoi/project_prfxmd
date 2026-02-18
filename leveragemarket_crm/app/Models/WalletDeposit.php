<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletDeposit extends Model
{
    use HasFactory;
    protected $table = 'wallet_deposit';
    protected $fillable = [
        'email',
        'deposit_amount',
        'deposit_type',
        'transaction_id',
        'Status',
        'created_at',
        'updated_at',
        'admin_email',
        'payment_log_id',
        'deposit_proof',
        'usdt_wallet_id',
        'usdt_wallet_qr'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'email','email');
    }
}
