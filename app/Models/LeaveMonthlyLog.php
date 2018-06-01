<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveMonthlyLog extends Model
{
    public $timestamps = true;
    protected $table = TBL_LEAVE_MONTHLY_LOG;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'month', 'year', 'leave_taken', 'balance_leave'];
}
