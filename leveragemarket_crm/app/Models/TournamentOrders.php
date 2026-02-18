<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentOrders extends Model
{
    use HasFactory;
    const UPDATED_AT =null;
    protected $table='tournament_deals';
    protected $fillable = ['id','tournament_id','email','login','action','order_id', 'symbol', 'deal_id', 'lot','time','contract_size','profit'];
    public function user(){
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
