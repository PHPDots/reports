@extends('admin.layouts.app')

@section('styles')
@endsection

@section('content')

<div class="page-content">
    <div class="container">
        <div class="row autoResizeHeight">
            <div class="col-md-12">
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i>
                           {{ $page_title }}
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $back_url }}">Back</a>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
                           {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!} 
                           
                                <div class="row">                                
                                    <div class="col-md-6">
                                        <label class="control-label">Name: <span class="required">*</span></label> 
                                        <div class="input-group">
                                        {!! Form::text('name',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Name']) !!}
                                        <span class="input-group-addon">
                                                <i class="fa fa-user"></i>
                                        </span>
                                        </div>                                            
                                    </div>                                                 
                                    <div class="col-md-6">
                                        <label class="control-label">Email: <span class="required">*</span></label>
                                        <div class="input-group">                            
                                            {!! Form::text('email',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Email']) !!}
                                            <span class="input-group-addon">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">Phone No:</label>
                                        <div class="input-group">                            
                                        {!! Form::text('phone',null,['class' => 'form-control','placeholder' => 'Enter Phone Number']) !!}
                                        <span class="input-group-addon">
                                                <i class="fa fa-phone"></i>
                                        </span>
                                        </div>                                            
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Country: </label>                            
                                        {!! Form::text('country',null,['class' => 'form-control','placeholder' => 'Enter Country']) !!}
                                    </div>
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">State: </label>                            
                                        {!! Form::text('state',null,['class' => 'form-control','placeholder' => 'Enter State']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">City: </label>                            
                                        {!! Form::text('city',null,['class' => 'form-control','placeholder' => 'Enter City']) !!}
                                    </div>
                                </div>
							<div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">Address: </label>
                                        {!! Form::textarea('address',null,['class' => 'form-control','placeholder' => 'Enter Address','rows'=>'2']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Currency: </label>
                                        {!! Form::select('client_currency',[''=>'Select Currency']+$currency,null,['class' => 'form-control']) !!}
                                    </div>
                                </div>
							<div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-6">
                                    <label class="control-label">GSTN No: </label>
                                        {!! Form::text('gstn_no',null,['class' => 'form-control','placeholder' => '']) !!}
                                    </div>
                                    <div class="col-md-6">
                                    <br/>
                                    <div class="form-group">
                                        <div class="col-md-3">Type : <span class="required">*</span></div>
                                        <div class="col-md-9">
                                            <div class="mt-radio-inline">
                                                <label class="mt-radio">
                                                     {{ Form::radio('client_type',1,null, ['class' => 'form-control']) }}
                                                    Local
                                                    <span></span>
                                                </label>
                                                <label class="mt-radio">
                                                    {{ Form::radio('client_type',2,null, ['class' => 'form-control']) }}
                                                    International
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-3">
                                        {{ Form::checkbox('send_email', 0, null, ['class' => 'field', 'style' =>"zoom:1.7",'id'=>'check_send_mail']) }}
                                        <label class="control-label"> Send Email Report </label>
                                    </div>
                                    <div id="sendTypeDiv" style="display: none;">
                                    <div class="col-md-3">
                                        <label class="control-label"> Send Email Type: </label>
                                        {!! Form::select('send_mail_type',[1=>'All Users',0=>'Selected Users'],null,['class' => 'form-control','id'=>'send_email_type']) !!}
                                    </div>
                                    <div class="col-md-6">
                                    <div class="portlet">
                                        <div class="portlet-body" style="display: none;" id="send_email_users_div">
                                        <label class="control-label">Send Email Users:</label>
                                            <select class="select_users" multiple="multiple" name="sendMailUsers[]">
                                    @foreach($sendMailUsers as $row)
                                            <option {{ in_array($row->id, $list_sendMailUsers) ? 'selected':''}} value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    </div>
                                    </div>
                                </div>
							<div class="clearfix">&nbsp;</div>
								 <div class="row">
                                    <div class="col-md-12">
                                    <div class="portlet">
                                    <div class="portlet-body" style="display: block;">
                                        <label class="control-label">Users:</label>
                                            <select class="select_users" multiple="multiple" name="users[]">
                                    @foreach($users as $row)
                                            <option {{ in_array($row->id, $list_tags) ? 'selected':''}} value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                            </select>                                            
                                    </div>
                                    </div>
                                    </div>
                                </div>
                                
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="submit" value="Save" class="btn btn-success pull-right" />
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

<style type="text/css">
    
</style>
@endsection

@section('scripts')

<script type="text/javascript">
    $(document).ready(function () {
        var check_send_email = '{{ $formObj->send_email }}';
        if(check_send_email == 1)
        {
            $("#sendTypeDiv").show();
            var send_mail_type = '{{ $formObj->send_mail_type }}';
            if(send_mail_type == 0)
            {
                $('#send_email_users_div').show();
            }
        }
        $('#send_email_type').on('change',function(){
            var type = $('#send_email_type').val();
            if(type == 1)
                $('#send_email_users_div').hide();
            else
                $('#send_email_users_div').show();
        });
        $("#check_send_mail").click(function() {
            if($(this).is(":checked")) {
                $("#sendTypeDiv").show();
            } else {
            $("#sendTypeDiv").hide();
            }
        });
		$(".select_users").select2({
                placeholder: "Search Users",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
		
        $('#main-frm1').submit(function () {
            
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
@endsection

