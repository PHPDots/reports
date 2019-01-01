<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $fillable = ['title'];
    public $timestamps = true;
    protected $table = TBL_ADMIN_USER_TYPES;
}
