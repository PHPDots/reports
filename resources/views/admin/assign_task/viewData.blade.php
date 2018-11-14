 <div class="portlet blue-hoki box">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-user"></i>{{ $user_name->name}}</div>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row static-info">
			<div class="col-md-5 name"> Task Title: </div>
			<div class="col-md-7 value"> {{ $view->title }} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Priority: </div>
			<div class="col-md-7 value"> @if($view->priority == 0) <div class="btn btn-xs btn-primary"> High</div> @elseif($row->priority == 2) <div class="btn btn-xs btn-warning">Medium</div> @else <div class="btn btn-xs btn-success">Low</div> @endif
			</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Due Date: </div>
			<div class="col-md-7 value"> {{ $view->due_date }} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Status: </div> 
			<div class="col-md-7 value"> @if($view->status == 0) <div class="btn btn-xs btn-danger"> Pending</div> @else <div class="btn btn-xs btn-success">Done</div> @endif 
			</div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Description: </div>
			<div class="col-md-7 value"> {{ $view->description }} </div>
		</div>
		<div class="row static-info">
			<div class="col-md-5 name"> Assigned: </div>
			<div class="col-md-7 value"> {{ $view->description }} </div>
		</div>  
	</div>
</div>
<!-- <hr>
 <div class="portlet blue-hoki box">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-history"></i>History
		</div>
	</div> 
</div> -->
