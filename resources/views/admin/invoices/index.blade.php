@extends('admin.layouts.app')

@section('styles')

<style type="text/css">
.amount-box
{
    border: solid;
    border-width:1px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-right: 5px;
    padding-left: 5px;
    margin-top: 5px;
    border-color: #adadad;
}
</style>
@endsection


@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
            
            @include($moduleViewName.".search") 

            <div class="clearfix"></div> 
             <span class="amount-box pull-right">
              Total Amounts: <span id="overall-amounts"> 0 </span>
            </span>
            <span class="amount-box pull-right" style="margin-right: 5px;">
              Total Unpaid: <span id="overall-unpaid"> 0 </span>
            </span>
            <span class="amount-box pull-right" style="margin-right: 5px;">
              Total Paid: <span id="overall-paid"> 0 </span>
            </span>
			
            <div class="clearfix">&nbsp;</div>  
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>{{ $page_title }}    
                    </div>
                    @if($btnAdd)
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $add_url }}">Add New</a>
                    @endif        
                </div>
                <div class="portlet-body">                    
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="5%">ID</th>
                               <th width="15%">Invoice</th>
                               <th width="15%">CLient Name</th>
                               <th width="5%">Amount</th>
								               <th width="5%">GST</th>
                               <th width="5%">Total Amount</th>
							                 <th width="5%">Invoice Date</th>
                               <th width="5%">Status</th>
                               <th width="10%">Created At</th>
                               <th width="25%" data-orderable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>                                              
                </div>
            </div>              
        </div>
    </div>
</div>
<div class="modal fade bs-modal-lg" id="invoice_view" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Invoice Details</h4>
        </div>
        <div class="modal-body">
          <table class="table" id="invoice_detail_table">
            
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
</div>
  <div class="modal fade" id="payment_status_model" role="dialog">
    <div class="modal-dialog">  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Payment Status Form.</h4>
        </div>
        <div class="modal-body">
          <p>
            {!! Form::open(['route'=>['invoices.change_paymet_status'],'method' => 'POST','id'=>'payment_form'])!!}

              <label class="control-label">Payment Status:<span class="required">*</span></label>
              {!! Form::select('payment_status',[1=>'Full',0=>'Partials'],null,['class'=>'form-group form-control','data-required' => true,'id'=>'payment_status_id'])!!}
			       <div id="partial_amt_div" style="display: none;">
              <label class="control-label">Patials Amount:<span class="required">*</span></label>
              {!! Form::text('partial_amount',null,['placeholder'=>'Enter Patials Amount','class'=>'form-group form-control']) !!}
              </div>
              <label class="control-label">Amount:<span class="required">*</span></label>
              {!! Form::text('amount',null,['placeholder'=>'Enter Amount','class'=>'form-group form-control','required'=>'required']) !!}

              <label class="control-label">Payment Date:<span class="required">*</span></label>
              <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy" style="z-index: 999999!important;">
                  {!! Form::text('payment_date',date('Y-m-d'),['class' => 'form-control', 'data-required' => true,'placeholder' => 'Select Date','id'=>'dob']) !!}
                  <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                  </span>
              </div>
              {!! Form::hidden('invoice_id',null,['id'=>'invoice_id'])!!}
              <input type="submit" name="submit" id="payment_submit" class="btn btn-primary pull-right">
              <div class="clearfix">&nbsp;</div>
            {!! Form::close()!!}

        </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>      
    </div>
</div>
<style type="text/css">
    #ui-datepicker-div
    {
      z-index: 9999999 !important;
    }
  </style>
@endsection

@section('styles')
  
@endsection

@section('scripts')
    <script type="text/javascript">
    
function openView($id){
      jQuery('#invoice_view').modal();     
      $('#AjaxLoaderDiv').fadeIn('slow');
      
      var invoice_url="{{asset('/invoices/view') }}";  
      $.ajax({
          type: "GET",
          url: invoice_url,
          data: 
          {
              invoice_id: $id
          },
          success: function (result)
          {
              $("#invoice_detail_table").html(result);
              $('#AjaxLoaderDiv').fadeOut('slow');
          },
          error: function (error) 
          {
              $('#AjaxLoaderDiv').fadeOut('slow');
          }
      });

    }
function openPaymentModel($id)
{
  jQuery('#payment_status_model').modal();
  $("#payment_form input[name='invoice_id']").val($id);
}
    $(document).ready(function(){
		$('#payment_status_id').on('change',function(){
        var status_val = $('#payment_status_id').val();
          if(status_val == 1)
            $('#partial_amt_div').hide();
          else
            $('#partial_amt_div').show();
      });
		$('#payment_form').submit(function () {
          $('#AjaxLoaderDiv').fadeIn('slow');
          $('#payment_submit').attr('disabled',true);
          if($(this).parsley('isValid'))
           {
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
                        $('#payment_submit').attr('disabled',false);
                        $.bootstrapGrowl(result.msg, {type: 'success', delay: 4000});
                        window.location = result.goto;    
                      }
                      else
                      {
                        $('#payment_submit').attr('disabled',false);
                        $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                      }
                  }
                });
           }
           return false;
        });
		
        $("#search-frm").submit(function(){
            oTableCustom.draw();
            return false;
        });
		$("#client_id").select2({
          placeholder: "Search Client Name",
          allowClear: true,
          minimumInputLength: 2,
          width: null
        });

        $.fn.dataTableExt.sErrMode = 'throw';

        var oTableCustom = $('#server-side-datatables').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            pageLength: '{{ $length }}',
            displayStart: '{{ $start }}',
            lengthMenu:
              [
                [100,150,200,250,300],
                [100,150,200,250,300]
              ],
            ajax: {
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function ( data ) 
                {
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_invoice_no = $("#search-frm input[name='search_invoice_no']").val();
					data.search_month = $("#search-frm select[name='search_month']").val();
                    data.search_client_name = $("#search-frm select[name='search_client_name']").val();
                    data.search_status = $("#search-frm select[name='search_status']").val();
					data.search_id = $("#search-frm input[name='search_id']").val();
					data.search_c_type = $("#search-frm select[name='search_c_type']").val();
                },
            dataSrc: function(response){
                    $("#overall-amounts").html(response.amounts);
					          $("#overall-paid").html(response.total_paid_amt);
                    $("#overall-unpaid").html(response.total_unpaid_amt);
                    $("#is_total").val(response.amounts);
                    return response.data;
                }
            },            
            "order": [[ "{{ $orderClm }}", "{{ $orderDir }}" ]],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'client_name', name: '{{ TBL_CLIENT }}.name' },
				        { data: 'total_without_gst', name: 'total_without_gst' },
                { data: 'total_with_gst', name: 'total_with_gst' },
                { data: 'total_amount', name: 'total_amount' },
				        { data: 'invoice_date', name: 'invoice_date' },
				        { data: 'payment', name: 'payment' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', orderable: false, searchable: false}
            ]
        });        
    });
    </script>
@endsection