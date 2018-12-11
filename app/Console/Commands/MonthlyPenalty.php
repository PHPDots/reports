<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ledger;
use App\Models\MultipleAccount;
class MonthlyPenalty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MonthlyPenalty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation Process For generate Monthly Penalty.';

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
        exit;
    }
}
