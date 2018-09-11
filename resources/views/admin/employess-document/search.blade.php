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
                    <label class="control-label">IDs</label>
                    <input type="text" value="{{ \Request::get("search_id") }}" class="form-control" name="search_id" placeholder="Search IDs" />
                </div>
                <div class="col-md-4">
                    <label class="control-label">Employee Name</label>
                    {!! Form::select('search_emp_nm', [''=>'Search Employee Name'] + $users, Request::get("search_emp_nm"), ['class' => 'form-control','id'=>'user_id']) !!}
                </div>
            </div>  
            <div class="clearfix">&nbsp;</div>   
            <div class="row">
                <div class="col-md-4">
                    <label class="control-label">Document Type</label>
                    {!! Form::select('search_type', [''=>'Search Document Type'] + $document, Request::get("search_type"), ['class' => 'form-control','id'=>'document_id']) !!}
                </div>

                <div class="row" align="center">                     
                    <input type="submit" class="btn blue mTop25" value="Search"/>
                    &nbsp;
                    <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>
                </div>                                   
            </div>
        </form>
    </div>    
</div>