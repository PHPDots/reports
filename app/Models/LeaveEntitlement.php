<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveEntitlement extends Model
{
    public $timestamps = true;
    protected $table = TBL_LEAVE_ENTITLEMENT;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'month', 'year', 'remark', 'leave_type','leave_day','type','is_run'];
}
