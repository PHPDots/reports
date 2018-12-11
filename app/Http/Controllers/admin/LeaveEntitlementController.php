<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\LeaveEntitlement;
use App\Models\LeaveEmtitlementLog;
use App\Models\User;
use App\Models\AdminAction;

class LeaveEntitlementController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "leave-entitlement";
        $this->moduleViewName = "admin.leave_entitlement";
        $this->list_url = route($this->moduleRouteText.".index");  

        $this->modelObj = new LeaveEntitlement();
        $this->adminAction= new AdminAction;

        $module = "Leave Entitlement";
        $this->module = $module;

        $this->addMsg = $module . " has been added successfully!";
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_LEAVE_ENTITLEMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array(); 
        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_LEAVE_ENTITLEMENT);
        $data['page_title'] = "Manage User Leave Entitlement";
        $data['users'] = User::whereIn('user_type_id',[1,3])->whereNotin('id',[10,1])->pluck('name','id')->all();
        $data["months"] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December']; 
        $data["years"] = ['2016'=>'2016','2017'=>'2017','2018'=>'2018','2019'=>'2019','2020'=>'2020','2021'=>'2021','2022'=>'2022','2023'=>'2023','2024'=>'2024','2025'=>'2025','2026'=>'2026','2027'=>'2027','2028'=>'2028','2029'=>'2029','2030'=>'2030'];
        $data = customSession($this->moduleRouteText,$data,100);

        return view($this->moduleViewName.".index", $data);         
    }

    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_LEAVE_ENTITLEMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = LeaveEntitlement::select(TBL_LEAVE_ENTITLEMENT.".*",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_LEAVE_ENTITLEMENT.".user_id","=",TBL_USERS.".id");

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
            ->editColumn('leave_type', function($row){
                $html = '';
                if($row->leave_type == 0)
                    $html = '<a class="btn btn-primary btn-xs">annual paid leave</a><a class="btn btn-primary btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i></a>';
                else if($row->leave_type == 2)
                    $html = '<a class="btn btn-warning btn-xs">extra deduct leave</a><a class="btn btn-warning btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i></a>';
                else
                    $html = '<a class="btn btn-success btn-xs">extra paid leave</a><a class="btn btn-success btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i></a>';
                return $html;
            })
            ->editColumn('leave_day', function($row){
                $html = '';
                if($row->leave_day == 1)
                    $html = '<a class="btn btn-default btn-xs">Full</a>';
                else
                    $html = '<a class="btn btn-default btn-xs">Half</a>';
                return $html;
            })
            ->rawColumns(['created_at','leave_type','leave_day'])
          
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

                    $query = $query->where(TBL_LEAVE_ENTITLEMENT.".created_at",">=",addslashes($convertFromDate));
                    $searchData['search_start_date'] = $search_start_date;
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_LEAVE_ENTITLEMENT.".created_at","<=",addslashes($convertToDate));
                    $searchData['search_end_date'] = $search_end_date;
                }
                if (!empty($search_user)) {
                    $query = $query->where(TBL_LEAVE_ENTITLEMENT . ".user_id", $search_user);
                    $searchData['search_user'] = $search_user;
                }
                if(!empty($search_month))
                {
                    $query = $query->where(TBL_LEAVE_ENTITLEMENT.".month", $search_month);
                    $searchData['search_month'] = $search_month;
                }
                if(!empty($search_year))
                {
                    $query = $query->where(TBL_LEAVE_ENTITLEMENT.".year", $search_year);
                    $searchData['search_year'] = $search_year;
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LEAVE_ENTITLEMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        
        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add ".$this->module;
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST"; 
        $data['action_url'] = $this->moduleRouteText.".store";
        $data['users'] =  User::whereIn('user_type_id',[1,3])
                                ->whereNotIn('id',[10,1])
                                ->where('status',1)->pluck('name','id')->all();
        $data["months"] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December']; 
        $data["years"] = ['2016'=>'2016','2017'=>'2017','2018'=>'2018','2019'=>'2019','2020'=>'2020','2021'=>'2021','2022'=>'2022','2023'=>'2023','2024'=>'2024','2025'=>'2025','2026'=>'2026','2027'=>'2027','2028'=>'2028','2029'=>'2029','2030'=>'2030'];
        $data = customBackUrl($this->moduleRouteText, $this->list_url, $data);

        return view($this->moduleViewName.'.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LEAVE_ENTITLEMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'month' => ['required',Rule::in(['01','02','03','04','05','06','07','08','09','10','11','12'])],
            'user_id' => 'required|exists:'.TBL_USERS.',id',
            'year' => ['required',Rule::in(['2016','2017','2018','2019','2020','2021','2022','2023','2024','2025','2026','2027','2028','2029','2030'])],
			'leave_day' => ['required',Rule::in(['1','0.5'])],
            'type' => ['required',Rule::in(['credit','debit'])],
        ]);
        if ($validator->fails())
        {
            $messages = $validator->messages();
            
            $status = 0;
            $msg = "";
            
            foreach ($messages->all() as $message) 
            {
                $msg .= $message . "<br />";
            }
        }         
        else
        {  
            $user_id = $request->get('user_id');
            $month = $request->get('month');
            $year = $request->get('year');
			$leave_day = $request->get('leave_day');
            $type = $request->get('type');
            $remark = $request->get('remark');
			if(!empty($remark))
                $remark = $request->get('remark');
            else
                $remark = 'monthly one extra paid leave added';

            $leave_type = 1;
            if($type == 'debit')
                $leave_type = 2;

            $leave = new LeaveEntitlement();

            $leave->user_id = $user_id;
            $leave->month = $month;
            $leave->year = $year;
            $leave->remark = $remark;
			$leave->leave_day = $leave_day;
            $leave->leave_type = 1;
            $leave->save();

			$remark = 'added from leave entitlement add form';
            if($type == 'credit'){
                LeaveEmtitlementLog::addBalancePaidLeave($user_id,$remark,$leave_day);
            }
            $id = $leave->id;
 
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_LEAVE_ENTITLEMENT;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Leave Entitlement::".$id;
                                    
            $logs= \App\Models\AdminLog::writeadminlog($params);
            
            session()->flash('success_message', $msg);
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data];              
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
