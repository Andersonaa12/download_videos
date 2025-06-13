<?php

namespace App\Models\User;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password','type_id', 'active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    public function Type()
    {
        return $this->belongsTo(UserType::class, 'type_id');
    }

}