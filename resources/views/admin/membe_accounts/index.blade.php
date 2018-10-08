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
                    @if($btnAdd)
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $add_url }}">New Account</a>
                    @endif
                </div>
                <div class="portlet-body">                    
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">Member</th>
                                <th width="12%">Balance</th>
                                <th width="12%">Loan Balance</th>
                                <th width="12%">Loan Amount</th>
                                <th width="15%">Status</th>
                                <th width="20%">Created</th>
                                <?php
                                $auth_user = Auth::guard("admins")->check();
                                if($auth_user){ echo '<th width="3%">Action</th>';}?>
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
                { data: 'balance', name: 'balance' },
                { data: 'loan_balance', name: 'loan_balance' },
                { data: 'loan_amount', name: 'loan_amount' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' }
                <?php $auth_user = Auth::guard("admins")->check();
                    if($auth_user){?>
                    ,{ data: 'action', orderable: false, searchable: false}
                <?php } ?>
            ]
        });
    });
    </script>
@endsection
