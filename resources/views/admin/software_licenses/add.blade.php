@extends('admin.layouts.app')
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
                                        <label class="control-label">Title: <span class="required">*</span></label>                                        
                                        {!! Form::text('title',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Title']) !!}
                                    </div>                                                 
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="control-label">URL: <span class="required">*</span></label>                                        
                                        {!! Form::text('url',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter URL']) !!}
                                    </div>                                                 
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="control-label">Download Link:</label>
                                        {!! Form::text('download_link',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Enter Link']) !!}
                                    </div>                                                 
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">                                
                                    <div class="col-md-12">
                                        <label class="control-label">License Key:<span class="required">*</span></label>
                                        {!! Form::text('license_key',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter License Key']) !!}
                                    </div>                                                 
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">                                
                                    <div class="col-md-6">
                                        <label class="control-label">Payment Type:</label>
                                        {!! Form::select('payment_type',[''=>'Select','CC'=>'CC','net banking'=>'Net Banking'],null,['class' => 'form-control', 'data-required' => false]) !!}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Expiry Date:</label>
                                        <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                                            {!! Form::text('expiry_date',null,['class' => 'form-control', 'data-required' => false,'placeholder' => 'Select Expiry Date','id'=>'start_date']) !!}
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
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
        $('#main-frm1').submit(function () {
            
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

