<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use Datatables;
use App\Models\Ledger;
use App\Models\MemberAccounts;
use App\Models\AdminAction;
use App\Models\Member;
use App\Models\Loans;
use App\Models\MultipleAccount;
use Illuminate\Validation\Rule;
use App\Models\SendMailUser;
class MemberAccountsController extends Controller
{
	 public function __construct() {

        $this->moduleRouteText = "member-accounts";
        $this->moduleViewName = "admin.membe_accounts";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "MemberAccounts";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new MemberAccounts();  

        $this->addMsg = $module . " has been added successfully!";
        $this->updateMsg = $module . " has been updated successfully!";
        $this->deleteMsg = $module . " has been deleted successfully!";
        $this->deleteErrorMsg = $module . " can not deleted!";       

        view()->share("list_url", $this->list_url);
        view()->share("moduleRouteText", $this->moduleRouteText);
        view()->share("moduleViewName", $this->moduleViewName);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_MEMBER_ACCOUNTS);
    	if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Bachat Member";
        $data['add_url'] = route($this->moduleRouteText.'.create');

        $isAddAccount = 0;
        $auth_user = Auth::guard("admins")->check();
        if($auth_user)
        {
            $auth_id = Auth::guard("admins")->user()->id;
            if(\App\Models\Admin::isAccess(\App\Models\Admin::$ADD_ACCOUNT))
                $isAddAccount = 1;
        }

