<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator;
use App\Models\AdminAction;
use App\Models\User;
use App\Models\SalarySlip;
use App\Custom;
use App\Models\HolidayDetail;
use PDF;
//require_once(app_path() . "/mpdf/mpdf.php");


class SalarySlipController extends Controller
{

    public function __construct() {
    
        $this->moduleRouteText = "salary_slip";
        $this->moduleViewName = "admin.salary_slips";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Salary Slip";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new SalarySlip();  

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();        
        $data['page_title'] = "Manage Salary Slip";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_SALARY_SLIP);
        $data["months"] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December']; 
        $data["years"] = ['2016'=>'2016','2017'=>'2017','2018'=>'2018','2019'=>'2019','2020'=>'2020','2021'=>'2021','2022'=>'2022','2023'=>'2023','2024'=>'2024','2025'=>'2025','2026'=>'2026','2027'=>'2027','2028'=>'2028','2029'=>'2029','2030'=>'2030']; 

        $auth_id = \Auth::guard('admins')->user()->user_type_id;

        if ($auth_id == NORMAL_USER) {
            $data['users'] = '';
            $viewName = $this->moduleViewName . ".userIndex";
        } else {
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
			
            $data['users'] = User::pluck("name","id")->all();
            $viewName = $this->moduleViewName . ".index";        
        }
        $data = customSession($this->moduleRouteText,$data);
        return view($viewName, $data);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() 
    {
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        
        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add ".$this->module;
        $data['action_url'] = $this->moduleRouteText.".store";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST"; 
        $data["months"] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December']; 
        $data["years"] = ['2016'=>'2016','2017'=>'2017','2018'=>'2018','2019'=>'2019','2020'=>'2020','2021'=>'2021','2022'=>'2022','2023'=>'2023','2024'=>'2024','2025'=>'2025','2026'=>'2026','2027'=>'2027','2028'=>'2028','2029'=>'2029','2030'=>'2030']; 
        $data['users'] = User::pluck("name","id")->all();
        
        $data = customBackUrl($this->moduleRouteText, $this->list_url, $data);
        return view($this->moduleViewName.'.add', $data);
    }
    public function getuserdetail(Request $request)
    {
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        $data =array();
        $user_id = $request->get('user_id');
        $joining_date = '';
        $workingDays = 0;
        $leave_taken = 0;
        $balance_leave = 0;

        $userSetail = User::find($user_id);
        if(!empty($userSetail)){
            $data['account_no'] = $userSetail->account_no;
            $data['name'] = $userSetail->name;
            $data['bank_nm'] = $userSetail->bank_nm;
            $data['joining_date'] = $userSetail->joining_date;
            $data['pan_num'] = $userSetail->pan_num;
            $data['designation'] = $userSetail->designation;
            $joining_date = $userSetail->joining_date;
        }
        $month = $request->get('month');
        $year = $request->get('year');
        $month_year = $year.'-'.$month;
        
        $working_days = \App\Custom::countworkingDays($month_year);

        //Calculate Joining date wise working days
        $joining_month = date('Y-m',strtotime($joining_date));
        $current_month = date('Y-m');
        if($joining_month == $current_month)
        {
            $working_days = \App\Custom::countworkingDaysFromJoinigDate($joining_date);
        }
        //Get user monthly leave
        $user_leave_days =  \App\Custom::getUserMonthlyLeaveTaken($user_id,$month,$year);
        $leave_taken = $user_leave_days['leave_taken'];
        $balance_leave = $user_leave_days['balance_leave'];

        $data['working_days'] = $working_days;
        $data['leave_taken'] = $leave_taken;
        $data['balance_leave'] = $balance_leave;
		return $data; 
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
		
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();
        $status = 1;
        $msg = $this->addMsg;
        $goto = $this->list_url;

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:'.TBL_USERS.',id',
            'ctc' => 'required|numeric|min:2',
            'month' => ['required',Rule::in(['01','02','03','04','05','06','07','08','09','10','11','12'])],
            'year' => ['required',Rule::in(['2016','2017','2018','2019','2020','2021','2022','2023','2024','2025','2026','2027','2028','2029','2030'])],
            'account_num' => 'required',
            'joining_date' => 'required',
            'bank_name' => 'required',
            'working_days' => 'required|numeric|min:0',
            'designation' => 'required',
            'leave_taken' => 'required|numeric|min:0',
			'remaining_leave' => 'required|numeric',
            'pan_num' => 'required',
            'basic_salary' => 'required|numeric|min:0',
            'advance' => 'required|numeric|min:0',
            'hra' => 'required|numeric|min:0',
            'leave_deduction' => 'required|numeric|min:0',
            'conveyance_allowance' => 'required|numeric|min:0',
            'other_deduction' => 'required|numeric|min:0',
            'telephone_allowance' => 'required|numeric|min:0',
            'tds' => 'required|numeric|min:0',
            'medical_allowance' => 'required|numeric|min:0',
            'uniform_allowance' => 'required|numeric|min:0',
            'special_allowance' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'arrear_salary' => 'required|numeric|min:0',
            'advance_given' => 'required|numeric|min:0',
            'leave_encashment' => 'required|numeric|min:0',
            'total_earning' => 'required|numeric|min:0',
            'total_deduction' => 'required|numeric|min:0',
            'net_pay' => 'required|numeric|min:0',
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
            $input = $request->all();
            $obj = $this->modelObj->create($input);
            $id = $obj->id;
			//send mail
            if(!empty($id))
            {
                $month = $request->get('month');
                $year = $request->get('year');
                $user_id = $request->get('user_id');
                
                $user = User::find($user_id);
                $subject = "Salary Slip -".$month.'/'.$year;
                $description ="Your Salary Slip of ".$month.'/'.$year."  has been created successfully. please find below link, for it.";
                $link = url('/')."/salary_slip";

                $message = array();             
                $message['firstname'] = $user->firstname;
                $message['lastname'] = $user->lastname;
                $message['description'] = $description;
                $message['link'] = $link;

                $returnHTML = view('emails.salary_slip_temp',$message)->render();
                $auth_id = \Auth::guard('admins')->user();
                $empName = ucfirst($auth_id->firstname)." ".ucfirst($auth_id->lastname);

                $params["to"]=$user->email;
                $params["subject"] = $subject;
                $params["from"] = $auth_id->email;
                $params["from_name"] = $empName;  
                $params["body"] = $returnHTML;
                sendHtmlMail($params);    
            }
 
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_SALARY_SLIP ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Salary Slip::".$id;
                                    
            $logs= \App\Models\AdminLog::writeadminlog($params);
            
            session()->flash('success_message', $msg);                    
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data, 'goto' => $goto];              
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
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
		
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $formObj = $this->modelObj->find($id);

