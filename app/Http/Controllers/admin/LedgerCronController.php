<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Datatables;
use App\Models\Ledger;
use App\Models\Loans;
use App\Models\AdminAction;
use App\Models\Member;
use App\Models\MemberAccounts;
use App\Models\MultipleAccount;
use Illuminate\Validation\Rule;
use App\Models\SendMailUser;
class LedgerCronController extends Controller
{
	public function MonthlyInstallment(Request $request)
    {
        

        $model =MultipleAccount::all(); 
        if($model){
            foreach ($model as $acdetail){
                $accountmodel = MultipleAccount::where('id',$acdetail->id)->first(); 
            
                if($accountmodel){
                    $last_balance = $accountmodel->balance+MONTHLY_INSTALLMENT;
                }else{
                    $last_balance=MONTHLY_INSTALLMENT;
                }

                $ledger = new Ledger();
                $ledger->bb_account_id=$accountmodel->id;
                $ledger->transaction_amount =MONTHLY_INSTALLMENT;
                $ledger->transaction_source = 'Credit';
                $ledger->balance = $last_balance;
                $ledger->transaction_type = 'Monthly Installment';
                $ledger->created_at =\Carbon\Carbon::now();
                $ledger->save();

                //update balance in account
                $accountmodel->ledger_amount=$accountmodel->ledger_amount-MONTHLY_INSTALLMENT;
                $accountmodel->balance=$accountmodel->balance+MONTHLY_INSTALLMENT;
                $accountmodel->save();

                //update balance in bachat
                $bachatmodel = MemberAccounts::where('id',$acdetail->bb_bachat_id)->first();
                $bachatmodel->balance=$bachatmodel->balance+MONTHLY_INSTALLMENT;
                $bachatmodel->save();
                echo "<br/>cron running out successfully";
            }
        }else{
            echo "<br/>no records found !";
        }        
    }
    public function MonthlyPenalty(Request $request){
        $model =MultipleAccount::all(); 
        if($model){
            foreach ($model as $accountmodel){
                $accountmodel = MultipleAccount::where('id',$accountmodel->id)->first(); 
                if($accountmodel->ledger_amount<0 || $accountmodel->ledger_amount==''){

                    $penalty=$accountmodel->ledger_amount*PENALTY_RATE/100;

                    //Add monthly penalty record in lageder list
                    
                    $ledger = new Ledger();
                    $ledger->bb_account_id=$accountmodel->id;
                    $ledger->transaction_amount =abs($penalty);
                    $ledger->transaction_source = 'Credit';
                    $ledger->balance = abs($penalty);
                    $ledger->transaction_type = 'Monthly Penalty';
                    $ledger->created_at =\Carbon\Carbon::now();
                    $ledger->save();

                    //update balance in account
                    $accountmodel->ledger_amount=$accountmodel->ledger_amount+$penalty;
                    $accountmodel->save();


                    echo "<br/>cron running out successfully";
                }
            }
        }else{
            echo "<br/>no records found !";
        }
    }
    public function MonthlyLoanPenalty(Request $request){
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

                if (!isset($acc[$acdetail->member_id.'_'.$acdetail->bb_account_id])){
                    $acc[$acdetail->member_id.'_'.$acdetail->bb_account_id]=$balance;
                }else{
                    $acc[$acdetail->member_id.'_'.$acdetail->bb_account_id]=$acc[$acdetail->member_id.'_'.$acdetail->bb_account_id]+$balance;
                }
                
            }                
            foreach($acc as $key=>$acvalue){
                $acdetail=explode('_',$key);

                $loan = new Loans();
                $penalty=$acvalue*PENALTY_RATE/100;
                $loan->member_id = $acdetail[0];
                $loan->bb_account_id = $acdetail[1];
                $loan->transaction_amount = $penalty;
                $loan->transaction_type = 'Penalty';
                $loan->created_at =\Carbon\Carbon::now();
                $loan->loan_due_date=date('Y-m-10',strtotime("+1 month"));
                $loan->status ='0';
                $loan->save();

                //update loan balance in account
                $account=MultipleAccount::where('id',$acdetail[1])->first();
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
    }
}
