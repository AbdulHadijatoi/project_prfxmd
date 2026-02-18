<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class EmployeeList extends Authenticatable
{
    protected $table = 'emplist';
    protected $primaryKey = 'client_index';
    protected $fillable = ['admin_login_otp', 'admin_login_otp_created_at'];
    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id', 'role_id');
    }
    public function rmMappings()
    {
        return $this->hasMany(RmMapping::class, 'supervisor_id', 'email');
    }
}
