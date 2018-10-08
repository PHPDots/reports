<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultipleAccount extends Model
{
    public $timestamps = true;
    protected $table = TBL_BB_ACOUNT;

    /**
     * @var array
     */
    protected $fillable = ['bb_bachat_id', 'balance', 'loan_balance', 'loan_amount','ledger_amount'];
	
    function getLoans($onlyIDS = 0)
    {
        $id = $this->id;

        $query = \DB::table(TBL_BB_ACOUNT)
                ->where("id",$id)
                ->get();

        if($onlyIDS == 1)
        {
            $arr = array();
            foreach($query as $row)
            {   
                $arr[] = $row->id;
            }
            return $arr;
        }        
        else
        {
            return $query;        
        }
    }
	
    

}
