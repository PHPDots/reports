@extends('admin.layouts.app')


@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
            
            @include($moduleViewName.".usersearch")

            <div class="clearfix"></div>    
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
                               <th>Title</th>                           
                               <th width="15%">Priority</th>                           
                               <th width="20%">Due Date</th>                           
                               <th width="15%">Status</th>                           
                               <th width="15%">Created At</th>                           
                               <th width="10%" data-orderable="false">Action</th>
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
<div class="modal fade bs-modal-lg" id="assign_task_view" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Assign Task</h4>
        </div>
        <div class="modal-body">
          <table class="table" id="detail_table">
            
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
</div>           
@endsection

@section('styles')
  
@endsection

@section('scripts')
<link rel="stylesheet" href="{{ asset('/thirdparty/fancybox/jquery.fancybox.css') }}" type="text/css" media="screen" />
<script type="text/javascript" src="{{ asset('/thirdparty/fancybox/jquery.fancybox.js') }}"></script>
    <script type="text/javascript">
function openView($id){
      jQuery('#assign_task_view').modal();     
      $('#AjaxLoaderDiv').fadeIn('slow');
      
      var task_url = "{{url('/assign-tasks/') }}" + '/'+ $id; 
       
      $.ajax({
          type: "GET",
          url: task_url,
          data: 
          {
              id: $id
          },
          success: function (result)
          {
              $("#detail_table").html(result);
              $('#AjaxLoaderDiv').fadeOut('slow');
          },
          error: function (error) 
          {
              $('#AjaxLoaderDiv').fadeOut('slow');
          }
      });

} 

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
                "url": "{!! route('assign.task.user.data') !!}",
                "data": function ( data ) 
                { 
                  data.search_title = $("#search-frm input[name='search_title']").val();
                  data.search_priority = $("#search-frm select[name='search_priority']").val();
                  data.search_status = $("#search-frm select[name='search_status']").val(); 
                }
            },            
            "order": [[ '0', "desc" ]],    
            columns: [
                { data: 'id', name: 'id' },          
                { data: 'title', name: 'title' },                                             
                { data: 'priority', name: 'priority' },          
                { data: 'due_date', name: 'due_date' },          
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },      
                { data: 'action', orderable: false, searchable: false}             
            ]
        });        
    });
    </script>
@endsection


