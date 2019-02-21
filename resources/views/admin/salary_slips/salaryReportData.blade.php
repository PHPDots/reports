<div class="col-md-12">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-file"></i>
                Report of {{ $username }}
            </div>
            @if(count($reportData))
                <a class="btn btn-default pull-right btn-sm mTop5 downloadXls" href="javascript:void(0);">Download</a>
            @endif
        </div>
        <div class="portlet-body">
                @if(count($reportData))
                <table class="table table-bordered table-striped table-condensed flip-content">
                    <thead>
                        <th>#</th>
                        <th>Basic Salary</th>
                        <th>HRA</th>
                        <th>Conveyance Allowance</th>
                        <th>Telephone Allowance</th>
                        <th>Medical Allowance</th>
                        <th>Uniform Allowance</th>
                        <th>Special Allowance</th>
                        <th>Bonus</th>
                        <th>Arrear Salary</th>
                        <th>Advance Given</th>
                        <th>Leave Encashment</th>
                        <th>Advance</th>
                        <th>Leave Deduction</th>
                        <th>Other Deduction</th>
                        <th>TDS</th>
                    </thead>
                    <tbody>
                        <?php $basic_salary=$hra=$conveyance_allowance=$telephone_allowance=$medical_allowance=$uniform_allowance=$special_allowance=$bonus=$arrear_salary=$advance_given=$leave_encashment=$advance=$leave_deduction=$other_deduction=$tds = 0;?>
                        @foreach($reportData as $id => $row)
                            <tr>
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $row['basic_salary'] }} <?php $basic_salary += $row['basic_salary']; ?></td>
                                <td>{{ $row['hra'] }} <?php $hra += $row['hra']; ?></td>
                                <td>{{ $row['conveyance_allowance'] }} <?php $conveyance_allowance += $row['conveyance_allowance']; ?></td>
                                <td>{{ $row['telephone_allowance'] }} <?php $telephone_allowance += $row['telephone_allowance']; ?></td>
                                <td>{{ $row['medical_allowance'] }} <?php $medical_allowance += $row['medical_allowance']; ?></td>
                                <td>{{ $row['uniform_allowance'] }} <?php $uniform_allowance += $row['uniform_allowance']; ?></td>
                                <td>{{ $row['special_allowance'] }} <?php $special_allowance += $row['special_allowance']; ?></td>
                                <td>{{ $row['bonus'] }} <?php $bonus += $row['bonus']; ?></td>
                                <td>{{ $row['arrear_salary'] }} <?php $arrear_salary += $row['arrear_salary']; ?></td>
                                <td>{{ $row['advance_given'] }} <?php $advance_given += $row['advance_given']; ?></td>
                                <td>{{ $row['leave_encashment'] }} <?php $leave_encashment += $row['leave_encashment']; ?></td>
                                <td>{{ $row['advance'] }} <?php $advance += $row['advance']; ?></td>
                                <td>{{ $row['leave_deduction'] }} <?php $leave_deduction += $row['leave_deduction']; ?></td>
                                <td>{{ $row['other_deduction'] }} <?php $other_deduction += $row['other_deduction']; ?></td>
                                <td>{{ $row['tds'] }} <?php $tds += $row['tds']; ?></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td><b>Total</b></td>
                            <td><b>{{ $basic_salary }} <b></td>
                            <td><b>{{ $hra }} <b></td>
                            <td><b>{{ $conveyance_allowance }} <b></td>
                            <td><b>{{ $telephone_allowance }} <b></td>
                            <td><b>{{ $medical_allowance }} <b></td>
                            <td><b>{{ $uniform_allowance }} <b></td>
                            <td><b>{{ $special_allowance }} <b></td>
                            <td><b>{{ $bonus }} <b></td>
                            <td><b>{{ $arrear_salary }} <b></td>
                            <td><b>{{ $advance_given }} <b></td>
                            <td><b>{{ $leave_encashment }} <b></td>
                            <td><b>{{ $advance }} <b></td>
                            <td><b>{{ $leave_deduction }} <b></td>
                            <td><b>{{ $other_deduction }} <b></td>
                            <td><b>{{ $tds }} <b></td>
                        </tr>  
                    </tbody>
                </table>
                @else
                <p align="center">Records not found!</p>
                @endif
        </div>
    </div>
</div> 