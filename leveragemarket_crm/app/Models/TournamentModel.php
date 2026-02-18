<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentModel extends Model
{
    use HasFactory;
    protected $table="tournaments";
    protected $casts = [
        'date' => 'date:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];
    protected $fillable=['name','date','entry_fee','description','email_description','starts_at','ends_at','shows_on','shows_list','status','send_notification','created_by','image','account_type','leverage'];
}
