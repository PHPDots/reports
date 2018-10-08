@extends('admin.layouts.bopal_app')

@section('content')

<!-- BEGIN PAGE CONTENT BODY -->
<div class="page-content">
    <div class="container">

        <div class="">
            

            <div class="clearfix"></div>    
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>{{ $page_title }}    
                    </div>
                    <a class="btn btn-default pull-right btn-sm mTop5" href="{{ $list_url }}">Back</a>
                                       

                </div>
                <div class="portlet-body">                    
                    <table class="table table-bordered table-striped table-condensed flip-content" id="server-side-datatables">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="25%">Member</th>
                                <th width="15%">Transaction Amount</th>
                                <th width="15%">Balance</th>
                                <th width="15%">Received</th>
                                <th width="15%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loanList as $loanData)
                                <tr>
                                    <td>{{ $loanData->id }}</td>
                                    <td>{{ $member->firstname }} {{ $member->middlename }} {{ $member->lastname }}</td>
                                    <td>{{ $loanData->transaction_amount }}</td>
                                    <td><?=($loanData->balance>0?$loanData->balance:'-')?></td>
                                    <td><?= (!empty($loanData->updated_at)?date("j M, Y h:i:s A",strtotime($loanData->updated_at)):'-')?> </td>
                                    <td><?php if ($loanData->status == 0) {
                                            echo "<a class='btn btn-danger btn-xs'>Pending</a>";
                                        }
                                        if ($loanData->status == 1) {
                                            echo "<a class='btn btn-warning btn-xs'>Partial</a>";
                                        }
                                        if ($loanData->status == 2) {
                                                echo "<a class='btn btn-success btn-xs'>Received</a>"; 
                                        }?>
                                    </td>
                                </tr>
                            @endforeach
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


