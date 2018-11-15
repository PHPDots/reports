  

<p>
    <b>Title: </b> {{$title}}.
</p> 
<p>
    <b>Comment: </b> {!! $comments !!}
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

<p>
    <a class="btn btn-primary" href='{{$link}}'>click here</a>
</p>
 
<p><span>Thanking You,</span></p>
 