<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientUsersRate extends Model
{
    public $timestamps = true;
    protected $table = TBL_CLIENT_USERS_RATES;

    /**
     * @var array
     */
    protected $fillable = ['client_id', 'user_id', 'rate'];
}
