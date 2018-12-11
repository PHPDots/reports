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
                  
                    @if($btnAdd)
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $add_url }}" style="margin-left: 10px;">Add New</a>
                        <div class="">
                            <button class="btn btn-primary pull-right btn-sm mTop5 unmapall">Unmap All</button>
                            <button class="btn btn-primary pull-right btn-sm mTop5 mapall">Map All</button>
                        </div>
                    @endif

                </div>
                <div class="portlet-body">
                <form id="check-form" method="post" action="{{ route('fix-tasks.check-status') }}">
                    {{ csrf_field() }}
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                               <th width="1%">
                                <div class="form-group form-md-checkboxes" style="padding-bottom: 10px; padding-left: 7px;">
                                        <div class="md-checkbox-inline">
                                            <div class="md-checkbox">
                                                <input type="checkbox" id="allcheck_id" class="md-check" name="allcheck">
                                                <label for="allcheck_id">
                                                    <span></span>
                                                    <span class="check" style="z-index: 1;"></span>
                                                    <span class="box" ></span>
                                                    </label>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                               <th width="5%">ID</th>
                               <th width="10%">Client Name</th>
                               <th width="15%">Tasks</th>
                               <th width="10%"># Date
                                            <br/> # Created At</th>
                               <th width="5%">Assigned by</th>
                               <th width="5%"># Hours
                                <br/># Fix 
                                <br/># Rate</th>
                               <th width="5%">Total</th>
                               <th width="5%">Status</th>
                               <th width="10%" data-orderable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                <input type="hidden" name="status_type" id="status_type_id">
                <input type="submit" name="submit" style="display: none;" id="check_submit">
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
        
        $("#allcheck_id").click(function() {
            if($(this).is(":checked")) {
                $( ".sub-check" ).prop( "checked", true );
            } else {
                $( ".sub-check" ).prop( "checked", false );
            }
        });
        $('.unmapall').on('click',function(){
            var text = 'Are you sure you want to unmap all tasks?';
            if(confirm(text))
            {
                $('#status_type_id').val(0);
                $('#check-form').submit();
            }
            return false;
        });
        $('.mapall').on('click',function(){
            var text = 'Are you sure you want to map all tasks?';
            if(confirm(text))
            {
                $('#status_type_id').val(1);
                $('#check-form').submit();
            }
            return false;
        });

        $('#check-form').submit(function () {
            $('#AjaxLoaderDiv').fadeIn('slow');
            $.ajax({
                type: "POST",
                url: "{{route('fix-tasks.check-status')}}",
                data: new FormData(this),
                contentType: false,
                processData: false,
                enctype: 'multipart/form-data',
                success: function (result)
                {
                    if (result.status == 1)
                    {
                        $.bootstrapGrowl(result.msg, {type: 'success', delay: 4000});
                        window.location = result.goto;    
                        $('#AjaxLoaderDiv').fadeOut('slow');
                    }
                    else
                    {
                        $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                        $('#AjaxLoaderDiv').fadeOut('slow');
                    }
                },
                error: function (error) {
                    $.bootstrapGrowl("Internal server error !", {type: 'danger', delay: 4000});
                    $('#AjaxLoaderDiv').fadeOut('slow');
                }
            });
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
            ajax: {
                "url": "{!! route($moduleRouteText.'.data') !!}",
                "data": function ( data ) 
                {
                    data.search_start_date = $("#search-frm input[name='search_start_date']").val();
                    data.search_end_date = $("#search-frm input[name='search_end_date']").val();
                    data.search_client = $("#search-frm select[name='search_client']").val();
                    data.search_status = $("#search-frm select[name='search_status']").val();
                }
            },
            lengthMenu:
              [
                [25,50,100,150,200],
                [25,50,100,150,200]
              ],
            "order": [[ '1', "desc" ]],
            columns: [
                { data: 'check_clm', orderable: false, searchable: false},
                { data: 'id', name: 'id' },
                { data: 'client', name: '{{ TBL_CLIENT }}.name' },
                { data: 'title', name: 'title' },
                { data: 'task_date', name: 'task_date' },
                { data: 'assigned_by', name: 'assigned_by' },
                { data: 'hour', name: 'hour' },
                { data: 'total', name: 'fix' },
                { data: 'invoice_status', name: 'invoice_status' },
                { data: 'action', orderable: false, searchable: false},
            ]
        });
    });
    </script>
@endsection
