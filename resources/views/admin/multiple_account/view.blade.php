@extends('admin.layouts.bopal_app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
            
            <!-- @include($moduleViewName.".search") -->          

            <div class="clearfix"></div>    
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>{{ $page_title }}    
                    </div>
                    @if($btnAdd)
                        <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $add_url }}">Add Loan</a>
                    @endif
                                       

                </div>
                <div class="portlet-body">                    
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="25%">Member</th>
                                <th width="15%">Balance</th>
                                <th width="15%">Loan Balance</th>
                                <th width="15%">Loan Amount</th>
                                <th width="15%">Ledger Amount</th>
                                <th width="5%" data-orderable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
          
@endsection

@section('styles')
  
@endsection


