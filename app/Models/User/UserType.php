<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table='user_types';
    public $timestamps = true;
  	protected $primaryKey = 'id';

    public const ID_ADMIN = 1;
    public const ID_CLIENT = 2;

}