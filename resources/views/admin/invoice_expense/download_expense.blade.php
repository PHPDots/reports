@extends('admin.layouts.app')

@section('styles')

<link href="{{ asset("themes/admin/assets/") }}/global/plugins/ladda/ladda-themeless.min.css" rel="stylesheet" type="text/css" />

@endsection

@section('content')
<?php
  $start_date = date('Y-m-d',strtotime('first day of this month'));
  $end_date = date('Y-m-d',strtotime('last day of this month'));
?>
<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">
        <div class="">
          <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-arrow-down"></i>Download Expense 
                </div>
            </div>
            <div class="portlet-body">
              <form id="exps-form">
              <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-4">
                  <label class="control-label">Expense Date Range</label>
                    <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                        <input type="text" class="form-control" value="{{ $start_date }}" name="search_invoice_start_date" id="search_start_leave" placeholder="Start Date">
                        <span class="input-group-addon"> To </span>
                        <input type="text" class="form-control" value="{{ $end_date }}" name="search_invoice_end_date" id="search_end_leave" placeholder="End Date">
                    </div>
                </div>
                <div class="col-md-4">
                  <label class="control-label">Action</label>
                      <select class="form-control input-large" name="expense_action" id="expense_action">
                          <option value="all"> All </option>
                          <option value="expense"> Expense </option>
                          <option value="invoice"> Invoice </option>
                      </select>
                </div>
              </div>
              <div class="clearfix">&nbsp;</div>
              <div class="clearfix">&nbsp;</div>
              <div class="row" align="center">
                <input type="hidden" name="is_download" id="is_download">
                <button type="button" class="btn btn-primary mt-ladda-btn ladda-button mt-ladda-btn ladda-button" data-style="expand-right" data-spinner-color="#333">
                    <span class="ladda-label btn-download-xls">
                        <i class="icon-arrow-down"></i> Download XLS </span>
                </button>&nbsp;
                <a class="btn btn-danger" href="">Reset</a>
              </div>
              </form>
            </div>
          </div>
        </div>
    </div>
</div>
 
@endsection

@section('styles')
  
@endsection

@section('scripts')
    <script type="text/javascript">

    $(document).ready(function(){
      
      $(document).on('click', '.btn-download-xls', function () {
            $("#is_download").val(1);
            $params = $("#exps-form").serialize();
            $url = '{{ url('download-expense-data') }}'+'?'+$params;
            window.location = $url;
        });
    });
    </script>
    <script src="{{ asset("themes/admin/assets/") }}/global/plugins/ladda/spin.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/assets/") }}/global/plugins/ladda/ladda.min.js" type="text/javascript"></script>
        <script src="{{ asset("themes/admin/assets/") }}/pages/scripts/ui-buttons.min.js" type="text/javascript"></script>
@endsection