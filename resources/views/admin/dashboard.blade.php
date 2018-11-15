@extends('admin.layouts.app')
<?php
$pageTitle = "Dashboard";
$bred_crumb_array = array(
    'Home' => url('backend'),
    'Dashboard' => '',
);
$monday = date('D');
?>
@section('styles')
<style type="text/css">
    .slimScrollDiv{
        height: auto !important;
    }
    .scroller{
        height: auto !important;
    }
</style>
@endsection
@section('content')
<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">
        <div class="page-content-inner">
            <div class="row">
            @if(Auth::guard('admins')->user()->user_type_id == 1)
                @if(!empty($pending_leave))
                    @if(count($pending_leave)>0)
                        <div class="col-md-12">
                            <div class="portlet" style="margin-bottom: 0px">
                            <div class="portlet-title tabbable-line">
                                <div class="caption">
                                    <i class="icon-globe font-dark hide"></i>
                                    <span class="caption-subject font-green-steel bold uppercase">Pending Leave Requests</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                                    <thead>
                                        <tr>
                                            <th width="15%">UserName</th>
                                            <th width="10%">From Date</th>
                                            <th width="10%">To Date</th>
                                            <th width="5%">Days</th>
											<th width="20%">Discription</th>
                                            <th width="10%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pending_leave as $leave)
                                        <tr>
                                            <th>{{$leave->username}}</th>
                                            <?php 
                                            $halfVal = 0;
                                            $sDate = date("Y-m-d", strtotime($leave->from_date));
                                            $mainID = $leave->id;
                                            $query = App\Models\LeaveDetail::where("leave_id", $mainID)
                                                    ->whereRaw("date_format(date,'%Y-%m-%d') = '" . $sDate . "'")
                                                    ->first();
                                            if ($query && $query->is_half == 1) {
                                                    $halfVal = 1;
                                            }

                                            $from_date ='' . date("j M, Y", strtotime($leave->from_date));
                                            ?>
                                            <th>{{$from_date}} 
                                                @if($halfVal == 1)
                                                <br/><a class='btn btn-outline btn-xs green'>Half</a>
                                                @else
                                                <br/><a class='btn btn-outline btn-xs green'>Full</a>
                                                @endif
                                            </th>
                                            <?php 
                                            $halfVal = 0;
                                            $sDate = date("Y-m-d", strtotime($leave->to_date));
                                            $mainID = $leave->id;
                                            $query = App\Models\LeaveDetail::where("leave_id", $mainID)
                                                    ->whereRaw("date_format(date,'%Y-%m-%d') = '" . $sDate . "'")
                                                    ->first();
                                            if ($query && $query->is_half == 1) {
                                                    $halfVal = 1;
                                            }

                                            $to_date ='' . date("j M, Y", strtotime($leave->to_date));
                                            ?>
                                            <th>{{$to_date}} 
                                                @if($halfVal == 1)
                                                <br/><a class='btn btn-outline btn-xs green'>Half</a>
                                                @else
                                                <br/><a class='btn btn-outline btn-xs green'>Full</a>
                                                @endif
                                            </th>
                                            <th>
                                            <?php
                                            $halfVal = "";
                                                $from_date = date("Y-m-d", strtotime($leave->from_date));
                                                $to_date = date("Y-m-d", strtotime($leave->to_date));
                                                $mainID = $leave->id;

                                                $query = App\Models\LeaveDetail::where("leave_id", $mainID)
                                                        ->whereBetween('date', [$from_date, $to_date])
                                                        ->get();
                                                $days = 0;
                                                foreach ($query as $q) {
                                                    if($q->is_half == 1)                        
                                                        $day =0.5;
                                                    else
                                                        $day =1;
                                                $days +=$day;
                                                }
                                            ?>
                                                {{$days}}
                                            </th>
                                            <th>{{ $leave->description }}</th>
                                            <th><a class="accepted btn btn-outline green btn-sm" data="{{$leave->id}}">
                                                    Appove</a>
                                                <a class="rejected btn btn-outline red btn-sm" data="{{$leave->id}}" id="reject_action">Reject</a></th>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>         
                            </div>
                            <hr/>    
                            </div> 
                        </div>
                    @endif
                @endif
				@if($monday != 'Mon' && $yesterday_holiday == 0)
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-bell-o"></i>YESTERDAY TASKS </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <span><h4><b>Not Added</b></h4></span>
                                    <div class="table-scrollable">
                                        <table class="table table-striped table-bordered table-advance table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="20%"> ID </th>
                                                    <th width="80%"> User </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($daily_tasks)>0)
                                                <?php $i=1; ?>
                                                @foreach($daily_tasks as $daily_task)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td>{{ $daily_task->name}}</td>
                                                </tr>
                                                <?php $i++; ?>
                                                @endforeach
                                                @else
                                                <tr><td colspan="2"><center>No Record Found</center></td></tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>    
                                </div>
                                <div class="col-md-6">
                                    <span><h4><b>Below 9 Hours</b></h4></span>
                                    <div class="table-scrollable">
                                        <table class="table table-striped table-bordered table-advance table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="20%"> ID </th>
                                                    <th width="80%"> User </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($daily_tasks_hours)>0)
                                                <?php $i=1;?>
                                                @foreach($daily_tasks_hours as $daily_tasks_hour => $hour)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td>
                                                    @php
                                                    $date = date("Y-m-d",strtotime($hour['date']));
                                                    $user = $hour['user_id'];
                                                    @endphp
                                                        <a href='{{ asset("/tasks?search_start_date=$date&search_end_date=$date&search_status=all&search_user=$user") }}' target="_blank">
                                                        {{ $hour['name'] }}
                                                        [<span style="color: blue">
                                                            {{ $hour['total'] }} Hr ]
                                                                @if($hour['below'] == 4)
                                                                    [ Half ]
                                                                @endif
                                                        </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php $i++; ?>
                                                @endforeach
                                                @else
                                                <tr><td colspan="2"><center>Record Not Found</center></td></tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				@endif
                @if(!empty($userOnLeaves))
                <div class=" ">
                    <div class="portlet" style="margin-bottom: 0px">
                        <div class="portlet-title tabbable-line">
                            <div class="caption">
                                <i class="icon-globe font-dark hide"></i>
                                <span class="caption-subject font-green-steel bold uppercase">Recent Month - Users OFF 
                                </span>
                                <br/><br/>
                                    <span style="color: #f00e28; font-size: 14px">[ Full Leave] </span>
                                    <span style="color: #5cf027; font-size: 14px">[ Half Leave] </span>
                                    <span style="color: #e2f700; font-size: 14px">[ Pending Leave] </span>
                                    <span style="color: #1f0cf0; font-size: 14px">[ Holiday] </span>
									<span style="font-size: 14px">[ Working Days - <b class="working_days">{{ $working_days }}</b>] </span>
                            </div>   
                        </div>    
                        <div class="portlet-body">
                            <div id='calendar'></div>                            
                        </div> 
                    </div>    
                </div>
                @endif
            @endif
            </div>
        </div>
    </div>
