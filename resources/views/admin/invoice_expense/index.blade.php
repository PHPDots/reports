@extends('admin.layouts.app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">

            @include($moduleViewName.".search")

            <div class="clearfix"></div>
            <a class="btn btn-default pull-right">
              Total Amount: <span id="overall-amount"> 0 </span>
            </a>
            <div class="clearfix"></div>
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>{{ $page_title }}    
                    </div>
                </div>
                <div class="portlet-body">                    
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="5%">ID</th>                                    
                               <th width="20%">Invoice No</th>
                               <th width="10%">Amount</th>
							   <th width="10%">Partial Amount</th>
                               <th width="10%">Payment Date</th>
                               <th width="10%">Status</th>
                               <th width="15%">Created At</th>
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
</div>            
@endsection

@section('styles')
  
@endsection

@section('scripts')
    <script type="text/javascript">
    
    $(document).ready(function(){

        $("#search-frm").submit(function(){
            oTableCustom.draw();
            return false;
        });
        $.fn.dataTableExt.sErrMode = 'throw';

        var oTableCustom = $('#server-side-datatables').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function ( data ) 
                {
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_invoice_id = $("#search-frm input[name='search_invoice_id']").val();
                    data.search_payment_status = $("#search-frm select[name='search_payment_status']").val();
                },
                dataSrc: function(response){
                    $("#overall-amount").html(response.amounts);
                    return response.data;
                }
            },   
            "order": [[ '0', "desc" ]],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'invoice_no', name: '{{ TBL_INVOICE}}.invoice_no' },
                { data: 'amount', name: 'amount' },
				{ data: 'partial_amount', name: 'partial_amount' },
                { data: 'payment_date', name: 'payment_date' },
                { data: 'payment_status', name: 'payment_status' },
                { data: 'created_at', name: 'created_at' }
            ]
        });
    });
    </script>
@endsection
