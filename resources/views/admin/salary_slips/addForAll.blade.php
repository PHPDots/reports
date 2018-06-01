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
                            <i class="fa fa-users"></i>
                            Add Salary Slip For Users
                        </div>
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body form">
                            <form id="generate_form_id" action="{{ route('salaryslipForAllData') }}" method="POST">
                                {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">
                                    <div align="center">
                                        <label class="control-label">Month - Year <span class="required">*</span></label>
                                        <div class="input-group input-large date-picker input-daterange" data-date="10/2012" data-date-format="M/yyyy">
                                            {!! Form::text('month_year',$today,['class' => 'form-control MonthYear', 'data-required' => true,'placeholder' => 'Select Month - Year','id'=>'month_year']) !!}
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="clearfix">&nbsp;</div>
                                    <div class="form-group form-md-checkboxes">
                                        <label><h4>Users</h4></label>
                                        <div class="md-checkbox-inline">
                                            @foreach($users as $user)
                                            <?php
                                                $yes = '';
                                                $breakup = \App\Models\SalaryBreakup::where('user_id',$user->id)->first();
                                                if($breakup)
                                                    $yes = '*';
                                             ?>
                                            <div class="col-md-3">
                                            <div class="md-checkbox">
                                                <input type="checkbox" id="checkbox{{$user->id}}" class="md-check" name="user[]" value="{{$user->id}}" checked="checked">
                                                <label for="checkbox{{$user->id}}">
                                                    <span></span>
                                                    <span class="check" style="z-index: 1;"></span>
                                                    <span class="box" ></span>
                                                    {{ ucfirst($user->firstname) }}   {{ ucfirst($user->lastname) }} {{ $yes }}
                                                    </label>
                                            </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row" align="center">
                                    <div class="col-md-12">
                                        <input type="submit" value="Generate For All" class="btn btn-success" />
                                    </div>
                                </div>
                                <hr/>
                                <p style="text-align: center; font-family: Arial, sans-serif; font-size: 12px; line-height: 18px; color: #636466;"> ( * )  which users have salary breakup records !</p>
                            </form>
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
		
        $('#generate_form_id').submit(function () {
            var month_year = $('#month_year').val();
            var txt = 'Are you sure with '+ month_year + ' ?';
            if(confirm(txt))
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
                            window.location = '{{ $list_url }}';    
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

