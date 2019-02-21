@extends('admin.layouts.app')
<?php
$today = date('F-Y');
?>
@section('content')

<div class="page-content">
    <div class="container">
        <div class="row autoResizeHeight">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file"></i>
                            Salary Report
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body form">
                            <form id="salaryReport" action="{{ route('salaryReportData') }}" method="POST">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="control-label">User <span class="required">*</span></label>
                                        <div class="input-group input-large date-picker input-daterange" data-date="10/2012" data-date-format="M/yyyy">
                                            {!! Form::select('user_id',[''=>'Select User']+$users,null,['class' => 'form-control select_search', 'data-required' => true,'id'=>'user_id']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">Month-Year Range <span class="required">*</span></label>
                                        <div class="input-group input-large date-picker input-daterange" data-date="10/2012" data-date-format="M/yyyy">
                                            {!! Form::text('start_month_year',$today,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Select Month - Year','id'=>'month_start_date','autocomplete'=>'off']) !!}
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            {!! Form::text('end_month_year',$today,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Select Month - Year','id'=>'month_end_date','autocomplete'=>'off']) !!}
                                        </div>
                                        <input type="hidden" name="is_download_xls" id="is_download_xls">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="clearfix">&nbsp;</div>
                                        <input type="submit" value="Go" class="btn btn-success" id="submitBtn" />
                                    </div>
                                </div>
                            </form>
                            <div class="clearfix">&nbsp;</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="reportView"></div>
        </div>
    </div>
</div>

<style type="text/css">
    
</style>
@endsection

@section('scripts')

<script type="text/javascript">
    $(document).ready(function () { 
        $("#user_id").select2({
                placeholder: "Search User",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#month_start_date").datepicker({
                dateFormat: 'MM-yy',
                changeYear: true,
                changeMonth: true,
                yearRange: '1900:2050',
                showButtonPanel: false,
                onClose: function (selectedDate) {
                    $("#end_date").datepicker("option", "minDate", selectedDate);
                }
            });

            $("#month_end_date").datepicker({
                dateFormat: 'MM-yy',
                changeYear: true,
                changeMonth: true,
                yearRange: '1900:2050',
                showButtonPanel: false,
                onClose: function (selectedDate) {
                    $("#start_date").datepicker("option", "maxDate", selectedDate);
                }
            });
		
        $('#salaryReport').submit(function () {

            if (true)
            {
                $("#is_download_xls").val(0);
                $('#AjaxLoaderDiv').fadeIn('slow');
                $('#submitBtn').fadeIn('disabled',true);
                $.ajax({
                    type: "POST",
                    url: $(this).attr("action"),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function (result)
                    {
                        $('#submitBtn').fadeIn('disabled',false);
                        $('#AjaxLoaderDiv').fadeOut('slow');
                        if (result.status == 1)
                        {
                            $.bootstrapGrowl(result.msg, {type: 'success', delay: 4000});
                            $('#reportView').html(result.viewTable);  
                        }
                        else
                        {
                            $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                        }
                    },
                    error: function (error) {
                        $('#submitBtn').fadeIn('disabled',false);
                        $('#AjaxLoaderDiv').fadeOut('slow');
                        $.bootstrapGrowl("Internal server error !", {type: 'danger', delay: 4000});
                    }
                });
            }
            return false;
        });

        $(document).on('click', '.downloadXls', function () {
            $("#is_download_xls").val(1);
            setTimeout(function(){
                $params = $("#salaryReport").serialize();
                $url = '{{ route("salaryReportData") }}'+'?'+$params;
                window.location = $url;
            },400);
        });
    });
</script>
@endsection

