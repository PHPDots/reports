<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use Datatables;

class UserTaskHRReportController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "users-task-report";
        $this->moduleViewName = "admin.task_HR_report";
        $this->list_url = route($this->moduleRouteText.".index");  

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$USERS_TASK_REPORT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Users Task Hours Report";
        $data['users'] = User::whereNull('client_user_id')->pluck('name','id')->all();
        $data['months'] = getMonths();
        $data['years'] = getYear();
        $data = customSession($this->moduleRouteText,$data,100);

        return view($this->moduleViewName.".index", $data);
    }

    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$USERS_TASK_REPORT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
            $model = Task::select(TBL_TASK.".task_date as task_date",TBL_USERS.".name as user_name",\DB::raw('SUM(total_time) as hours'))
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->groupBy('user_id')
                ->groupBy(\DB::raw("DATE_FORMAT(tasks.task_date,'%Y-%m-%d')"))
                ->orderBy('task_date','desc');

            $data = \Datatables::eloquent($model)

            ->editColumn('task_date', function($row){
                
                if(!empty($row->task_date))          
                    return date("j F, Y",strtotime($row->task_date));
                else
                    return '-';
            })
            ->editColumn('hours', function($row){
                
                if($row->hours < 9)
                    return '<a class="btn btn-warning btn-xs">'.$row->hours.'</a>';
                else
                    return $row->hours;
            })
            ->rawColumns(['task_date','hours'])
          
            ->filter(function ($query)
            {
                $search_start_date = request()->get("search_start_date");
                $search_end_date = request()->get("search_end_date");
                $search_user = request()->get("search_user");
                $search_month = request()->get("search_month");
                $search_year = request()->get("search_year");

                $searchData = array();
                customDatatble($this->moduleRouteText);

                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_TASK.".task_date",">=",addslashes($convertFromDate));
                    $searchData['search_start_date'] = $search_start_date;
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_TASK.".task_date","<=",addslashes($convertToDate));
                    $searchData['search_end_date'] = $search_end_date;
                }

                if (!empty($search_month)) {

                    $query = $query->where(\DB::raw("DATE_FORMAT(".TBL_TASK.".task_date,'%m')"), $search_month);
                    $searchData['search_month'] = $search_month;
                }
                if (!empty($search_year)) {

                    $query = $query->where(\DB::raw("DATE_FORMAT(".TBL_TASK.".task_date,'%Y')"), $search_year);
                    $searchData['search_year'] = $search_year;
                }
                if (!empty($search_user)) {
                    $query = $query->where(TBL_TASK . ".user_id", $search_user);
                    $searchData['search_user'] = $search_user;
                }
                $goto = \URL::route($this->moduleRouteText.'.index', $searchData);
                \session()->put($this->moduleRouteText.'_goto',$goto);
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
