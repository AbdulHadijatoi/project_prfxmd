<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycUpdate extends Model
{
    use HasFactory;
    protected $table = 'kyc_update';
    public $timestamps=false;
    protected $fillable = [
        'email',
        'kyc_type','kyc_frontside','kyc_backside',
        'front_image','back_image'];
}
