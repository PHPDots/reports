<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Datatables;
use Validator; 
use App\Models\AdminAction;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Expense;
use App\Models\InvoiceExpense;
use PDF;
use Excel;

class InvoicesController extends Controller
{
     public function __construct() {

        $this->moduleRouteText = "invoices";
        $this->moduleViewName = "admin.invoices";
        $this->list_url = route($this->moduleRouteText.".index");

        $module = "Invoice";
        $this->module = $module;  

        $this->adminAction= new AdminAction; 
        
        $this->modelObj = new Invoice();  

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
    public function index(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Manage Invoices";

        $data['add_url'] = route($this->moduleRouteText.'.create');
        $data['btnAdd'] = \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_INVOICE);        
        $dates = \DB::table(TBL_INVOICE)->select(\DB::raw("MIN(invoice_date)as mindate,MAX(invoice_date) as maxdate"))->get();
         
        foreach ($dates as $date) {
            $mindate = $date->mindate;
            $maxdate = $date->maxdate;
        } 
        $start_date = $mindate;
        $end_date = date('Y-m-d h:m:s');

        while (strtotime($start_date) <= strtotime($end_date))
        {
            $start_date = date('Y-M',strtotime($start_date));
            $data['months'][date('Y-m',strtotime($start_date))] = $start_date; 
            $start_date = date ("Y-M", strtotime("+1 month", strtotime($start_date)));
        }
        
        $data['clients'] = Client::pluck("name","id")->all();
        $auth_id = \Auth::guard('admins')->user()->user_type_id;
        if($auth_id == CLIENT_USER){
          
            $viewName = $this->moduleViewName.".clientIndex";
        }else{
            //Check Admin Type
            $auth_id = \Auth::guard("admins")->user()->id;
            $authUser = \Auth::guard("admins")->user();
            $auth_user =  superAdmin($auth_id);
            if($auth_user == 0 && $authUser->user_type_id != ACCOUNT_USER) 
            {
                return Redirect('/dashboard');
            }
            if($request->get("changeID") > 0)
            {
                $goto = session()->get($this->moduleRouteText.'_goto');
                if(empty($goto)){  $goto = $this->list_url;  }

            $invoice_id = $request->get("changeID");   
            $payment = $request->get("changeStatus");

            $request = Invoice::find($invoice_id);
                if($request)
                {
                    $status = $request->payment;

                    if($status == 0)
                        $status = 1;
                    else
                        $status = 0;
                    

                    $request->payment = $status;
                    $request->save();            

                        session()->flash('success_message', "Payment Status has been changed successfully.");
                        return redirect($goto);
                }
                else
                {
                    session()->flash('success_message', "Payment Status not changed, Please try again");
                    return redirect($goto);
                }

            return redirect("invoices");
            }
        
            $viewName = $this->moduleViewName.".index";
        }
        $data = customSession($this->moduleRouteText,$data, 100);
        return view($viewName, $data);        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_INVOICE);
        
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
        $data["currency"] = ['in_rs'=>'In Rs.','in_usd'=>'In USD','in_gbp'=>'In GBP'];
        $last_invoice = Invoice::select('invoice_no','id')->where('invoice_no','NOT LIKE','%exp-%')->orderBy('created_at','DES')->first();
            $this_year = date('Y');
            $next_year = $this_year + 1;
            if(!empty($last_invoice)){
                $last_invoice = str_replace("exp-","",$last_invoice->invoice_no);
                $last_no =  explode("/",$last_invoice);
                $no = $last_no[2] + 1;
                $invoice_no = 'PDots/'.$this_year.'-'.$next_year.'/'.$no; 
            }else{
                $invoice_no = 'PDots/'.$this_year.'-'.$next_year.'/1';
            }
        $data['invoice_no'] = $invoice_no;
        $data['address'] = \Config('app.phpdots_address');
        $data['clients'] = Client::pluck("name","id")->all();
        $data = customBackUrl($this->moduleRouteText, $this->list_url, $data);
        $copytocreate = $request->get('copytocreate');
        if(!empty($copytocreate))
        {
            $invoice = Invoice::find($copytocreate);
            if($invoice)
            {
                $data["currency"] = ['in_rs'=>'In Rs.','in_usd'=>'In USD','in_gbp'=>'In GBP'];
                $client = Client::find($invoice->client_id);
                if($client)
                {
                    $client_type = $client->client_type;
                    if($client_type == 1)
                    {
                        $last_invoice = Invoice::select('invoice_no','id')->where('invoice_no','NOT LIKE','%Exp-%')->orderBy('created_at','DES')->first();
                        $this_year = date('Y');
                        $next_year = $this_year + 1;
                        if(!empty($last_invoice))
                        {
                            $last_invoice = str_replace("Exp-","",$last_invoice->invoice_no);
                            $last_no =  explode("/",$last_invoice);
                            $no = $last_no[2] + 1;
                            $invoice_no = 'PDots/'.$this_year.'-'.$next_year.'/'.$no; 
                        }
                    }
                    if ($client_type == 2)
                    {
                        $last_invoice = Invoice::select('invoice_no','id')->where('invoice_no','LIKE','%Exp-%')->orderBy('created_at','DES')->first();

                        $this_year = date('Y');
                        $next_year = $this_year + 1;
                        if(!empty($last_invoice))
                        {
                            $last_invoice = str_replace("Exp-","",$last_invoice->invoice_no);
                            $last_no =  explode("/",$last_invoice);
                            $no = $last_no[2] + 1;
                            $invoice_no = 'PDots/'.$this_year.'-'.$next_year.'/Exp-'.$no;
                        }
                    }
                    $data['invoice_detail'] = InvoiceDetail::where('invoice_id',$copytocreate)->get();
                    $data['invoice_no'] = $invoice_no;
                    $data['formObj'] = $invoice;
                }
                return view($this->moduleViewName.'.copyAdd', $data);
            }
            return redirect()->back();
        }
        return view($this->moduleViewName.'.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$ADD_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }      
        $data = array();
        $status = 1;
        $msg = $this->addMsg;
        $goto = $this->list_url;

        $validator = Validator::make($request->all(), [
            'address' => 'required|min:2',
            'to_address' => 'required|min:2',
            'invoice_no' => 'required|unique:'.TBL_INVOICE.',invoice_no',
            'invoice_date' => 'required',
            'sac_code' => 'required',
            'amount.*' => 'required|numeric',
            'particular.*' => 'required|min:2',
            'cgst_amount' => 'required|numeric',
            'sgst_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'require_gst' => Rule::in([1, 0]),
            'currency' => ['required',Rule::in(['in_rs','in_usd','in_gbp'])],
            'client_id' => 'required|exists:'.TBL_CLIENT.',id',
            'require_igst' => Rule::in([1, 0]),
            'igst_amount' => 'required|numeric',
        ]);
        
        // check validations
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
            $to_address = $request->get('to_address');
            $invoice_no = $request->get('invoice_no');
            $invoice_date = $request->get('invoice_date');
            $invoice_date = date("Y-m-d h:i:s",strtotime($invoice_date));
            $cgst_amount = $request->get('cgst_amount');
            $sgst_amount = $request->get('sgst_amount');
            $total_amount = $request->get('total_amount');
            $total_amount_words = $request->get('total_amount_words');
            $address = $request->get('address');
            $require_gst = $request->get('require_gst');
            $currency = $request->get('currency');
            $client_id = $request->get('client_id');
            $total_with_gst = $request->get('total_with_gst');
            $total_without_gst = $request->get('total_without_gst');
            $require_igst = $request->get('require_igst');
            $igst_amount = $request->get('igst_amount');
            $pan_no = 'AAUFP4850D';
            $gst_regn_no = '24AAUFP4850D1Z3';
            $bank_account_no = '201001635127';
            $bank_name = 'Induslnd BANK';
            $bank_swift_code = 'INDBINBBAHA';
            $ifsc_code = 'INDB0000232';
            
            $invoice = new Invoice();
            $invoice->to_address = $to_address;
            $invoice->invoice_no = $invoice_no;
            $invoice->invoice_date = $invoice_date;
            $invoice->cgst_amount = $cgst_amount;
            $invoice->sgst_amount = $sgst_amount;
            $invoice->total_amount = $total_amount;
            $invoice->total_amount_words = $total_amount_words;
            $invoice->address = $address;
            $invoice->pan_no = $pan_no;
            $invoice->gst_regn_no = $gst_regn_no;
            $invoice->bank_account_no = $bank_account_no;
            $invoice->bank_name = $bank_name;
            $invoice->bank_swift_code = $bank_swift_code;
            $invoice->ifsc_code = $ifsc_code;
            $invoice->require_gst = $require_gst;
            $invoice->currency = $currency;
            $invoice->client_id = $client_id;
            $invoice->total_with_gst = $total_with_gst;
            $invoice->total_without_gst = $total_without_gst;
            $invoice->require_igst = $require_igst;
            $invoice->igst_amount = $igst_amount;
            $invoice->save();
            $invoice_id = $invoice->id;

            $particular = $request->get('particular');
            $amount = $request->get('amount');
            $max = count($particular);
            
            for ($i=0; $i < $max; $i++) {

                $detail = new InvoiceDetail(); 
                $detail->invoice_id = $invoice_id; 
                $detail->particular = $particular[$i]; 
                $detail->amount = $amount[$i]; 
                $detail->save(); 
            }

            $id = $invoice->id;
            $send_id = $request->get('send_id');
            if($send_id == 1)
            {
                $data = array();
                $data['invoices'] = Invoice::where('id',$invoice_id)->first();
                $data['invoice_details'] = InvoiceDetail::where('invoice_id',$invoice_id)->get();
            
                $filename = 'invoice_'.$invoice_id.'.pdf';
                $pdfFilePath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'invoices/'.$filename;
                $pdf = PDF::loadView('pdf.invoice', $data);
                $pdf->save($pdfFilePath);
                
                //$pdfFilePath = public_path().'/uploads/invoices/invoice.pdf';
                $client = \App\Models\Client::find($client_id);
                if($client)
                {
                    if(is_file($pdfFilePath))
                    {
                        $temp = array();
                        $temp['client_name'] = $client->name;
                        $temp['invoice_no'] = $invoice_no;
                        $temp['invoice_date'] = $invoice_date;
                        $subject = 'PHPdots : Invoice '.date('d-M-y',strtotime($invoice_date));
                        $message = view('emails.invoice',$temp)->render();
                        
                        $file[1]['path'] = $pdfFilePath;

                        $params["to"]= $client->email;
                        $params["subject"] = $subject;
                        //$params["from"] = '';
                        $params["from_name"] = 'PHPdots : Invoice';  
                        $params["files"] = $file;
                        $params["body"] = $message;
                        sendHtmlMail($params);
                    }
                }
            }

            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->ADD_INVOICE ;
            $params['actionvalue']  = $id;
            $params['remark']       = "Add Invoice ::".$id;
                                    
            $logs=\App\Models\AdminLog::writeadminlog($params);

            session()->flash('success_message', $msg);

        }
        
