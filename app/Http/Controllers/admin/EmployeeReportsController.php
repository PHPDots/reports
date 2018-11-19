<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminAction;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Client; 
use DB;

class EmployeeReportsController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "employee-reports";
        $this->moduleViewName = "admin.employee_reports";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Task";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Task();

        $this->addMsg = $module . " has been added successfully!";
        $this->updateMsg = $module . " has been updated successfully!";
        $this->deleteMsg = $module . " has been deleted successfully!";
        $this->deleteErrorMsg = $module . " can not deleted!";       

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EMPLOYEE_WISE_MONTHY_REPORT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();        
        $data['page_title'] = "Employee wise monthly hours by Project";
        $data['projects'] = \App\Models\Project::getList();
        $data['users'] = User::getList();

        $dates = \DB::table(TBL_TASK)->select(\DB::raw("MIN(task_date)as mindate,MAX(task_date) as maxdate"))->get();
        foreach ($dates as $date)
        {
            $start_date = $date->mindate;
            $mindate = date_create($date->mindate);
            $maxdate = date_create($date->maxdate);
        }

        $data['task_data'] = [];
        $start_date = $start_date;
        $end_date = date('Y-m-d h:m:s');

        while (strtotime($start_date) <= strtotime($end_date))
        {
            $start_date = date('Y-M',strtotime($start_date));
            $data['task_data'][date('Y-m',strtotime($start_date))] = $start_date; 
            $start_date = date ("Y-M", strtotime("+1 month", strtotime($start_date)));
        }

        return view($this->moduleViewName.".index", $data);
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
    

    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EMPLOYEE_WISE_MONTHY_REPORT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        DB::enableQueryLog();

        $model = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name",\DB::raw('SUM(total_time) as hours'))
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->groupBy(TBL_TASK.".user_id")
                ->groupBy(TBL_TASK.".project_id")
                ->groupBy(\DB::raw("(DATE_FORMAT(".TBL_TASK.".task_date,'%Y-%m'))"));

        $hours_query = Task::select(TBL_TASK.".*"); 

        $hours_query = Task::listFilter($hours_query);

        $totalHours = $hours_query->sum("total_time");
        $totalHours = number_format($totalHours,2); 

        $data = \Datatables::eloquent($model) 
              
            ->editColumn('task_date', function($row){
                if(!empty($row->task_date))          
                    return date("M, Y",strtotime($row->task_date));
                else
                    return '-';
            })->rawColumns(['task_date'])             
          
            ->filter(function ($query) 
            {
                $query = Task::listFilter($query); 
            }); 
        $data = $data->with('hours',$totalHours); 
        $data = $data->make(true);
        return $data;
    }
}
