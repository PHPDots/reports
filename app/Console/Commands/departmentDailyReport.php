<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\AdminAction;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class departmentDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'departmentDailyReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation Process For sending daily work report to department team member.';

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
        $date = date("Y-m-d");
        $date= '2018-12-17';
        // Get Tasks
        $sql = "
              SELECT  tasks.*,users.firstname,users.lastname,users.department_id,users.email as user_email,projects.title as project_name 
              FROM tasks
              JOIN users ON users.id = tasks.user_id
              JOIN projects ON tasks.project_id = projects.id              
              WHERE date_format(tasks.task_date, '%Y-%m-%d') = '".$date."'
              ORDER BY users.firstname
            ";
        $rows = \DB::select($sql);

        $user_id = 0;
        $client_id = 0;
        $clients = array();
        if(count($rows) > 0 ) {
            foreach ($rows as $task_detail) {
              if(!empty($client_id) && !empty($user_id) && ($user_id!=$task_detail->user_id))
              {

              }

              $user_id = $task_detail->user_id;
              $client_id = $task_detail->user_id;
              if(!in_array($task_detail->user_id, $clients)) 
              {
                $clients[] = $task_detail->user_id;
              }
                $report_data[$task_detail->user_id][$task_detail->user_id][]= $task_detail;
            }
            foreach ($report_data as $user_id => $user_report) 
            {
                $i = 1; $j=0;
                foreach ($user_report as $client_id => $user_client_reportRow) 
                {
                    $user_client_reportRow = json_decode(json_encode($user_client_reportRow),1);

                    $empName = "";

                    $table = "<p><b>Hello Sir,</b></p>";
                    $table .= "<p>Please find daily report below.</p>";

                    $table .= '<table width="100%" border="0" cellspacing="0" cellpadding="3" style="font-size:13px; border-top:1px solid #666; border-left:1px solid #666; font-family:Arial, Helvetica, sans-serif;">';
                    $table .= "<tr>";
                    $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Sr. No.</td>';
                    $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Project</td>';
                    $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Task/Feature</td>';
                    $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Date</td>';
                    $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Hour</td>';
                    $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Status</td>';
                    $table .= '<td width="9%" align="left" valign="middle" style="font-weight:600; background-color:#d9d9d9; border-right:1px solid #777; border-bottom:1px solid #777;">Task Link</td>';
                    $table .= "</tr>";            $totalHours = 0;

                    foreach($user_client_reportRow as $user_client_report )
                    {
                      $from_email = $user_client_report['user_email'];
                      $department_id = $user_client_report['department_id'];
                      $empName = ucfirst($user_client_report['firstname'])." ".ucfirst($user_client_report['lastname']);

                      $status = $user_client_report['status'] == 1 ? "DONE":"In Progress";

                      $table .= "<tr>";
                      $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$i."</td>";
                      $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['project_name']."</td>";
                      $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['title']."</td>";
                      $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.date("m/d/Y",strtotime($user_client_report['task_date']))."</td>";
                      $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['total_time']."</td>";
                      $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$status."</td>";
                      $table .= '<td align="left" valign="middle" style="border-right:1px solid #777; border-bottom:1px solid #777;">'.$user_client_report['ref_link']."</td>";
                      $table .= "</tr>";
                      $i++;

                      $totalHours += floatval($user_client_report['total_time']);
                    }  

                    $table .= "</table>";
                    $team_email = User::where("user_type_id",TEAM_LEADER)
                                    ->where('id','!=',1)
                                    ->where('department_id','=',$department_id)
                                    ->pluck("email")
                                    ->first();
                    $table .= "<p>Thanks & Regards,<br />".$empName."</p>";
                    $subject = "Daily Report - (Hr-".$totalHours.") - ".date("j M, Y")." - $empName";
                     //echo "<p>Subject: $subject</p>";

                    $params["to"]= $team_email;
                    $params["subject"] = $subject;
                    $params["from"] = $from_email;
                    $params["from_name"] = $empName;  
                    $params["body"] = "<html><body>".$table."</body></html>";

                    $data =array();
                    $data['body']= $table;
                    // if($from_email != 'mayur.devmurari@phpdots.com')
                    sendHtmlMail($params);
                    $returnHTML = view('emails.index',$data)->render();
                    //echo $returnHTML;
                }
            }
        }
        echo 'Mail sent successfully.';
        exit;
    }
}
