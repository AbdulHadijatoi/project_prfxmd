<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusLogs extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    protected $table = 'bonus_logs';
    protected $fillable = [
        'log_id',
        'bonus_id',
        'data',
        'created_at',
        'created_by'];
}
