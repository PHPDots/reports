<div class="btn-group">
@if(isset($isEdit) && $isEdit)
<a href="{{ route($currentRoute.'.edit',['id' => $row->id]) }}" class="btn btn-xs btn-primary" title="edit">
    <i class="fa fa-edit"></i>
</a>         
@endif

@if(isset($isEditHistory) && $isEditHistory)
<a href="{{ route($currentRoute.'.edit',['id' => $row->id]) }}" class="btn btn-sm btn-primary" title="edit history">
    <i class="fa fa-history"></i>
</a>         
@endif

@if(isset($isDelete) && $isDelete)
<a data-id="{{ $row->id }}" href="{{ route($currentRoute.'.destroy',['id' => $row->id]) }}" class="btn btn-xs btn-danger btn-delete-record" title="delete">
    <i class="fa fa-trash-o"></i>
</a>          
@endif
@if(isset($isAssignDelete) && $isAssignDelete)
<a data-id="{{ $row->id }}" href="{{ route($currentRoute.'.destroy',['id' => $row->id]) }}" class="btn btn-sm btn-danger btn-delete-record" title="delete">
    <i class="fa fa-trash-o"></i>
</a>          
@endif

@if(isset($assign_task_done) && $assign_task_done)
	@if($row->status == 0)
		<a class="btn btn-sm btn-success accepted" title="Change Status To Done" href="{{ route($currentRoute.'.index',['changeID' => $row->id, 'changeStatus' => 1]) }}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	
	@endif
@endif

@if(isset($isAccept) && $isAccept)
@if($row->status == 0)
<a data="{{ $row->id }}" class="btn btn-xs btn-success accepted" title="Accept">
    <i class="fa fa-check"></i>
</a>          
@endif
@endif

@if(isset($isReject) && $isReject)
@if($row->status == 0)
<a data="{{ $row->id }}" class="btn btn-xs btn-warning rejected" title="Reject" id="reject_action">
    <i class="fa fa-times"></i>
</a>          
@endif
@endif
@if(isset($isView) && $isView)
<a data-id="{{ $row->id }}" class="btn btn-xs btn-success" onclick="openView({{$row->id}})" title="view">
    <i class="fa fa-eye"></i>
</a>
@endif
	
@if(isset($isPDF) && $isPDF)
<a href="{{ url('salary_slip/download?slip_id='.$row->id) }}" class="btn btn-xs btn-warning" title="Download PDF">
    <i class="fa fa-arrow-down" aria-hidden="true"></i>
</a>
@endif

@if(isset($inPDF) && $inPDF)
<a href="{{ url('invoices/download?invoice_id='.$row->id) }}" class="btn btn-xs btn-warning" title="Download PDF">
    <i class="fa fa-arrow-down" aria-hidden="true"></i>
</a>          
@endif
	
@if(isset($isActive) && $isActive)
@if($row->status == 0)
<a data="{{ $row->id }}" class="btn btn-xs btn-success accepted" title="Active">
    <i class="fa fa-check"></i>
</a>
@endif
@endif 

@if(isset($user_status_link) && $user_status_link == 1)
	@if($row->status == 1)
		<a class="btn btn-xs btn-success accepted" title="Change Status To Inactive" href="{{ url('users?changeStatus=0&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@else
		<a class="btn btn-xs btn-danger accepted" title="Change Status To Active" href="{{ url('users?changeStatus=1&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@endif	   
@endif
@if(isset($payment) && $payment == 1)
	@if($row->payment == 1)
		<a class="btn btn-xs btn-success paid_payment" title="Change Payment Status" onclick="openPaymentModel({{ $row->id }})">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@else
		<a class="btn btn-xs btn-danger paid_payment" title="Change Payment Status" onclick="openPaymentModel({{ $row->id }})">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@endif	   
@endif
	@if(isset($isShowMem) && $isShowMem)
<a class="btn btn-xs btn-warning" target="_blank" href='{{ asset("/members-family?search_member=$row->id") }}' title="Show Family Members">
	<i class="fa fa-user"></i>
</a>
@endif
@if(isset($member_status_link) && $member_status_link == 1)
	@if($row->status == 1)
		<a class="btn btn-xs btn-success accepted" title="Change Status To Inactive" href="{{ url('members?changeStatus=0&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@else
		<a class="btn btn-xs btn-danger accepted" title="Change Status To Active" href="{{ url('members?changeStatus=1&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@endif
@endif

@if(isset($viewTask) && $viewTask)
<?php
$url = url('/tasks?').'search_task_date='.date('Y-m').'&search_status=all&search_user='.$row->id;
?>
<a href="{{ $url }}" class="btn btn-xs btn-warning" title="View This Month Tasks" target="_blank">
    <i class="fa fa-eye" aria-hidden="true"></i>
</a>          
@endif

@if(isset($viewInvoice) && $viewInvoice)
<?php
$url = url('/invoices?').'search_client_name='.$row->id;
?>
<a href="{{ $url }}" class="btn btn-xs btn-warning" title="View Invoices" target="_blank">
    <i class="fa fa-eye" aria-hidden="true"></i>
</a>          
@endif
@if(isset($viewExpe) && $viewExpe)
<?php
$url = url('/invoice-expense?').'search_invoice_id='.$row->id;
?>
<a href="{{ $url }}" class="btn btn-xs btn-primary purple" title="View Invoice Expense Log" target="_blank">
    <i class="fa fa-eye" aria-hidden="true"></i>
</a>          
@endif

@if(isset($isMapStatus) && $isMapStatus)
	@if($row->invoice_status == 1)
		<a class="btn btn-xs btn-warning" title="Change Status To Unmap Invoice" href="{{ url('fix-tasks?changeStatus=0&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@else
		<a class="btn btn-xs btn-success" title="Change Status To Map Invoice" href="{{ url('fix-tasks?changeStatus=1&changeID='.$row->id)}}" onclick="return confirm('Are you sure ?');">
		    <i class="fa fa-check-circle-o"></i>
		</a>
	@endif
@endif
@if(isset($copyInvoice) && $copyInvoice)
	<a class="btn btn-xs btn-default yellow" title="Click To Copy & Create" href="{{ url('invoices/create?copytocreate='.$row->id) }}">
	    <i class="fa fa-clipboard" aria-hidden="true"></i>
	</a>
@endif
</div>

@if(isset($assignTaskView) && $assignTaskView)
<a data-id="{{ $row->id }}" class="btn btn-xs btn-success" onclick="openView({{$row->id}})" title="view Assign Task">
    <i class="fa fa-eye"></i>
</a>
@endif


