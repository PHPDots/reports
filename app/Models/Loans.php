<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{
    public $timestamps = false;
    protected $table = TBL_LOAN_MASTER;

    /**
     * @var array
     */
    protected $fillable = ['member_id','bb_account_id','transaction_amount
','balance', 'transaction_type','remarks','updated_at','created_at','received_date','loan_due_date','status'];
	
    function getLoans($onlyIDS = 0)
    {
        $id = $this->id;

        $query = \DB::table(TBL_LOAN_MASTER)
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
