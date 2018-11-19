
@extends('admin.layouts.app') 
@section('content')
<?php
$auth = Auth::guard('admins')->user()->user_type_id; 
 
$current_user = Auth::guard('admins')->user()->id;

$user_id = $formObj->user_id;
if(!empty($user_id))
    $user_id = $user_id;
else
    $user_id = $current_user;

$due_date = $formObj->due_date;
$date = date('Y-m-d');
if(!empty($due_date))
    $due_date = $due_date;
else
    $due_date = $date;
 
?>
<div class="page-content">
    <div class="container">
        <section class="task_details_page">
    <div class="container">
        <!--<div class="badge badge-default mb-1">Chat History</div>-->
        <div class="task_edit"></div>
        <div class="task_info">
            <div class="task_title">
                <div class="row">
                    <div class="col-md-8 task_title_left">

                        <h2 class="title">{{ $viewTask->title }}</h2> 
                        
                        <span class="proj_name">{{ $viewTask->pro_title }}</span> </div>
                    <div class="col-md-4 task_title_right">
                        <div class="proj_date"><?php echo date('j M, Y',strtotime($viewTask->created_at));?></div>
                        <div class="greylabel">
                        @if($viewTask->status == 0)
                           <span class="label pending"> Pending </span>
                        @endif
                        @if($viewTask->status == 1)
                         <span class="label done"> Done </span>
                        @endif
                        </span>

                        </div>
                    </div>
                </div>
            </div>
            <div class="task_desc">
                <div class="row">
                    <div class="col-md-9 left"> 
                        <p>{!! $viewTask->description !!}</p>
                    </div> 
                    <div class="col-md-3 right">
                        <div class="prioritylabel">
                        @if($viewTask->priority == 0)
                        <span class="label high">Priority:   High </span>
                        @endif
                        @if($viewTask->priority == 1)
                        <span class="label low">Priority:    Low </span>
                        @endif
                        @if($viewTask->priority == 2)
                        <span class="label medium">Priority:   Medium </span>
                        @endif</div>

                        <div class="greylabel"><span class="label grey">Assign to: 
                            @if(!empty($assignUserTask)) 
                            {{ ucfirst($assignUserTask->user_names)}}
                            @endif 
                            </span></div>
                        <div class="greylabel"><span class="label grey">Due Date: <?php echo date('j M, Y',strtotime($viewTask->due_date));?></span></div>
                    </div>
                </div>
            </div>
            <div class="task_comment">
                <h2 class="title">Task Discussion</h2>

                @foreach($viewComment as $comments)
                <div class="task_comment_item">
                    <div class="row">
                        <div class="col-md-9 left"> 
                            <div class="comment_txt">
                                <p>{!! $comments->comments !!}</p>
                            </div>
                        </div>
                        <div class="col-md-3 right"> 
                            <div class="assign_to">Comment by <strong>  
                            {{ $comments->user_name }}</strong> On <?php echo date('j M, h:m:s', strtotime($comments->created_at)); ?></div>
                        </div>
                    </div>
                </div>
                @endforeach 
            </div>
            <div class="add_comment">
                <h3>Add a comment</h3>
                <div class="comment_form"> 

                    {!! Form::model($formObj,['method' => $method,'files' => true, 'action' => 'admin\AssignTasksController@SaveComment' ,'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!}

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <label>Asssign to</label>
                                {!! Form::select('user_id',[''=>'Select User']+$users,null,['class' => 'form-control user', 'data-required' => true,'id'=>'user_id']) !!} 
                            </div>
                            
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label> 
                                {!! Form::select('task_status',['0'=>'Pending','1'=>'Done'],$viewTask->status,['class' => 'form-control', 'data-required' => true]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Due date</label> 

                                {!! Form::text('task_due_date',null,['class' => 'task_due_date input-group form-control date-picker  data-date-format="dd/mm/yyyy" ', 'data-required' => false,'id'=>'','placeholder'=>'Due Date']) !!}
                            </div>
                        </div>
                        @if(!empty($auth) && $auth == 3)
                        <input type="hidden" value="{{ $action_params}}" name="task_priority">
                        @endif

                        @if(!empty($auth) && $auth == 1)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Priority</label>
                                {!! Form::select('task_priority',['0'=>'High','1'=>'Low','2'=>'Medium'],$viewTask->priority,['class' => 'form-control', 'data-required' => true]) !!}
                            </div>
                        </div>
                         @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Comments</label>
                                {!! Form::textarea('comments',null,['class' => 'form-control ckeditor','placeholder' => 'Enter Comment','rows'=>'1','id' => 'ckeditor-1']) !!}

                            </div>
                        </div>
                        <input type="hidden" value="{{ $action_params }}" name="assing_task_id">
                        <div class="col-md-12">
                            {!! Form::hidden('current_date',$due_date,['id' => 'current_date']) !!}
                            <div class="form-group">
                                <input value="Add" class="btn btn-success" id="task_add_btn" type="submit">
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
        </div>
        </div>
</section>
</div>
<style type="text/css">
    .task_details_page{}
.task_details_page .task_edit{
text-align:right;
margin-top:20px;
margin-bottom:25px;
}
.task_details_page .task_edit a{
color:#7a8ca5;
font-size:14px;
font-weight:600;
margin-left:25px;
letter-spacing:0.04em;
}
.task_details_page .task_title{
 border-bottom:#e7ecf1 solid 1px;
 padding-bottom:15px;
 margin-bottom:15px;
}
.task_details_page .task_title .title{
color:#333333;
font-size:24px;
margin-top:0;
margin-bottom:5px;
}
.task_details_page .task_title .proj_date{
color:#333333;
font-size:14px;
margin-bottom:5px;
display:inline-block;
margin-right:20px;
}
.task_details_page .task_title .greylabel{
display:inline-block;
position:relative;
top:-2px;
}
.task_details_page .greylabel{
color:#7a8ca5;
font-size:14px;
font-weight:400;
}
.task_details_page .task_title_right{
text-align:right;
margin-top:2px;
}
.task_details_page .proj_name{
color:#7a8ca5;
}
.task_details_page .proj_date{

}
.task_details_page .label {
    display: inline;
    padding: .1em .5em .3em;
    font-size: 14px;
    font-weight: 400;
    line-height: 1;
    color: #333;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
    letter-spacing:0.02em;
}
.pending {
   /* background-color: #e3e4e6;*/
    background-color: #f56d6d;
    
}
.done {
   /* background-color: #e3e4e6;*/
    background-color: #36c6d3;
    
}
.high {
    background-color: #f56d6d;
    color: #fff !important;
}
.medium {
    background-color: #F1C40F;
    color: #fff !important;
}
.low {
    background-color: #36c6d3;
    color: #fff !important;
}
.grey {
    background-color: #e3e4e6;
    color: #333 !important;
}
.task_desc {
 border-bottom:#e7ecf1 solid 1px;
 padding-bottom:20px;
 margin-bottom:20px;
 }
.task_desc .prioritylabel{ margin-bottom:15px;}
.task_desc .greylabel{ margin-bottom:15px;}
.task_desc .title{
color:#444d58;
font-size:18px;
font-weight:700;
margin-top:0;
margin-bottom:5px;
}
.task_desc .left{
border-right:#e7ecf1 solid 1px;
}
.task_comment{}
.task_comment .title{
color:#444d58;
font-size:18px;
font-weight:700;
margin-top:25px;
margin-bottom:20px;
}
.task_comment .left{
border-right:#e7ecf1 solid 1px;
}
.task_comment .comment_date{
color:#7a8ca5;
font-size:13px;
margin-bottom:10px;
}
.task_comment .assign_to{
color:#7a8ca5;
font-size:13px;
}
.task_comment .avtar{
float:left;
width:70px;
height:70px;
background-color:#CCCCCC;
-webkit-border-radius:50%;
-moz-border-radius:50%;
border-radius:50%;
margin-right:15px;
}
.task_comment .task_comment_item + .task_comment_item{ margin-top:35px;}
.comment_txt{ overflow:hidden;}
.add_comment{ margin-top:25px;}
</style>
@endsection



@section('scripts')
 
<script src="{{ asset('themes/admin/assets/global/plugins/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    function resetValidator()
        {
            setTimeout(function(){
              $("#main-frm1").parsley().destroy();
              $("#main-frm1").parsley();          
              $(".task_due_date").datepicker({
                        dateFormat: 'yy-mm-dd',
                        changeYear: true,
                        changeMonth: true,
                        yearRange: '1900:2050',
                        showButtonPanel: false,
                    });
            },400)
        }
    $(document).ready(function () {
        var current_date = $('#current_date').val();
        $('.task_due_date').val(current_date); 
         
        $("#user_id").select2({
                placeholder: "Search User",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });

        $(".ckeditor").each(function (){
            CKEDITOR.replace($(this).attr('id'),
            {
                toolbarGroups: 
                [
                    {"name":"basicstyles","groups":["basicstyles"]},
                    {"name":"paragraph","groups":["list"]}
                ],
            });
        });
         
        $('#main-frm1').submit(function () {
            
            $form = $(this);

            for(instance in CKEDITOR.instances) 
            {
                CKEDITOR.instances[instance].updateElement();
            }   

            if ($(this).parsley('isValid'))
            {
                $('#AjaxLoaderDiv').fadeIn('slow');
                $.ajax({
                    type: "POST",
                    url: $(this).attr("action"),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function (result)
                    {
                        $('#AjaxLoaderDiv').fadeOut('slow');
                        if (result.status == 1)
                        {
                            $.bootstrapGrowl(result.msg, {type: 'success', delay: 4000});
                            window.location = result.goto;
                        }
                        else
                        {
                            $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                        }
                    },
                    error: function (error) {
                        $('#AjaxLoaderDiv').fadeOut('slow');
                        $.bootstrapGrowl("Internal server error !", {type: 'danger', delay: 4000});
                    }
                });
            }            
            return false;
        });
    });
</script>
<script src="{{ asset("themes/admin/assets/")}}/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>

@endsection

