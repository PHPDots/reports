<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendDailyWorkReportOfDepartment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendDailyWorkReportOfDepartment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Daily Work Report Of Department';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(true){
            $date = date("Y-m-d");
            // Get Tasks
            $sql = "
                SELECT  tasks.*,users.firstname,users.lastname,users.department_id,users.user_type_id,users.send_reports_type,users.email as user_email,projects.title as project_name 
                FROM tasks
                JOIN users ON users.id = tasks.user_id 
                JOIN projects ON tasks.project_id = projects.id              
                WHERE date_format(tasks.task_date, '%Y-%m-%d') = '".$date."' AND users.user_type_id != ".TEAM_LEADER." 
                ORDER BY users.firstname
                ";
            $rows = \DB::select($sql);

            $user_id = 0;
            $department_id = 0;
            $multiMail = array();
            $singleMail = array();
            $report_data = array();

            if(count($rows) > 0 ) {
                foreach ($rows as $task_detail)
                {
                    $user_id = $task_detail->user_id;
                    $department_id = $task_detail->department_id;
                    $teamLead = User::where('user_type_id',TEAM_LEADER)
                                 ->where('department_id',$department_id)
                                 ->first();
                    if($teamLead)
                    {
                        if($teamLead->send_reports_type == 1)
                        {
                            $multiMail[$department_id][$user_id][] = $task_detail;
                        }else{
                            $singleMail[$department_id][$user_id][] = $task_detail;
                        }
                    }
                    if(empty($department_id))
                    {
                        $report_data[0][$user_id][]= $task_detail;
                    }
                }
                if(count($multiMail) > 0)
                {
                    $empName = '';
                    $table = "<p><b>Hello Sir,</b></p>";
                    $table .= "<p>Please find daily report below.</p>";
                    
                    $table .= '<table width="100%" border="0" cellspacing="0" cellpadding="3" style="font-size:13px; border-top:1px solid #666; border-left:1px solid #666; font-family:Arial, Helvetica, sans-serif;">';
                    $table .= "<tr>";
                    $table .= '<td width="2%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">No.</td>';
                    $table .= '<td width="12%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Users Name</td>';
                    $table .= '<td width="12%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Project</td>';
                    $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Task/Feature</td>';
                    $table .= '<td width="4%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Date</td>';
                    $table .= '<td width="3%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Hour</td>';
                    $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Status</td>';
                    $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Task Link</td>';
                    $i=1;
                    foreach ($multiMail as $mail => $tasks)
                    {
                        foreach ($tasks as $key => $userReport) 
                        {
                            $totalHours=0;
                            foreach($userReport as $user_client_report )
                            {
                                $emplID = $user_client_report->user_id;
                                $from_email = $user_client_report->user_email;
                                $department_id = $user_client_report->department_id;

                                $empName = ucfirst($user_client_report->firstname)." ".ucfirst($user_client_report->lastname);

                                $status = $user_client_report->status == 1 ? "DONE":"In Progress";

                                $table .= "<tr>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$i."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$empName."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report->project_name."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report->title."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.date("m/d/Y",strtotime($user_client_report->task_date))."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report->total_time."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$status."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report->ref_link."</td>";
                                $table .= "</tr>";
                                $i++;

                                $totalHours += floatval($user_client_report->total_time);
                            }"</br></table>"; 
                        }
                    }
                    $table .= "</table>";

                    $team_email = User::where("user_type_id",TEAM_LEADER) 
                                        ->where('department_id','=',$department_id)
                                        ->pluck("email")
                                        ->first();
                    $table .= "</br><p>Thanks & Regards,<br /></p>";
                    $subject = "Daily Report - (Hr-".$totalHours.") - ".date("j M, Y");

                    if(empty($team_email))
                    $team_email = 'jitendra.rathod@phpdots.com';
                    
                    $params["to"]= $team_email;
                    $params["subject"] = $subject;
                    $params["from"] = $from_email;
                    $params["from_name"] = "Daily Report";  
                    $params["body"] = "<html><body>".$table."</body></html>";
                    //dd($params);
                    $data =array();
                    $data['body']= $table;
                    sendHtmlMail($params);
                    $returnHTML = view('emails.index',$data)->render();
                }
                if($singleMail)
                {
                    foreach ($singleMail as $mail => $tasks)
                    {
                        foreach ($tasks as $key => $userReport) 
                        {
                            $k = 1;
                            $totalHours=0;
                            $table = "<p><b>Hello Sir,</b></p>";
                            $table .= "<p>Please find daily report below.</p>";

                            $table .= '<table width="100%" border="0" cellspacing="0" cellpadding="3" style="font-size:13px; border-top:1px solid #666; border-left:1px solid #666; font-family:Arial, Helvetica, sans-serif;">';
                            $table .= "<tr>";
                            $table .= '<td width="2%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">No.</td>';
                            $table .= '<td width="12%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Project</td>';
                            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Task/Feature</td>';
                            $table .= '<td width="4%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Date</td>';
                            $table .= '<td width="3%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Hour</td>';
                            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Status</td>';
                            $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Task Link</td>';
                            $table .= "</tr>";$totalHours = 0;

                            foreach($userReport as $user_client_report )
                            {
                                $emplID = $user_client_report->user_id;
                                $from_email = $user_client_report->user_email;
                                $department_id = $user_client_report->department_id;

                                $empName = ucfirst($user_client_report->firstname)." ".ucfirst($user_client_report->lastname);

                                $status = $user_client_report->status == 1 ? "DONE":"In Progress";

                                $table .= "<tr>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$k."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report->project_name."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report->title."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.date("m/d/Y",strtotime($user_client_report->task_date))."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report->total_time."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$status."</td>";
                                $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report->ref_link."</td>";
                                $table .= "</tr>";
                                $k++;

                                $totalHours += floatval($user_client_report->total_time);
                            }
                            $table .= "</br></table>"; 
                        }
                        $team_email = User::where("user_type_id",TEAM_LEADER) 
                                            ->where('department_id','=',$department_id)
                                            ->pluck("email")
                                            ->first();
                        $table .= "</br><p>Thanks & Regards,<br />".$empName."</p>";
                        $subject = "Daily Report - (Hr-".$totalHours.") - ".date("j M, Y")." - $empName";

                        if(empty($team_email))
                        $team_email = 'jitendra.rathod@phpdots.com';
                        
                        $params["to"]= $team_email;
                        $params["subject"] = $subject;
                        $params["from"] = $from_email;
                        $params["from_name"] = $empName;  
                        $params["body"] = "<html><body>".$table."</body></html>";

                        $data =array();
                        $data['body']= $table;
                        sendHtmlMail($params);
                        $returnHTML = view('emails.index',$data)->render();
                    }
                }
            }
        }
    }
}
