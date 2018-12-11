<?php
$search_task_date = \Request::get("search_task_date");
if(isset($search_task_date))
    $search_task_date = $search_task_date;
else
    $search_task_date = date('Y-m',strtotime('first day of this month')); 
?>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-search"></i>Advance Search 
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand"> </a>
        </div>                    
    </div>
    <div class="portlet-body" style="display: none">  
        <form id="search-frm">
            <div class="row"> 
                <div class="col-md-4">
                    <label class="control-label">User Name</label>
                    {!! Form::select('search_user', [''=>'Search User'] + $users, Request::get("search_user"), ['class' => 'form-control','id'=>'user_id']) !!}
                </div> 
                <div class="col-md-4">
                    <label class="control-label">Client Name</label>
                    {!! Form::select('search_client', [''=>'Search Client'] + $clients, Request::get("search_client"), ['class' => 'form-control','id'=>'client_id']) !!}
                </div> 
                <div class="col-md-4">
                    <label class="control-label">Month</label>
                    {!! Form::select('search_task_date',['' => 'All'] + $task_data,$search_task_date,['class' => 'form-control','id'=>'task_date_id'] ) !!}
                </div>
                <div class="clearfix">&nbsp;</div>  
                <div class="col-md-12">              
                <div class="row" align="center"> 
                    <input type="hidden" name="is_total" id="is_total"/>
                    <input type="submit" class="btn blue mTop25" value="Search"/>
                    <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>
                </div>
            </div>  
            </div> 
            &nbsp; 

            </div>
        </form>
    </div>
</div>