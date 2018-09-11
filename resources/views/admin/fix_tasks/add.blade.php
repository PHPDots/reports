@extends('admin.layouts.app')


@section('content')

<div class="page-content">
    <div class="container">
        <div class="row autoResizeHeight">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file"></i>
                           {{ $page_title }}
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $back_url }}">Back</a>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                           {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'mt-repeater form-horizontal sky-form form form-group', 'id' => 'main-frm1']) !!}
            
                                    <h3 class="mt-repeater-title">Client Name</h3>
                                    {!! Form::select('client_id',[''=>'Select Client']+$clients,null,['class' => 'form-control', 'data-required' => true,'id'=>'client_id']) !!}
                                    <hr/>
                                    <div class="fix_div" style="display: none;">
                                    <div data-repeater-list="group-a">
                                        <div data-repeater-item class="mt-repeater-item" style="background-color:#eff4f7;">
                                        <div class="clearfix">&nbsp;</div>
                                            <div class="col-md-12">
                                                <div class="col-md-6">
                                                    {!! Form::text('title',null,['class' => 'form-control title','placeholder' => 'Enter Title','required'=>'required']) !!}
                                                </div>
                                                <div class="col-md-2">
                                                    {!! Form::text('hours',null,['class' => 'form-control','required'=>'required','placeholder' => 'Enter Hours']) !!}
                                                </div>
                                                <div class="col-md-2">
                                                    {!! Form::text('fix',null,['class' => 'form-control fix','required'=>'required','placeholder' => 'Enter Fixed']) !!}
                                                </div>
                                                <div class="col-md-2">
                                                    {!! Form::text('rate',null,['class' => 'form-control','required'=>'required','placeholder' => 'Enter Rate']) !!}
                                                </div>
                                            </div>
                                            <div class="clearfix">&nbsp;</div>
                                            <div class="col-md-12">
                                                <div class="col-md-6">
                                                    {!! Form::text('ref_link',null,['class' => 'form-control','placeholder' => 'Enter Ref. Link']) !!}
                                                </div>
                                                <div class="col-md-3">
                                                    {!! Form::text('assigned_by',null,['class' => 'form-control','placeholder' => 'Enter Assigned by']) !!}
                                                </div>
                                                <div class="col-md-3">
                                                    {!! Form::text('task_date',date('Y-m-d'),['class' => 'input-group form-control form-control-inline date date-picker taskdate','required'=>'required','placeholder'=>'Task Date']) !!}
                                                </div>
                                            </div>
                                            <div class="clearfix">&nbsp;</div>
                                            <div class="col-md-12">
                                                <div class="col-md-10">
                                                {!! Form::textarea('description',null,['class' => 'form-control','placeholder' => 'Enter Description','rows'=>'3']) !!}
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mt-repeater-input">
                                                        <a href="javascript:;" data-repeater-delete class="btn btn-danger mt-repeater-delete" title="Remove This Task">
                                                            <i class="fa fa-close"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix">&nbsp;</div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="fix_div" style="display: none;">
                                    <a href="javascript:;" data-repeater-create class="btn btn-success mt-repeater-add" title="Add New Task" onclick="task_date()">
                                        <i class="fa fa-plus"></i></a>
                                    <input type="submit" value="{{ $buttonText }}" class="btn btn-success pull-right" id="submit_btn" />
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

@section('scripts')
<script type="text/javascript">
    function task_date()
    {
        setTimeout(function(){
        $('.taskdate').val('{{ date("Y-m-d")}}');
        //$("input[name='title']").attr('required','required');

        },1000);
    }
    $(document).ready(function () { 
        $("#client_id").select2({
                placeholder: "Search Client Name",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $('#client_id').on('change',function(){
            var client_id = $('#client_id').val();
            if(client_id != '')
                $('.fix_div').show();
            else
                $('.fix_div').hide();
        });

        $('#main-frm1').submit(function () {
            
            if ($(this).parsley('isValid'))
            {
                $('#submit_btn').attr('disabled',true);
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
                            $('#submit_btn').attr('disabled',false);
                            $.bootstrapGrowl(result.msg, {type: 'success', delay: 4000});
                            window.location = result.goto;    
                        }
                        else
                        {
                            $('#submit_btn').attr('disabled',false);
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
<script src="{{ asset("themes/admin/assets/")}}/global/plugins/jquery-repeater/jquery.repeater.js" type="text/javascript"></script> 
<script src="{{ asset("themes/admin/assets/")}}/pages/scripts/form-repeater.min.js" type="text/javascript"></script> 
<script src="{{ asset("themes/admin/assets/")}}/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
@endsection

