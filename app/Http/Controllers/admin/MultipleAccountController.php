<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use Datatables;
use App\Models\MultipleAccount;
use App\Models\MemberAccounts;
use App\Models\AdminAction;
use App\Models\Member;
use Illuminate\Validation\Rule;
use App\Models\Loans;
use App\Models\LoanMain;
use App\Models\Ledger;
class MultipleAccountController extends Controller
{
	 public function __construct() {

        $this->moduleRouteText = "multiple-account";
        $this->moduleViewName = "admin.multiple_account";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Bachat Accounts";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new MultipleAccount();  

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
    	$checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_MULTIPLE_ACCOUNT);
    	if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Bachat Account";

        return view($this->moduleViewName.".index", $data); 

    }

	public function create(Request $request)
    {
      $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LOAN);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $id=$request->get('id');

        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add Loan";
        $data['action_url'] = $this->moduleRouteText.".store";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST";
        $data["loan_type"] = ['normal_loan'=>'Normal','extra_loan'=>'Extra Loan'];

        $member =Member::select(TBL_MEMBER.".id",TBL_MEMBER.".firstname",TBL_MEMBER.".middlename",TBL_MEMBER.".lastname") 
            ->join(TBL_LOAN_BACHAT, TBL_LOAN_BACHAT.".member_id", "=", TBL_MEMBER.".id")
            ->join(TBL_BB_ACOUNT, TBL_BB_ACOUNT.".bb_bachat_id", "=", TBL_LOAN_BACHAT.".id")
            ->where(TBL_BB_ACOUNT.".id","=",$id)->first();

        $data['members'] = $member;
        $data['id']=$id;
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
            'transaction_amount' => 'required|min:3',
            'member_id'=>'exists:'.TBL_MEMBER.',id',
            'loan_type' => Rule::in(['normal_loan','extra_loan']),
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
            $transaction_amount = $request->get('transaction_amount');
            $member_id = $request->get('member_id');
            $account_id=$request->get('account_id');
            $loan_type=$request->get('loan_type');

            //add loan data in main table
            $loan = new LoanMain();
            $loan->member_id = $member_id;
            $loan->bb_account_id = $account_id;
            $loan->loan_amount = $transaction_amount;
            $loan->created_at =\Carbon\Carbon::now();
            $loan->save();
            $loan_id=$loan->id;

            $loan_pr=$transaction_amount/LOAN_EMI;
            $loanflag=MemberAccounts::select("loan_flag")->where('member_id',$member_id)->first();
            $cnt=11;
            if($loanflag->loan_flag==1){
                $cnt=10;
            }

            for($i=1;$i<=$cnt;$i++){
                if($i==11){
                    $trn_type='Interest';
                    if($loan_type=='extra_loan'){
                        $extra=$loan_pr/2;
                        $loanamount= $loan_pr+$extra;
                    }else{
                        $loanamount = $loan_pr;
                    }
                }else{
                    $trn_type='Principal';
                    $loanamount = $loan_pr;
                }
                $loan = new Loans();
                $loan->loan_id = $loan_id;
                $loan->transaction_amount = $loanamount;
                $loan->transaction_type = $trn_type;
                $loan->created_at =\Carbon\Carbon::now();
                $loan->loan_due_date=date('Y-m-10',strtotime("+".$i. 'month'));
                $loan->status ='0';
                $loan->save();
            }      
            //update loan balance and amount in account
            $account=MultipleAccount::where('id',$account_id)->first();
            if($account){
                if($loanflag->loan_flag==0){
                    if($loan_type=='extra_loan'){
                        $extra=$loan_pr/2;
                        $extraamount= $loan_pr+$extra;
                    }else{
                        $extraamount = $loan_pr;
                    }
                }else{
                    $extraamount=0;                    
                }
                $account->loan_amount=$account->loan_amount+$transaction_amount;
                $account->loan_balance=$account->loan_balance+$transaction_amount+$extraamount;
                $account->save();
            }
            //update loan balance and amount in bachat
            $totalLoanAmount=MultipleAccount::where('bb_bachat_id',$account->bb_bachat_id)->sum('loan_amount');
            $totalBalance=MultipleAccount::where('bb_bachat_id',$account->bb_bachat_id)->sum('loan_balance');

            $bachat = MemberAccounts::where('id',$account->bb_bachat_id)->first();
            if($bachat){
                $bachat->loan_amount=$totalLoanAmount;
                $bachat->loan_balance=$totalBalance;
                $bachat->save();
            }
            $msg="Loan has been added successfully!";

            // store logs detail (Loan Table)
            $params=array();    
            $id=$loan_id;
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_LOAN;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Loan ::".$id;
            $logs=\App\Models\AdminLog::writeadminlog($params);
        }
        return ['status' => $status, 'msg' => $msg, 'data' => $data,'goto' => $goto];    
    }

    public function edit($id)
    {    
        
    }
    public function update(Request $request, $id)
    {
       
    }
    public function show($id)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LEDGER_LIST);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();
        $data['page_title'] = "Ladger List";

        if(!empty($id)){
        $model =Ledger::select(TBL_LEDGER.".*", TBL_MEMBER.".firstname",TBL_MEMBER.".middlename",TBL_MEMBER.".lastname") 
            ->join(TBL_BB_ACOUNT, TBL_LEDGER.".bb_account_id", "=", TBL_BB_ACOUNT.".id")
            ->join(TBL_LOAN_BACHAT, TBL_BB_ACOUNT.".bb_bachat_id", "=", TBL_LOAN_BACHAT.".id")
            ->join(TBL_MEMBER, TBL_LOAN_BACHAT.".member_id", "=", TBL_MEMBER.".id")
            ->where(TBL_BB_ACOUNT.".bb_account_id","=",$id)
            ->orderBy('member_id', 'asc');
        }

        return view($this->moduleViewName.'.show', $data);        
    }
    public function addpayment($id)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LOAN_PAYMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        //$bb_bachat_id=$id;
        $model =MemberAccounts::select(TBL_LOAN_BACHAT.".member_id") 
            ->join(TBL_BB_ACOUNT, TBL_BB_ACOUNT.".bb_bachat_id", "=", TBL_LOAN_BACHAT.".id")
            ->where(TBL_BB_ACOUNT.".id","=",$id)
            ->first();

        $member = Member::find($model->member_id);
        if(!$member)
            return abort(404);

        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add Installment";
        $data['action_url'] = $this->moduleRouteText.".editpayment";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST";
        $data['member'] = $member;
        $data['bb_account_id']=$id;
        return view($this->moduleViewName.'.addpayment', $data);   
    }
    public function editpayment(Request $request)
    {

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LEDGER);
        
        if($checkrights) 
        {
            return $checkrights;
        }      
        $data = array();
        $status = 1;
        $msg = $this->addMsg;
        $goto = $this->list_url;

        $validator = Validator::make($request->all(), [
            'transaction_amount' => 'required|min:3',
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
            $transaction_amount = $request->get('transaction_amount');
            $member_id = $request->get('member_id');
            $bb_account_id=$request->get('bb_account_id'); 

            //update account model balance
            $accountmodel = MultipleAccount::where('id',$bb_account_id)->first(); 
            $accountmodel->ledger_amount =$accountmodel->ledger_amount+$transaction_amount;
            $accountmodel->save();
            $msg="Installment has been added successfully!";
        }

        // store logs detail (Add Installment)
        $params=array();    
        $id=$bb_account_id;
        $params['adminuserid']  = \Auth::guard('admins')->id();
        $params['actionid']     = $this->adminAction->ADD_INSTALLMENT;
        $params['actionvalue']  = $id;
        $params['remark']       = "Add ledger amount ::".$id;
        $logs=\App\Models\AdminLog::writeadminlog($params);

        return ['status' => $status, 'msg' => $msg, 'data' => $data,'goto' => $goto];
    }
    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_MULTIPLE_ACCOUNT);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model =MultipleAccount::select(TBL_BB_ACOUNT.".*", TBL_MEMBER.".firstname",TBL_MEMBER.".middlename",TBL_MEMBER.".lastname")
            ->join(TBL_LOAN_BACHAT, TBL_LOAN_BACHAT.".id", "=", TBL_BB_ACOUNT.".bb_bachat_id")
            ->join(TBL_MEMBER, TBL_LOAN_BACHAT.".member_id", "=", TBL_MEMBER.".id");
        
        return Datatables::eloquent($model)        
             ->editColumn('firstname', function($row){                
                   return  $row->firstname." ".$row->middlename." ".$row->lastname;        
             })
             ->editColumn('balance', function($row){                
                if(empty($row->balance))          
                    return '-';
                else
                    return $row->balance;
            })
             ->editColumn('loan_balance', function($row){                
                if(empty($row->loan_balance))          
                    return '-';
                else
                    return $row->loan_balance;
            })
             ->editColumn('loan_amount', function($row){                
                if(empty($row->loan_amount))          
                    return '-';
                else
                    return $row->loan_amount;
            })
             ->editColumn('ledger_amount', function($row){                
                if(empty($row->ledger_amount))          
                    return '-';
                else
                    return $row->ledger_amount;
            })
             ->addColumn('action', function(MultipleAccount $row) {
                $isAddLoan = 0; $isLoanPay = 0;
                $auth_user = Auth::guard("admins")->check();
                if($auth_user)
                {
                    $auth_id = Auth::guard("admins")->user()->id;
                    if(\App\Models\Admin::isAccess(\App\Models\Admin::$ADD_LOAN))
                        $isAddLoan = 1;
                    $auth_id = Auth::guard("admins")->user()->id;
                    if(\App\Models\Admin::isAccess(\App\Models\Admin::$ADD_LOAN_PAYMENT))
                        $isLoanPay = 1;
                }
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isAddLoan' => $isAddLoan,
                        'isLoanPay' => $isLoanPay,
                        'isviewledger' =>1,//\App\Models\Admin::isAccess(\App\Models\Admin::$VIEW_LEDGER),
                    ]
                )->render();
            })->rawColumns(['action','firstname']) 

            ->make(true);        
    }
    
    
}
