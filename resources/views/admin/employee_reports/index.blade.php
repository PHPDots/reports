@extends('admin.layouts.app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class=""> 
            @include($moduleViewName.".search")
            <div class="clearfix"></div>
			<a class="btn btn-default pull-right">
              Total Hours: <span id="overall-hours"> 0 </span>
            </a>    
            <div class="clearfix">&nbsp;</div>
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>{{ $page_title }}    
                    </div> 
                </div>
                <div class="portlet-body">                    
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="2%">ID</th>
                               <th>UserName</th>
                               <th width="20%">Project</th> 
                               <th width="10%">Hours</th> 
                               <th width="15%">Month</th> 
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
        $("#user_id").select2({
                placeholder: "Search User Type",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });
        $("#project_id").select2({
                placeholder: "Search Project",
                allowClear: true,
                minimumInputLength: 2,
                width: null
        });

        $("#search-frm").submit(function(){
            oTableCustom.draw();
            return false;
        });

        $.fn.dataTableExt.sErrMode = 'throw';

        var oTableCustom = $('#server-side-datatables').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            lengthMenu:
              [
                [100,200,300,400,500],
                [100,200,300,400,500]
              ],  
            ajax: {
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function ( data )
                {
                    data.search_user = $("#search-frm select[name='search_user']").val();
                    data.search_project = $("#search-frm select[name='search_project']").val();
                    data.search_task_date = $("#search-frm select[name='search_task_date']").val();
                }, 
            dataSrc: function(response){
                    $("#overall-hours").html(response.hours);
                    $("#is_total").val(response.hours);
                    return response.data;
                }
            },
            "order": [[ '0', "desc" ]],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'project_name', name: '{{ TBL_PROJECT }}.title' },
                { data: 'hours', name: 'total_time' },
                { data: 'task_date', name: 'task_date' }
            ],
        });
    });
    </script>
@endsection

