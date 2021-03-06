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
                    <label class="control-label">Created Date Range</label>
                    <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                        <input type="text" class="form-control" value="{{ \Request::get("search_start_date") }}" name="search_start_date" id="start_date" placeholder="Start Date">
                        <span class="input-group-addon"> To </span>
                        <input type="text" class="form-control" value="{{ \Request::get("search_end_date") }}" name="search_end_date" id="end_date" placeholder="End Date"> 
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="control-label">ID(s)</label>
                    <input type="text" value="{{ \Request::get("search_id") }}" class="form-control" name="search_id" placeholder="Search Ids" />
                </div>
                <div class="col-md-4">
                    <label class="control-label">Holiday Title</label>
                    <input type="text" value="{{ \Request::get("search_title") }}" class="form-control" name="search_title" placeholder="Search Holiday Title" />
                </div>
            </div>
                <div class="clearfix">&nbsp;</div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label">Holiday Date Range</label>
                        <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                            <input type="text" class="form-control" value="{{ \Request::get("search_start_leave") }}" name="search_start_leave" id="search_start_leave" placeholder="From Date">
                            <span class="input-group-addon"> To </span>
                            <input type="text" class="form-control" value="{{ \Request::get("search_end_leave") }}" name="search_end_leave" id="search_end_leave" placeholder="To Date"> 
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label">Status</label>
                        <select name="search_status" class="form-control">
                            <option value="all" {!! \Request::get("search_status") == "all" ? 'selected="selected"':'' !!}>Search Status</option>
                            <option value="0" {!! \Request::get("search_status") == "0" ? 'selected="selected"':'' !!}>In Active</option>
                            <option value="1" {!! \Request::get("search_status") == "1" ? 'selected="selected"':'' !!}>Active</option>
                        </select>
                    </div>
                    <div class="col-md-4"><center>
                        <input type="hidden" name="isDownload" id="is_download"/>
                        <input type="submit" class="btn blue mTop25" value="Search"/>
                        &nbsp;
                        <a href="{{ $list_url }}" class="btn red mTop25">Reset</a></center>
                    </div>
                </div>
            <div class="clearfix">&nbsp;</div>  
            </div>                
        </form>
    </div>    
</div>      