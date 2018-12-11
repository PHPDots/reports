<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixTask extends Model
{
    protected $fillable = ['client_id','title','description','task_date','ref_link','assigned_by','hour','fix','rate','invoice_status'];
    public $timestamps = true;
    protected $table = TBL_FIX_TASKS;

    public static function listFilter($query)
    {
        $search_start_date = request()->get("search_start_date");
        $search_end_date = request()->get("search_end_date");
        $search_client = request()->get("search_client");
        $search_status = request()->get("search_status");

        $searchData = array();
        customDatatble('fix-tasks');

        if (!empty($search_start_date)) {

            $from_date = $search_start_date . ' 00:00:00';
            $convertFromDate = $from_date;

            $query = $query->where(TBL_FIX_TASKS.".created_at", ">=", addslashes($convertFromDate));
            $searchData['search_start_date'] = $search_start_date;
        }
        if (!empty($search_end_date)) {

            $to_date = $search_end_date . ' 23:59:59';
            $convertToDate = $to_date;

            $query = $query->where(TBL_FIX_TASKS.".created_at", "<=", addslashes($convertToDate));
            $searchData['search_end_date'] = $search_end_date;
        }
        if (!empty($search_client)) {
            $query = $query->where(TBL_CLIENT.".id",$search_client);
            $searchData['search_client'] = $search_client;
        }
        if($search_status == "1" || $search_status == "0")
        {
            $query = $query->where(TBL_FIX_TASKS.".invoice_status", $search_status);
        }
            $searchData['search_status'] = $search_status;
            $goto = \URL::route('fix-tasks.index', $searchData);
            \session()->put('fix-tasks_goto',$goto);

        return $query;
    }
    public static function getFixTasksList($client_id)
    {
        $rows = FixTask::where('client_id',$client_id)->where('invoice_status',0)->get();
        return $rows;
    }
}
