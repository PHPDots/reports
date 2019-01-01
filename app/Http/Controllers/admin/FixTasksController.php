<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\FixTask;
use App\Models\Client;

class FixTasksController extends Controller
{
    public function __construct() {
    
        $this->moduleRouteText = "fix-tasks";
        $this->moduleViewName = "admin.fix_tasks";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Task";
        $this->module = $module;

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new FixTask();

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_FIX_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Fixed Tasks";
        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_FIX_TASK);
        $data['clients'] = Client::pluck("name","id")->all();

        if($request->get("changeID") > 0)
        {
            $goto = session()->get($this->moduleRouteText.'_goto');
            if(empty($goto)){  $goto = $this->list_url;  }
            $task_id = $request->get("changeID");   
            $invoice_status = $request->get("changeStatus");

            $request = \App\Models\FixTask::find($task_id);
                if($request)
                {
                    $status = $request->invoice_status;

                    if($status == 0)
                        $invoice_status = 1;
                    else
                        $invoice_status = 0;

                    $request->invoice_status = $invoice_status;
                    $request->save();            

                        session()->flash('success_message', "Status has been changed successfully.");
                        return redirect($goto);
                }
                else
                {
                    session()->flash('success_message', "Status not changed, Please try again");
                    return redirect($goto);
                }

            return redirect($this->list_url);
        }
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_FIX_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        
        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add Fixed ".$this->module;
        $data['action_url'] = $this->moduleRouteText.".store";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST"; 
        $data["clients"] = \App\Models\Client::pluck('name','id')->all();
        $data = customBackUrl($this->moduleRouteText, $this->list_url, $data);
        return view($this->moduleViewName.'.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_FIX_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();
        $status = 1;
        $msg = $this->addMsg;
        $goto = $this->list_url;
        
        $validator = Validator::make($request->all(), [
            'client_id'=>'required|exists:'.TBL_CLIENT.',id',
            'title' => 'required|min:2',
            'description' => 'min:5',
            'assigned_by' => 'min:2',
            'task_date' => 'required',
            'hour' => 'required|numeric|min:0',
            'fix' => 'required|min:0|numeric',
            'rate' => 'required|min:0|numeric',
            'ref_link' => 'min:2',
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

            //store logs detail
            $params=array();
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_FIX_TASK;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Fix Task::".$id;
            $logs= \App\Models\AdminLog::writeadminlog($params);
            session()->flash('success_message', $msg);
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data, 'goto' => $goto];              
    }
    public function storess(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_FIX_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $status = 1;
        $msg = $this->addMsg;
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'client_id'=>'required|exists:'.TBL_CLIENT.',id',
            /*'title.*' => 'required|min:2',
            'description.*' => 'min:5',
            'assigned_by.*' => 'min:2',
            'task_date.*' => 'required',
            'hour.*' => 'required|numeric|min:0',
            'fix.*' => 'required|min:0|numeric',
            'rate.*' => 'required|min:0|numeric',
            'ref_link.*' => 'min:2',*/
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
            $client_id = $request->get('client_id');
            $title = $request->get('title');
            $hours = $request->get('hours');
            $fix = $request->get('fix');
            $rate = $request->get('rate');
            $ref_link = $request->get('ref_link');
            $assigned_by = $request->get('assigned_by');
            $task_date = $request->get('task_date');
            $description = $request->get('description');

            $i = 0;
            foreach($request->get("group-a") as $r)
            {
                $task_title = isset($r['title']) ? $r['title']:'';
                if(!empty($task_title))
                {
                $obj = new FixTask();
                $obj->client_id = $client_id;
                $obj->title = isset($r['title']) ? $r['title']:'';
                $obj->hour = isset($r['hours']) ? $r['hours']:'';
                $obj->fix = isset($r['fix']) ? $r['fix']:'';
                $obj->rate = isset($r['rate']) ? $r['rate']:'';
                $obj->ref_link = isset($r['ref_link']) ? $r['ref_link']:'';
                $obj->assigned_by = isset($r['assigned_by']) ? $r['assigned_by']:'';
                $obj->task_date = isset($r['task_date']) ? $r['task_date']:'';

                $obj->description = isset($r['description']) ? $r['description']:'';
                $obj->save();
                $id = $obj->id;
                
                //store logs detail
                $params=array();
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->ADD_FIX_TASK;
                $params['actionvalue']  = $id;
                $params['remark']       = "Add Fix Task::".$id;
                $logs= \App\Models\AdminLog::writeadminlog($params);                        
                }
                $i++;
            }
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_FIX_TASK);
        
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
        $data["clients"] = \App\Models\Client::pluck('name','id')->all();
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
       $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_FIX_TASK);
        
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
            'client_id'=>'required|exists:'.TBL_CLIENT.',id',
            'title' => 'required|min:2',
            'description' => 'min:5',
            'assigned_by' => 'min:2',
            'task_date' => 'required',
            'hour' => 'required|numeric|min:0',
            'fix' => 'required|min:0|numeric',
            'rate' => 'required|min:0|numeric',
            'ref_link' => 'min:2',
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
            $params['actionid']     = $this->adminAction->EDIT_FIX_TASK;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Fix Task::".$id;

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
    public function destroy($id,Request $request)
    {     
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_FIX_TASK);
        
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
                $params['actionid']     = $this->adminAction->DELETE_FIX_TASK;
                $params['actionvalue']  = $id;
                $params['remark']       = "Delete Fix Task::".$id;
                
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_FIX_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = FixTask::select(TBL_FIX_TASKS.".*",TBL_CLIENT.".name as client")
                ->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_FIX_TASKS.".client_id");

        return \Datatables::eloquent($model)
               
            ->addColumn('action', function(FixTask $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,
                        'isEdit' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_FIX_TASK),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_FIX_TASK),
                        'isMapStatus' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_FIX_TASK),
                    ]
                )->render();
            })
            ->editColumn('invoice_status', function ($row) { 
                if ($row->invoice_status == 1){
                    $html = "<a class='btn btn-xs btn-success'>Map</a><br/>";
                }
                else{
                    $html ='<a class="btn btn-xs btn-warning">Unmap</a><br/>';
                }
                    return $html;
            })
            ->addColumn('check_clm', function ($row) {
                    return '<div class="form-group form-md-checkboxes">
                                <div class="md-checkbox-inline">
                                    <div class="col-md-3">
                                    <div class="md-checkbox">
                                        <input type="checkbox" name="taskIds[]" id="checkbox'.$row->id.'" class="md-check sub-check" value="'.$row->id.'">
                                        <label for="checkbox'.$row->id.'">
                                            <span></span>
                                            <span class="check" style="z-index: 1;"></span>
                                            <span class="box" ></span>
                                        </label>
                                    </div>
                                    </div>
                                </div>
                            </div>';

            })
            ->editColumn('hour', function ($row) { 
                    $html = "# ".$row->hour.'<br/> # '.$row->fix.'<br/># '.$row->rate;
                    return $html;
            })
            ->editColumn('task_date', function ($row) { 
                $created_at = '-';
                $task_date = '-';
                if(!empty($row->task_date))
                    $task_date =  date("j M, Y",strtotime($row->task_date));
                if(!empty($row->created_at))
                    $created_at =  date("j M, Y",strtotime($row->created_at));

                $html = "# ".$task_date.'<br/> # '.$created_at;
                return $html;
            })
            ->editColumn('total', function ($row) { 
                    $row_total = ($row->hour * $row->rate) + $row->fix;
                    return $row_total;
            })
            ->rawColumns(['action','invoice_status','hour','check_clm','task_date'])
            
            ->filter(function ($query) 
            {
                $query = FixTask::listFilter($query);
            })
            ->make(true);
    }
    public function change_checked_status(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_FIX_TASK);
        
        if($checkrights)
        {
            return $checkrights;
        }
        
        $data = array();
        $status = 1;
        $msg = 'Status has been changed successfully !';
        $goto = session()->get($this->moduleRouteText.'_goto');
        if(empty($goto)){  $goto = $this->list_url;  }
        
        $rules = ['taskIds.required' => 'Please check atleast one id'];
        $validator = Validator::make($request->all(), [
            'taskIds'=>'required|exists:'.TBL_FIX_TASKS.',id',
            'status_type' => ['required', Rule::in([0,1])],
        ],$rules);
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
            $checkIDs = $request->get('taskIds');
            $status_type = $request->get('status_type');
            if(is_array($checkIDs) && !empty($checkIDs))
            {                
                \DB::table(TBL_FIX_TASKS)
                ->whereIn("id",$checkIDs)
                ->update(['invoice_status' => $status_type]);
                session()->flash('success_message', $msg);
            }
        }

        return ['status' => $status, 'msg' => $msg, 'data' => $data, 'goto' => $goto];
    }
}
