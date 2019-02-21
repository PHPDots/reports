<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $guard = "admins";
    protected $table = TBL_USERS;
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['firstname','lastname','email','user_type_id','address','phone','password'];
        
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ]; 

    // admin group pages varibales
    public static $error_msg = "You are not authorised to view this page.";
    public static $LIST_ADMIN_LOG_ACTIONS = 1;
    public static $ADD_ADMIN_LOG_ACTIONS = 2;
    public static $EDIT_ADMIN_LOG_ACTIONS = 3;
    public static $DELETE_ADMIN_LOG_ACTIONS = 4;

    public static $LIST_ADMIN_MODULES = 5;
    public static $ADD_ADMIN_MODULES = 6;
    public static $EDIT_ADMIN_MODULES = 7;
    public static $DELETE_ADMIN_MODULES = 8;
    
    public static $LIST_ADMIN_MODULES_PAGES = 9;
    public static $ADD_ADMIN_MODULES_PAGES = 10;
    public static $EDIT_ADMIN_MODULES_PAGES = 11;
    public static $DETELE_ADMIN_MODULES_PAGES = 12;
    
    public static $ADMIN_USERS = 21;
    public static $ASSIGN_RIGHTS = 22;

    public static $LIST_PROJECT = 23;  
    public static $ADD_PROJECT = 24;  
    public static $EDIT_PROJECT = 25;  
    public static $DELETE_PROJECT = 26;

    public static $LIST_USERS = 35;     
    public static $ADD_USERS = 36;
    public static $EDIT_USERS = 37; 
    public static $DELETE_USERS = 38;

    public static $LIST_USER_TYPE = 39;
    public static $ADD_USER_TYPE = 40;
    public static $EDIT_USER_TYPE = 41;
    public static $DELETE_USER_TYPE = 42;

    public static $LIST_LEAVE_REPORT = 43;
    public static $ADD_LEAVE_REPORT = 44;
    public static $EDIT_LEAVE_REPORT = 45;
    public static $DELETE_LEAVE_REPORT = 46;

    public static $LIST_CLIENT = 47;
    public static $ADD_CLIENT = 48;
    public static $EDIT_CLIENT = 49;
    public static $DELETE_CLIENT = 50;

    public static $LIST_CLIENT_USER = 51;
    public static $ADD_CLIENT_USER = 52;
    public static $EDIT_CLIENT_USER = 53;
    public static $DELETE_CLIENT_USER = 54;

    public static $LIST_TASKS = 55;
    public static $ADD_TASKS = 56;
    public static $EDIT_TASKS = 57;
    public static $DELETE_TASKS = 58;

    public static $MY_PROFILE = 59;
    public static $MY_PROFILE_PWD = 60;    
    
    public static $LIST_EMP_DOCUMENT = 61;
    public static $ADD_EMP_DOCUMENT = 62;
    public static $EDIT_EMP_DOCUMENT = 63;
    public static $DELETE_EMP_DOCUMENT = 64;

    public static $LIST_PROJECT_CREDENTIAL = 65;
    public static $ADD_PROJECT_CREDENTIAL = 66;
    public static $EDIT_PROJECT_CREDENTIAL = 67;
    public static $DELETE_PROJECT_CREDENTIAL = 68;

    public static $LIST_EMAIL_SENT = 69;
    public static $VIEW_EMAIL_SENT = 70;
	
	public static $LIST_SALARY_SLIP = 71;
    public static $ADD_SALARY_SLIP = 72;
    public static $EDIT_SALARY_SLIP = 73;
    public static $DELETE_SALARY_SLIP = 74;

	public static $DOWNLOAD_MONTHLY_REPORT = 75;

	public static $LIST_HOLIDAYS = 76;
    public static $ADD_HOLIDAYS = 77;
    public static $EDIT_HOLIDAYS = 78;
    public static $DELETE_HOLIDAYS = 79;
	
	public static $LIST_ESTIMATE_TASK = 80;
    public static $ADD_ESTIMATE_TASK = 81;
    public static $EDIT_ESTIMATE_TASK = 82;
    public static $DELETE_ESTIMATE_TASK = 83;
	
	public static $LIST_EXPENSE = 84;
    public static $ADD_EXPENSE = 85;
    public static $EDIT_EXPENSE = 86;
    public static $DELETE_EXPENSE = 87;
	
	public static $LIST_INVOICE = 88;
    public static $ADD_INVOICE = 89;
    public static $EDIT_INVOICE = 90;
    public static $DELETE_INVOICE = 91;

	public static $BANK_DETAIL = 92;
  	
	public static $LIST_APPRAISAL_FORM = 93;
    public static $ADD_APPRAISAL_FORM = 94;
    public static $EDIT_APPRAISAL_FORM = 95;
	public static $VIEW_APPRAISAL_FORM = 96;
	
	public static $LIST_NOT_ADDED_TASK = 97;
	public static $SEND_SMS_FORM = 98;
	public static $LIST_SMS_SENT_LOG = 99;
	
	public static $LIST_MEMBER_LOG = 100;
	
	public static $LIST_LEAVE_ENTITLEMENT = 102;
    public static $ADD_LEAVE_ENTITLEMENT = 103;
	public static $LIST_LEAVE_ENTITLEMENT_LOG = 104;
	
	public static $LIST_MONTHLY_LEAVE_LOG = 105;
	
	public static $LIST_SOFTWARE_LICENSE = 106;
    public static $ADD_SOFTWARE_LICENSE = 107;
    public static $EDIT_SOFTWARE_LICENSE = 108;
    public static $DELETE_SOFTWARE_LICENSE = 109;
	
	public static $LIST_INVOICE_EXPENSE = 110;
    public static $DOWNLOAD_INVOICE_EXPENSE = 111;

    public static $LIST_FIX_TASK = 112;
    public static $ADD_FIX_TASK = 113;
    public static $EDIT_FIX_TASK = 114;
    public static $DELETE_FIX_TASK = 115;
    public static $USERS_TASK_REPORT = 116;

    public static $LIST_LOAN = 117;
    public static $ADD_LOAN = 118;
    public static $ADD_LOAN_PAYMENT= 119;
    public static $DELETE_LOAN = 120;
    public static $LIST_MEMBER_ACCOUNTS = 121;
    public static $LIST_MULTIPLE_ACCOUNT = 122;
    public static $LEDGER_LIST = 123;
    public static $ADD_LEDGER = 124;
    public static $VIEW_LEDGER=125;
    public static $VIEW_LOAN=126;
    public static $ADD_ACCOUNT=127;

    public static $LIST_ASSIGN_TASK = 128;
    public static $ADD_ASSIGN_TASK = 129;
    public static $EDIT_ASSIGN_TASK = 130;
    public static $DELETE_ASSIGN_TASK = 131;
    public static $CHANGE_ASSIGN_TASK_STATUS = 132;

    public static $CLIENT_WISE_MONTHY_REPORT = 133;
    public static $EMPLOYEE_WISE_MONTHY_REPORT = 134;

    public static $FINANCIAL_YEAR_REPORTS = 136;
    public static $SALARY_REPORT = 137;
 
    /**
     * check page acces permissions
     *          
     * @var $page_id
     */
    public static function checkPermission($intCurAdminUserRight)
    {
        $userrights = session("admin_user_rights_ids");
        if(is_array($userrights) && !empty($userrights)){
            if (!in_array($intCurAdminUserRight, (array)$userrights)) {
                session()->flash('error_message',\App\Models\Admin::$error_msg);
                return redirect('dashboard');
            }
        }
    }
    /**
     * check page acces permissions
     *
     * @var $page_id
     */        
    public static function isAccess($page_id)
    {
        $array = session("admin_user_rights_ids");
        $status = 0;
        
        if(is_array($array) && in_array($page_id, $array))
        {
            $status = 1;
        }
        return $status;
    }   
}
