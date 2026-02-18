<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $rm_id
 * @property string $supervisor_id
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_at
 */
class RmMapping extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'rm_mapping';

    /**
     * @var array
     */
    protected $fillable = ['rm_id', 'supervisor_id', 'created_by', 'created_at', 'updated_at'];
}
