<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;
use Datatables;
use App\Models\User;
use App\Models\UserType;
use App\Models\AdminAction;
use App\Models\LeaveEmtitlementLog;
use App\Custom;
use Mail;
class UsersController extends Controller
{
    public function __construct() {

        $this->moduleRouteText = "users";
        $this->moduleViewName = "admin.users";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "User";
        $this->module = $module;

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new User();  

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
    public function index( Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_USERS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        if($request->get("changeID") > 0)
        {
            $goto = session()->get($this->moduleRouteText.'_goto');
            if(empty($goto)){  $goto = $this->list_url;  }

            $user_id = $request->get("changeID");   
            $status = $request->get("changeStatus");

            $request = \App\Models\User::find($user_id);
                if($request)
                {
                    $status = $request->status;

                    if($status == 0)
                        $status = 1;
                    else
                        $status = 0;

                    $request->status = $status;
                    $request->save();            

                        session()->flash('success_message', "Status has been changed successfully.");
                        return redirect($goto);
                }
                else
                {
                    session()->flash('success_message', "Status not changed, Please try again");
                    return redirect($goto);
                }

            return redirect("users");
        }

        $data = array();
        $data['page_title'] = "Manage Users"; 
        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_USERS);
        $data["types"] = \App\Models\UserType::pluck('title','id')->all();
        $data = customSession($this->moduleRouteText,$data);

        return view($this->moduleViewName.".index", $data); 
    }