        if(!$formObj)
        {
            abort(404);
        }   

        $data = array();
        $data['formObj'] = $formObj;
        $data['page_title'] = "Edit ".$this->module;
        $data['buttonText'] = "Update";

        $data['action_url'] = $this->moduleRouteText.".update";
        $data['action_params'] = $formObj->id;
        $data['method'] = "PUT";
        $data["months"] = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December']; 
        $data["years"] = ['2016'=>'2016','2017'=>'2017','2018'=>'2018','2019'=>'2019','2020'=>'2020','2021'=>'2021','2022'=>'2022','2023'=>'2023','2024'=>'2024','2025'=>'2025','2026'=>'2026','2027'=>'2027','2028'=>'2028','2029'=>'2029','2030'=>'2030']; 
        $data['users'] = User::pluck("name","id")->all();
        $data = customBackUrl($this->moduleRouteText, $this->list_url, $data);

        return view($this->moduleViewName.'.edit', $data);
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
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
		
       $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $this->modelObj->find($id);

        $data = array();
        $status = 1;
        $msg = $this->updateMsg;
        $goto = session()->get($this->moduleRouteText.'_goto');
        if(empty($goto)){  $goto = $this->list_url;  }
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:'.TBL_USERS.',id',
            'ctc' => 'required|numeric|min:2',
            'month' => ['required',Rule::in(['1','2','3','4','5','6','7','8','9','10','11','12'])],
            'year' => ['required',Rule::in(['2016','2017','2018','2019','2020','2021','2022','2023','2024','2025','2026','2027','2028','2029','2030'])],
            'account_num' => 'required',
            'joining_date' => 'required',
            'bank_name' => 'required',
            'working_days' => 'required|numeric|min:0',
            'designation' => 'required',
            'leave_taken' => 'required|numeric|min:0',
			'remaining_leave' => 'required|numeric',
            'pan_num' => 'required',
            'basic_salary' => 'required|numeric|min:0',
            'advance' => 'required|numeric|min:0',
            'hra' => 'required|numeric|min:0',
            'leave_deduction' => 'required|numeric|min:0',
            'conveyance_allowance' => 'required|numeric|min:0',
            'other_deduction' => 'required|numeric|min:0',
            'telephone_allowance' => 'required|numeric|min:0',
            'tds' => 'required|numeric|min:0',
            'medical_allowance' => 'required|numeric|min:0',
            'uniform_allowance' => 'required|numeric|min:0',
            'special_allowance' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'arrear_salary' => 'required|numeric|min:0',
            'advance_given' => 'required|numeric|min:0',
            'leave_encashment' => 'required|numeric|min:0',
            'total_earning' => 'required|numeric|min:0',
            'total_deduction' => 'required|numeric|min:0',
            'net_pay' => 'required|numeric|min:0',
        ]);
        
        // check validations
        if(!$model)
        {
            $status = 0;
            $msg = "Record not found !";
        }
        else if ($validator->fails()) 
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
            $input = $request->all();
            $model->update($input); 
			if(!empty($id))
            {
                $month = $request->get('month');
                $year = $request->get('year');
                $user_id = $request->get('user_id');
                
                $user = User::find($user_id);
                $subject = "Salary Slip -".$month.'/'.$year;
                $description ="Your Salary Slip of ".$month.'/'.$year."  has been created successfully. please find below link, for it.";
                $link = url('/')."/salary_slip";

                $message = array();             
                $message['firstname'] = $user->firstname;
                $message['lastname'] = $user->lastname;
                $message['description'] = $description;
                $message['link'] = $link;

                $returnHTML = view('emails.salary_slip_temp',$message)->render();
                $auth_id = \Auth::guard('admins')->user();
                $empName = ucfirst($auth_id->firstname)." ".ucfirst($auth_id->lastname);

                $params["to"]=$user->email;
                $params["subject"] = $subject;
                $params["from"] = $auth_id->email;
                $params["from_name"] = $empName;  
                $params["body"] = $returnHTML;
                sendHtmlMail($params);      
            }

            //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->EDIT_SALARY_SLIP;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit Salary Slip::".$id;

                $logs=\App\Models\AdminLog::writeadminlog($params);         
        }
        
        return ['status' => $status,'msg' => $msg, 'data' => $data, 'goto' => $goto];               
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
		
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $modelObj = $this->modelObj->find($id); 

        if($modelObj) 
        {
            try 
            {
                $backUrl = $request->server('HTTP_REFERER');
                $modelObj->delete();
                $goto = session()->get($this->moduleRouteText.'_goto');
                if(empty($goto)){  $goto = $this->list_url;  }
                session()->flash('success_message', $this->deleteMsg); 

                //store logs detail
                    $params=array();
                    
                    $params['adminuserid']  = \Auth::guard('admins')->id();
                    $params['actionid']     = $this->adminAction->DELETE_SALARY_SLIP;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Salary Slip::".$id;

                    $logs=\App\Models\AdminLog::writeadminlog($params);    

                return redirect($goto);
            } 
            catch (Exception $e) 
            {
                session()->flash('error_message', $this->deleteErrorMsg);
                return redirect($this->list_url);
            }
        } 
        else 
        {
            session()->flash('error_message', "Record not exists");
            return redirect($this->list_url);
        }
    }

    public function data(Request $request)
    {
		//Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
		
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = SalarySlip::select(TBL_SALARY_SLIP.".*",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_SALARY_SLIP.".user_id");
		
		$salary_query = SalarySlip::select(TBL_SALARY_SLIP.".*")
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_SALARY_SLIP.".user_id");

        $salary_query = SalarySlip::listFilter($salary_query);
        $net_total = $salary_query->sum("net_pay");
        $net_total = number_format($net_total,2);

        $data = \Datatables::eloquent($model)        
            ->editColumn('month',function($row){
                return $row->month.'/'.$row->year;
            })  
            ->addColumn('action', function(SalarySlip $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_SALARY_SLIP),
                        'isPDF' =>\App\Models\Admin::isAccess(\App\Models\Admin::$LIST_SALARY_SLIP),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_SALARY_SLIP),
                        'isView' => \App\Models\Admin::isAccess(\App\Models\Admin::$LIST_SALARY_SLIP),
                    ]
                )->render();
            })
            
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['action'])
            
            ->filter(function ($query)
            {
                $query = SalarySlip::listFilter($query);
            });
            $data = $data->with('net_total',$net_total);

            $data = $data->make(true);

            return $data;        
    }
    public function userData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->id();

        $model = SalarySlip::select(TBL_SALARY_SLIP.".*",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_SALARY_SLIP.".user_id")
                ->where(TBL_SALARY_SLIP.".user_id", $auth_id);

        return \Datatables::eloquent($model)        
            ->editColumn('month',function($row){
                return $row->month.'/'.$row->year;
            })  
            ->addColumn('action', function(SalarySlip $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,                                 
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_SALARY_SLIP),
                        'isPDF' =>\App\Models\Admin::isAccess(\App\Models\Admin::$LIST_SALARY_SLIP),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_SALARY_SLIP),
						'isView' => \App\Models\Admin::isAccess(\App\Models\Admin::$LIST_SALARY_SLIP),
                    ]
                )->render();
            })
            
            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })->rawColumns(['action'])             
            
            ->filter(function ($query) 
            {                              
                $search_month = request()->get("search_month");                                
                $search_year = request()->get("search_year");                                         
                if(!empty($search_month))
                {
                    $query = $query->where(TBL_SALARY_SLIP.".month", $search_month);
                }
                if(!empty($search_year))
                {
                    $query = $query->where(TBL_SALARY_SLIP.".year", $search_year);
                }                   
            })
            ->make(true);        
    }


    function download_salary_slip(Request $request) 
    {
		$auth_id = \Auth::guard('admins')->id();
        $slip_id = $request->get('slip_id');
        $slip_detail = SalarySlip::select(TBL_SALARY_SLIP.".*",TBL_USERS.".firstname as firstname",TBL_USERS.".lastname as lastname",TBL_USERS.".name as user_name")
                    ->join(TBL_USERS,TBL_USERS.".id","=",TBL_SALARY_SLIP.".user_id")
                    ->where(TBL_SALARY_SLIP.'.id',$slip_id)
                    ->first();
        
		
		 $auth_user =  superAdmin($auth_id);
		
		if(($slip_detail && $slip_detail->user_id == $auth_id) || $auth_user == 1)
		{
			$empName = ucfirst($slip_detail->firstname)."_".ucfirst($slip_detail->lastname);
			$date = $empName.'_'.$slip_detail->month.'_'.$slip_detail->year;
			$data = array();
			$data['slip'] = $slip_detail;
			$pdf = PDF::loadView('pdf.salary_slip', $data);

        return $pdf->download("salary_slip_".$date.".pdf");
		}        
		else
		{
			abort(404);
		}
		
    }
	public function viewData(Request $request)
    {
		$auth_id = \Auth::guard('admins')->id();
		
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $slip_id = $request->get('slip_id');
        
        $data = array();

        if(!empty($slip_id))
		{
            $slip_detail = SalarySlip::select(TBL_SALARY_SLIP.".*",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_USERS.".id","=",TBL_SALARY_SLIP.".user_id")
                ->where(TBL_SALARY_SLIP.'.id',$slip_id)
                ->first();
        }
		
        $auth_user =  superAdmin($auth_id);
		
		if(($slip_detail && $slip_detail->user_id == $auth_id) || $auth_user == 1)
		{
			$data['slip'] = $slip_detail;	
			return view("pdf.salary_slip", $data);
		}        
		else
		{
			abort(404);
		}
    }
	public function salaryslip_for_all()
    {
        $auth_id = \Auth::guard("admins")->user()->id;
		$auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();
        $data['users'] = User::where('status',1)->orderBy('firstname')->whereNull('client_user_id')->where('id','!=',1)->where('is_salary_generate',1)->get();
        return view($this->moduleViewName.'.addForAll', $data);
    }
	public function salaryslip_for_all_data(Request $request)
    {
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
		
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_SALARY_SLIP);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();
        $status = 1;
        $msg = 'Salary slip has been generated successfully !';
        $working_days = 0;
        $month = '';
        $year = '';

        $validator = Validator::make($request->all(), [
            'user.*' => 'required|exists:'.TBL_USERS.',id',
            'month_year' => 'required',
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
            $users = $request->get('user');
            $month_year = $request->get('month_year');
            $salary_month_year = date('m-y',strtotime($request->get('month_year')));
            $current_month_year = date('m-y');

            $this_month = date('F-Y');
            
            if($salary_month_year > $current_month_year)
            {
                $status = 0;
                $msg = 'you can not add future month, please enter valid date !';
                return ['status' => $status, 'msg' => $msg];
            }
            if (!empty($month_year))
            {
                $date = explode('-', $month_year);
                if(is_array($date))
                {
                    $month = date('m',strtotime($date[0]));
                    $year = date('Y',strtotime($date[1]));
                }
                else
                {  
                    $status = 0;
                    $msg = 'please enter valid date format !';
                    return ['status' => $status, 'msg' => $msg];       
                }
                //Get Working Days
                $working_days = \App\Custom::countworkingDays($month_year);
            }
            if(is_array($users) && !empty($users))
            {
                $net_pay_words = '';
                $ctc = $basic_salary =$hra = $conveyance_allowance = $telephone_allowance = $medical_allowance = $uniform_allowance = $bonus = $arrear_salary = $advance_given = $leave_encashment = $advance = $leave_deduction = $other_deduction = $tds = $left_top_total = $left_down_total = $left_total = $right_total = $final_total = $total_earning = 0.0;
                            
                $i = 0;
                foreach ($users as $user_id)
                {
                    $user_data = \App\Models\User::find($user_id);
                    if($user_data)
                    {
                        $breakup = \App\Models\SalaryBreakup::where('user_id',$user_id)->first();
                        if($breakup)
                        {
                            if(!empty($user_data->salary))
                            {
                                $user_salary = round($user_data->salary);
                                $ctc = $user_data->salary;

                                $basic_salary = $ctc/2;
                                $hra = $breakup->hra;
                                $conveyance_allowance = $breakup->conveyance_allowance;
                                $telephone_allowance = $breakup->telephone_allowance;
                                $medical_allowance = $breakup->medical_allowance;
                                $uniform_allowance = $breakup->uniform_allowance;
                                $bonus = $breakup->bonus;
                                $arrear_salary = $breakup->arrear_salary;
                                $advance_given = $breakup->advance_given;
                                $leave_encashment = $breakup->leave_encashment;
                                $advance = $breakup->advance;
                                $leave_deduction = $breakup->leave_deduction;
                                $other_deduction = $breakup->other_deduction;
                                $tds = $breakup->tds;
                                //Leave Taken
                                $user_leave_days =  \App\Custom::getUserMonthlyLeaveTaken($user_id,$month,$year);
                                $leave_taken = $user_leave_days['leave_taken'];
                                $balance_leave = $user_leave_days['balance_leave'];
                                
                                $joining_month = date('Y-m',strtotime($user_data->joining_date));
                                $current_month = date('Y-m');
                                if($joining_month == $current_month)
                                {
                                    $joining_date = $user_data->joining_date;
                                    $working_days = \App\Custom::countworkingDaysFromJoinigDate($joining_date);
                                }

                                //Calculate Leave Deduction
                                $leaves = $leave_taken - $balance_leave; 
                                if($leaves > 0)
                                {
                                    $user_month_days = $working_days - $leaves;

                                    $leave_deduction = round(($user_salary * ($working_days-$user_month_days))/$working_days);
                                }
                                //Left Total 
                                $left_top_total = round($basic_salary) + round($hra) + round($conveyance_allowance) + round($telephone_allowance) + round($medical_allowance) + round($uniform_allowance);
                            
                                $special_allowance = $ctc - $left_top_total;

                                $left_down_total = round($bonus) + round($arrear_salary) + round($advance_given) + round($leave_encashment);

                                $left_total = $left_top_total + $special_allowance + $left_down_total;
                                $total_earning = $left_total;
                                //Right total
                                $right_total = round($advance) + round($leave_deduction) + round($other_deduction) + round($tds);


                                $final_total = $left_total - $right_total;
                            
                                $total_deduction = $right_total;
                                $net_pay = $final_total;

                                $net_pay_words = numberToWord($net_pay);

                                $salary = new SalarySlip();
                                $salary->user_id = $user_data->id;
                                $salary->ctc = $ctc;
                                $salary->month = $month;
                                $salary->year = $year;
                                $salary->account_num = $user_data->account_no;
                                $salary->joining_date = $user_data->joining_date;
                                $salary->bank_name = $user_data->bank_nm;
                                $salary->working_days = $working_days;
                                $salary->designation = $user_data->designation;
                                $salary->leave_taken = $leave_taken;
                                $salary->pan_num = $user_data->pan_num;
                                $salary->basic_salary = $basic_salary;
                                $salary->advance = $breakup->advance;
                                $salary->hra = $breakup->hra;
                                $salary->leave_deduction = $leave_deduction;
                                $salary->conveyance_allowance = $breakup->conveyance_allowance;
                                $salary->other_deduction = $breakup->other_deduction;
                                $salary->telephone_allowance = $breakup->telephone_allowance;
                                $salary->tds = $breakup->tds;
                                $salary->medical_allowance = $breakup->medical_allowance;
                                $salary->uniform_allowance = $breakup->uniform_allowance;
                                $salary->special_allowance = $breakup->special_allowance;
                                $salary->bonus = $breakup->bonus;
                                $salary->arrear_salary = $breakup->arrear_salary;
                                $salary->advance_given = $breakup->advance_given;
                                $salary->leave_encashment = $breakup->leave_encashment;
                                $salary->total_earning = $total_earning;
                                $salary->total_deduction = $total_deduction;
                                $salary->net_pay = $net_pay;
                                $salary->net_pay_words = $net_pay_words;
                                $salary->save();
                                
                                $salary_id = $salary->id;

                                $subject = "Salary Slip -".$month_year;
                                $description ="Your Salary Slip of ".$month_year." has been created successfully. please find below link, for it.";
                                $link = url('/')."/salary_slip";

                                $message = array();
                                $message['firstname'] = $user_data->firstname;
                                $message['lastname'] = $user_data->lastname;
                                $message['description'] = $description;
                                $message['link'] = $link;

                                $returnHTML = view('emails.salary_slip_temp',$message)->render();

                                $params["to"]=$user_data->email;
                                $params["subject"] = $subject;
                                $params["from"] = 'reports.phpdots@gmail.com';
                                $params["from_name"] = 'Reports PHPdots';  
                                $params["body"] = $returnHTML;
                                //sendHtmlMail($params);
                                
                                //store logs detail
                                $params=array();

                                $params['adminuserid']  = \Auth::guard('admins')->id();
                                $params['actionid']     = $this->adminAction->ADD_SALARY_SLIP;
                                $params['actionvalue']  = $salary_id;
                                $params['remark']       = "Add Salary Slip::".$salary_id;
                                //\App\Models\AdminLog::writeadminlog($params);
            
                                $i++;
                            }
                        }
                    }
                    $msg = 'Salary slip has been generated successfully ! , counter : <span style="color: red;"><b>'.$i.'<b></span>';
                }
            }
            else
            {
                $status = 0;
                $msg = 'Plese select at least one user !';
                return ['status' => $status, 'msg' => $msg];
            }
            session()->flash('success_message', $msg);
        }
        return ['status' => $status, 'msg' => $msg];
    }
}
