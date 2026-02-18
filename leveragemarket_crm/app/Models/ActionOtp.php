<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ActionOtp extends Model
{
    protected $table = 'action_otps';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'client_index',
        'module',
        'action',
        'otp_code',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    | Assumes your Employee model uses table: emplist
    */

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'client_index', 'client_index');
    }

    /*
    |--------------------------------------------------------------------------
    | Modules
    |--------------------------------------------------------------------------
    */

    const MODULE_USERGROUP   = 'usergroup';
    const MODULE_WALLET  = 'wallet';
    const MODULE_TRADING = 'trading';
    const MODULE_CRM     = 'crm';

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    const ACTION_VIEW   = 'view';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_EXPORT = 'export';

    /*
    |--------------------------------------------------------------------------
    | OTP Validity (60 seconds)
    |--------------------------------------------------------------------------
    */

    public function isExpired()
    {
        return Carbon::now()->diffInSeconds($this->created_at) > 60;
    }
}
