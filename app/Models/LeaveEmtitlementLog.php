<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class LeaveEmtitlementLog extends Model
{
    public $timestamps = false;
    protected $table = TBL_LEAVE_ENTITLEMENT_LOG;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'credit_debit_type', 'total_leaves', 'remark', 'old_balance_leave','new_balance_leave','created_at'];

    public static function addBalancePaidLeave($user_id,$remark,$leave_day)
	{
	    $user_detail = User::find($user_id);
	    if(!empty($user_detail))
	    {
	        $old_balance_leave = $user_detail->balance_paid_leave;
	        $new_balance_leave = $old_balance_leave + $leave_day;

	        $user_detail->balance_paid_leave = $new_balance_leave;
	        $user_detail->save();

	        $log = new LeaveEmtitlementLog();

	        $log->user_id = $user_id;
	        $log->credit_debit_type = 'credit';
	        $log->total_leaves = $leave_day;
	        $log->old_balance_leave = $old_balance_leave;
	        $log->new_balance_leave = $new_balance_leave;
	        $log->remark = $remark;
	        $log->save();
	    }
	}
}
