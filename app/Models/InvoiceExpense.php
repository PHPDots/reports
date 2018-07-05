<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceExpense extends Model
{
    public $timestamps = true;
    protected $table = TBL_INVOICE_EXPENSE;
     
    protected $fillable = ['invoice_id', 'payment_status', 'amount', 'payment_date','partial_amount'];

    public static function listFilter($query)
    {
    	$search_start_date = request()->get("search_start_date");
        $search_end_date = request()->get("search_end_date");
        $search_invoice_id = request()->get("search_invoice_id");
        $search_payment_status = request()->get("search_payment_status");

        if (!empty($search_start_date)){

            $from_date=$search_start_date.' 00:00:00';
            $convertFromDate= $from_date;

            $query = $query->where(TBL_INVOICE_EXPENSE.".created_at",">=",addslashes($convertFromDate));
        }
        if (!empty($search_end_date)){

            $to_date=$search_end_date.' 23:59:59';
            $convertToDate= $to_date;

            $query = $query->where(TBL_INVOICE_EXPENSE.".created_at","<=",addslashes($convertToDate));
        }
        if (!empty($search_invoice_id)) {
            $query = $query->where(TBL_INVOICE_EXPENSE.".invoice_id", $search_invoice_id);
        }
        if($search_payment_status == "1" || $search_payment_status == "0")
        {
            $query = $query->where(TBL_INVOICE_EXPENSE.".payment_status", $search_payment_status);
        }
        return $query; 
    }
}
