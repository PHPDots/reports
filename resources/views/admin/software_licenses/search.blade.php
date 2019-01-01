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
                    <label class="control-label">Title</label>
                    <input type="text" value="{{ \Request::get("search_title") }}" class="form-control" name="search_title" />
                </div>
                <div class="col-md-4">
                    <label class="control-label">Payment Type</label>
                    <select name="search_type" class="form-control">
                        <option value="all" {!! \Request::get("search_type") == "all" ? 'selected="selected"':'' !!}>All</option>
                        <option value="CC" {!! \Request::get("search_type") == "CC" ? 'selected="selected"':'' !!}>CC</option>
                        <option value="net banking" {!! \Request::get("search_type") == "net banking" ? 'selected="selected"':'' !!}>Net Banking</option>                        
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="control-label">License key</label>
                    <input type="text" value="{{ \Request::get("search_license") }}" class="form-control" name="search_license" />
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