    /**
     * Show the form for creating a new resource.   
     * 
     * @return \Illuminate\Http\Response 
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_USERS);
        
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
        $data['show_password'] = 1;
        $data["show_image"] =''; 
        $data['blood_groups'] = ['A+'=>'A+','B+'=>'B+','O+'=>'O+','AB+'=>'AB+','AB-'=>'AB-','A-'=>'A-','B-'=>'B-','O-'=>'O-'];
        $data["users_type"] = \App\Models\UserType::pluck('title','id')->all();
        $data = customBackUrl($this->moduleRouteText, $this->list_url, $data);

        return view($this->moduleViewName.'.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\m_responsekeys(conn, identifier)
     */
    public function store(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_USERS);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();
        $status = 1;
        $msg = "User has been created successfully.";
        $goto = $this->list_url;

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|min:2',
            'lastname' => 'required|min:2',
            'email' => 'required|email|unique:'.TBL_USERS.',email',
            'password' => 'required|min:4|same:password',            
            'confirm_password' => 'required|min:4|same:password',            
            'user_type_id' => 'required|exists:'.TBL_ADMIN_USER_TYPES.',id',           
            'address' => 'required|min:2',            
            'phone' => 'required|max:15',
            'status' => ['required', Rule::in([0,1])],
			'is_add_task' => ['required', Rule::in([0,1])],
            'image' => 'image|max:4000',
            'joining_date' => 'required',
            'blood_group' => ['required', Rule::in(['A+','B+','O+','AB+','AB-','A-','B-','O-'])],
            'account_nm' => 'required',
            'account_no' => 'required|numeric',
            'bank_nm' => 'required',
            'ifsc_code' => 'required',
			'dob' => 'required',
			'designation' => 'required',
			'salary' => 'min:0|numeric',
            'is_salary_generate' => Rule::in([0,1]),
        ]);
        
        // check validations
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
            $password = $request->get("password");
            $confirm_password = $request->get("confirm_password");
            $user_type_id = $request->get("user_type_id");
            $email = $request->get("email");
            $firstname = $request->get("firstname");
            $lastname = $request->get("lastname");
            $statuss = $request->get("status");
            $image = $request->file("image");
            $name = $firstname." ".$lastname;
            $joining_date = $request->get("joining_date");
            $blood_group = $request->get("blood_group");
            $account_nm = $request->get("account_nm");
            $account_no = $request->get("account_no");
            $bank_nm = $request->get("bank_nm");
            $ifsc_code = $request->get("ifsc_code");
			$dob = $request->get("dob");
            $pan_num = $request->get("pan_num");
            $adhar_num = $request->get("adhar_num");
            $designation = $request->get("designation");
			$is_add_task = $request->get("is_add_task");
            $salary = $request->get("salary");
            $is_salary_generate = $request->get("is_salary_generate");
			$relieving_date = $request->get("relieving_date");
			
            if($confirm_password == $password)
            {
                $user = new \App\Models\User;
                $user_id = $user->id;
                if(!empty($image)){
                
                //$destinationPath = public_path().'/images/users/';  
                $destinationPath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.$user_id;

                   $image_name =$image->getClientOriginalName();              
                   $extension =$image->getClientOriginalExtension();
                   $image_name=md5($image_name);
                   $profile_image= $image_name.'.'.$extension;
                   $file =$image->move($destinationPath,$profile_image);

                $user->image = $profile_image;
                } 
                
                $user->user_type_id = $user_type_id;
                $user->firstname = $firstname;
                $user->lastname = $lastname;
                $user->phone = $request->get("phone");
                $user->address = $request->get("address");
                $user->email = $email;
                $user->name = $name;
                $user->status = $statuss;
                $user->password = bcrypt($password);
                $user->joining_date = $joining_date;
                $user->blood_group = $blood_group;
                $user->account_nm = $account_nm;
                $user->account_no = $account_no;
                $user->bank_nm = $bank_nm;
                $user->ifsc_code = $ifsc_code;
				$user->dob = $dob;
                $user->pan_num = $pan_num;
                $user->adhar_num = $adhar_num;
                $user->designation = $designation;
                $user->is_add_task = $is_add_task;
                $user->relieving_date = $relieving_date;
                if(\Auth::guard('admins')->user()->id == SUPER_ADMIN_ID){
                    $user->salary = $salary;
				    $user->is_salary_generate = $is_salary_generate;
                }
                $user->save();   
                
                if(\Auth::guard('admins')->user()->id == SUPER_ADMIN_ID)
                {
                    if(!empty($salary))
                    {
                        $record = array();
                        $record['name'] = ucfirst($firstname).' '.ucfirst($lastname);
                        $record['designation'] = $designation;
                        $record['salary'] = $salary;
                        $record['annual'] = $salary*12;

                        $latter = view('pdf.offerLetter',['record'=>$record])->render();
                        $filename = $firstname.'_'.$lastname.'.doc';
                        $docfilename = "offer_letter_".$filename;                        
                        $makeDir = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.$user->id;
                        
						if(!is_dir($makeDir))
                        {
                            mkdir($makeDir);
                            if(!is_dir($makeDir.'/docs'))
                                mkdir($makeDir.'/docs');
                        }else{
                            if(!is_dir($makeDir.'/docs'))
                                mkdir($makeDir.'/docs');
                        }

                        $openfile = $makeDir.'/docs/offer_letter_'.$filename;
						
                        $myfile = fopen($openfile, "w") or die("Unable to open file!");
                        fwrite($myfile, $latter);
                        fclose($myfile);

                        if($myfile)
                        {
                            $document = new \App\Models\EmployessDocument(); 
                            $document->user_id = $user->id; 
                            $document->doc_type_id = OFFER_LETTER_ID;
                            $document->filename = $docfilename;
                            $document->save();
                        }
                    }
                }   
                
                $id = $user->id;
                
                //store logs detail
                $params = array();

                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->ADD_USERS;
                $params['actionvalue']  = $id;
                $params['remark']       = "Add User::".$id;

                $logs = \App\Models\AdminLog::writeadminlog($params); 

                // send email to user                
                $subject = "Reports PHPdots: Account Details";
                
                $Path = url("/");

                $message = array();             
                $message['firstname'] = $firstname;
                $message['lastname'] = $lastname;
                $message['email'] = $email;
                $message['password'] = $password;
                $message['link'] = $Path;

                $returnHTML = view('emails.create_user_temp',$message)->render();
            
                $params["to"]=$email;
                $params["subject"] = $subject;
                $params["body"] = $returnHTML;
                sendHtmlMail($params);
                
                session()->flash('success_message', $this->addMsg);
            }
            else
            {
                $status = 0;
                $msg = "Password and confirm password not matched.";
            }
        }
        return ['status' => $status, 'msg' => $msg, 'data' => $data, 'goto' => $goto];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $idate(format)
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
    public function edit($user)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_USERS);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $formObj = $user;

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
        $data["show_image"] ='1'; 
        $data['blood_groups'] = ['A+'=>'A+','B+'=>'B+','O+'=>'O+','AB+'=>'AB+','AB-'=>'AB-','A-'=>'A-','B-'=>'B-','O-'=>'O-'];
        $data["users_type"] = \App\Models\UserType::pluck('title','id')->all();
        $data = customBackUrl($this->moduleRouteText, $this->list_url, $data);

        return view($this->moduleViewName.'.add', $data);   
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_USERS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = $id;
        $id= $model->id;

        // $model = $this->modelObj->find($id);

        $data = array();
        $status = 1;
        $msg = $this->updateMsg;
        $goto = session()->get($this->moduleRouteText.'_goto');
        if(empty($goto)){  $goto = $this->list_url;  }

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|min:2',
            'lastname' => 'required|min:2',
            'email' => 'required|email|unique:'.TBL_USERS.',email,'.$id,
            'user_type_id' => 'required|exists:'.TBL_ADMIN_USER_TYPES.',id',         
            'address' => 'required|min:2',            
            'phone' => 'required|max:15',
            'status' => ['required', Rule::in([0,1])],
			'is_add_task' => ['required', Rule::in([0,1])],
            'image' => 'image|max:4000',
            'joining_date' => 'required',
            'blood_group' => ['required', Rule::in(['A+','B+','O+','AB+','AB-','A-','B-','O-'])],
            'account_nm' => 'required',
            'account_no' => 'required|numeric',
            'bank_nm' => 'required',
            'ifsc_code' => 'required',
			'dob' => 'required',
			'balance_paid_leave' => 'required|min:0',
			'salary' => 'min:0|numeric',
            'is_salary_generate' => Rule::in([0,1]),
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
            $user_type_id = $request->get("user_type_id");
            $email = $request->get("email");
            $firstname = $request->get("firstname");
            $lastname = $request->get("lastname");
            $statuss = $request->get("status");
            $image = $request->file("image");
            $name = $firstname." ".$lastname;
            $joining_date = $request->get("joining_date");
            $blood_group = $request->get("blood_group");
            $account_nm = $request->get("account_nm");
            $account_no = $request->get("account_no");
            $bank_nm = $request->get("bank_nm");
            $ifsc_code = $request->get("ifsc_code");
			$dob = $request->get("dob");
            $pan_num = $request->get("pan_num");
            $adhar_num = $request->get("adhar_num");
            $designation = $request->get("designation");
			$is_add_task = $request->get("is_add_task");
			$balance_paid_leave = $request->get("balance_paid_leave");
            $salary = $request->get("salary");
            $is_salary_generate = $request->get("is_salary_generate");
			$relieving_date = $request->get("relieving_date");
			$old_balance_leave = $model->balance_paid_leave;
            
            if($request->get('balance_paid_leave') > 0 && $request->get('balance_paid_leave') != $old_balance_leave)
            {
                $type ='';

                if($request->get('balance_paid_leave') > $old_balance_leave)
                {
                    $type = 'credit';
                    $days = $request->get('balance_paid_leave') - $old_balance_leave;
                }
                else
                {
                    $type = 'debit';
                    $days = $old_balance_leave - $request->get('balance_paid_leave');
                }
                
                $log = new LeaveEmtitlementLog();
                $log->user_id = $id;
                $log->credit_debit_type = $type;
                $log->total_leaves = $days;
                $log->old_balance_leave = $old_balance_leave;
                $log->new_balance_leave = $request->get('balance_paid_leave');
                $log->remark = 'Admin updated via user edit form.';
                $log->save();
            }
             
            if($request->hasFile('image'))
            {
                if(!empty($image)){
                    $destinationPath = public_path().'/uploads/users/'.$id.'/'; 
            
                    $image_name =$image->getClientOriginalName();              
                    $extension =$image->getClientOriginalExtension();
                    $image_name=md5($image_name);
                    $profile_image= $image_name.'.'.$extension;
                    $file =$image->move($destinationPath,$profile_image);
                    
                    $url = public_path().'/uploads/users/'.$id.'/'.$model->image;
                   // unlink($url); 

                $model->image = $profile_image;    
                }
            }
            $model->user_type_id = $user_type_id;
            $model->firstname = $firstname;
            $model->lastname = $lastname;
            $model->phone = $request->get("phone");
            $model->address = $request->get("address");
            $model->email = $email;
            $model->name = $name;
            $model->status = $statuss;
            $model->joining_date = $joining_date;
            $model->blood_group = $blood_group;
            $model->account_nm = $account_nm;
            $model->account_no = $account_no;
            $model->bank_nm = $bank_nm;
            $model->ifsc_code = $ifsc_code;
			$model->dob = $dob;
            $model->pan_num = $pan_num;
            $model->adhar_num = $adhar_num;
            $model->designation = $designation;
			$model->is_add_task = $is_add_task;
            $model->balance_paid_leave = $balance_paid_leave;
            $model->relieving_date = $relieving_date;
            if(\Auth::guard('admins')->user()->id == SUPER_ADMIN_ID){
                $model->salary = $salary;
                $model->is_salary_generate = $is_salary_generate;
            }
			$model->save();

            $id = $model->id;

            //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->EDIT_USERS;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit User::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_USERS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $modelObj = $id;
        $id= $modelObj->id;

        if($modelObj) 
        {
            try 
            {             
                $backUrl = $request->server('HTTP_REFERER');
                $goto = session()->get($this->moduleRouteText.'_goto');
                if(empty($goto)){  $goto = $this->list_url;  }
                $url = public_path().'/uploads/users/'.$id.'/'.$modelObj->image;
                if (is_file($url)) {
                    unlink($url);
                }

                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg); 

                //store logs detail
                    $params=array();
                    
                    $params['adminuserid']  = \Auth::guard('admins')->id();
                    $params['actionid']     = $this->adminAction->DELETE_USERS;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete User::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_USERS);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->user()->id;

        $model = User::select(TBL_USERS.".*",TBL_ADMIN_USER_TYPES.".title as user_type")
                ->join(TBL_ADMIN_USER_TYPES,TBL_USERS.".user_type_id","=",TBL_ADMIN_USER_TYPES.".id");


        return Datatables::eloquent($model)
               
            ->addColumn('action', function(User $row) {                

                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_USERS),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_USERS),
                        'user_status_link' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_USERS),
						'viewTask' => \App\Models\Admin::isAccess(\App\Models\Admin::$LIST_TASKS),
                    ]
                )->render();
            })
            ->editColumn('status', function ($row) {
                    if ($row->status == 1)
                        return "<a class='btn btn-xs btn-success'>Active</a><br/># ".$row->balance_paid_leave;
                    else 
                        return '<a class="btn btn-xs btn-danger">Inactive</a>';
            })->rawColumns(['status','action'])
                            
            ->filter(function ($query) 
            {
                $search_start_date = request()->get("search_start_date");
                $search_end_date = request()->get("search_end_date");
                $search_id = request()->get("search_id");                                         
                $search_fnm = request()->get("search_fnm");                                         
                $search_lnm = request()->get("search_lnm");                                         
                $search_email = request()->get("search_email");
                $search_type = request()->get("search_type");
                $search_status = request()->get("search_status");

                $searchData = array();
                customDatatble($this->moduleRouteText);

                if (!empty($search_start_date)){

                    $from_date=$search_start_date.' 00:00:00';
                    $convertFromDate= $from_date;

                    $query = $query->where(TBL_USERS.".created_at",">=",addslashes($convertFromDate));
                    $searchData['search_start_date'] = $search_start_date;
                }
                if (!empty($search_end_date)){

                    $to_date=$search_end_date.' 23:59:59';
                    $convertToDate= $to_date;

                    $query = $query->where(TBL_USERS.".created_at","<=",addslashes($convertToDate));
                    $searchData['search_end_date'] = $search_end_date;
                }

                if(!empty($search_id))
                {
                    $idArr = explode(',', $search_id);
                    $idArr = array_filter($idArr);                
                    if(count($idArr)>0)
                    {
                        $query = $query->whereIn(TBL_USERS.".id",$idArr);
                        $searchData['search_id'] = $search_id;
                    } 
                } 
                if(!empty($search_fnm))
                {
                    $query = $query->where(TBL_USERS.".firstname", 'LIKE', '%'.$search_fnm.'%');
                    $searchData['search_fnm'] = $search_fnm;
                }
                if(!empty($search_lnm))
                {
                    $query = $query->where(TBL_USERS.".lastname", 'LIKE', '%'.$search_lnm.'%');
                    $searchData['search_lnm'] = $search_lnm;
                }
                if(!empty($search_email))
                {
                    $query = $query->where(TBL_USERS.".email", 'LIKE', '%'.$search_email.'%');
                    $searchData['search_email'] = $search_email;
                }
                if(!empty($search_type))
                {
                    $query = $query->where(TBL_USERS.".user_type_id",$search_type);
                    $searchData['search_type'] = $search_type;
                }
                if($search_status == "1" || $search_status == "0" )
                {
                    $query = $query->where(TBL_USERS.".status", $search_status);
                }
                    $searchData['search_status'] = $search_status;
                    $goto = \URL::route($this->moduleRouteText.'.index', $searchData);
                    \session()->put($this->moduleRouteText.'_goto',$goto);
            })
            ->make(true);
	}
	 
    
}
