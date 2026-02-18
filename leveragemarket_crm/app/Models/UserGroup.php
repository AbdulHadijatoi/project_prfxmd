<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;
    protected $primaryKey='user_group_id';
    protected $fillable = ['user_group_id', 'group_name','group_code','status','is_visible','bankwire','bankwire_status','agent_account','agent_status','usdt_wallet_id','usdt_wallet_qr','usdt_status'];
}
