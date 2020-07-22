<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;


    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'emp_id', 'field', 'office', 'part', 'center_money','type','tel','user_id'
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
    const APPROVE2_TYPE = 5;
    const APPROVE1_TYPE = 4;
    const USER_TYPE = 3;
    const ADMIN_TYPE = 2;
    const SUPERADMIN_TYPE = 1;
    public function isSuperAdmin(){
      return $this->type === self::SUPERADMIN_TYPE;
    }
    public function isAdmin(){
      return $this->type === self::ADMIN_TYPE;
    }
    public function isUser(){
      return $this->type === self::USER_TYPE;
    }
    public function isApprove1(){
      return $this->type === self::APPROVE1_TYPE;
    }
    public function isApprove2(){
      return $this->type === self::APPROVE2_TYPE;
    }

    // const ACCOUNT_TYPE = 3;
    // const DEFAULT_TYPE = 2;
    // const ADMIN_TYPE = 1;
    // public function isAdmin(){
    //   return $this->type === self::ADMIN_TYPE;
    // }
    // public function isAccount(){
    //   return $this->type === self::ACCOUNT_TYPE;
    // }

}
