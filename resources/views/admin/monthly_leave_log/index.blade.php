@extends('admin.layouts.app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">

            @include($moduleViewName.".search")

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
                               <th width="20%">User Name</th>
                               <th width="10%">Month</th>
                               <th width="10%">Year</th>
                               <th width="5%">Leave Taken</th>
                               <th width="5%">Balance Leave</th>
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
        $("#user_id").select2({
                placeholder: "Search User Name",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#month_id").select2({
            placeholder: "Search Month",
            allowClear: true,
            minimumInputLength: 2,
            width: null
        });
        $("#year_id").select2({
            placeholder: "Search Year",
            allowClear: true,
            minimumInputLength: 2,
            width: null
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
                    data.search_user = $("#search-frm select[name='search_user']").val();
                    data.search_month = $("#search-frm select[name='search_month']").val();
                    data.search_year = $("#search-frm select[name='search_year']").val();
                }
            },
            lengthMenu:
              [
                [100,200,300,400,500],
                [100,200,300,400,500]
              ],          
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },
                { data: 'user_name', name: '{{ TBL_USERS }}.name' },
                { data: 'month', name: 'month' },
                { data: 'year', name: 'year' },
                { data: 'leave_taken', name: 'leave_taken' },
                { data: 'balance_leave', name: 'balance_leave' },
                { data: 'created_at', name: 'created_at' }
            ]
        });
    });
    </script>
@endsection
