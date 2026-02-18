<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leverage extends Model
{
    use HasFactory;
    protected $table = 'leverage';
    public $timestamps=false;

    protected $fillable = [
        "account_type_id",
        "account_leverage"
    ];
}