</div>
    <div class="modal fade" id="leave_reject" role="dialog">
        <div class="modal-dialog">  
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Enter Reason For Reject Leave Request.</h4>
                </div>
                <div class="modal-body">
                    <p><form id="reason_form">
                        <textarea rows="3" cols="75" id="reason"></textarea>
                        <input type="submit" name="submit" id="reason_submit" class="btn btn-primary pull-right">

                    </form></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>      
        </div>
    </div>
    @endsection
    @section('styles')
    @endsection
    @section('scripts') 

    <link href='{{asset("js/calender/")}}/fullcalendar.min.css' rel='stylesheet' />
    <link href='{{asset("js/calender/")}}/fullcalendar.print.min.css' rel='stylesheet' media='print' />
    <script src='{{asset("js/calender/")}}/moment.min.js'></script>
    <script src='{{asset("js/calender/")}}/fullcalendar.min.js'></script>

    <script type="text/javascript">
        $(document).ready(function(){
        
            $('#reason_form').submit(function () {
                var reason = $('#reason').val();
        
            if(reason == ''){
                alert('please enater valid reason!');
                return false;
            }else{
                var id = $('#reject_action').attr('data');
                var status = 2;
                var leave_url = "{{url('/leave-request/status') }}";
                $.ajax({
                    type: "GET",
                    url: leave_url,
                    data:{leave_id:id, status:status, reason:reason},
                    success: function (result)
                    {
                    if (result.flag == 1)
                        {
                            $.bootstrapGrowl(result.msg, {type: 'success',delay: 4000});
                            setTimeout(function(){
                                window.location = "{{url('/dashboard')}}";
                                //window.location.reload();
                            },3000);
                        }
                    }
            });
            }
			return false;
        });

        $(document).on('click', '.accepted', function () {
            $text = 'Are you sure you want to accept the request?';
            if (confirm($text) == true){

            var id = $(this).attr('data');
            var status = 1;
            var leave_url = "{{url('/leave-request/status') }}";
            $.ajax({
            type: "GET",
                url: leave_url,
                data:{leave_id:id, status:status},
                success: function (result)
                {
                    if (result.flag == 1)
                    {
                        $.bootstrapGrowl(result.msg, {type: 'success',delay: 4000});
                        setTimeout(function(){
                            window.location = "{{url('/dashboard')}}";
                        },3000);
                    }
                }
            });
        }
        return false;
        });
        $(document).on('click', '.rejected', function () {
            $text = 'Are you sure you want to reject the request?';
            if (confirm($text) == true){

            jQuery('#leave_reject').modal();
            }
            return false;
        });

        $('#calendar').fullCalendar({
        header: {
        left: 'prev,next,today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,listWeek'
        },
                defaultDate: '{{ date("Y-m-d") }}',
                navLinks: true, // can click day/week names to navigate views
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                events: [

                        @foreach($userOnLeaves as $d)
                {
                    @php 
                    ///$bgcolor = '#5cf027';//green
                    if($d['status'] === 0 && $d['is_half'] != ''){
                        
                        $bgcolor ='#e2f700';//yellow                        
                        $title = $d['is_half'] == 1 ? $d['name'].' - HALF':$d['name'].' - FULL';
                    }else if($d['status'] == 1 && $d['status'] != ''){
                        if ($d['is_half'] == 0) 
                        {
                            $bgcolor ='#f00e28';//red
                            $title = $d['name'].' - FULL';
                        }else{
                            $bgcolor ='#5cf027';//green
                            $title = $d['name'].' - HALF';
                        }
                    }
                    else if($d['status'] == '' && $d['is_half'] == ''){
                    
                        $bgcolor ='#1f0cf0';//blue
                        $title = $d['name'];
                    } 

                    @endphp
                        title: '{{ $title }}',
                        start: '{{ date("Y-m-d",strtotime($d['date'])) }}',
                        color: '{{$bgcolor}}',
                         },
                        
                        @endforeach

                ],
                eventRender: function (event, element, view) 
                { 
                    @foreach($userOnLeaves as $d)
                        @if(isset($d['is_hoilday']))
                        var dateString = '{{ date("Y-m-d",strtotime($d['date'])) }}';
                        $(view.el[0]).find('.fc-day[data-date=' + dateString + ']').css('background-color', '#fbb6b6'); 
                        @endif
                    @endforeach
                  
                }
        });
		
        $(document).on('click','.fc-prev-button',function(){
            calendar_month();
        });
        $(document).on('click','.fc-next-button',function(){
            calendar_month();
        }); 
		
    });
</script>
<script type="text/javascript">
        function calendar_month(){
            
            var startDate = $('#calendar').fullCalendar('getView').intervalStart;
            var start_date = new Date(startDate);

            var start_date =  start_date.getFullYear() + "-"+(start_date.getMonth()+1) +"-"+start_date.getDate() + ' '+start_date.toString().split(' ')[4];

            var urlAction = "{{url('dashboard/calendar') }}";
            $('.working_days').html('Loading...');
            $.ajax({
                type: "GET",
                url: urlAction,
                data: {start_date: start_date},
                success: function (result)
                {
                    $('.working_days').html(result);
                },
                error: function (error) {
                }
            });
        }
       
    </script>
@endsection
