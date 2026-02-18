<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'aspnetusers';
    protected $primaryKey = 'id';
     protected $fillable = [
        'email',
        'gender',
        'imgName',
        'password',
        'email_confirmed',
        'mfa_secret',
        'mfa_enable',
        'ib1',
        'ib2',
        'ib3',
        'ib4',
        'ib5',
        'ib6',
        'ib7',
        'ib8',
        'ib9',
        'ib10',
        'ib11',
        'ib12',
        'ib13',
        'ib14',
        'ib15',
        'emailToken',
        'group_id',
        'kycdocumentRequest'
    ];
    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
