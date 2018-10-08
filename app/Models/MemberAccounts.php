<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberAccounts extends Model
{
    public $timestamps = true;
    protected $table = TBL_LOAN_BACHAT;

    /**
     * @var array
     */
    protected $fillable = ['member_id', 'balance', 'loan_balance', 'loan_amount','loan_flag', 'status', 'created_at','updated_at'];
	
    function getLoans($onlyIDS = 0)
    {
        $id = $this->id;

        $query = \DB::table(TBL_LOAN_BACHAT)
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
