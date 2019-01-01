<?php
$search_month = \Request::get("search_month");
if(!empty($search_month))
    $search_month = $search_month;
else
    $search_month = date('Y-m',strtotime('first day of this month')); 
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
                    <label class="control-label">Created Date Range</label>
                    <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                        <input type="text" class="form-control" value="{{ \Request::get("search_start_date") }}" name="search_start_date" id="start_date" placeholder="Start Date">
                        <span class="input-group-addon"> To </span>
                        <input type="text" class="form-control" value="{{ \Request::get("search_end_date") }}" name="search_end_date" id="end_date" placeholder="End Date"> 
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="control-label">Invoice No</label>
                    <input type="text" value="{{ \Request::get("search_invoice_no") }}" class="form-control" name="search_invoice_no" />
                </div>
				<div class="col-md-4">
                    <label class="control-label">Month :</label>
                    {!! Form::select('search_month',['' => 'All'] + $months, $search_month,['class' => 'form-control','id'=>'month_id'] ) !!}
                </div>
            </div>
            <div class="clearfix">&nbsp;</div>
             <div class="row">                
                <div class="col-md-4">
                    <label class="control-label">Client Name :</label>
                    {!! Form::select('search_client_name',[''=>'Select']+$clients,\Request::get("search_client_name"),['class' => 'form-control','id'=>'client_id']) !!}
                </div>
				 <div class="col-md-4">
                    <label class="control-label">Status</label>
                    <select name="search_status" class="form-control">
                        <option value="all" {!! \Request::get("search_status") == "all" ? 'selected="selected"':'' !!}>All</option>                        
                        <option value="0" {!! \Request::get("search_status") == "0" ? 'selected="selected"':'' !!}>UnPaid</option>                        
                        <option value="1" {!! \Request::get("search_status") == "1" ? 'selected="selected"':'' !!}>Paid</option>
                    </select>                                                                 
                </div>
				 <div class="col-md-4">
                    <label class="control-label">ID(s)</label>
                    <input type="text" value="{{ \Request::get("search_id") }}" class="form-control" name="search_id" placeholder="Search Ids" />
                </div>
            </div>   
            <div class="clearfix">&nbsp;</div>
            <div class="row">
                <div class="col-md-4">
                    <label class="control-label">Client Type</label>
                    <select name="search_c_type" class="form-control">
                        <option value="all" {!! \Request::get("search_c_type") == "all" ? 'selected="selected"':'' !!}>All</option>
                        <option value="2" {!! \Request::get("search_c_type") == "2" ? 'selected="selected"':'' !!}>International</option>
                        <option value="1" {!! \Request::get("search_c_type") == "1" ? 'selected="selected"':'' !!}>Local</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="submit" class="btn blue mTop25" value="Search"/>
                    &nbsp;
                    <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>
                </div>
            </div>
        </form>
    </div>    
</div>      