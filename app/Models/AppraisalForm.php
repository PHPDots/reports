<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppraisalForm extends Model
{
    public $timestamps = true;
    protected $table = TBL_APPRAISAL_FORM;

    /**
     * @var array
     */
    protected $fillable = ['user_id','past_year_rate','past_year_achieved','job_satisfaction','achievements','goal','duty_responsibility','suggestion','current_salary','expected_salary','raise','is_submit','years','months','submited_at','english_communication','requirement_understanding','timely_work','office_on_time','generate_work','git_knowledge','proactive_on_work','job_profile','attitude','work_quality','Work_independently','form_year'];
}
