<?php
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use Datatables;
use App\Models\Ledger;
use App\Models\Loans;
use App\Models\AdminAction;
use App\Models\Member;
use App\Models\MemberAccounts;
use App\Models\MultipleAccount;
use Illuminate\Validation\Rule;
class LedgerController extends Controller
{
	 public function __construct() {

        $this->moduleRouteText = "ledger";
        $this->moduleViewName = "admin.ledger";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Ledger";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Ledger();  

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
    	$checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LEDGER_LIST);
    	if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Ledger";
        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_LEDGER);
        $data['members'] = Member::getaddledgerMembers();
        $data['back_url']='multiple-account';
        return view($this->moduleViewName.".index", $data); 

    }

	public function create()
    {
        
    }

    public function store(Request $request)
    {    
        
    }

    public function edit($id)
    {    
       
    }
    public function update(Request $request, $id)
    {
        
    }
    public function show($id)
    {
        //
    }
    public function destroy($id,Request $request)
    {     
        
    }
    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LEDGER_LIST);
        
        if($checkrights) 
        {
            return $checkrights;
        }
       $bb_account_id = request()->get("bb_account_id");
       $model =Ledger::select(TBL_LEDGER.".*", TBL_MEMBER.".firstname",TBL_MEMBER.".middlename",TBL_MEMBER.".lastname") 
            ->join(TBL_BB_ACOUNT, TBL_LEDGER.".bb_account_id", "=", TBL_BB_ACOUNT.".id")
            ->join(TBL_LOAN_BACHAT, TBL_BB_ACOUNT.".bb_bachat_id", "=", TBL_LOAN_BACHAT.".id")
            ->join(TBL_MEMBER, TBL_LOAN_BACHAT.".member_id", "=", TBL_MEMBER.".id")
            //->where(TBL_LEDGER.".bb_account_id", $bb_account_id)
            ->orderBy('firstname', 'asc');
    
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
                                              
            ->filter(function ($query) 
            {  
                $bb_account_id = request()->get("bb_account_id");
                if(!empty($bb_account_id))
                {
                    $query = $query->where(TBL_LEDGER.".bb_account_id", $bb_account_id);
                } 
            })
            ->make(true); 

    }
    
    
}
