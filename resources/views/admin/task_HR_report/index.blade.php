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
                               <th width="20%">User Name</th>
                               <th width="20%">Task Date</th>
                               <th width="10%">Hours</th>
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
        $("#user_id").select2({
                placeholder: "Search User Name",
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
            ajax: {
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function ( data ) 
                {   
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_month = $("#search-frm select[name='search_month']").val();
                    data.search_year = $("#search-frm select[name='search_year']").val();
                    data.search_user = $("#search-frm select[name='search_user']").val();
                }
            },
            lengthMenu:
              [
                [100,200,300,400,500],
                [100,200,300,400,500]
              ],
            "order": [[ '1', "desc" ]],
            columns: [
                { data: 'user_name', name: '{{ TBL_USERS }}.name' },
                { data: 'task_date', name: 'task_date' },
                { data: 'hours', orderable: false, searchable: false}             
            ]
        });
    });
    </script>
@endsection
