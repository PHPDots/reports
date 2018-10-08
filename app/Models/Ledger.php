<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    public $timestamps = false;
    protected $table = TBL_LEDGER;

    /**
     * @var array
     */
    protected $fillable = ['member_id','transaction_amount
','balance', 'transaction_source','transaction_type','created_at'];
	
    function getLedger($onlyIDS = 0)
    {
        $id = $this->id;

        $query = \DB::table(TBL_LEDGER)
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
