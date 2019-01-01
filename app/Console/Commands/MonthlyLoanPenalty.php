<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loans;
use App\Models\LoanMain;
use App\Models\MemberAccounts;
use App\Models\MultipleAccount;

class MonthlyLoanPenalty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MonthlyLoanPenalty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation Process For generate Monthly Loan Penalty.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this_month = date('Y-m-d');

        $loans =Loans::select("*")
        ->where(\DB::raw("(DATE_FORMAT(loan_due_date,'%Y-%m-%d'))"),"<",$this_month)
        ->whereNull("received_date")
        ->where("transaction_amount","!=","balance")
        ->get();

        $total=0;
        $bachat_total = 0;
        $totalpenalty=0;
        $acc=array();
        if(count($loans)){
            foreach ($loans as $acdetail){
                
                if($acdetail->balance!=''){
                    $balance=$acdetail->balance;
                }else{
                    $balance=$acdetail->transaction_amount;
                }

                if (!isset($acc[$acdetail->loan_id])){
                    $acc[$acdetail->loan_id]=$balance;
                }else{
                    $acc[$acdetail->loan_id]=$acc[$acdetail->loan_id]+$balance;
                }
                
            }
            foreach($acc as $loan_id=>$balance){
                $loan = new Loans();
                $penalty=$balance*PENALTY_RATE/100;
                $loan->loan_id = $loan_id;
                $loan->transaction_amount = $penalty;
                $loan->transaction_type = 'Penalty';
                $loan->created_at =\Carbon\Carbon::now();
                $loan->loan_due_date=date('Y-m-10',strtotime("+1 month"));
                $loan->status ='0';
                $loan->save();

                //update loan balance in account
                $loanDetail=LoanMain::where('id',$loan_id)->first();
                $account=MultipleAccount::where('id',$loanDetail->bb_account_id)->first();
                if($account){
                    $account->loan_balance=$account->loan_balance+$penalty;
                    $account->save();
                }
                //update loan balance in bachat
                $bachat = MemberAccounts::where('id',$account->bb_bachat_id)->first();
                if($bachat){
                    $bachat->loan_balance=$bachat->loan_balance+$penalty;
                    $bachat->save();
                }
            } 
            echo "<br/>cron running out successfully";     
        }else{
               echo "<br/>no records found !"; 
        }
        exit;
    }
}
