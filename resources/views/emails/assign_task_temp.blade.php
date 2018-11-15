  
<p>
    <b>Task By: </b> {{$firstname}} {{$lastname}}.
</p>
<p>
    <b>Task Title: </b> {{$title}}.
</p>
<p>
    <b>Project: </b> {{$pro_title}}
</p>
@if($status == 0)
<p>
    <b>Status:</b> <span><b style='color:red'>Pending</span></b>
</p>
@else
<p>
    <b>Status:</b> <span><b style='color:blue'>Done</span></b>
</p>
@endif

@if($priority == 0)
 
<p>
    <b>Priority:</b> <span><b style='color:red'>High</span></b>
</p>
@elseif($priority == 1)
<p>
    <b>Priority:</b> <span><b style='color:blue'>Low</span></b>
</p>
@elseif($priority == 2)
<p>
    <b>Priority:</b> <span><b style='color:gold'>Medium</span></b>
</p> 
@endif

<p>
    <a class="btn btn-primary" href='{{$link}}'>click here</a>
</p>
 
<p><span>Thanking You,</span></p>
 