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
                <div class="col-md-6">
                   <label class="control-label">Select Member: </label><span class="required">*</span>

                    {!! Form::select('bb_account_id', [''=>'Search Member'] + $members, Request::get("bb_account_id"), ['class' => 'form-control','bb_account_id'=>'bb_account_id']) !!}

                </div>
                    <input type="hidden" value="{{\Request::get("bb_account_id") }}" name="bb_account_id">
                <div class="row" align="center">                     
                    <input type="submit" class="btn blue mTop25" value="Search"/>
                    &nbsp;
                    <a href="{{ $list_url }}" class="btn red mTop25">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>