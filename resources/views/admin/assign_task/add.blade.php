@extends('admin.layouts.app')

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
@section('styles')
<style type="text/css">
.ui-datepicker {
z-index: 9!important;
}
</style> 
@endsection
@section('content')

<div class="page-content">
    <div class="container">
        <div class="row autoResizeHeight">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-tasks"></i>
                           {{ $page_title }}
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                    </div>
                    <div class="portlet-body"> 
                        <div class="form-body">
                            <div class="form-group">
                                {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!}
                                    <div class="add_row clearfix">
                                        <div class="col-sm-4 nopadding">
                                            <div class="form-group">
                                            {!! Form::select('user_id[]',[''=>'Select User']+$users,null,['class' => 'form-control user', 'data-required' => true,'id'=>'user_id']) !!}
                                            </div>
                                        </div> 
                                        <div class="col-sm-4 nopadding">
                                            <div class="input-group" style="width:100%;">
                                              {!! Form::select('project_id[]',[''=>'Select Project']+$projects,null,['class' => 'form-control projects', 'data-required' => true,'id'=>'project_id']) !!}
                                              </select>
                                          </div>
                                        </div>  
                                        <div class="col-sm-4 nopadding">
                                            <div class="form-group">
                                                {!! Form::text('title[]',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Task Title']) !!}
                                            </div>
                                        </div>  
                                        <div class="col-sm-4 nopadding">
                                            <div class="form-group">
                                                {!! Form::select('status[]',['0'=>'Pending','1'=>'Done'],null,['class' => 'form-control', 'data-required' => true]) !!}
                                            </div>
                                        </div>  
                                        <div class="col-sm-4 nopadding">
                                            <div class="form-group">
                                                {!! Form::select('priority[]',['0'=>'High','1'=>'Low','2'=>'Medium'],null,['class' => 'form-control', 'data-required' => true]) !!}
                                            </div>
                                        </div> 
                                        <div class="col-sm-4 nopadding">
                                            <div class="form-group">  
                                                {!! Form::text('due_date[]',null,['class' => 'due_date input-group form-control date-picker  data-date-format="dd/mm/yyyy" ', 'data-required' => false,'id'=>'','placeholder'=>'Task Date']) !!}
                                            </div>
                                        </div>              
                                        <div class="col-sm-12 nopadding">
                                            <div class="form-group">
                                                {!! Form::textarea('description[]',null,['class' => 'form-control ckeditor','placeholder' => 'Enter Description','rows'=>'1','id' => 'ckeditor-1']) !!}
                                            </div>
                                        </div> 
                                        <div class="col-sm-12 nopadding">
                                            <div class="form-group">
                                                <div class="input-group">  
                                                    <div class="input-group-btn btn-add-task" style="padding-left:5px">
                                                        <button class="btn btn-success pull-right" type="button"  onclick="assign_task();"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div> 
                                    </div> 
                                <div id="assign_task">
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        {!! Form::hidden('current_date',$due_date,['id' => 'current_date']) !!}
                                        <input type="submit" value="Save" class="btn btn-success pull-right" id="submit_btn" />
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>                 
            </div>
        </div>
    </div>
</div>            

@endsection
<style type="text/css">
.add_row_new{
    border: solid 1px #e3e6e8;
    padding-top: 15px;
    background-color: #f7f8f9;
    margin-top: 15px;
}
.add_row{
    border: solid 1px #e3e6e8;
    padding-top: 15px;
    background-color: #f7f8f9;
}
.btn.btn-danger,
.btn.btn-success{
    padding: 9px 10px;
}
</style>
@section('scripts')
<script src="{{ asset('themes/admin/assets/global/plugins/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    var room = 1;
function assign_task() { 

    room++;
    var objTo = document.getElementById('assign_task');
  
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclass"+room);
    var rdiv = 'removeclass'+room;
    var first = document.getElementById('project_id');
    var options = first.innerHTML; 
    var second = document.getElementById('user_id');
    var current_date = $('#current_date').val();
  if(second != null){
     
    var users = second.innerHTML;
     
    divtest.innerHTML = '<div class="add_row clearfix"><div class="col-sm-4 nopadding"><div class="form-group"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control user" id=" " name="user_id[]" style="width:100%;">'+users+'</select></div></div></div><div class="col-sm-4 nopadding"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control projects" id=" " name="project_id[]" style="width:100%;">'+options+'</select></div></div><div class="col-sm-4 nopadding"><div class="form-group"><input type="text" class="form-control" data-required="true" id="title" name="title[]" value="" placeholder="Enter Task Title"></div></div><div class="col-sm-4 nopadding"><div class="form-group"><select class="form-control" id="status" name="status[]" data-required="true"><option value="0">Pending</option><option value="1">Done</option></select></div></div><div class="col-sm-4 nopadding"><div class="form-group"><select class="form-control" id="priority" name="priority[]" data-required="true"><option value="0">High</option><option value="1">Low</option><option value="2">Medium</option></select></div></div><div class="col-sm-4 nopadding"><div class="form-group"><input type="text" name="due_date[]" value="'+current_date+'" class="due_date input-group form-control form-control-inline date date-picker " size="16" data-date-format="dd/mm/yyyy" id=""></div></div><div class="col-sm-12 nopadding"><div class="form-group"><textarea rows="1" class="form-control ckeditor1-'+room+'" id="ckeditor1-'+room+'" name="description[]" value="" placeholder="Enter Description"></textarea></div></div><div class="col-sm-12 nopadding"><div class="form-group"><div class="input-group-btn" style="padding-left:5px"><button class="btn btn-danger pull-right" type="button" onclick="remove_education_fields('+ room +');"><span class="glyphicon glyphicon-minus"aria-hidden="true"></span></button></div></div></div></div></div></div>';}
     else{ 
         divtest.innerHTML = '<div class="add_row_new clearfix"><div class="col-sm-4 nopadding"><div class="form-group"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control projects" id=" " name="project_id[]" style="width:100%;">'+options+'</select></div></div></div><div class="col-sm-4 nopadding"><div class="form-group"><input type="text" class="form-control" data-required="true" id="title" name="title[]" value="" placeholder="Enter Task Title"></div></div></div>';
        }
        objTo.appendChild(divtest)
        serach_project();
        resetValidator(); 
        $('.ckeditor1-'+room).each(function (){
            CKEDITOR.replace(this.id,
            {
                toolbarGroups: 
                [
                    {"name":"basicstyles","groups":["basicstyles"]},
                    {"name":"paragraph","groups":["list"]}
                ],
            });
        });
}

function remove_education_fields(rid) 
{
    $('.removeclass'+rid).remove();
    resetValidator();
   
}
function resetValidator()
   {
    setTimeout(function(){
      $("#main-frm1").parsley().destroy();
      $("#main-frm1").parsley();          
      $(".due_date").datepicker({
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                changeMonth: true,
                yearRange: '1900:2050',
                showButtonPanel: false,
            });
    },400)
}
    function serach_project()
    {
        $(".projects").select2({
                placeholder: "Search Project",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $(".user").select2({
                placeholder: "Search User",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
    }
     
    $(document).ready(function () {

        $(".ckeditor").each(function (){
            CKEDITOR.replace(this.id,
            {
                toolbarGroups: 
                [
                    {"name":"basicstyles","groups":["basicstyles"]},
                    {"name":"paragraph","groups":["list"]}
                ],
            });
        });

        $(".projects").select2({
                placeholder: "Search Project",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });

        $(".user").select2({
                placeholder: "Search User",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        
        var current_date = $('#current_date').val();
        $('.due_date').val(current_date);
        $(document).on("click",".btn-add-task",function(){
            $(".projects").select2({
                placeholder: "Search Project",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });

        },1000);
        /*$(document).on("click",".btn-add-task",function(){
            setTimeout(function(){
                $("select[name='user_id[]']").each(function(){
                    if($.trim($(this).val()) == '')
                    {
                        $(this).val('{{ Auth::guard('admins')->user()->id }}');
                    }
                });
            },1000);
        });*/
        
        $('#main-frm1').submit(function () {

            $form = $(this);

            for(instance in CKEDITOR.instances) 
            {
                CKEDITOR.instances[instance].updateElement();
            } 

            if ($(this).parsley('isValid'))
            {
                $('#submit_btn').attr("disabled", true);
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
                            $('#submit_btn').attr("disabled", false);
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
<script src="{{ asset("themes/admin/assets/")}}/global/plugins/jquery-repeater/jquery.repeater.js" type="text/javascript"></script>
<script src="{{ asset("themes/admin/assets/")}}/pages/scripts/form-repeater.min.js" type="text/javascript"></script>
<script src="{{ asset("themes/admin/assets/")}}/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
@endsection

