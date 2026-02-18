<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusTransaction extends Model
{
    use HasFactory;
    protected $table="bonus_trans";
    public $timestamps=false;
    protected $fillable=["email","trade_id","bonus_amount","bonus_type","bonus_id","status","adminRemark","bonus_currency","created_by"];
}