        return ['status' => $status, 'msg' => $msg, 'data' => $data, 'goto' => $goto];

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }     
        $formObj = $this->modelObj->find($id);

        if(!$formObj)
        {
            abort(404);
        }   

        $data = array();
        $data['formObj'] = $formObj;
        $data['page_title'] = "Edit ".$this->module;
        $data['buttonText'] = "Update";
        $data['action_url'] = $this->moduleRouteText.".update";
        $data['action_params'] = $formObj->id;
        $data['method'] = "PUT";
        $data['invoice_detail'] = InvoiceDetail::where('invoice_id',$id)->get();
        $data["currency"] = ['in_rs'=>'In Rs.','in_usd'=>'In USD','in_gbp'=>'In GBP'];
        $data['clients'] = Client::pluck("name","id")->all();
        $data = customBackUrl($this->moduleRouteText, $this->list_url, $data);

        return view($this->moduleViewName.'.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$EDIT_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }      
        $model = $this->modelObj->find($id);

        $data = array();
        $status = 1;
        $msg = $this->updateMsg;
        $goto = session()->get($this->moduleRouteText.'_goto');
        if(empty($goto)){  $goto = $this->list_url;  }

        $validator = Validator::make($request->all(), [
            'address' => 'required|min:2',
            'to_address' => 'required|min:2',
            'invoice_no' => 'required|unique:'.TBL_INVOICE.',invoice_no,'.$id,
            'invoice_date' => 'required',
            'sac_code' => 'required',
            'amount.*' => 'required|numeric',
            'particular.*' => 'required|min:2',
            'cgst_amount' => 'required|numeric',
            'sgst_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'require_gst' => Rule::in([1, 0]),
            'currency' => ['required',Rule::in(['in_rs','in_usd','in_gbp'])],
            'client_id' => 'required|exists:'.TBL_CLIENT.',id',
            'igst_amount' => 'required|numeric',
            'require_igst' => Rule::in([1, 0]),
        ]);
        
        // check validations
        if(!$model)
        {
            $status = 0;
            $msg = "Record not found !";
        }
        else if ($validator->fails()) 
        {
            $messages = $validator->messages();
            
            $status = 0;
            $msg = "";
            
            foreach ($messages->all() as $message) 
            {
                $msg .= $message . "<br />";
            }
        }else
        {
            $address = $request->get('address');
            $to_address = $request->get('to_address');
            $invoice_no = $request->get('invoice_no');
            $invoice_date = $request->get('invoice_date');
            $invoice_date = date("Y-m-d h:i:s",strtotime($invoice_date));
            $cgst_amount = $request->get('cgst_amount');
            $sgst_amount = $request->get('sgst_amount');
            $total_amount = $request->get('total_amount');
            $total_amount_words = $request->get('total_amount_words');
            $require_gst = $request->get('require_gst');
            $currency = $request->get('currency');
            $client_id = $request->get('client_id');
            $total_with_gst = $request->get('total_with_gst');
            $total_without_gst = $request->get('total_without_gst');
            $require_igst = $request->get('require_igst');
            $igst_amount = $request->get('igst_amount');
            $pan_no = 'AAUFP4850D';
            $gst_regn_no = '24AAUFP4850D1Z3';
            $bank_account_no = '201001635127';
            $bank_name = 'Induslnd BANK';
            $bank_swift_code = 'INDBINBBAHA';
            $ifsc_code = 'INDB0000232';
            
            $model->to_address = $to_address;
            $model->invoice_no = $invoice_no;
            $model->invoice_date = $invoice_date;
            $model->cgst_amount = $cgst_amount;
            $model->sgst_amount = $sgst_amount;
            $model->total_amount = $total_amount;
            $model->total_amount_words = $total_amount_words;
            $model->address = $address;
            $model->pan_no = $pan_no;
            $model->gst_regn_no = $gst_regn_no;
            $model->bank_account_no = $bank_account_no;
            $model->bank_name = $bank_name;
            $model->bank_swift_code = $bank_swift_code;
            $model->ifsc_code = $ifsc_code;
            $model->require_gst = $require_gst;
            $model->currency = $currency;
            $model->client_id = $client_id;
            $model->total_with_gst = $total_with_gst;
            $model->total_without_gst = $total_without_gst;
            $model->igst_amount = $igst_amount;
            $model->require_igst = $require_igst;
            $model->save(); 

            $invoice_details = InvoiceDetail::where('invoice_id',$id);
            $invoice_details->delete();

            $particular = $request->get('particular');
            $amount = $request->get('amount');
            $max = count($particular);
            
            for ($i=0; $i < $max; $i++) {

                $detail = new InvoiceDetail(); 
                $detail->invoice_id = $id; 
                $detail->particular = $particular[$i]; 
                $detail->amount = $amount[$i]; 
                $detail->save(); 
            }
            //store logs detail
            $params=array();    
                                    
            $params['adminuserid']  = \Auth::guard('admins')->id();
            $params['actionid']     = $this->adminAction->EDIT_INVOICE;
            $params['actionvalue']  = $id;
            $params['remark']       = "Edit Invoice ::".$id;

            $logs=\App\Models\AdminLog::writeadminlog($params);

            session()->flash('success_message', $msg);
        }
        return ['status' => $status, 'msg' => $msg, 'data' => $data, 'goto' => $goto];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0) 
        {
            return Redirect('/dashboard');
        }
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DELETE_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }

       $modelObj = $this->modelObj->find($id);

        if ($modelObj) {
            try {
                $holiData = InvoiceDetail::where('invoice_id', $id);
                $holiData->delete();
                
                $backUrl = $request->server('HTTP_REFERER');
                $goto = session()->get($this->moduleRouteText.'_goto');
                if(empty($goto)){  $goto = $this->list_url;  }
                $modelObj->delete();
                session()->flash('success_message', $this->deleteMsg);

                //store logs detail
                    $params=array();    
                                            
                    $params['adminuserid']  = \Auth::guard('admins')->id();
                    $params['actionid']     = $this->adminAction->DELETE_INVOICE;
                    $params['actionvalue']  = $id;
                    $params['remark']       = "Delete Invoice ::".$id;
                                            
                    $logs=\App\Models\AdminLog::writeadminlog($params);

                return redirect($goto);
            } catch (Exception $e) {
                session()->flash('error_message', $this->deleteErrorMsg);
                return redirect($this->list_url);
            }
        } else {
            session()->flash('error_message', "Record not exists");
            return redirect($this->list_url);
        }
    }

    public function data(Request $request)
    {
        //Check Admin Type
        $auth_id = \Auth::guard("admins")->user()->id;
        $authUser = \Auth::guard("admins")->user();
        $auth_user =  superAdmin($auth_id);
        if($auth_user == 0 && $authUser->user_type_id != ACCOUNT_USER) 
        {
            return Redirect('/dashboard');
        }
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $model = Invoice::select(TBL_INVOICE.".*",TBL_CLIENT.".name as client_name")
                ->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_INVOICE.".client_id");

        $amount_query1 = Invoice::select(TBL_INVOICE.".*",TBL_CLIENT.".name as client_name")
                ->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_INVOICE.".client_id");
        //$amount_query2 = Invoice::select(TBL_INVOICE.".*",TBL_CLIENT.".name as client_name")
                //->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_INVOICE.".client_id");
        
        $amount_query2 = Invoice::select(TBL_INVOICE.".*",TBL_CLIENT.".name as client_name",TBL_INVOICE_EXPENSE.'.partial_amount as unpaid_amount',TBL_INVOICE_EXPENSE.'.amount as paid_amount',TBL_INVOICE_EXPENSE.'.payment_status')
                ->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_INVOICE.".client_id")
                ->leftJoin(TBL_INVOICE_EXPENSE,TBL_INVOICE.".id","=",TBL_INVOICE_EXPENSE.".invoice_id");

        $amount_query1 = Invoice::listFilter($amount_query1);
        $amount_query2 = Invoice::listFilter($amount_query2);
        
        $totalamounts = $amount_query1->where('currency','in_rs')->sum("total_amount");
        //$totalamountsUSD = $amount_query2->where('currency','in_usd')->sum("total_amount");
        $totalamountsUSD = $amount_query2->get();
        
        $invoice_id = 0;
        $current_amount = 0;
        $remaining_amount = 0;
        $total_amount_rs = 0;
        $current_amount_usd = 0;
        
        $total_paid_amt = 0;
        $total_unpaid_amt = 0;;
        $current_amount_usd = 0;
        $current_amount_inr = 0;
        $total_amount_rs = 0;
        $totalamounts   = 0;

        if(!empty($totalamountsUSD))
        {
            foreach ($totalamountsUSD as $row)
            {
               /* if(!empty($invoice_id)) {
                    echo " Display Amount ::".$totalamounts;
                }
                echo "<br> Total Amount :: ".$row->total_amount."<=Paid $==>".$row->unpaid_amount."<==Paid RS==>".$row->paid_amount."<==Payment Status==>".$row->payment_status."===>";*/

                if($invoice_id != $row->id) {

                    if(!empty($invoice_id)) {
                        $total_paid_amt += $total_amount_rs;
                        if(!empty($current_amount_usd)) {
                            $total_amount_rs += ($current_amount_usd * CURRENCY_USD);
                            $current_amount_usd = 0;
                        }

                        if(!empty($current_amount_inr)) {
                            $total_amount_rs += $current_amount_inr;
                            $current_amount_inr = 0;
                        }

                        $totalamounts += $total_amount_rs;
                    }

                    $total_amount_rs = 0;
                    $invoice_id = $row->id;

                    if($row->currency == 'in_usd') {
                        $current_amount_usd = $row->total_amount;
                    } else {
                        $current_amount_inr = $row->total_amount;
                    }
                }

                if(!empty($row->unpaid_amount) || !empty($row->payment_status)) {
                    $total_amount_rs += $row->paid_amount;
                    if($row->currency == 'in_usd') {
                        if(!empty($row->payment_status)) {
                            $current_amount_usd = 0;
                        } else {
                            $current_amount_usd = $current_amount_usd - $row->unpaid_amount;
                        }
                    } else {
                        if(!empty($row->payment_status)) {
                            $current_amount_inr = 0;
                        } else {
                            $current_amount_inr = $current_amount_inr - $row->unpaid_amount;
                        }
                    }
                }
            }
        }

        $total_paid_amt += $total_amount_rs;

        if(!empty($current_amount_usd)) {
            $total_amount_rs += ($current_amount_usd * CURRENCY_USD);
        }

        if(!empty($current_amount_inr)) {
            $total_amount_rs += $current_amount_inr;
        }

        $totalamounts += $total_amount_rs;

        $total_unpaid_amt = $totalamounts - $total_paid_amt;
        
        /*echo "\r\n:: Total Amount :: ".$totalamounts." :: Paid :: ".$total_paid_amt." :: UnPaid :: ".$total_unpaid_amt;
        die;*/
        
        $total_arr['total_paid_amt'] = number_format($total_paid_amt,0);
        $total_arr['total_unpaid_amt'] = number_format($total_unpaid_amt,0);
        
        //$totalamountsUSD = $totalamountsUSD * CURRENCY_USD;
        //$totalamounts = $totalamounts + $totalamountsUSD; 
        $totalamounts = number_format($totalamounts,0);

        $data = \Datatables::eloquent($model)
            ->editColumn('created_at', function($row){                
                if(!empty($row->created_at))          
                    return date("j M, Y",strtotime($row->created_at));
                else
                    return '-';    
            })
            ->editColumn('payment', function ($row) { 
                if ($row->payment == 1){
                    return "<a class='btn btn-xs btn-success'>Paid</a><br/>";
                }
                else{
                    return '<a class="btn btn-xs btn-danger">UnPaid</a><br/>';
                }
            })
            ->editColumn('invoice_date', function($row){
                if(!empty($row->invoice_date))          
                    return date("M-Y",strtotime($row->invoice_date));
                else
                    return '-';
            })
            ->addColumn('action', function(Invoice $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_INVOICE),
                        'inPDF' =>\App\Models\Admin::isAccess(\App\Models\Admin::$LIST_INVOICE),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_INVOICE),
                        'isView' => \App\Models\Admin::isAccess(\App\Models\Admin::$LIST_INVOICE), 
                        'payment' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_INVOICE),
                        'viewExpe' => \App\Models\Admin::isAccess(\App\Models\Admin::$LIST_INVOICE_EXPENSE),
                        'copyInvoice' => \App\Models\Admin::isAccess(\App\Models\Admin::$ADD_INVOICE)
                    ]
                )->render();
            })->rawColumns(['action','created_at','payment','invoice_date'])
            ->filter(function ($query) {
                $query = Invoice::listFilter($query);                 
            });
            
            $total_arr['amounts'] = $totalamounts;
            $data = $data->with($total_arr);

            $data = $data->make(true);

            return $data;        
    }
     function download_invoice(Request $request) 
    {
        $auth_id = \Auth::guard('admins')->user()->id;
        $authUser = \Auth::guard('admins')->user();
        $auth_user =  superAdmin($auth_id);

        $invoice_id = $request->get('invoice_id');
        
        $data = array();

        if(!empty($invoice_id)){
            $invoices = Invoice::where('id',$invoice_id)->first();
            $invoice_details = InvoiceDetail::where('invoice_id',$invoice_id)->get();
        
            $client_type = 0;
            $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                $client_type = $client_user->client_id;
            }           
            
            if(($invoices && $invoices->client_id == $client_type) || superadmin($auth_id) || $authUser->user_type_id == ACCOUNT_USER)  
            {
                $name = $invoices->invoice_date;
                $data['invoices'] = $invoices;
                $data['invoice_details'] = $invoice_details;
                $pdf = PDF::loadView('pdf.invoice', $data);

                return $pdf->download("invoice_".$name.".pdf");
            }
        }
        else{
            abort(404);
        }
    }

     public function viewData(Request $request)
    {   

        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $auth_id = \Auth::guard('admins')->user()->id;
        $authUser = \Auth::guard('admins')->user();
        $auth_user =  superAdmin($auth_id); 
        
        $invoice_id = $request->get('invoice_id');
        
        $data = array();

        if(!empty($invoice_id))
        {
            $invoices = Invoice::where('id',$invoice_id)->first();
            $invoice_details = InvoiceDetail::where('invoice_id',$invoice_id)->get();

            $client_type = 0;
            $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                $client_type = $client_user->client_id;
            }
           
            if(($invoices && $invoices->client_id == $client_type) || $auth_user == 1 || $authUser->user_type_id == ACCOUNT_USER)
            {    
                $data['invoices'] = $invoices;
                $data['invoice_details'] = $invoice_details;
                return view("pdf.invoice", $data);
            }
            else{
                abort(404);
            }
        }
    }
    public function clientData(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_INVOICE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $client_type = 0;
        $client_id = \Auth::guard('admins')->user()->client_user_id;
            $client_user = ClientUser::find($client_id);
            if(!empty($client_user))
            {
                    $client_type = $client_user->client_id;
            }        
        $model = Invoice::select(TBL_INVOICE.".*")
                ->where(TBL_INVOICE.".client_id",$client_type);
        
        $data = \Datatables::eloquent($model)
            ->editColumn('created_at', function($row){
                if(!empty($row->created_at))          
                    return date("j M, Y h:i:s A",strtotime($row->created_at));
                else
                    return '-';
            })
            ->editColumn('payment', function ($row) { 
                if ($row->payment == 1){
                    return "<a class='btn btn-xs btn-success'>Paid</a><br/>";
                }
                else{
                    return '<a class="btn btn-xs btn-danger">UnPaid</a><br/>';
                }
            }) 
            ->addColumn('action', function(Invoice $row) {
                return view("admin.partials.action",
                    [
                        'currentRoute' => $this->moduleRouteText,
                        'row' => $row, 
                        'isEdit' => \App\Models\Admin::isAccess(\App\Models\Admin::$EDIT_INVOICE),
                        'inPDF' =>\App\Models\Admin::isAccess(\App\Models\Admin::$LIST_INVOICE),
                        'isDelete' => \App\Models\Admin::isAccess(\App\Models\Admin::$DELETE_INVOICE),
                        'isView' => \App\Models\Admin::isAccess(\App\Models\Admin::$LIST_INVOICE),                                                     
                    ]
                )->render();
            })->rawColumns(['action','created_at','payment']);
            $data = $data->make(true);

            return $data;        
    }
    public function client_type(Request $request)
    {
        $data = [];
        $client_id = $request->get('id');
        $request->get('invoice_no');
        $type = '';
        $address = null;
        $currency = '';
        if ($client_id) {

            $client = Client::find($client_id);
            if($client)
            {
                $address = $client->address;
                $currency = $client->client_currency;
                if(empty($currency))
                {
                    $currency = 'in_rs';
                }
                if($currency == 'in_gbp'){
                    $currency = 'in_usd';
                }
                $type = $client->client_type;
                if ($request->get('invoice_no'))
                {
                    $invoice_on_form = $request->get('invoice_no');
                    $invoice_no = $request->get('invoice_no');
                    $invoice_id = $request->get('invoice_id');

                    $expPos = strpos($invoice_no,'Exp-');
                    if($expPos > 0)
                    {
                        if ($type == 2)
                        {
                            $last_invoice = Invoice::select('invoice_no','id')->where('invoice_no','LIKE','%Exp-%')->orderBy('created_at','DES')->first();

                            $this_year = date('Y');
                            $next_year = $this_year + 1;
                            if(!empty($last_invoice))
                            {
                                $last_invoice = str_replace("exp-","",$last_invoice->invoice_no);
                                $last_no =  explode("/",$last_invoice);
                                $no = $last_no[2] + 1;
                                $invoice_no = 'PDots/'.$this_year.'-'.$next_year.'/Exp-'.$no; 
                                
                                if($invoice_id)
                                {
                                    $yes = strpos($invoice_on_form, 'Exp-');
                                    if($yes > 0){
                                        $invoice_no = $invoice_on_form;
                                    }
                                }
                            }
                        }

                        if ($type == 1)
                        {
                            $last_invoice = Invoice::select('invoice_no','id')->where('invoice_no','NOT LIKE','%Exp-%')->orderBy('created_at','DES')->first();
                            $this_year = date('Y');
                            $next_year = $this_year + 1;
                            if(!empty($last_invoice))
                            {
                                $last_invoice = str_replace("Exp-","",$last_invoice->invoice_no);
                                $last_no =  explode("/",$last_invoice);
                                $no = $last_no[2] + 1;
                                $invoice_no = 'PDots/'.$this_year.'-'.$next_year.'/'.$no; 
                            }
                        }
                    }
                    else
                    {
                        if ($type == 2)
                        {
                            $last_invoice = Invoice::select('invoice_no','id')->where('invoice_no','LIKE','%Exp-%')->orderBy('created_at','DES')->first();

                            $this_year = date('Y');
                            $next_year = $this_year + 1;
                            if(!empty($last_invoice))
                            {
                                $last_invoice = str_replace("Exp-","",$last_invoice->invoice_no);
                                $last_no =  explode("/",$last_invoice);
                                $no = $last_no[2] + 1;
                                $invoice_no = 'PDots/'.$this_year.'-'.$next_year.'/Exp-'.$no; 
                                
                                if($invoice_id)
                                {
                                    $yes = strpos($invoice_on_form, 'Exp-');
                                    if($yes > 0){
                                        $invoice_no = $invoice_on_form;
                                    }
                                }
                            }
                        }
                        if ($type == 1)
                        {
                            $invoice_no = $invoice_no;
                        }
                    }
                }
                
                $data['invoice_no'] = $invoice_no;
                $data['address'] = $address;
                $data['currency'] = $currency;
                return $data;
            }
            else {
                $data['error'] = "Client not found";
                return $data;
            }
        }
    }
    public function change_paymet_satus(Request $request)
    {
        $data = array();
        $status = 1;
        $msg = 'Amount has been added Successfully !';
        $goto = session()->get($this->moduleRouteText.'_goto');
        if(empty($goto)){  $goto = $this->list_url;  }
        
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:'.TBL_INVOICE.',id',
            'payment_status' => ['required',Rule::in([1,0])],
            'amount' => 'required|min:0',
            'payment_date' => 'required',
            'partial_amount' => 'min:0',
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
        }else
        {
            $invoice_id = $request->get('invoice_id');
            $payment_status = $request->get('payment_status');
            $amount = $request->get('amount');
            $payment_date = $request->get('payment_date');
            $partial_amount = $request->get('partial_amount');
            if($payment_status == 1)
                $partial_amount = 0;
            
            $invoice = Invoice::find($invoice_id);
            if($invoice)
            {
                $exp = new InvoiceExpense();       
                $exp->invoice_id = $invoice_id;
                $exp->payment_status = $payment_status;
                $exp->partial_amount = $partial_amount;
                $exp->amount = $amount;
                $exp->payment_date = $payment_date;
                $exp->save();
                
                if(!empty($payment_status) && $payment_status == 1)
                { 
                    $invoice->payment = 1;
                    $invoice->save();
                }else
                {
                    $invoice->payment = 0;
                    $invoice->save();
                }
                session()->flash('success_message', $msg);
            }
            else
            {
                $status = 0;
                $msg ='Record not found !';
                return ['status' => $status, 'msg' => $msg, 'goto' => $goto];
            }
        }
        return ['status' => $status,'msg' => $msg, 'goto' => $goto];
    }
}
