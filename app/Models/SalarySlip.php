<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    public $timestamps = true;
    protected $table = TBL_SALARY_SLIP;

    /**
     * @var array
     */
    protected $fillable = ['user_id','ctc','month', 'year','account_num','joining_date','bank_name','working_days','designation','leave_taken','pan_num','basic_salary','advance','hra','leave_deduction','conveyance_allowance','other_deduction','telephone_allowance','tds', 'medical_allowance', 'uniform_allowance', 'special_allowance', 'bonus', 'arrear_salary','advance_given', 'leave_encashment', 'total_earning','total_deduction','net_pay','net_pay_words'];

	public static function listFilter($query)
    {
		$search_name = request()->get("search_name");
        $search_month = request()->get("search_month");
        $search_year = request()->get("search_year");
        $search_start_date = request()->get("search_start_date");
        $search_end_date = request()->get("search_end_date");

        $searchData = array();
        customDatatble('salary_slip');

        if(!empty($search_name))
        {
            $query = $query->where(TBL_SALARY_SLIP.".user_id", $search_name);
            $searchData['search_name'] = $search_name;
        }
        if(!empty($search_month))
        {
            $query = $query->where(TBL_SALARY_SLIP.".month", $search_month);
            $searchData['search_month'] = $search_month;
        }
        if(!empty($search_year))
        {
            $query = $query->where(TBL_SALARY_SLIP.".year", $search_year);
            $searchData['search_year'] = $search_name;
        }
        if (!empty($search_start_date))
        {
            $search_start_date = date('Y-m',strtotime($search_start_date));
            $from_date=$search_start_date.'-01 00:00:00';
            $convertFromDate= $from_date;
           
            $query = $query->where(\DB::raw("CONCAT(".TBL_SALARY_SLIP.".year, '-', ".TBL_SALARY_SLIP.".month, '-01 00:00:00')"),">=",addslashes($convertFromDate));
            $searchData['search_start_date'] = $search_start_date;
        }
        if (!empty($search_end_date))
        {
            $search_end_date = date('Y-m',strtotime($search_end_date));
            $to_date=$search_end_date.'-01 23:59:59';
            $convertToDate= $to_date;

            $query = $query->where(\DB::raw("CONCAT(".TBL_SALARY_SLIP.".year, '-', ".TBL_SALARY_SLIP.".month, '-01 23:59:59')"),"<=",addslashes($convertToDate));
            $searchData['search_end_date'] = $search_end_date;
        }
            $goto = \URL::route('salary_slip.index', $searchData);
            \session()->put('salary_slip_goto',$goto);

        return $query;
    }
}
