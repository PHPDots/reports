<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-search"></i>Advance Search 
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse"> </a>
        </div>                    
    </div>
    <div class="portlet-body">  
        <form id="search-frm">
            <div class="row">

                <?php 

                    $searchName = "";
                    if(isset($request->search_name) && $request->search_name !=""){
                     $searchName =  $request->search_name;
                    }

                    $search_start_date = "";

                    if(isset($request->search_start_date) && $request->search_start_date !=""){
                        $search_start_date =  $request->search_start_date;
                    }else{
                        $search_start_date = (date('Y') - 1).'-04-01';
                    }

                    $search_end_date = "";

                    if(isset($request->search_end_date) && $request->search_end_date !=""){
                        $search_end_date =  $request->search_end_date;
                    }else{
                        $search_end_date = (date('Y')).'-03-31';
                    }
                 ?>
                @if(!empty($users))
                <div class="col-md-4">
                    <label class="control-label">User Name</label>
                    {!! Form::select('search_name', [''=>'Search User'] + $users, $searchName, ['class' => 'form-control','id'=>'user_id']) !!}
                </div>
                @endif
                 <div class="col-md-4">
                    <label class="control-label">Financial Year</label>
                    <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                        <input type="text" class="form-control" value="{{$search_start_date}}" name="search_start_date" id="start_date" placeholder="Start Date">
                        <span class="input-group-addon"> To </span>
                        <input type="text" class="form-control" value="{{$search_end_date}}" name="search_end_date" id="end_date" placeholder="End Date"> 
                    </div>
                </div> 
                &nbsp;
                <div class="row" align="center">                     
                    <input type="submit" class="btn blue mTop25" value="Search"/>
                    &nbsp;
                    <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>
                </div>
            </div>                
        </form>
    </div>    
</div>      