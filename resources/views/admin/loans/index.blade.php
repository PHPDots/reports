@extends('admin.layouts.bopal_app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">
        <div class="">
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
                                <th width="3%">ID</th>
                                <th width="30%">Member</th>
                                <th width="10%">Loan Amount</th>
                                <th width="20%">Created</th>
                                <th width="10%">Status</th>
                                <th width="5%">Action</th>
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
                    //data.search_loan_amount = $("#search-frm input[name='search_loan_amount']").val();
                    //data.search_balance = $("#search-frm input[name='search_balance']").val();
                }
            },
            lengthMenu:
              [
                [25,50,100,200],
                [25,50,100,200]
              ],
            "order": [[ 0, 'asc']],  
            columns: [
                { data: 'id', name: 'id' },
                { data: 'firstname', name: 'firstname' },
                { data: 'loan_amount', name: 'loan_amount' },
                { data: 'created_at', name: 'created_at' },
                { data: 'status', name: 'status' },
                { data: 'action', orderable: false, searchable: false}
            ]
        });
    });
    </script>
@endsection
