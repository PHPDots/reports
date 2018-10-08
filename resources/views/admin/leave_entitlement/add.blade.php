@extends('admin.layouts.app')
<?php
$auth = Auth::guard('admins')->user()->user_type_id;
$month =0;
if(!empty($formObj->month))
    $month = $formObj->month;
else
    $month = date('m');
$year =0;
if(!empty($formObj->year))
    $year = $formObj->year;
else
    $year = date('Y');
?>

@section('content')

<div class="page-content">
    <div class="container">
        <div class="row autoResizeHeight">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file-text-o"></i>
                           {{ $page_title }}
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $back_url }}">Back</a>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
                           {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!} 
                           
                                <div class="row">                                
                                    <div class="col-md-12">
                                        <label class="control-label">User Name: <span class="required">*</span></label>                                        
                                        {!! Form::select('user_id',[''=>'Select User']+$users,null,['class' => 'form-control', 'data-required' => false,'id'=>'user_id']) !!}
                                    </div>                                                 
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="control-label">Month: <span class="required">*</span></label>

                                        {!! Form::select('month',[''=>'Select Month']+$months,$month,['class' => 'form-control', 'data-required' => false,'id'=>'month_id']) !!}
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Year: <span class="required">*</span></label>

                                        {!! Form::select('year',[''=>'Select Year']+$years,$year,['class' => 'form-control', 'data-required' => false,'id'=>'year_id']) !!}
                                    </div>
									<div class="col-md-3">
                                        <label class="control-label">Leave: <span class="required">*</span></label>
                                        {!! Form::select('leave_day',['1'=>'Full','0.5'=>'Half'],null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Type: <span class="required">*</span></label>
                                        {!! Form::select('type',['credit'=>'Add','debit'=>'Deduct'],null,['class' => 'form-control', 'data-required' => true]) !!}
                                    </div>
                                </div>
							<div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="control-label">Remark:</label>                                        
                                        {!! Form::text('remark',null,['class' => 'form-control', 'data-required' => false]) !!}
                                    </div>                                    
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
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


@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () { 
        $("#user_id").select2({
                placeholder: "Search User Name",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
         
        $('#main-frm1').submit(function () {
            
            if ($(this).parsley('isValid'))
            {   $('#submit_btn').attr("disabled", true);
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
                            window.location = '{{ $list_url }}';    
                        }
                        else
                        {
                            $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                            $('#submit_btn').attr('disabled', false);
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
@endsection

