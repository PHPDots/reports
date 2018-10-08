<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use Datatables;
use App\Models\Loans;
use App\Models\LoanMain;
use App\Models\Ledger;
use App\Models\MultipleAccount;
use App\Models\MemberAccounts;
use App\Models\AdminAction;
use App\Models\Member;
use Illuminate\Validation\Rule;
use App\Models\SendMailUser;
class LoansController extends Controller
{
	 public function __construct() {

        $this->moduleRouteText = "loans";
        $this->moduleViewName = "admin.loans";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Loan";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Loans();  

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
    	$checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_LOAN);
    	if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Bachat Loans Account";
        return view($this->moduleViewName.".index", $data); 

    }
    public function addpayment($id)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LOAN_PAYMENT);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $model =LoanMain::select("member_id")->where("id","=",$id)->first();

        $member = Member::find($model->member_id);
        
        if(!$member->id)
            return abort(404);
        $data = array();
        $data['formObj'] = $this->modelObj;
        $data['page_title'] = "Add ".$this->module." Payment";
        $data['action_url'] = $this->moduleRouteText.".editpayment";
        $data['action_params'] = 0;
        $data['buttonText'] = "Save";
        $data["method"] = "POST";
        $data['member'] = $member;
        $data['loan_id']=$id;

        return view($this->moduleViewName.'.addpayment', $data);
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
    public function editpayment(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_LOAN_PAYMENT);
        if($checkrights) 
        {
            return $checkrights;
        } 
        $data = array();
        $status = 1;
        $msg = 'Loan payment successfully added !';
        $goto = $this->list_url;
        $validator = Validator::make($request->all(), [
            'transaction_amount' => 'required|min:3',
            'member_id'=>'exists:'.TBL_LOAN.',member_id',
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
            //0-pending 1-partial 2-complete
            $transaction_amount = $request->get('transaction_amount');
            $member_id = $request->get('member_id');
            $loan_id=$request->get('loan_id');

            $bb_account = LoanMain::where('member_id',$member_id)->where('id',$loan_id)->first();

            $bb_account_id=$bb_account->bb_account_id;
            $dbmodel = Loans::where('loan_id',$loan_id)->where('status','!=','2' )->first();

            $dbamount = $dbmodel->transaction_amount;
            $insbal=0;
            if($dbmodel->status=='1' || $dbmodel->status=='0'){

                $insamt=$dbamount-$dbmodel->balance;
                if($insamt>$transaction_amount){    
                    $dbmodel->balance=$dbmodel->balance+$transaction_amount;
                    $dbmodel->status = '1';
                    $dbmodel->updated_at = \Carbon\Carbon::now();
                    $dbmodel->save();
                }else{
                    $dbmodel->balance=$dbamount;
                    $dbmodel->status = '2';
                    $dbmodel->updated_at = \Carbon\Carbon::now();
                    $dbmodel->received_date = \Carbon\Carbon::now();
                    $dbmodel->save();
                }
                
                $transaction_amount=$transaction_amount-$insamt;
            }

            $cnt=$transaction_amount/$dbamount;
            $fincnt=(ceil($cnt));

            $remainTotal=0;$val=0;
            $totalamt=$transaction_amount;
            //exit;
            for($i=1;$i<=$fincnt;$i++){
                $model = Loans::where('loan_id',$loan_id)->where('status','0')->first();
                $remainamount=$transaction_amount-$dbamount;
                if($fincnt==$i && is_float($cnt)){
                    $remain=$totalamt-$val;
                    $model->balance=$remain;
                    $model->updated_at = \Carbon\Carbon::now();
                    $model->status = '1';
                    $model->save();
                }else{
                    $model->balance=$dbamount;
                    $val=$dbamount*$i;
                    $model->received_date = \Carbon\Carbon::now();
                    $model->updated_at = \Carbon\Carbon::now();
                    $model->status = '2';
                    $model->save();
                }
                $transaction_amount=$remainamount;
            }
            //upadte loan balance in account 
            $updloan= MultipleAccount::where("id",$bb_account_id)->first();
            $loan_amount=$request->get('transaction_amount');
            $loan_balance=$updloan->loan_balance-$loan_amount;
            if($loan_balance==0){
                $updloan->loan_amount=0;
            }
            $updloan->loan_balance=$loan_balance;
            $updloan->save();

            //Update Loan balance in bachat detail
            $totalLoanbalance=MultipleAccount::where('bb_bachat_id',$updloan->bb_bachat_id)->sum('loan_balance');
            $account=MemberAccounts::where('id',$updloan->bb_bachat_id)->first();
            if($account){
                if($totalLoanbalance==0){
                    $account->loan_amount=0;
                }
                $account->loan_balance=$totalLoanbalance;
                $account->save();
            }
            // store logs detail (Pay Loan)
            $params=array();    
            $id=$loan_id;
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->PAY_LOAN;
            $params['actionvalue']  = $id;
            $params['remark']       = "Pay Loan ::".$id;
            $logs=\App\Models\AdminLog::writeadminlog($params);
        }    
        return ['status' => $status, 'msg' => $msg, 'data' => $data,'goto' => $goto];
    }
    public function store(Request $request)
    {    
        //
    }

    public function edit($id)
    {    
        //
    }
    public function update(Request $request, $id)
    {
        //
    }
    public function view($id,Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$VIEW_LOAN);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $member_id=$request->get('member_id'); 
        $member = Member::find($member_id);

        if(!$member)
        {
            abort(404);
        }
        $data = array();
        $data['page_title'] = "View ".$this->module;
        $data['list_url'] =$this->list_url;
        $model = Loans::where('loan_id',$id)->get();
        $data['loanList'] = $model;
        $data['member'] = $member;

        return view($this->moduleViewName.'.view',$data);
    }
    public function destroy($id,Request $request)
    {     
        //
    }
    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_LOAN);
        
        if($checkrights) 
        {
            return $checkrights;
        }

       $model =LoanMain::select(TBL_LOAN.".*", TBL_MEMBER.".firstname",TBL_MEMBER.".middlename",TBL_MEMBER.".lastname")
            ->join(TBL_MEMBER, TBL_LOAN.".member_id", "=", TBL_MEMBER.".id");
    
        return Datatables::eloquent($model)        
            ->editColumn('created_at', function($row){                
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';    
            })
            ->editColumn('firstname', function($row){                
                   return  $row->firstname." ".$row->middlename." ".$row->lastname;        
            }) 
            ->editColumn('status' , function ($row)
            {
                $model = Loans::where('loan_id',$row->id)->orderBy("id","desc")->first();

                if ($model->status == 0) {
                    return "<a class='btn btn-danger btn-xs'>Pending</a>";
                }
                if ($model->status == 1) {
                    return "<a class='btn btn-warning btn-xs'>Partial</a>";
                }
                if ($model->status == 2) {
                    return "<a class='btn btn-success btn-xs'>Received</a>";
                }
                
            })
            ->addColumn('action', function(LoanMain $row) {
                $isLoaninstallment = 0;
                $auth_user = Auth::guard("admins")->check();
                if($auth_user)
                {
                    $auth_id = Auth::guard("admins")->user()->id;
                    if(\App\Models\Admin::isAccess(\App\Models\Admin::$ADD_LOAN_PAYMENT)){
                        $isLoaninstallment = 1;
                    }
                }
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isLoaninstallment' => $isLoaninstallment,
                        'isviewloan' =>1,//\App\Models\Admin::isAccess(\App\Models\Admin::$VIEW_LEDGER),
                    ]
                )->render();
            })->rawColumns(['action','firstname','created_at','status']) 
                    
            ->make(true);        
    }
    
    
}