        $data['btnAdd'] = $isAddAccount;
        return view($this->moduleViewName.".index", $data); 

    }

	public function create()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LOAN);
        
        if($checkrights) 
        {
            return $checkrights;
        } 
        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add ".$this->module;
        $data['action_url'] = $this->moduleRouteText.".store";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST";
        $data['members'] = Member::getMembers();

        return view($this->moduleViewName.'.add', $data);
    }

    public function store(Request $request)
    {    
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LOAN);
        
        if($checkrights) 
        {
            return $checkrights;
        }      
        $data = array();
        $status = 1;
        $msg = $this->addMsg;
        $goto = $this->list_url;

        $validator = Validator::make($request->all(), [
            'member_id'=>'exists:'.TBL_MEMBER.',id',
            'member_id'=>'unique:'.TBL_LOAN_BACHAT.',member_id',
        ]);
        if ($validator->fails()) 
        {
            $messages = $validator->messages();
            
            $status = 0;
            $msg = "";
            
            foreach ($messages->all() as $message) 
            {
                $msg .= $message . "<br />";
            }
        }         
        else
        {
            $member_id = $request->get('member_id');
            $balance=$request->get('balance');

            $loan = new MemberAccounts();
            $loan->member_id = $member_id;
            $loan->balance= $balance;
            $loan->created_at =\Carbon\Carbon::now();
            $loan->status ='1';
            $loan->save();
            $lastinsertedId=$loan->id;

            $loanac= new MultipleAccount(); 
            $loanac->bb_bachat_id=$lastinsertedId;
            $loanac->balance= $balance;
            $loanac->created_at=\Carbon\Carbon::now();
            $loanac->save();

            //add balance in ladger
            
            $ledger = new Ledger();
            $ledger->bb_account_id=$lastinsertedId;
            $ledger->transaction_amount =$balance;
            $ledger->transaction_source = 'Credit';
            $ledger->balance = $balance;
            $ledger->transaction_type = 'initial balance';
            $ledger->created_at =\Carbon\Carbon::now();
            $ledger->save();

            $id = $lastinsertedId;
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_BACHAT_ACCOUNT;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Bachat Account ::".$id;
                                    
            $logs=\App\Models\AdminLog::writeadminlog($params);

            session()->flash('success_message', $msg);

        }
        return ['status' => $status, 'msg' => $msg, 'data' => $data,'goto' => $goto];
    }

    public function edit($id)
    {    
        
    }
    public function update(Request $request, $id)
    {
       
    }
    public function addmultipleAc(Request $request)
    {
       $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LOAN);
        
        if($checkrights) 
        {
            return $checkrights;
        }      
        $data = array();
        $status = 1;
        $msg = $this->addMsg;
        $goto = $this->list_url;

        $validator = Validator::make($request->all(), [
            'member_id'=>'exists:'.TBL_MEMBER.',id',
        ]);
        if ($validator->fails()) 
        {
            $messages = $validator->messages();
            
            $status = 0;
            $msg = "";
            
            foreach ($messages->all() as $message) 
            {
                $msg .= $message . "<br />";
            }
        }         
        else
        {
            $member_id = $request->get('member_id');
            $balance=$request->get('balance');
            
            $dbmodel = MemberAccounts::where('member_id',$member_id)->first();
            $loan = new MultipleAccount();
            $loan->bb_bachat_id = $dbmodel->id;
            $loan->balance= $balance;
            $loan->created_at =\Carbon\Carbon::now();
            $loan->save();
            $lastinsertedId=$loan->id;
            
            $totalBalance=MultipleAccount::where('bb_bachat_id',$dbmodel->id)->sum('balance');
            $totalLoanAmount=MultipleAccount::where('bb_bachat_id',$dbmodel->id)->sum('loan_amount');
            $account=MemberAccounts::where('id',$dbmodel->id)->first();
            if($account){
                $account->balance=$totalBalance;
                $account->loan_balance=$totalLoanAmount;
                $account->save();
            }
            // store logs detail (bb_account Table)
            $params_account=array();    
            $id=$lastinsertedId;
            $params_account['adminuserid']  = \Auth::guard('admins')->id();
            $params_account['actionid']     = $this->adminAction->ADD_MULTIPLE_ACCOUNT;
            $params_account['actionvalue']  = $id;
            $params_account['remark']       = "Add Multiple Account ::".$id;
            $logs=\App\Models\AdminLog::writeadminlog($params_account);

            session()->flash('success_message', $msg);
        }
        return ['status' => $status, 'msg' => $msg, 'data' => $data,'goto' => $goto]; 
    }
    public function add($id)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_ACCOUNT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        
        $model =MemberAccounts::select(TBL_LOAN_BACHAT.".member_id") 
            ->join(TBL_BB_ACOUNT, TBL_BB_ACOUNT.".bb_bachat_id", "=", TBL_LOAN_BACHAT.".id")
            ->where(TBL_BB_ACOUNT.".bb_bachat_id","=",$id)
            ->first();

        $member = Member::find($model->member_id);
        if(!$member)
            return abort(404);

        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add ".$this->module;
        $data['action_url'] = $this->moduleRouteText.".addmultipleAc";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST";
        $data['member'] =$member;

        return view($this->moduleViewName.'.addmultiple', $data);
        
    }
    
    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_MEMBER_ACCOUNTS);
        
        if($checkrights) 
        {
            return $checkrights;
        }

       //$model = Loans::query();
       $model =MemberAccounts::select(TBL_LOAN_BACHAT.".*", TBL_MEMBER.".firstname",TBL_MEMBER.".middlename",TBL_MEMBER.".lastname")
            ->join(TBL_MEMBER, TBL_LOAN_BACHAT.".member_id", "=", TBL_MEMBER.".id");
            //->orderBy(TBL_LOAN_BACHAT.".member_id",'asc');

        return Datatables::eloquent($model)        
             ->editColumn('firstname', function($row){                
                return  $row->firstname." ".$row->middlename." ".$row->lastname;        
            })  
            ->editColumn('created_at', function($row){                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })
            ->editColumn('balance', function($row){                
                if(!empty($row->balance))          
                    return $row->balance;
                else
                    return '0';    
            })
            ->editColumn('loan_balance', function($row){                
                if(!empty($row->loan_balance))          
                    return $row->loan_balance;
                else
                    return '0';    
            })
            ->editColumn('loan_amount', function($row){                
                if(!empty($row->loan_amount))          
                    return $row->loan_amount;
                else
                    return '0';    
            })
            ->editColumn('status' , function ($row)
            {
                if ($row->status == 1) {
                    return "<a class='btn btn-success btn-xs'>Active</a>";
                }
                if ($row->status == 0) {
                    return "<a class='btn btn-danger btn-xs'>Inactive</a>";
                }
            })
            ->addColumn('action', function(MemberAccounts $row) {
                $isAddAccount = 0;
                $auth_user = Auth::guard("admins")->check();
                if($auth_user)
                {
                    $auth_id = Auth::guard("admins")->user()->id;
                    if(\App\Models\Admin::isAccess(\App\Models\Admin::$ADD_ACCOUNT)){
                        $isAddAccount = 1;
                    }
                }
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isAddAccount' =>$isAddAccount,
                    ]
                )->render();
            })->rawColumns(['action','firstname','created_at','balance','updated_at','status'])
            ->make(true);        
    }
    
    
}
