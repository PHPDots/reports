<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public $timestamps = true;
    protected $table = TBL_INVOICE;
     
    protected $fillable = ['to_address', 'invoice_no', 'invoice_date', 'cgst_amount','sgst_amount','total_amount','total_amount_words','address','pan_no','gst_regn_no','bank_account_no','bank_name','bank_swift_code','ifsc_code','require_gst','currency','client_id','payment','total_without_gst','total_with_gst'];
	
	public static function listFilter($query){
    	
    	$search_start_date = request()->get("search_start_date");
        $search_end_date = request()->get("search_end_date");
        $search_invoice_no = request()->get("search_invoice_no");
		$search_month = request()->get("search_month");
        $search_client_name = request()->get("search_client_name");
		$search_status = request()->get("search_status");
		$search_id = request()->get("search_id");
		$search_c_type = request()->get("search_c_type");
		$search_invoice_start_date = request()->get("search_invoice_start_date");
        $search_invoice_end_date = request()->get("search_invoice_end_date");
        $expense_action = request()->get("expense_action");

        if (!empty($search_start_date))
        {
            $from_date = $search_start_date . ' 00:00:00';
            $convertFromDate = $from_date;

            $query = $query->where(TBL_INVOICE.".created_at", ">=", addslashes($convertFromDate));
        }
        if (!empty($search_end_date)) {

            $to_date = $search_end_date . ' 23:59:59';
            $convertToDate = $to_date;

            $query = $query->where(TBL_INVOICE.".created_at", "<=", addslashes($convertToDate));
        }
		if (!empty($search_invoice_start_date))
        {
            $from_date = $search_invoice_start_date . ' 00:00:00';
            $convertFromDate = $from_date;

            $query = $query->where(TBL_INVOICE.".invoice_date", ">=", addslashes($convertFromDate));
        }
        if (!empty($search_invoice_end_date)) {

            $to_date = $search_invoice_end_date . ' 23:59:59';
            $convertToDate = $to_date;

            $query = $query->where(TBL_INVOICE.".invoice_date", "<=", addslashes($convertToDate));
        }
        if(!empty($search_invoice_no))
        {
            $query = $query->where(TBL_INVOICE.'.invoice_no', 'LIKE', '%'.$search_invoice_no.'%');
        	 
        }
		if (!empty($search_month)) {
            $query = $query->where(TBL_INVOICE.".invoice_date", "LIKE", '%'.$search_month.'%');
        }
        if (!empty($search_client_name)) {
            $query = $query->where(TBL_CLIENT.'.id',$search_client_name);
        }
		if($search_status == "1" || $search_status == "0" )
        {
            $query = $query->where('payment',$search_status);    
        }
		if(!empty($search_id))
        {
            $idArr = explode(',', $search_id);
            $idArr = array_filter($idArr);
            if(count($idArr)>0)
            {
                $query = $query->whereIn(TBL_INVOICE.".id",$idArr);
            } 
        }
		if($search_c_type == "1" || $search_c_type == "2" )
        {
            $query = $query->where(TBL_CLIENT.'.client_type',$search_c_type);
        }
        return $query;
    }
}