@extends('admin.layouts.app')
@section('styles')
 
@endsection

<?php
    if(!empty($formObj->address) && isset($formObj->address))
        $address = $formObj->address;
    else
        $address = $address;
    if(!empty($formObj->invoice_date) && isset($formObj->invoice_date))
        $today = $formObj->invoice_date;
    else
        $today = date('d-M-y');
    if(!empty($formObj->invoice_no) && isset($formObj->invoice_no))
        $invoice_no = $formObj->invoice_no;
    else
        $invoice_no = $invoice_no;
?>
@section('content')
<div class="page-content">
    <div class="container">
        
        <div class="col-md-12">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                    <i class="fa fa-picture"></i>{{ $page_title }}
                    </div>
                    <a href="javascript:;" class="reload" onclick="invoice_cal()" id="reload_id"> </a>
                    <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $back_url }}">Back</a>
                </div>
                <div class="portlet-body">
                    <div class="table">
                        {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'sky-form form form-group', 'id' => 'main-frm1']) !!} 
                        <table class="table table-bordered table-hover" id="invoice_table" width="100%">
                                <tr>
                                    <td><img src="{{ asset("images/pd-logo.png")}}" alt="logo" class="logo-default" style="max-width: 100px;margin-top: 15px !important; max-height: 250px"></td>
                                    <td colspan="4"><b style="font-size: 40px;"><center>PHPDots Technologies</center></b></td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        {!! Form::text('address',$address,['class' => 'form-control', 'data-required' => true]) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5"><b style="font-size: 30px"><center>TAX INVOICE</center></b></td>
                                </tr>
                                <tr>
                                    <td colspan="3"><b>To</b></td>
                                    <td colspan="2"><b><center>Reference Details:</center></b></td>
                                </tr>
                                <tr>
                                    <td rowspan="4" colspan="3">
                                        {!! Form::textarea('to_address',null,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Type Address.','rows'=>'6']) !!}
                                    </td>
                                    <td> <center><b>Invoice No. </b></center></td>
                                    <td> {!! Form::text('invoice_no',$invoice_no,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Type Invoice No.','id'=>'invoice_no']) !!} </td>
                                </tr>
                                <tr>
                                    <td><center><b> Invoice Date </b></center></td>
                                    <td> {!! Form::text('invoice_date',$today,['class' => 'form-control', 'data-required' => true,'placeholder' => 'Select Invoice Date','id'=>'start_date']) !!} </td>
                                </tr>
                                <tr>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                                <tr>
                                    <td> <center><b>State code </b></center></td>
                                    <td> <center>24</center> </td>
                                </tr>
                                <tr>
                                    <td colspan="5"> </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td><b>Client Name</b></td>
                                    <td>{!! Form::select('client_id',[''=>'Select']+$clients,null,['class' => 'form-control client_list', 'data-required' => true]) !!}
                                    </td>
                                    <td>{!! Form::select('currency',$currency,null,['class' => 'form-control', 'data-required' => true,'id'=>'currency']) !!}</td>
                                </tr>
                                <tr>
                                    <td width="10%">
                                        <a class="btn btn-danger" id="delete_tr"><i class="fa fa-close"></i></a>
                                        <a class="btn btn-primary" id="add_tr"><i class="fa fa-plus"></i></a>
                                    </td>
                                    <td width="10%"><b> SR.No. </b></td>
                                    <td width="15%"><b> SAC CODE </b></td>
                                    <td width="40%"><b> Particular </b></td>
                                    <td width="25%"><b> Amount <span id="curr_name">(In Rs.)</span></b></td>
                                </tr>
                                <tr  id="add_new_tr">
                                    <td></td>
                                    <td> 1</td>
                                    <td> {!! Form::text('sac_code[]','9983',['class' => 'form-control']) !!} </td>
                                    <td> {!! Form::text('particular[]',null,['class' => 'form-control', 'data-required' => true,'placeholder'=>'Type...']) !!}</td>
                                    <td align="left">  {!! Form::text('amount[]',0,['class' => 'form-control amounts', 'data-required' => true,'id'=>'amount']) !!}</td>
                                </tr>
                                <tr>
                                    <td colspan="5"> </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="require_gst" class="form-control" checked="true" id="is_gst" value="1"></td>
                                    <td colspan="2" align="center"><b>CGST </b> </td>
                                    <td>{!! Form::text('cgst','9.00%',['class' => 'form-control', 'data-required' => true,'id'=>'net_pay']) !!}</td>
                                    <td align="right">{!! Form::text('cgst_amount',0,['class' => 'form-control', 'data-required' => true,'id'=>'cgst_amount']) !!}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td colspan="2" align="center"><b>SGST </b> </td>
                                    <td>{!! Form::text('sgst',"9.00%",['class' => 'form-control', 'data-required' => true,'id'=>'sgst']) !!}</td>
                                    <td align="left">{!! Form::text('sgst_amount',0,['class' => 'form-control', 'data-required' => true,'id'=>'sgst_amount']) !!}</td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="require_igst" class="form-control" checked="true" id="is_igst" value="1"></td>
                                    <td colspan="2" align="center"><b>IGST </b> </td>
                                    <td>{!! Form::text('igst','18.00%',['class' => 'form-control', 'data-required' => true,'id'=>'igst_id']) !!}</td>
                                    <td align="right">{!! Form::text('igst_amount',0,['class' => 'form-control', 'data-required' => true,'id'=>'igst_amount']) !!}</td>
                                </tr>
                                <tr>
                                    <td align="right" colspan="4" style="font-size: 16px"><b>Total : </b></td>
                                    <td align="left">{!! Form::text('total_amount',0,['class' => 'form-control', 'data-required' => true,'id'=>'total_amount']) !!}</td>
                                </tr>
                                <tr>
                                    <td colspan="3"><b>Total invoice value (in words) </b></td>
                                    <td colspan="2"><span id="invoice_words"></span>
                                        <input type="hidden" name="total_amount_words" id="total_amount_words">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5"> </td>
                                </tr>
                                <tr>
                                    <td colspan="5"><b>Details:</b></td>
                                </tr>
                                <tr>
                                    <td colspan="5">Cheque/DD should be in favour of PHPDOTS TECHNOLOGIES</td>
                                </tr>
                                <tr>
                                    <td colspan="3"><b>Payable at Ahmedabad</b> </td>
                                    <td colspan="2">PAN : <b>AAUFP4850D</b></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Bank Name : <b>Induslnd BANK</b></td>
                                    <td colspan="2">GST Regn. No. :  <b>24AAUFP4850D1Z3</b> </td>
                                </tr>
                                <tr>
                                    <td colspan="3">Bank Account Number : <b>201001635127</b></td>
                                    <td colspan="2">Bank SWIFT CODE : <b>INDBINBBAHA</b> </td>
                                </tr>
                                <tr>
                                    <td colspan="3">Bank IFSC CODE : <b>INDB0000232</b></td>
                                    <td colspan="2">Subject to Ahmedabad Juridiction </td>
                                </tr>
                                <tr>
                                    <td colspan="5">Indian FTP Service Category : Service Category </td>
                                </tr>
                                <tr>
                                    <td colspan="5"><b>Thank you !</b> </td>
                                </tr>                               
                                <tr>
                                    <td colspan="5"><b>PHPDots Technologies</b> </td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <td colspan="2" align="right">
                                        <input type="hidden" id="no" value="1">
                                        <input type="hidden" id="add_no" value="11">
										<input type="hidden" id="total_with_gst" name="total_with_gst">
                                        <input type="hidden" id="total_without_gst" name="total_without_gst">
										<input type="hidden" id="send_id" name="send_id">
                                        <button type="button" class="btn btn-primary" id="save_btn">Generate & Save</button>
                                        <button type="button" class="btn btn-primary" id="sent_btn">Generate & Save & Send</button>
                                    </td>
                                </tr>
                        </table>

                    {!!Form::close()!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    function invoice_cal(){

        $('#AjaxLoaderDiv').fadeIn('slow');    

        var texts = document.getElementsByClassName("amounts");
        var amounts = 0;
        for( var i = 0; i < texts.length; i ++ ) {
          var aa=parseFloat(texts[i].value);
          if(aa=="NaN" || aa==null || aa==""){aa=parseFloat("0");}
            amounts = amounts + aa;
        }  
   
        var gst_total = 0;
        var is_gst = parseInt($('#is_gst').val());
        var is_igst = parseInt($('#is_igst').val());
        if(is_gst == 1)
        {
            var cgst_amount = amounts*9/100;
            var cgst_amount = parseInt(cgst_amount);
            var sgst_amount = amounts*9/100;
            var sgst_amount = parseInt(sgst_amount);
            $('#cgst_amount').val(cgst_amount);
            $('#sgst_amount').val(sgst_amount); 
        var gst_total = cgst_amount + sgst_amount;      
        }
        if(is_gst == 0)
        {
            $('#cgst_amount').val(0);
            $('#sgst_amount').val(0);
        }
        if(is_igst == 1)
        {
            var igst_amount = amounts*18/100;
            var igst_amount = parseInt(igst_amount);
            $('#igst_amount').val(igst_amount);
            var gst_total = gst_total + igst_amount;
        }
        if(is_igst == 0)
        {
            $('#igst_amount').val(0);
        }
		$('#total_without_gst').val(amounts);
        $('#total_with_gst').val(gst_total);
        var total_amount = amounts + gst_total;
        var total_amount = parseInt(total_amount);
        $('#total_amount').val(total_amount);

        //In words
            var num =  total_amount;
            var a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
            var b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];

            if ((num = num.toString()).length > 9) return 'overflow';
                n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
            if (!n) return; var str = '';
        str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
        str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
        str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
        str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
        str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) : '';
        str = str + 'only';
        str = str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});

        $('#invoice_words').html(str);
        $('#total_amount_words').val(str);
        $('#AjaxLoaderDiv').fadeOut('slow');
    }
    $(document).ready(function(){

        $(document).on('change','#is_gst',function(){
            var checkval = parseInt($('#is_gst').val());
            if(checkval == 1){
                $('#is_gst').val(0);
                $("#reload_id").trigger('click');
            }else{
                $('#is_gst').val(1);
                $("#reload_id").trigger('click');
            }
        });

        $(document).on('change','#is_igst',function(){
            var checkval = parseInt($('#is_igst').val());
            if(checkval == 1){
                $('#is_igst').val(0);
                $("#reload_id").trigger('click');
            }else{
                $('#is_igst').val(1);
                $("#reload_id").trigger('click');
            }
        });

        $(document).on('change','#currency',function(){
            var curr_name = $('#currency').val();
            if(curr_name == 'in_usd'){ var curr = '(In USD)';}
            else if(curr_name == 'in_gbp'){ var curr = '(In GBP)';}
            else{var curr = '(In Rs.)';}
            $('#curr_name').html(curr);
        });

        $(document).on('change','.amounts',function(){
            $("#reload_id").trigger('click'); 
        });
		$(document).on('change', '.client_list', function(){
          var client_id = $(".client_list").val();
          var invoice_no = $("#main-frm1 input[name='invoice_no']").val();
		  var invoice_id = null;
            $.ajax({
                type : 'POST',
                url : "{{ route('invoices.client_type') }}",
                data : {
                    "_token": "{{ csrf_token() }}",
                        "id" : client_id,
                        "invoice_no" : invoice_no,
						"invoice_id" : invoice_id,
                },
                success : function (result) {
                    if (result.error) {
                        alert(result.error);
                    }              
                    else { 
                        if (result.invoice_no) {
                            $("#main-frm1 input[name='invoice_no']").val(result.invoice_no);
							$("#main-frm1 textarea[name='to_address']").val(result.address);
                            $("#main-frm1 select[name='currency']").val(result.currency);
                        }      
                    }
                },
                error : function (error) {
                    alert(error);
                }
            });
        });
        $('#add_tr').click(function() {
            var no = parseInt($('#no').val());
            var no = no + 1;
            $('#no').val( no);
            var table = document.getElementById("invoice_table");
            
            var add_no = parseInt($('#add_no').val());
            var add_no = add_no + 1;
            var row = table.insertRow(add_no);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            cell1.innerHTML = "";
            cell2.innerHTML = no;
            cell3.innerHTML = '<input type="text" name="sac_code[]" value="9983" class="form-control" required>';
            cell4.innerHTML = '<input type="text" name="particular[]" class="form-control"  placeholder="Type..." required>';
            cell5.innerHTML = '<input type="text" name="amount[]" value="0" class="form-control amounts" required>';
            $('#add_no').val(add_no);
            $('#no').val(no);
                       
        });
        $('#delete_tr').click(function(){
            $text = 'Are you sure you want to remove?';
            if (confirm($text)==true){
                var del_no = parseInt($('#add_no').val());
                var del_no = del_no;
                document.getElementById("invoice_table").deleteRow(del_no);
                var del_no = del_no -1;
                $('#add_no').val(del_no);
                var no = parseInt($('#no').val());
                var no = no - 1;
                $('#no').val( no);
                $("#reload_id").trigger('click'); 
            }
            return false;
        });
    });
     $(document).ready(function () { 
		 $(".client_list").select2({
                placeholder: "Search Client",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $('#main-frm1').submit(function () {
            
            if ($(this).parsley('isValid'))
            {
				$('#sent_btn').attr('disabled',true);
                $('#save_btn').attr('disabled',true);
				
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
							$('#sent_btn').attr('disabled',false);
                            $('#save_btn').attr('disabled',false);
							
                            $.bootstrapGrowl(result.msg, {type: 'success', delay: 4000});
                            window.location = result.goto;    
                        }
                        else
                        {
							$('#save_btn').attr('disabled',false);
                            $('#sent_btn').attr('disabled',false);
							
                            $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                        }
                    },
                    error: function (error) {
						$('#save_btn').attr('disabled',false);
                        $('#sent_btn').attr('disabled',false);
                        $('#AjaxLoaderDiv').fadeOut('slow');
                        $.bootstrapGrowl("Internal server error !", {type: 'danger', delay: 4000});
                    }
                });
            }            
            return false;
        });
		 $(document).on('click','#save_btn',function(){
            $('#send_id').val(0);
            $('#main-frm1').submit();
        });
        $(document).on('click','#sent_btn',function(){
            $('#send_id').val(1);
            $('#main-frm1').submit();
        });
    });
</script>
  
@endsection