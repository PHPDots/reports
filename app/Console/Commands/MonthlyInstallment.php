<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Ledger;
use App\Models\Loans;
use App\Models\Member;
use App\Models\MemberAccounts;
use App\Models\MultipleAccount;

class MonthlyInstallment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MonthlyInstallment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation Process For generate Monthly Installment.';

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
                echo "</br>cron running out successfully";
            }
        }else{
            echo "</br>no records found !";
        }

      exit;
    }
    
}
