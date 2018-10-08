<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanMain extends Model
{
    public $timestamps = false;
    protected $table = TBL_LOAN;

    /**
     * @var array
     */
    protected $fillable = ['member_id','bb_account_id','loan_amount','created_at'];
	
    function getLoans($onlyIDS = 0)
    {
        $id = $this->id;

        $query = \DB::table(TBL_LOAN)
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
