<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;
use Datatables;
use App\modells\AdminLog;
use App\Models\AdminAction; 
use App\Models\AssignTask; 
use App\Models\User;
use App\Models\TaskComment;

class AssignTasksController extends Controller
{
    public function __construct() {

        $this->moduleRouteText = "assign-tasks";
        $this->moduleViewName = "admin.assign_task";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Assign Task";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new AssignTask();  

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ASSIGN_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();        
        $data['page_title'] = "Manage Assign Task"; 

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_ASSIGN_TASK);

        $data['users'] = User::where('status',1)
        ->where('user_type_id', '!=', CLIENT_USER)
        ->where('id', '!=' ,'1')
        ->pluck("name","id")
        ->all();

        $data['projects'] = \App\Models\Project::getList();

        $auth_id = \Auth::guard('admins')->user()->user_type_id;
        
        $changeStatus = $request->get("changeStatus");
        $changeID = $request->get('changeID');


        if($auth_id == NORMAL_USER || $auth_id == TRAINEE_USER){
             
            $data['users_task']=''; 
            $viewName = $this->moduleViewName.".index";
            if($changeID > 0 && $changeStatus > 0)
            { 
                $this->changeStatus($changeID);
                return view($this->moduleViewName.".assignUserTaskIndex", $data);
            }

            return view($this->moduleViewName.".assignUserTaskIndex", $data);
        }
        else if($auth_id == ADMIN_USER_TYPE)
        {
            $data['users_task'] = User::getList();
        } 
        return view($this->moduleViewName.".index", $data); 
    }

    public function changeStatus($id)
    {  
        $record = $this->modelObj->find($id);
        if($record)
        {
            $oldStatus = $record->status;

            if($oldStatus == 0)
                $newStatus = 1;
            else
                $newStatus = 0;

            $record->status = $newStatus;
            $record->save(); 

            session()->flash('success_message', "Status has been changed successfully.");
            return redirect($this->list_url);
        }
        else
        {
            session()->flash('success_message', "Status not changed, Please try again");
            return redirect($this->list_url);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_ASSIGN_TASK);
        
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

        $data['users'] = User::where('status',1)
        ->where('user_type_id', '!=', CLIENT_USER)
        ->where('id', '!=' ,'1')
        ->pluck("name","id")
        ->all();

        $data['projects'] = \App\Models\Project::getList();
         
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_ASSIGN_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();
        $status = 1;
        $msg = $this->addMsg;
        $goto = $this->list_url; 

         
        $validator = Validator::make($request->all(), [
            'user_id.*' => 'exists:'.TBL_USERS.',id',
            'project_id.*' => 'required|exists:'.TBL_PROJECT.',id',
            'title.*' => 'required',
            'priority.*' => ['required', Rule::in([0,1,2])],
            'due_date.*' => 'required',
            'status.*' => ['required', Rule::in([0,1])],
            'description.*' => 'required'
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
        else{
         
            $project_id = $request->get('project_id');
            $title = $request->get('title');
            $description = $request->get('description'); 
            $statuss = $request->get('status');
            $priority = $request->get('priority');

            $auth_id = \Auth::guard('admins')->user()->user_type_id;
            $user = $request->get('user_id');

             
            if(!empty($user) && $auth_id == 1 && is_array($user))
            {
                $user_id = $request->get('user_id'); 
                $due_date = $request->get('due_date');
                $count = count($project_id);
                
                for($i=0; $i<$count; $i++)
                {
                    $obj = new AssignTask();
                    $obj->user_id = $user_id[$i];
                    $obj->project_id = $project_id[$i];
                    $obj->title = $title[$i];
                    $obj->description = $description[$i]; 
                    $obj->status = $statuss[$i];
                    $obj->priority = $priority[$i];
                    if(!empty($due_date[$i]))
                    {
                        $due_dates = $due_date[$i]; 
                        $due_dates=date("Y-m-d",strtotime($due_dates));
                    }
                    else{
                        $due_dates =  date("Y-m-d");
                    }

                    $obj->due_date = $due_dates;
                    $obj->save(); 
                    $id = $obj->id;  

                    //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->ADD_ASSIGN_TASK;
                $params['actionvalue']  = $id;
                $params['remark']       = "Add Assign Task::".$id;

                $logs=\App\Models\AdminLog::writeadminlog($params);
            
                }
            } 
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
    public function show($id, Request $request)
    {   
         
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_ASSIGN_TASK);
        
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
        $data['buttonText'] = "Add Comment"; 
        $data['action_url'] = $this->moduleRouteText.".update";
        $data['action_params'] = $formObj->id;
        $data['method'] = "PUT";
        $data['editMode'] = "";
         
        $data['users'] = User::where('status',1)
        ->where('user_type_id', '!=', CLIENT_USER)
        ->where('id', '!=' ,'1')
        ->pluck("name","id")
        ->all(); 

        
        $data['projects'] = \App\Models\Project::getList(); 
        //$data['viewTask'] = AssignTask::where('id','=',$id)->first(); 

        $data['viewTask'] = AssignTask::select(TBL_ASSIGN_TASK.".*",TBL_USERS.".name as user_name",TBL_PROJECT.".title as pro_title")
                ->join(TBL_USERS,TBL_ASSIGN_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_ASSIGN_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_ASSIGN_TASK.".id",$id) 
                ->first();
               

        $data['assignUser'] = TaskComment::select(TBL_TASK_COMMENT.".*",TBL_USERS.".name as user_names")
                ->join(TBL_USERS,TBL_TASK_COMMENT.".user_id","=",TBL_USERS.".id")
                ->where(TBL_TASK_COMMENT.".assing_task_id",$id)
                ->orderBy(TBL_TASK_COMMENT.'.created_at','desc')
                ->first(); 

         $data['assignUserTask'] = AssignTask::select(TBL_ASSIGN_TASK.".*",TBL_USERS.".name as user_names")
                ->join(TBL_USERS,TBL_ASSIGN_TASK.".user_id","=",TBL_USERS.".id")
                ->where(TBL_ASSIGN_TASK.".id",$id)  
                ->first();

        $data['viewComment'] = TaskComment::select(TBL_TASK_COMMENT.".*",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK_COMMENT.".comment_by_user_id","=",TBL_USERS.".id") 
                ->where(TBL_TASK_COMMENT.".assing_task_id",$id) 
                ->get();

        $data['viewCommentBy'] = TaskComment::select(TBL_TASK_COMMENT.".*",TBL_USERS.".name as comment_by_user_name")
                ->join(TBL_USERS,TBL_TASK_COMMENT.".comment_by_user_id","=",TBL_USERS.".id") 
                ->get();


        return view($this->moduleViewName.'.edit', $data);
    }
    public function SaveComment(Request $request)
    {   
        $data = array();
        $data['user_id'] = $request->user_id;
        $data['assing_task_id'] = $request->assing_task_id;
        $data['task_due_date'] = date("Y-m-d",strtotime($request->task_due_date));
        $data['task_priority'] = $request->task_priority;
        $data['comment_by_user_id'] = \Auth::guard('admins')->user()->id;
        $data['comments'] = $request->comments;

        $assign = AssignTask::find($request->assing_task_id);
        if($assign){
            $assign->user_id = $request->user_id;
            $assign->priority = $request->task_priority;
            $assign->due_date = date("Y-m-d",strtotime($request->task_due_date));
            $assign->save();
        }
        if(TaskComment::create($data)){

            session()->flash('success_message', 'Comment Saved!');  
            return ['status' => '1', 'msg' => 'Comment Saved', 'data' => '', 'goto' =>'']; 
        }
            return ['status' => '0', 'msg' => 'Something is wrong!', 'data' => '', 'goto' =>'']; 
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_ASSIGN_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        //$model= \App\Models\AssignTask::find($id);
        $model = $this->modelObj->find($id);

        $status = 1;
        $msg = $this->addMsg;
         
        $data = array();
        
        $validator = Validator::make($request->all(), [ 
            'user_id' => 'exists:'.TBL_USERS.',id',
            'comments' => 'required',
            'task_priority' => ['required', Rule::in([0,1,2])],
            'task_due_date' => 'required'
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
            $comments = $request->get('comments'); 
            $task_priority = $request->get('task_priority');

            $auth_id = \Auth::guard('admins')->user()->user_type_id;
            $user = $request->get('user_id');
            //dd($user);
            if(!empty($user) && $auth_id == 1 && is_array($user))
            {
                $user_id = $request->get('user_id');
                $task_due_date = $request->get('task_due_date'); 
                 
                    $model = new TaskComment();
                    $model->user_id = $user_id; 
                    $model->comments = $comments;
                    $model->task_priority = $task_priority; 
                    //dd($model);exit();  
                    if(!empty($task_due_date))
                    {
                        $task_due_dates = $task_due_date; 
                        $task_due_dates=date("Y-m-d",strtotime($task_due_dates));
                    }
                    else{
                        $task_due_dates =  date("Y-m-d");
                    }

                    $model->task_due_date = $task_due_dates;

                    $model->save(); 
                    $id = $model->id;
                    
                    //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->EDIT_ASSIGN_TASK;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit Assign Task::".$id;

                $logs=\App\Models\AdminLog::writeadminlog($params); 
            }            
            session()->flash('success_message', $msg);
        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_ASSIGN_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $modelObj = AssignTask::find($id);
        //$modelObj = $this->modelObj->find($id);

        if($modelObj) 
        {
            try 
            {             
                $backUrl = $request->server('HTTP_REFERER');
                $goto = session()->get($this->moduleRouteText.'_goto');
                if(empty($goto)){  $goto = $this->list_url;  }

                $taskComment = TaskComment::where('assing_task_id',$id)->first();
                $taskComment->delete();
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg); 

                //store logs detail
                $params=array();    
                                        
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->DELETE_ASSIGN_TASK;
                $params['actionvalue']  = $id;
                $params['remark']       = "Delete Assign Task::".$id;
                                        
                $logs=\App\Models\AdminLog::writeadminlog($params); 
                return redirect($goto); 
            } catch (Exception $e) 
            {
                session()->flash('error_message', $this->deleteErrorMsg);
                return redirect($this->list_url);
            }
        } else 
        {
            session()->flash('error_message', "Record not exists");
            return redirect($this->list_url);
        }
    }

    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ASSIGN_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }
       
         $model = AssignTask::select(TBL_ASSIGN_TASK.".*",TBL_USERS.".name as user_name",TBL_PROJECT.".title as pro_title")
                ->join(TBL_USERS,TBL_ASSIGN_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_ASSIGN_TASK.".project_id","=",TBL_PROJECT.".id");
        return Datatables::eloquent($model)
               
            ->addColumn('action', function($row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,  
                        'isEditHistory' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_ASSIGN_TASK),                
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_ASSIGN_TASK),
                        /*'isView' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_ASSIGN_TASK),*/               
                                                     
                    ]
                )->render();
            })
            ->editColumn('status', function($row) {
                if($row->status == 0)
                    return '<label class="badge badge-danger">Pending</label></td>';
                else
                    return '<label class="badge badge-success">Done</label></td>';
            })
            ->editColumn('priority', function($row) {
                if($row->priority == 0)
                    return '<label class="badge badge-primary">High</label></td>';
                elseif($row->priority == 2)
                    return '<label class="badge badge-warning">Medium</label></td>';
                else
                    return '<label class="badge badge-success">Low</label></td>';
            })
            ->editColumn('due_date', function($row){
                if(!empty($row->due_date))          
                    return date("j M, Y",strtotime($row->due_date)).'<br/><span style="color: blue; font-size: 12px">'.date("j M, Y",strtotime($row->created_at))."</span>";
                else
                    return '-';
            })->rawColumns(['status','action','created_at','priority','due_date'])                
            ->filter(function ($query) 
            {
                $search_user = request()->get("search_user");
                $search_title = request()->get("search_title"); 
                $search_priority = request()->get("search_priority"); 
                $search_status = request()->get("search_status");                                      
                if(!empty($search_user))
                {
                    $query = $query->where(TBL_ASSIGN_TASK.".user_id",$search_user);
                    $searchData['search_user'] = $search_user;
                }
                if(!empty($search_title))
                {
                    $query = $query->where(TBL_ASSIGN_TASK.".title", 'LIKE', '%'.$search_title.'%');
                    $searchData['search_title'] = $search_title;
                }
                if($search_priority == "1" || $search_priority == "0" || $search_priority == '2')
                {
                    $query = $query->where(TBL_ASSIGN_TASK.".priority", $search_priority);
                }
                if($search_status == "1" || $search_status == "0")
                {
                    $query = $query->where(TBL_ASSIGN_TASK.".status", $search_status);
                }
                 
            })
            ->make(true); 
    }
    public function assignUserTaskData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_ASSIGN_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->id(); 

        $model = AssignTask::select(TBL_ASSIGN_TASK.".*") 
                ->where(TBL_ASSIGN_TASK.".user_id",$auth_id);

        return Datatables::eloquent($model)
               
            ->addColumn('action', function(AssignTask $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row,
                        'isEditHistory' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_ASSIGN_TASK),                   
                        'assign_task_done' =>\App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_ASSIGN_TASK),                                
                    ]
                )->render();
            })
            ->editColumn('status', function($row) {
                if($row->status == 0)
                    return '<label class="badge badge-danger">Pending</label></td>';
                else
                    return '<label class="badge badge-success">Done</label></td>';
            })
            ->editColumn('priority', function($row) {
                if($row->priority == 0)
                    return '<label class="badge badge-primary">High</label></td>';
                elseif($row->priority == 2)
                    return '<label class="badge badge-warning">Medium</label></td>';
                else
                    return '<label class="badge badge-success">Low</label></td>';
            })->rawColumns(['status','action','created_at','priority'])                 
            ->filter(function ($query) 
            {   
                $search_title = request()->get("search_title"); 
                $search_priority = request()->get("search_priority"); 
                $search_status = request()->get("search_status"); 
                 
                if(!empty($search_title))
                {
                    $query = $query->where(TBL_ASSIGN_TASK.".title", 'LIKE', '%'.$search_title.'%');
                    $searchData['search_title'] = $search_title;
                }
                if($search_priority == "1" || $search_priority == "0" || $search_priority == '2')
                {
                    $query = $query->where(TBL_ASSIGN_TASK.".priority", $search_priority);
                }
                if($search_status == "1" || $search_status == "0")
                {
                    $query = $query->where(TBL_ASSIGN_TASK.".status", $search_status);
                }
            })
            ->make(true); 

    }

   /* public function viewData(Request $request)
    {     
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_ASSIGN_TASK);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $id = $request->get('task_id');

        if(!empty($id)){
            $data = array();
            $task = AssignTask::select(TBL_ASSIGN_TASK.".*",TBL_PROJECT.".title as project_name")
                ->join(TBL_PROJECT,TBL_PROJECT.".id","=",TBL_ASSIGN_TASK.".project_id")
                ->where(TBL_ASSIGN_TASK.".id",$id)
                ->first();
            $data['user_name'] = User::select('name')->where('id',$task->user_id)->first();
            $data['view'] = $task;    
        }
        return view("admin.assign_task.viewData",$data);
    }*/
     
}
