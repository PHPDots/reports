@extends('admin.layouts.bopal_app')

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
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
                           {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!} 
                           
                                <div class="row">
                                    <div class="col-md-6">
                                       <label class="control-label">Selected Member: </label>
                                        <label class="form-control">{{ $member->firstname }}
                                       {{ $member->middlename }} {{ $member->lastname }}</label>
                                       <input type="hidden" value="{{$member->id}}" name="member_id">
                                       <input type="hidden" value="{{$loan_id}}" name="loan_id">
                                    </div>                                
                                    <div class="col-md-6">
                                        <label class="control-label">Loan Amount: <span class="required">*</span></label> 
                                        {!! Form::text('transaction_amount',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Enter Loan Amount']) !!}
                                    </div>                                                 
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="submit" value="Pay" class="btn btn-success pull-right" />
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

