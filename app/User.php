<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role,status','position',
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
     * Check if the user is admin or normal user
     *
     */
    public function isAdmin(){
        return $this->role === 1? true: false;
    }

    /**
     * Check if the user is admin or normal user
     *
     */
    public function isActivated(){
        return $this->status === 1? true: false;
    }


}
