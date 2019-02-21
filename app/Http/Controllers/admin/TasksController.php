<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\ClientUsersRate;
use Excel;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
    
        $this->moduleRouteText = "tasks";
        $this->moduleViewName = "admin.tasks";
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

    public function index(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();        
        $data['page_title'] = "Manage Tasks";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_TASKS);
        $data['projects'] = \App\Models\Project::getList();
		$dates = \DB::table(TBL_TASK)->select(\DB::raw("MIN(task_date)as mindate,MAX(task_date) as maxdate"))->get();
        foreach ($dates as $date) 
		{
			$start_date = $date->mindate;
            $mindate = date_create($date->mindate);
            $maxdate = date_create($date->maxdate);
		} 
		
        //$maxdate->modify('+1 month');
		
        $data['task_data'] = [];
		$start_date = $start_date;
        $end_date = date('Y-m-d h:m:s');

		while (strtotime($start_date) <= strtotime($end_date))
		{
			$start_date = date('Y-M',strtotime($start_date));
			$data['task_data'][date('Y-m',strtotime($start_date))] = $start_date; 
			$start_date = date ("Y-M", strtotime("+1 month", strtotime($start_date)));
		}
		
        /*for ($i=$mindate; $i <= $maxdate; $i->modify('+1 month')) 
		{            
            $data['task_data'][$i->format('Y-m')] = $i->format('M-Y');          
        }*/
		
        $auth_id = \Auth::guard('admins')->user()->user_type_id;

        if($auth_id == NORMAL_USER || $auth_id == TRAINEE_USER){
            $data['users']='';
			$data['clients']='';
            $viewName = $this->moduleViewName.".userIndex";
        }
        else if($auth_id == ADMIN_USER_TYPE){
            $data['users'] = User::getList();
            $data['clients'] = Client::pluck("name","id")->all();
			
			$is_download = $request->get("isDownload");
			$is_download_xls = $request->get("isDownloadXls");

            if (!empty($is_download) && $is_download == 1) {
	
				$total = $request->get("is_total");
				
                $query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id");
                $rows = Task::listFilter($query);
                

                $records[] = array("No","User Name","Project","Task","Date","Hours","Status","Reference Link","Description");
                $i = 1;
                foreach($rows as $row)
                {
                    if($row->status == 1) $sts = "Completed"; else $sts = "In Progress";
                    $task_date = date("j M, Y",strtotime($row->task_date));
                    $records[] = [$i,$row->user_name,$row->project_name,$row->title,$task_date,$row->total_time,$sts,$row->ref_link,$row->description];
                $i++;
                }
				$records[] = array("total","","","","",$total,"","");
                $file_name = 'TasksDetails';
                header("Content-type: text/csv; charset=utf-8");
                header("Content-Disposition: attachment; filename=".$file_name.".csv");
                
                $fp = fopen('php://output', 'w');                
                fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
                    foreach ($records as $fields) {
                        fputcsv($fp, $fields);
                    }

                fclose($fp);                
                $path = public_path().'/'.$file_name.'.csv';
                exit;
            }
			if (!empty($is_download_xls) && $is_download_xls == 1)
            {
                $xls_client_id = $request->get('search_client');            
                $query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id");
                $query = Task::listFilter($query);
				$query = $query->orderBy('task_date');
                $rows = $query->get();

                $userWiseTasks= [];
                
                 if(count($rows) > 0 ) {
                    $xls_sheet = Excel::create('TasksDetails', function($excel) use ($rows,$xls_client_id) {
                         
                    $i = 1;
                   
                    foreach($rows as $row)
                    {
                    if($row->status == 1) $sts = "Completed"; else $sts = "In Progress";

                    $task_date = date("j M, Y",strtotime($row->task_date));
                     
                    $userWiseTasks[$row->user_id]['name'] = $row->user_name;
                    $userWiseTasks[$row->user_id]['time'][] = $row->total_time;

                    $time_total = (float)$row->total_time;
                    $userWiseTasks[$row->user_id]['tasks'][] = [$i,$row->project_name,$row->title,$task_date,$time_total,$sts,$row->ref_link,$row->description];

                    $i++;
                    }
                   
                    $task_title[] = array("No","Project","Task","Date","Hours","Status","Reference Link","Description");

                    foreach($userWiseTasks as $k => $v)
                    {
                        $total_time = array_sum($userWiseTasks[$k]['time']);
                        $time[$k][] = array("Total","","","",$total_time,"","");
                        $i =1;
                        foreach ($userWiseTasks[$k]['tasks'] as $key => $value) {
                            $userWiseTasks[$k]['tasks'][$key][0] = $i;
                            $i++;
                        }
                            $task_count = count($userWiseTasks[$k]['tasks']);
                            $task_count = $task_count+2;
                            //$userWiseTasks[$k]['count'] = $task_count;
                        //Bill Details 
                        $bill_detail[] = ['',$userWiseTasks[$k]['name'],$total_time,1,$total_time];
                        $bill_detail_rates[] = [$k,$userWiseTasks[$k]['name'],$total_time,1,$total_time];
                        $j =1;
                        $bill_total = 0;
                        foreach ($bill_detail as $key =>$v) {
                            $bill_detail[$key][0] = $j;
                            $j++;
                            $bill_total += $bill_detail[$key][4];
                        
                        }                          
                        //echo "<pre/>"; print_r($userWiseTasks);exit;

                        $excel->sheet($userWiseTasks[$k]['name'], function($sheet) use ($userWiseTasks,$k,$task_title,$time,$task_count) {
                            /*$sheet->setSize(array(
                                'A1' => array('width'=> 7,'height'=> 15),
                                'B1' => array('width'=> 25,'height' => 15),
                                'C1' => array('width'=> 50,'height' => 15),
                                'D1' => array('width'=> 15,'height' => 15),
                                'E1' => array('width'=> 10,'height' => 15),
                                'F1' => array('width'=> 13,'height' => 15),
                                'G1' => array('width'=> 60,'height' => 15),
                            ));*/
                            $sheet->setAutoSize(true);
                            $sheet->cell('A1:H1', function($cell) {
                                $cell->setAlignment('center');
                                $cell->setBackground('#aebbc2');
                                $cell->setFont(array('family'=> 'Calibri','size'=>'12','bold'=>true));
                            });
                            $sheet->setBorder('A1:H'.$task_count, 'thin');
                            $sheet->mergeCells('A'.$task_count.':D'.$task_count);
                            $sheet->cell('A'.$task_count.':D'.$task_count, function($cell) {
                                $cell->setAlignment('center');
                                $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                            });
                            $sheet->cell('E'.$task_count, function($cell) {
                                $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                            });
                            $sheet->freezeFirstRow();
                            $sheet->fromArray($task_title, null, 'A1', false, false);
                            $sheet->fromArray($userWiseTasks[$k]['tasks'], null, 'A1', false, false);
                            $sheet->fromArray($time[$k], null, 'A1', true, false);
                        });                        
                    }
                    $merg = count($bill_detail); $merg = $merg+2;  
                    $border = count($bill_detail); $border = $border+2;  
                    //billing without fix tasks
                    $fixTasks = \App\Models\FixTask::getFixTasksList($xls_client_id);
                    if(empty($xls_client_id) || count($fixTasks) == 0){
                        $bill_totals[] = array('Total','','','',$bill_total);
                        $excel->sheet('Bill Detail', function($sheet) use ($bill_detail,$bill_totals,$merg,$border) {
                            /*$sheet->setSize(array(
                                'A1' => array('width'=> 10,'height'=> 15),
                                'B1' => array('width'=> 30,'height' => 15),
                                'C1' => array('width'=> 10,'height' => 15),
                                'D1' => array('width'=> 10,'height' => 15),
                                'E1' => array('width'=> 20,'height' => 15),
                            ));*/
                            $sheet->setAutoSize(true);
                            $sheet->row(1, array('No','Name','Hours','Rate','Total'));
                            $sheet->mergeCells('A'.$merg.':D'.$merg);
                            $sheet->setBorder('A1:E'.$border, 'thin');
                            $sheet->cell('A1:E1', function($cell) {
                                $cell->setBackground('#aebbc2');
                                $cell->setAlignment('center');
                                $cell->setFont(array('family'=>'Calibri','size'=>'12','bold'=>true));
                            });
                            $sheet->cell('A'.$merg.':D'.$merg, function($cell) {
                                $cell->setAlignment('center');
                                $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                            });
                            $sheet->cell('E'.$merg, function($cell) {
                                $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                            });
                            $sheet->fromArray($bill_detail, null, 'A2', false, false);
                            $sheet->fromArray($bill_totals, null, 'A2', false, false);
                        });
                    }
                        //Client fix task sheet
                        if(!empty($xls_client_id))
                        {
                            $k = 1;
                            $billId = 1;
                            $fixTasks = \App\Models\FixTask::getFixTasksList($xls_client_id);
                            $merg3 = count($fixTasks); $merg3 = $merg3 + 2;

                            if(!empty($fixTasks) && count($fixTasks) > 0)
                            {
                                $fix_totals = 0;
                                $clientTask = [];
                                $clientTotalHr = 0;
                                $clientTotalRate = 0;
                                foreach ($fixTasks as $fixTask)
                                {
                                    $fix_task_date = date("j M, Y",strtotime($fixTask->task_date));
                                    
                                    $task_hrs = doubleval($fixTask->hour);
                                    $task_fix = doubleval($fixTask->fix);
                                    $task_rate = doubleval($fixTask->rate);
                                    $row_total = ($task_hrs * $task_rate) + $task_fix;
                                    if(empty($task_hrs)) $task_hrs = 0;
                                    if(empty($task_fix)) $task_fix = 0;
                                    if(empty($task_rate)) $task_rate = 0;

                                    $clientTask[] = [$k,$fixTask->title,$fix_task_date,$fixTask->ref_link,$fixTask->assigned_by,$task_hrs,$task_fix,$task_rate,$row_total];
                                    $fix_totals += $row_total;
                                    $clientTotalHr += $task_hrs;
                                    $clientTotalRate += $task_rate;
                                    $k++;
                                }
                                //calculation of clients users rates
                                $p = 1;
                                $bill_total_fix = 0;
                                foreach ($bill_detail_rates as $key =>$v) {
                                   $user_id = $bill_detail_rates[$key][0];
                                   
                                    $rate_value = \App\Models\ClientUsersRate::where('user_id',$user_id)
                                                    ->where('client_id',$xls_client_id)
                                                    ->first();
                                    if($rate_value)
                                    {
                                        $bill_detail_rates[$key][3] = $rate_value->rate;
                                        $bill_detail_rates[$key][4] = $bill_detail_rates[$key][2] * $rate_value->rate; 
                                    }else{
                                        $bill_detail_rates[$key][3] = 1;
                                    }
                                    $bill_detail_rates[$key][0] = $p;
                                    $bill_total_fix += $bill_detail_rates[$key][4];
                                    $p++;
                                }
                                $bill_total_fix += $fix_totals;
                                $billId = count($bill_detail) + 1;
                                $bill_totals[] = array($billId,'Fix Tasks',$clientTotalHr,0,$fix_totals);
                                $bill_totals[] = array('Total','','','',$bill_total_fix);
                                //Billing when Fix tasks
                                $excel->sheet('Bill Detail', function($sheet) use ($bill_detail_rates,$bill_totals,$merg,$border) {
                                    
                                    $merg +=1;
                                    $border +=1; 
                                    $sheet->setSize(array(
                                        'A1' => array('width'=> 10,'height'=> 15),
                                        'B1' => array('width'=> 30,'height' => 15),
                                        'C1' => array('width'=> 10,'height' => 15),
                                        'D1' => array('width'=> 10,'height' => 15),
                                        'E1' => array('width'=> 20,'height' => 15),
                                    ));
                                    $sheet->row(1, array('No','Name','Hours','Rate','Total'));
                                    $sheet->mergeCells('A'.$merg.':D'.$merg);
                                    $sheet->setBorder('A1:E'.$border, 'thin');
                                    $sheet->cell('A1:E1', function($cell) {
                                        $cell->setBackground('#aebbc2');
                                        $cell->setAlignment('center');
                                        $cell->setFont(array('family'=>'Calibri','size'=>'12','bold'=>true));
                                    });
                                    $sheet->cell('A'.$merg.':D'.$merg, function($cell) {
                                        $cell->setAlignment('center');
                                        $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                                    });
                                    $sheet->cell('E'.$merg, function($cell) {
                                        $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                                    });
                                    $sheet->fromArray($bill_detail_rates, null,'A2',false,false);
                                    $sheet->fromArray($bill_totals, null, 'A2', true, false);
                                    //dd($bill_detail_rates);
                                });
                                //client fix tasks
                                $fix_total[] = array('Total','','','','','','','',doubleval($fix_totals));
                                $excel->sheet('Fix Tasks', function($sheet) use ($clientTask,$merg3,$fix_total){
                                    $sheet->setAutoSize(true);
                                    $sheet->row(1, array('Sr. No.','Tasks','Date','Ref. Link','Assigned by','Hours','Fixed','Rate','Total'));
                                    $sheet->mergeCells('A'.$merg3.':H'.$merg3);
                                    $sheet->setBorder('A1:I'.$merg3, 'thin');
                                    $sheet->cell('A1:I1', function($cell) {
                                        $cell->setBackground('#aebbc2');
                                        $cell->setAlignment('center');
                                        $cell->setFont(array('family'=>'Calibri','size'=>'12','bold'=>true));
                                    });
                                    $sheet->cell('A'.$merg3.':H'.$merg3, function($cell) {
                                        $cell->setAlignment('center');
                                        $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                                    });
                                    $sheet->cell('I'.$merg3, function($cell) {
                                        $cell->setFont(array('family'=>'Calibri','size'=>'14','bold'=>true));
                                    });
                                    $sheet->setColumnFormat(array(
                                        'F' => '#,##0.00',
                                        'G' => '#,##0.00',
                                        'H' => '#,##0.00',
                                        'I' => '#,##0.00',
                                    ));
                                    $sheet->fromArray($clientTask, null, 'A2', true, false);
                                    $sheet->fromArray($fix_total, null, 'A2', true, false);
                                });
                            }
                        }
                        $activeSheet = count($userWiseTasks);
                        $excel->setActiveSheetIndex($activeSheet);
                    });
                    $xls_sheet->download('xlsx');
                }
            }
			
            $viewName = $this->moduleViewName.".index";
        }
		else if($auth_id == CLIENT_USER){
            $client_type = 0;
			$client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                    $client_type = $client_user->client_id;
            }
            $data['projects'] = \App\Models\Project::getProjectList($client_type);
            $data['users'] = \App\Models\ClientEmployee::getUserList($client_type);
            $data['clients']='';
            $viewName = $this->moduleViewName.".clientIndex";
        }
        $data = customSession($this->moduleRouteText,$data, 100);
        return view($viewName, $data);  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_TASKS);
        
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
        $data["editMode"] = 1; 
        $data['projects'] = Project::where('status',1)->pluck("title","id")->all();
        $data['users'] = User::where('status',1)->pluck("name","id")->all();
        //$data['users'] = User::getList();
        $data['hours'] = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24'];
        $data['mins'] = ['0.00'=>'0.00','0.25'=>'0.25','0.50'=>'0.50','0.75'=>'0.75'];
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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();
        $status = 1;
        $msg = $this->addMsg;
		$goto = $this->list_url;

        $message = ['task_date.*'=>'Task date must be less than equal to today.'];
        $validator = Validator::make($request->all(), [
            'project_id.*' => 'required|exists:'.TBL_PROJECT.',id',
            'user_id.*' => 'exists:'.TBL_USERS.',id',
            'title.*' => 'required|min:2',
            //'description' => 'required',
            'hour.*' => ['required', Rule::in([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24])],
            'min.*' => ['required', Rule::in([0.00,0.25,0.50,0.75])],
            'status.*' => ['required', Rule::in([0,1])],
			'task_date.*'=>'before:tomorrow'
        ],$message);
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
            $hourFlag= 1;
            $hour = $request->get('hour');
            $min = $request->get('min');
            $hour_count = count($hour);
            for ($i=0; $i <$hour_count; $i++) { 
                if($hour[$i] == 0 && $min[$i] == 0.00)
                    $hourFlag = 0;
            }
            if($hourFlag == 0){
                $status = 0;
                return ['status' => $status, 'msg' => 'please enter valid time','goto' => $goto]; 
            }


            $project_id = $request->get('project_id');
            $title = $request->get('title');
            $description = $request->get('description');
            //$hour = $request->get('hour');
            //$min = $request->get('min');
            $statuss = $request->get('status');
            $ref_link = $request->get('ref_link');

            $auth_id = \Auth::guard('admins')->user()->user_type_id;
            $user = $request->get('user_id');
            
            $p = 0;
            foreach ($project_id as $project)
            {
                if($project == MISCELLANEOUS_PROJECT)
                {
                    $desc = isset($description[$p]) ? $description[$p] : '';
                    if(empty($desc) || strlen($desc) < 100)
                    {
                        $status = 0;
                        return ['status' => $status, 'msg' => 'Please enter description of min 100 charecters for <b style="color : red;""> Miscellaneous </b>','goto' => $goto];
                    }
                }
            $p++;
            }

            if(!empty($user) && $auth_id == 1 && is_array($user))
            {
                $user_id = $request->get('user_id');
                $task_date = $request->get('task_date');
                $count = count($project_id);
                
                for($i=0; $i<$count; $i++)
                {
                    $obj = new Task();
                    $obj->user_id = $user_id[$i];
                    $obj->project_id = $project_id[$i];
                    $obj->title = $title[$i];
                    $obj->description = $description[$i];
                    $obj->hour = $hour[$i];
                    $obj->min = $min[$i];
                    $obj->total_time = $hour[$i] + $min[$i];
                    $obj->status = $statuss[$i];
                    $obj->ref_link = $ref_link[$i];
                    if(!empty($task_date[$i]))
                    {
                        $task_dates = $task_date[$i]; 
                    }
                    else{
                        $task_dates =  date("Y-m-d h:i:sa");
                    }

                    $obj->task_date = $task_dates;
                    $obj->save();

                    $id = $obj->id;
                    
                    //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->ADD_TASKS;
                $params['actionvalue']  = $id;
                $params['remark']       = "Add Task::".$id;

                $logs=\App\Models\AdminLog::writeadminlog($params);
            
                }
            }
            else{
                $user_id = \Auth::guard('admins')->id();
                $task_date = date("Y-m-d h:i:sa");

                $count = count($project_id);
                for($i=0; $i<$count; $i++)
                {
                    $obj = new Task();
                    $obj->user_id = $user_id;
                    $obj->project_id = $project_id[$i];
                    $obj->title = $title[$i];
                    $obj->description = $description[$i];
                    $obj->hour = $hour[$i];
                    $obj->min = $min[$i];
                    $obj->total_time = $hour[$i] + $min[$i];
                    $obj->status = $statuss[$i];
                    $obj->ref_link = $ref_link[$i];
                    $obj->task_date = $task_date;
                    $obj->save();

                    $id = $obj->id;
                    
                    //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->ADD_TASKS;
                $params['actionvalue']  = $id;
                $params['remark']       = "Add Task::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $formObj = $this->modelObj->find($id);

        if(!$formObj)
        {
            abort(404);
        }   
		$auth_user = \Auth::guard('admins')->user();
        $created_at = date('Y-m-d',strtotime($formObj->task_date));
        $today = date('Y-m-d');

        if($auth_user->user_type_id == NORMAL_USER && $auth_user->id != $formObj->user_id)
        {
            session()->flash('error_message',"You are not authorised to view this page.");
            return redirect('dashboard');
        }
        if($auth_user->user_type_id == NORMAL_USER && $created_at != $today)
        {
            session()->flash('error_message',"You are not authorised to view this page.");
            return redirect('dashboard');   
        }
        $data = array();
        $data['formObj'] = $formObj;
        $data['page_title'] = "Edit ".$this->module;
        $data['buttonText'] = "Update";
	
        $data['action_url'] = $this->moduleRouteText.".update";
        $data['action_params'] = $formObj->id;
        $data['method'] = "PUT";
        $data['editMode'] = "";
        $data['projects'] = Project::where('status',1)->pluck("title","id")->all();
        $data['users'] = User::where('status',1)->pluck("name","id")->all();
        $data['hours'] = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24'];
        $data['mins'] = ['0.00'=>'0.00','0.25'=>'0.25','0.50'=>'0.50','0.75'=>'0.75'];

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
       $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_TASKS);
        
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

		$auth_user = \Auth::guard('admins')->user();
        $created_at = date('Y-m-d',strtotime($model->task_date));
        $today = date('Y-m-d');

        if($auth_user->user_type_id == NORMAL_USER && $auth_user->id != $model->user_id)
        {
            $msg = "You are not authorised to edit this record.";
            return ['status' => 0,'msg' => $msg, 'data' => $data];
        }
        if($auth_user->user_type_id == NORMAL_USER && $created_at != $today)
        {
            $msg = "You are not authorised to edit this record.";
            return ['status' => 0,'msg' => $msg, 'data' => $data];
        }
        $message = ['task_date.*'=>'Task date must be less than equal to today.'];
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:'.TBL_PROJECT.',id',
            'title' => 'required',            
            'hour.*' => ['required', Rule::in([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24])],
            'min.*' => ['required', Rule::in([0.00,0.25,0.50,0.75])],
            'status.*' => ['required', Rule::in([0,1])],
			'task_date.*'=>'before:tomorrow'
        ],$message);
        
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
            $hourFlag= 1;
            $hour = $request->get('hour');
            $min = $request->get('min');
            $hour_count = count($hour);
            for ($i=0; $i <$hour_count; $i++) { 
                if($hour[$i] == 0 && $min[$i] == 0.00)
                    $hourFlag = 0;
            }
            if($hourFlag == 0){
                $status = 0;
                return ['status' => $status, 'msg' => 'please enter valid time','goto' => $goto]; 
            }

            $project_id = $request->get('project_id');
            $title = $request->get('title');
            $description = $request->get('description');
            //$hour = $request->get('hour');
            //$min = $request->get('min');
            $statuss = $request->get('status');
            $ref_link = $request->get('ref_link');

            $auth_id = \Auth::guard('admins')->user()->user_type_id;
            $user = $request->get('user_id');

            $p = 0;
            foreach ($project_id as $project)
            {
                if($project == MISCELLANEOUS_PROJECT)
                {
                    $desc = isset($description[$p]) ? $description[$p] : '';
                    if(empty($desc) || strlen($desc) < 100)
                    {
                        $status = 0;
                        return ['status' => $status, 'msg' => 'Please enter description of min 100 charecters for <b style="color : red;""> Miscellaneous </b>','goto' => $goto];
                    }
                }
            $p++;
            }
            
            if(!empty($user) && $auth_id == 1 && is_array($user))
            {
                $user_id = $request->get('user_id');
                $task_date = $request->get('task_date');
                $count = count($project_id);

                $model->user_id = $user_id[0];
                $model->project_id = $project_id[0];
                $model->title = $title[0];
                $model->description = $description[0];
                $model->hour = $hour[0];
                $model->min = $min[0];
                $model->total_time = $hour[0] + $min[0];
                $model->status = $statuss[0];
                $model->ref_link = $ref_link[0];
                if(!empty($task_date[0]))
                    $task_date = $task_date[0];
                else
                    $task_date =  date("Y-m-d h:i:sa");

                $model->task_date = $task_date;
                $model->update();                
            }else
            {
                $task_date = date("Y-m-d h:i:sa");
                
                $model->user_id = \Auth::guard('admins')->id();
                $model->project_id = $project_id[0];
                $model->title = $title[0];
                $model->description = $description[0];
                $model->hour = $hour[0];
                $model->min = $min[0];
                $model->total_time = $hour[0] + $min[0]; 
                $model->status = $statuss[0];
                $model->ref_link = $ref_link[0];
                $model->task_date = $task_date;
                $model->update();
            }
            //store logs detail
                $params=array();
                
                $params['adminuserid']  = \Auth::guard('admins')->id();
                $params['actionid']     = $this->adminAction->EDIT_TASKS;
                $params['actionvalue']  = $id;
                $params['remark']       = "Edit Tasks::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_TASKS);
        
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
                    $params['actionid']     = $this->adminAction->DELETE_TASKS;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Task::".$id;

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
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id");
		
		$hours_query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id");

        $hours_query = Task::listFilter($hours_query);        

        $totalHours = $hours_query->sum("total_time");
        $totalHours = number_format($totalHours,2);

        $data = \Datatables::eloquent($model)        
               
            ->addColumn('action', function(Task $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_TASKS),                                                  
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_TASKS),
                        'isView' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_TASKS),
                                                  
                    ]
                )->render();
            })
            ->editColumn('status', function ($row) { 
                if ($row->status == 1){
                    $html = "<a class='btn btn-xs btn-success'>Completed</a><br/>";
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
                else{
                    $html ='<a class="btn btn-xs btn-danger">In Progress</a><br/>';
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
            })
            ->editColumn('task_date', function($row){
                if(!empty($row->task_date))          
                    return date("j M, Y",strtotime($row->task_date)).'<br/><span style="color: blue; font-size: 12px">'.date("j M, Y",strtotime($row->created_at))."</span>";
                else
                    return '-';    
            })
            ->editColumn('ref_link', function($row){
                $html='';

                if(!empty($row->ref_link))
                {
                  $label = strlen($row->ref_link) > 15 ? substr($row->ref_link,0,15)."...":$row->ref_link; 
                  $html = "<a href='".$row->ref_link."' target='_blank'>".$label."</a>";  
                }
                return $html;  
            })            
            ->rawColumns(['status','action','ref_link','description','task_date'])             
          
            ->filter(function ($query) 
            {                              
                $query = Task::listFilter($query);                  
            });
		$data = $data->with('hours',$totalHours);
        $data = $data->make(true);
		return $data;
    }

    public function viewData(Request $request)
    {     
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $id = $request->get('task_id');

        if(!empty($id)){
            
            $task = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_TASK.".id",$id)
                ->get();
                //dd($task);
        }
        return view("admin.tasks.viewData", ['views'=>$task]);
    }

    public function userData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->id();

        $model = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_TASK.".user_id",$auth_id);

		$hours_query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_TASK.".user_id",$auth_id);

	    $hours_query = Task::listFilter($hours_query);        
        $totalHours = $hours_query->sum("total_time");
        $totalHours = number_format($totalHours,2);

        $data = \Datatables::eloquent($model)        
               
            ->editColumn('status', function ($row) {
                    if ($row->status == 1)
                        return "<a class='btn btn-xs btn-success'>Completed</a>";
                    else
                        return '<a class="btn btn-xs btn-danger">In Progress</a>';
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 1){
                    $html = "<a class='btn btn-xs btn-success'>Completed</a><br/>";
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
                else{
                    $html ='<a class="btn btn-xs btn-danger">In Progress</a><br/>';
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
            })
            ->addColumn('action', function(Task $row) {
                return view("admin.tasks.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_TASKS),                                                  
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_TASKS),                                                  					'isView' =>\App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_TASKS),
                    ]
                )->render();
            })
            ->editColumn('task_date', function($row){
                
                if(!empty($row->task_date))          
                    return date("j M, Y",strtotime($row->task_date));
                else
                    return '-';    
            })
            ->editColumn('ref_link', function($row){
                $html='';

                if(!empty($row->ref_link))
                {
                  $label = strlen($row->ref_link) > 15 ? substr($row->ref_link,0,15)."...":$row->ref_link; 
                  $html = "<a href='".$row->ref_link."' target='_blank'>".$label."</a>";  
                }
                return $html;  
            }) 
            
            ->rawColumns(['status','ref_link','action'])             
            
            ->filter(function ($query) 
            {                              
                $query = Task::listFilter($query);                  
            });
			$data = $data->with('hours',$totalHours);
            $data = $data->make(true);
            return $data;
    }
	public function clientData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_TASKS);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $client_type = 0;
        $client_id = \Auth::guard('admins')->user()->client_user_id;
        $client_user = ClientUser::find($client_id);
        if(!empty($client_user))
        {
                $client_type = $client_user->client_id;
        }

        $model = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_PROJECT.".client_id",$client_type);

        $hours_query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->where(TBL_PROJECT.".client_id",$client_type);

        $hours_query = Task::listFilter($hours_query);
        $totalHours = $hours_query->sum("total_time");
        $totalHours = number_format($totalHours,2);

        $data = \Datatables::eloquent($model)
               
            ->editColumn('status', function ($row) {
                    if ($row->status == 1)
                        return "<a class='btn btn-xs btn-success'>Completed</a>";
                    else
                        return '<a class="btn btn-xs btn-danger">In Progress</a>';
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 1){
                    $html = "<a class='btn btn-xs btn-success'>Completed</a><br/>";
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
                else{
                    $html ='<a class="btn btn-xs btn-danger">In Progress</a><br/>';
                    $html.='<i class="fa fa-clock-o" aria-hidden="true"></i>  '.$row->total_time;
                    return $html;
                }
            })
            ->addColumn('action', function(Task $row) {
                return view("admin.tasks.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => 0,
                        'isDelete' =>0,
                        'isView' =>\App\Models\Admin::isAccess(\App\Models\Admin::$LIST_TASKS),
                    ]
                )->render();
            })
            ->editColumn('task_date', function($row){
                
                if(!empty($row->task_date))
                    return date("j M, Y",strtotime($row->task_date));
                else
                    return '-';    
            })
            ->editColumn('ref_link', function($row){
                $html='';

                if(!empty($row->ref_link))
                {
                  $label = strlen($row->ref_link) > 15 ? substr($row->ref_link,0,15)."...":$row->ref_link; 
                  $html = "<a href='".$row->ref_link."' target='_blank'>".$label."</a>";  
                }
                return $html;  
            }) 
            ->rawColumns(['status','ref_link','action','task_date'])
            
            ->filter(function ($query) 
            {
                $query = Task::listFilter($query);
            });
            $data = $data->with('hours',$totalHours);

            $data = $data->make(true);

            return $data;        
    }
	
	public function getMonthlyReport(Request $request)
    {
		$checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DOWNLOAD_MONTHLY_REPORT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();
        $data['users'] = User::getList();
        $data['clients'] = Client::pluck("name","id")->all();
        $data['months'] = ['1017-10'=>'OCTOBER - 2017','2017-11'=>'NOVEMBER - 2017','2017-12'=>'DECEMBER - 2017','2018-01'=>'JANUARY - 2018','2018-02'=>'FEBRUARY - 2018','2018-03'=>'MARCH - 2018','2018-04'=>'APRIL - 2018','2018-05'=>'MAY - 2018','2018-06'=>'JUNE - 2018','2018-07'=>'JULY - 2018','2018-08'=>'AUGUST - 2018','2018-09'=>'SEPTEMBER - 2018','2018-10'=>'OCTOBER - 2018','2018-11'=>'NOVEMBER - 2018','2018-12'=>'DECEMBER - 2018'];
            
        return view('admin.tasks.DownloadMonthlyReport',$data);
    }

    public function PreviewMonthlyReport(Request $request)
    {
        $data = array();
        $user_id = $request->get('user_id');
        $client_id = $request->get('client_id');
        $months = $request->get('months');

        $query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                ->join(TBL_CLIENT,TBL_PROJECT.".client_id","=",TBL_CLIENT.".id")
                ->where(TBL_TASK.'.user_id',$user_id)
                ->where(TBL_CLIENT.'.id',$client_id)
                ->where(TBL_TASK.'.task_date','LIKE','%'.$months.'%');
        $query = $query->get();
        $total = $query->sum('total_time');

        $user = User::find($user_id);
        $fullname = $user->firstname.' '.$user->lastname;
        
        $data['reports'] = $query;
        $data['total'] = $total;
        $data['months'] = $months;
        $data['fullname'] = $fullname;
        return view('admin.tasks.previewReport',$data); 
    }

    public function DownloadMonthlyReport(Request $request)
    {       
        $status = 1;
        $msg = "Downloaded Monthly Report Successfully!";
        $data = array();
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:'.TBL_USERS.',id',
            'client_id' => 'required|exists:'.TBL_CLIENT.',id',
            'months' => 'required',
        ]);
        if ($validator->fails())
        {
            return back()->withInput()->withErrors($validator->errors());
        } 
        else{
            $user_id = request()->get('user_id');
            $client_id = request()->get('client_id');
            $months = request()->get('months');

                $query = Task::select(TBL_TASK.".*",TBL_PROJECT.".title as project_name",TBL_USERS.".name as user_name")
                        ->join(TBL_USERS,TBL_TASK.".user_id","=",TBL_USERS.".id")
                        ->join(TBL_PROJECT,TBL_TASK.".project_id","=",TBL_PROJECT.".id")
                        ->join(TBL_CLIENT,TBL_PROJECT.".client_id","=",TBL_CLIENT.".id")
                        ->where(TBL_TASK.'.user_id',$user_id)
                        ->where(TBL_CLIENT.'.id',$client_id)
                        ->where(TBL_TASK.'.task_date','LIKE','%'.$months.'%');
                $query = $query->get();
                $total = $query->sum('total_time');

                $data['reports'] = $query;
                $data['total'] = $total;

                $user = User::find($user_id);
                $fullname = $user->firstname.'_'.$user->lastname;
                $file_name = $months.'_'.$fullname.'_Billind_hours_'.$total;     

                //$path = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'MonthlyReports';

                $records[] = array("No","Project","Task","Date","Hours","Status","Reference Link");
                $i = 1;
                foreach($query as $row)
                {
                    if($row->status == 1) $sts = "Completed"; else $sts = "In Progress";
                    $task_date = date("j M, Y",strtotime($row->task_date));
                    $records[] = [$i,$row->project_name,$row->title,$task_date,$row->total_time,$sts,$row->ref_link];
                $i++;
                }

                header("Content-type: text/csv; charset=utf-8");
                header("Content-Disposition: attachment; filename=".$file_name.".csv");
                //header('Pragma: no-cache');
                //header("Expires: 0");
                
                $records[] = array("Total","","","",$total,"","");
                $fp = fopen('php://output', 'w');                
                fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
                //$fp = fopen($file_name.'.csv', 'w');
                    foreach ($records as $fields) {
                        fputcsv($fp, $fields);
                    }

                fclose($fp);                
                $path = public_path().'/'.$file_name.'.csv';
                //downloadFile($file_name.'.csv',$path);
                exit;                
                // return redirect('/download-monthly-reports');
        }
        return redirect('/download-monthly-reports');
    }
}
