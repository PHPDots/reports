<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendMailUser extends Model
{
    public $timestamps = true;
    protected $table = TBL_SEND_MAIL_USERS;

    protected $fillable = ['client_id','user_id'];
}
