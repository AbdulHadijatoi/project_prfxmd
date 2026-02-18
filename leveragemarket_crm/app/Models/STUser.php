<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $st_user_id
 * @property integer $user_id
 * @property string $st_username
 * @property string $st_password
 * @property boolean $st_role
 * @property boolean $st_status
 * @property string $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property Aspnetuser $aspnetuser
 */
class STUser extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'st_users';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'st_user_id';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'st_username', 'st_password', 'st_role', 'st_status', 'updated_by', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
