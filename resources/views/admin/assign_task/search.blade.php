
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-search"></i>Advance Search 
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand"> </a>
        </div>                    
    </div>
    <div class="portlet-body" style="display: none">  
        <form id="search-frm">
            <div class="row"> 
                <div class="col-md-4">
                    <label class="control-label">User Name</label>
                    {!! Form::select('search_user', [''=>'Search User'] + $users, Request::get("search_user"), ['class' => 'form-control','id'=>'user_id']) !!}
                </div>
                <div class="col-md-4">
                    <label class="control-label">Task Title</label>
                    <input type="text" value="{{ \Request::get("search_title") }}" class="form-control" name="search_title" />
                </div>
                <div class="col-md-4">
                    <label class="control-label">Priority</label>
                    <select name="search_priority" class="form-control">
                        <option value="all" {!! \Request::get("search_priority") == "all" ? 'selected="selected"':'' !!}>All</option>
                        <option value="0" {!! \Request::get("search_priority") == "0" ? 'selected="selected"':'' !!}>High</option>
                        <option value="1" {!! \Request::get("search_priority") == "1" ? 'selected="selected"':'' !!}>Low</option>
                        <option value="2" {!! \Request::get("search_priority") == "2" ? 'selected="selected"':'' !!}>Medium</option>
                    </select>
                </div>
            </div>
            <div class="clearfix">&nbsp;</div> 
            <div class="row"> 
                <div class="col-md-4">
                    <label class="control-label">Status</label>
                    <select name="search_status" class="form-control">
                        <option value="0" {!! \Request::get("search_status") == "1" ? 'selected="selected"':'' !!}>Pending</option>
                        <option value="1" {!! \Request::get("search_status") == "0" ? 'selected="selected"':'' !!}>Done</option>
                        <option value="all" {!! \Request::get("search_status") == "all" ? 'selected="selected"':'' !!}>All</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="row" align="center"> 
                        <input type="submit" class="btn blue mTop25" value="Search"/>
                        <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>
                    </div>
                </div>
            </div>
            &nbsp;               
            </div>
        </form>
    </div>
</div>