<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LeaveEmtitlementLog;
use App\Models\User;

class LeaveEmtitlementLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
    
        $this->moduleRouteText = "leave-entitlement-log";
        $this->moduleViewName = "admin.leave_emtitlement_log";
        $this->list_url = route($this->moduleRouteText.".index");  

        $this->modelObj = new LeaveEmtitlementLog();

        view()->share("list_url", $this->list_url);
        view()->share("moduleRouteText", $this->moduleRouteText);
        view()->share("moduleViewName", $this->moduleViewName);
    }

    public function index()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_LEAVE_ENTITLEMENT_LOG);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Leave Entitlement Log";
        $data['users'] = User::whereIn('user_type_id',[1,3])->whereNotin('id',[10,1])->pluck('name','id')->all();
        return view($this->moduleViewName.".index", $data);         
    }
    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_LEAVE_ENTITLEMENT_LOG);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = LeaveEmtitlementLog::select(TBL_LEAVE_ENTITLEMENT_LOG.".*",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_LEAVE_ENTITLEMENT_LOG.".user_id","=",TBL_USERS.".id");

        $data = \Datatables::eloquent($model)

            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))
                    return date("j M, Y h:m:s A",strtotime($row->created_at));
                else
                    return '-';    
            })
            ->editColumn('credit_debit_type', function($row){
                $html = '';
                if($row->credit_debit_type == 'credit')
                    $html = '<a class="btn btn-primary btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i></a>';
                else
                    $html = '<a class="btn btn-danger btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i></a>';
                return $html;
            })
            ->editColumn('balance_leave', function($row){
                
                    $html = 'Old [ '.$row->old_balance_leave.' ] ';
                    $html .= '<br/>New [ '.$row->new_balance_leave.' ] ';
                return $html;
            })             
            ->rawColumns(['created_at','credit_debit_type','balance_leave'])
          
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

                    $query = $query->where(TBL_LEAVE_ENTITLEMENT_LOG.".created_at",">=",addslashes($convertFromDate));
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_LEAVE_ENTITLEMENT_LOG.".created_at","<=",addslashes($convertToDate));
                }
                if (!empty($search_user)) {
                    $query = $query->where(TBL_LEAVE_ENTITLEMENT_LOG . ".user_id", $search_user);
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
