@extends('admin.layouts.app')
@section('styles')
 
@endsection
@section('content')
<div class="page-content">
  <div class="container">
      @include($moduleViewName.".viewSearch")

      <?php
        $net_pay_words = "";
        $total_earning = 0;
        $total_earning += (isset($record->basic_salary ))         ? $record->basic_salary : 0 ; 
        $total_earning += (isset($record->hra ))                  ? $record->hra : 0 ; 
        $total_earning += (isset($record->conveyance_allowance )) ? $record->conveyance_allowance : 0 ; 
        $total_earning += (isset($record->telephone_allowance )) ? $record->telephone_allowance : 0 ; 
        $total_earning += (isset($record->medical_allowance ))  ? $record->medical_allowance : 0 ; 
        $total_earning += (isset($record->uniform_allowance ))  ? $record->uniform_allowance : 0 ; 
        $total_earning += (isset($record->special_allowance ))  ? $record->special_allowance : 0 ; 
        $total_earning += (isset($record->bonus ))              ? $record->bonus : 0 ; 
        $total_earning += (isset($record->arrear_salary ))      ? $record->arrear_salary : 0 ; 
        $total_earning += (isset($record->advance_given ))      ? $record->advance_given : 0 ; 
        $total_earning += (isset($record->leave_encashment ))   ? $record->leave_encashment : 0 ; 

        $total_deduction = 0;
        $total_deduction += (isset($record->advance ))          ? $record->advance : 0 ; 
        $total_deduction += (isset($record->leave_deduction ))  ? $record->leave_deduction : 0 ; 
        $total_deduction += (isset($record->other_deduction ))  ? $record->other_deduction : 0 ; 
        $net_pay = $total_earning - $total_deduction;
        $net_pay_words = numberToWord($net_pay);

      ?>
    <div class="row autoResizeHeight">
      <div class="col-md-12">
        <div class="portlet box blue ">
          <div class="portlet-title">
              <div class="caption">
              <i class="fa fa-file"></i> Financial Year Reports</div>
                <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $back_url }}">Back</a>
          </div>
          <div class="portlet-body form"> 
            <div class="form-body"> 
              <table width="100%" border="0" cellspacing="0" style="border-top:1px solid #aaa; border-left:1px solid #aaa;">
                <tr>
                  <th width="25%" height="32" align="left" valign="middle" bgcolor="#d4d4d4" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Particular</th>
                  <th width="25%" align="center" valign="middle" bgcolor="#d4d4d4" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Amount</th>
                  <th width="25%" align="left" valign="middle" bgcolor="#d4d4d4" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Deduction</th>
                  <th width="25%" align="center" valign="middle" bgcolor="#d4d4d4" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Amount</th>
                </tr>
                <tr>
                  <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Basic Salary</td>
                  <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"> @if(isset($record->basic_salary ))   {{$record->basic_salary}} @endif </td>
                  <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Advance</td>
                  <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"> @if(isset($record->advance ))   {{$record->advance}} @endif</td>
                </tr>
                <tr>
                  <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">HRA</td>
                  <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">@if(isset($record->hra ))   {{$record->hra}} @endif </td>
                  <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Leave Deduction</td>
                  <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">@if(isset($record->leave_deduction ))   {{$record->leave_deduction}} @endif </td>
                  </tr>
                <tr>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Conveyance Allowance</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"> @if(isset($record->conveyance_allowance ))   {{$record->conveyance_allowance}} @endif</td>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Other Deduction</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"> @if(isset($record->other_deduction ))   {{$record->other_deduction}} @endif</td>
                </tr>
                <tr>
                  <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Telephone Allowance</td>
                  <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">@if(isset($record->telephone_allowance ))   {{$record->telephone_allowance}} @endif </td>
                  <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">TDS</td>
                  <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">@if(isset($record->tds ))   {{$record->tds}} @endif </td>
                  </tr>
                <tr>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Medical Allowance</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"> @if(isset($record->medical_allowance ))   {{$record->medical_allowance}} @endif</td>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Uniform Allowance</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">@if(isset($record->uniform_allowance ))   {{$record->uniform_allowance}} @endif </td>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Special Allowance</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">@if(isset($record->special_allowance ))   {{$record->special_allowance}} @endif </td>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Bonus / Incentive</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">@if(isset($record->bonus ))   {{$record->bonus}} @endif </td>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Arrear Salary</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"> @if(isset($record->arrear_salary ))   {{$record->arrear_salary}} @endif</td>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Advance Given</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"> @if(isset($record->advance_given ))   {{$record->advance_given}} @endif</td>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">Leave Encashment</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"> @if(isset($record->leave_encashment ))   {{$record->leave_encashment}} @endif</td>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"><strong>Total Earnings</strong></td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"><em><strong> {{$total_earning}}</strong></em></td>
                <td align="left" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;"><em><strong>Total Deductions</strong></em></td>
                <td align="right" valign="middle" style="border-bottom:1px solid #aaa; border-right:1px solid #aaa;padding:4px;">{{$total_deduction}} </td>
              </tr>
            </table>
            <tr>
              <td align="left" valign="top"><table width="100%" border="0" cellspacing="4" cellpadding="0">
                <tr>
                  <td width="20%" align="left" valign="middle">Net Pay</td>
                  <td width="80%" align="left" valign="middle"><strong><b> {{$net_pay}}  </b></strong></td>
                </tr>
                <tr>
                  <td align="left" valign="middle">Net Pay In Words</td>
                  <td align="left" valign="middle"> <?php 
                                          $net_pay_words = $net_pay_words; 
                                          $only = strpos($net_pay_words,"only");
                                          if($only == ''){
                                            $net_pay_words = $net_pay_words.' only';
                                            echo ucwords($net_pay_words); }
                                          else{
                                            echo ucwords($net_pay_words);}
                                           ?></td>
                </tr>
              </table></td>
            </tr>
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
      $(document).ready(function(){
      $("#user_id").select2({
                placeholder: "Search User Name",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#client_id").select2({
                placeholder: "Search Client Name",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#country_list").select2({
                placeholder: "Search Month",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $('#preview').click(function(){
            var user_val = $('#user_id').val();
            var client_val = $('#client_id').val();
            var months_val = $('#country_list').val();
            
            if(user_val == ''){
                alert('User Name Is Require!');
                return false;
            }
            if(client_val == '')
            {
                alert('Client Name Is Require!');
                return false;   
            }
            if(months_val == '')
            {
                alert('Month Is Require!');
                return false;   
            }
            if(user_val != '' && client_val != '' && months_val != '')
            {
                jQuery('#report_view').modal();     
                $('#AjaxLoaderDiv').fadeIn('slow');
      
                var report_url = "{{asset('/download-monthly-reports/ReportPreview') }}";  
                $.ajax({
                  type: "GET",
                  url: report_url,
                  data: 
                  {
                      user_id: user_val,client_id: client_val,months: months_val
                  },
                  success: function (result)
                  {
                      $("#monthly_report").html(result);
                      $('#AjaxLoaderDiv').fadeOut('slow');
                  },
                  error: function (error) 
                  {
                      $('#AjaxLoaderDiv').fadeOut('slow');
                  }
              });

            }
        });
      });


   </script>

    @endsection