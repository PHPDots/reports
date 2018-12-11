<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\SoftwareLicense;

class SoftwareLicenseConntroller extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "software-licenses";
        $this->moduleViewName = "admin.software_licenses";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Software License";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new SoftwareLicense();  

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
    public function index(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_SOFTWARE_LICENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
		$auth_id = \Auth::guard("admins")->user()->id;
		$auth_user =  superAdmin($auth_id);
		if($auth_user == 0) 
		{
			return Redirect('/dashboard');
		}
        $data = array();        
        $data['page_title'] = "Manage Software Licenses";
        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_SOFTWARE_LICENSE);
        $data = customSession($this->moduleRouteText,$data, 100);

       return view($this->moduleViewName.".index", $data);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_SOFTWARE_LICENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard("admins")->user()->id;
		$auth_user =  superAdmin($auth_id);
		if($auth_user == 0) 
		{
			return Redirect('/dashboard');
		}
        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add ".$this->module;
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST"; 
        $data['action_url'] = $this->moduleRouteText.".store";
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_SOFTWARE_LICENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
		$auth_id = \Auth::guard("admins")->user()->id;
		$auth_user =  superAdmin($auth_id);
		if($auth_user == 0) 
		{
			return Redirect('/dashboard');
		}
        $data = array();
        $status = 1;
        $msg = $this->addMsg;
        $goto = $this->list_url;

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'url' => 'required',
            'license_key' => 'required',
            'payment_type' => Rule::in(['CC','net banking']),
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
        {   $title = $request->get('title');
 
            $input = $request->all();
            $obj = $this->modelObj->create($input);
            $id = $obj->id;
 
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_SOFTWARE_LICENSE;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Software License::".$id;
                                    
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
    public function edit($id, Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_SOFTWARE_LICENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
		$auth_id = \Auth::guard("admins")->user()->id;
		$auth_user =  superAdmin($auth_id);
		if($auth_user == 0) 
		{
			return Redirect('/dashboard');
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
       $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_SOFTWARE_LICENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
		$auth_id = \Auth::guard("admins")->user()->id;
		$auth_user =  superAdmin($auth_id);
		if($auth_user == 0) 
		{
			return Redirect('/dashboard');
		}
        $model = $this->modelObj->find($id);

        $data = array();
        $status = 1;
        $msg = $this->updateMsg;
        $goto = session()->get($this->moduleRouteText.'_goto');
        if(empty($goto)){  $goto = $this->list_url;  }

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'url' => 'required',
            'license_key' => 'required',
            'payment_type' => Rule::in(['CC','net banking']),
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

            //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->EDIT_SOFTWARE_LICENSE;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit Software License::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_SOFTWARE_LICENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
		$auth_id = \Auth::guard("admins")->user()->id;
		$auth_user =  superAdmin($auth_id);
		if($auth_user == 0) 
		{
			return Redirect('/dashboard');
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
                    $params['actionid']     = $this->adminAction->DELETE_SOFTWARE_LICENSE;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Software License::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_SOFTWARE_LICENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
		$auth_id = \Auth::guard("admins")->user()->id;
		$auth_user =  superAdmin($auth_id);
		if($auth_user == 0) 
		{
			return Redirect('/dashboard');
		}
        $model = SoftwareLicense::query();

        return \Datatables::eloquent($model) 
               
            ->addColumn('action', function(SoftwareLicense $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_SOFTWARE_LICENSE),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_SOFTWARE_LICENSE),
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
                $search_title = request()->get("search_title");                                
                $search_license = request()->get("search_license");
                $search_type = request()->get("search_type");

                $searchData = array();
                customDatatble($this->moduleRouteText);

                if(!empty($search_title))
                {
                    $query = $query->where("title", 'LIKE', '%'.$search_title.'%');
                    $searchData['search_title'] = $search_title;
                }
                if(!empty($search_license))
                {
                    $query = $query->where("license_key", 'LIKE', '%'.$search_license.'%');
                    $searchData['search_license'] = $search_license;
                }
                if($search_type == "net banking" || $search_type == "CC")
                {
                    $query = $query->where("payment_type", $search_type);
                }                
                    $searchData['search_type'] = $search_type;
                    $goto = \URL::route($this->moduleRouteText.'.index', $searchData);
                    \session()->put($this->moduleRouteText.'_goto',$goto);
            })
            ->make(true);        
    }
}
