<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LeaveMonthlyLog;

class LeaveMonthlyLogController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "monthly-leave-log";
        $this->moduleViewName = "admin.monthly_leave_log";
        $this->list_url = route($this->moduleRouteText.".index");

        $this->modelObj = new LeaveMonthlyLog();

        view()->share("list_url", $this->list_url);
        view()->share("moduleRouteText", $this->moduleRouteText);
        view()->share("moduleViewName", $this->moduleViewName);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_MONTHLY_LEAVE_LOG);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Monthly Leave Log";
        $data['users'] = User::whereIn('user_type_id',[1,3])->whereNotin('id',[10,1])->pluck('name','id')->all();
        $data["months"] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December']; 
        $data["years"] = ['2016'=>'2016','2017'=>'2017','2018'=>'2018','2019'=>'2019','2020'=>'2020','2021'=>'2021','2022'=>'2022','2023'=>'2023','2024'=>'2024','2025'=>'2025','2026'=>'2026','2027'=>'2027','2028'=>'2028','2029'=>'2029','2030'=>'2030'];
        return view($this->moduleViewName.".index", $data);         
    }


    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_MONTHLY_LEAVE_LOG);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = LeaveMonthlyLog::select(TBL_LEAVE_MONTHLY_LOG.".*",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_LEAVE_MONTHLY_LOG.".user_id","=",TBL_USERS.".id");

        $data = \Datatables::eloquent($model)

            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))
                    return date("j M, Y h:m:s A",strtotime($row->created_at));
                else
                    return '-';    
            })
            ->editColumn('month', function($row){
                $month ='';
                if(!empty($row->month))
                    if($row->month == 01)$month = 'January';
                    if($row->month == 02)$month = 'February';
                    if($row->month == 03)$month = 'March';
                    if($row->month == 04)$month = 'April';
                    if($row->month == 05)$month = 'May';
                    if($row->month == 06)$month = 'June';
                    if($row->month == 07)$month = 'July';
                    if($row->month == 8)$month = 'August';
                    if($row->month == 9)$month = 'September';
                    if($row->month == 10)$month = 'October';
                    if($row->month == 11)$month = 'November';
                    if($row->month == 12)$month = 'December';
                return $month;    
            })             
            ->rawColumns(['created_at','month'])
          
            ->filter(function ($query) 
            {
                $search_start_date = request()->get("search_start_date");
                $search_end_date = request()->get("search_end_date");
                $search_user = request()->get("search_user");
                $search_month = request()->get("search_month");
                $search_year = request()->get("search_year");

                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_LEAVE_MONTHLY_LOG.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_LEAVE_MONTHLY_LOG.".created_at","<=",addslashes($convertToDate));
                }
                if (!empty($search_user)) {
                    $query = $query->where(TBL_LEAVE_MONTHLY_LOG . ".user_id", $search_user);
                }
                if(!empty($search_month))
                {
                    $query = $query->where(TBL_LEAVE_MONTHLY_LOG.".month", $search_month);
                }
                if(!empty($search_year))
                {
                    $query = $query->where(TBL_LEAVE_MONTHLY_LOG.".year", $search_year);
                }
            });
            
            $data = $data->make(true);

            return $data;        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
