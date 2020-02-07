<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'emp_id', 'field', 'office', 'part', 'center_money','type','tel'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const ACCOUNT_TYPE = 3;
    const DEFAULT_TYPE = 2;
    const ADMIN_TYPE = 1;
    public function isAdmin(){
      return $this->type === self::ADMIN_TYPE;
    }
    public function isAccount(){
      return $this->type === self::ACCOUNT_TYPE;
    }

}
