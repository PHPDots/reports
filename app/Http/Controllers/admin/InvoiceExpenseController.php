<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InvoiceExpense;
use App\Models\Expense;
use App\Models\Invoice;
use Excel;
use Datatables;

class InvoiceExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
    
        $this->moduleRouteText = "invoice-expense";
        $this->moduleViewName = "admin.invoice_expense";
        $this->list_url = route($this->moduleRouteText.".index");  

        $this->modelObj = new InvoiceExpense();

        view()->share("list_url", $this->list_url);
        view()->share("moduleRouteText", $this->moduleRouteText);
        view()->share("moduleViewName", $this->moduleViewName);
    }

    public function index()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_INVOICE_EXPENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $data = array();        
        $data['page_title'] = "Invoices Payment Status Log";

        return view($this->moduleViewName.".index", $data);         
    }

    public function data(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$LIST_INVOICE_EXPENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $model = InvoiceExpense::select(TBL_INVOICE_EXPENSE.".*",TBL_INVOICE.".invoice_no as invoice_no")
                ->join(TBL_INVOICE,TBL_INVOICE_EXPENSE.".invoice_id","=",TBL_INVOICE.".id");

        $model1 = InvoiceExpense::select(TBL_INVOICE_EXPENSE.".*",TBL_INVOICE.".invoice_no as invoice_no")
                ->join(TBL_INVOICE,TBL_INVOICE_EXPENSE.".invoice_id","=",TBL_INVOICE.".id");
        
        $model1 = InvoiceExpense::listFilter($model1);
        $totalamounts = $model1->sum("amount");
        $totalamounts = number_format($totalamounts,2);
        $data = Datatables::of($model)

            ->editColumn('created_at', function($row){
                
                if(!empty($row->created_at))
                    return date("j M, Y h:m:s A",strtotime($row->created_at));
                else
                    return '-';    
            })
            ->editColumn('payment_status', function($row){
                $html = '';
                if($row->payment_status == 1)
                    $html = '<a class="btn btn-primary btn-xs">Full</a>';
                else
                    $html = '<a class="btn btn-danger btn-xs">Partials</a>';
                return $html;
            })
            ->rawColumns(['created_at','payment_status'])
          
            ->filter(function ($query) 
            {
                $query = InvoiceExpense::listFilter($query);
            });
            
            $data = $data->with('amounts',$totalamounts);
            $data = $data->make(true);

            return $data;
    }

    public function get_expense_view()
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DOWNLOAD_INVOICE_EXPENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }

        $data = array();
        return view($this->moduleViewName.".download_expense", $data);
    }
    public function download_expense(Request $request)
    {
        $checkrights = \App\Models\Admin::checkPermission(\App\Models\Admin::$DOWNLOAD_INVOICE_EXPENSE);
        
        if($checkrights) 
        {
            return $checkrights;
        }
        $is_download_xls = request()->get('is_download');
        $expense_action = request()->get("expense_action");

        if (!empty($is_download_xls) && $is_download_xls == 1)
        {
            if($expense_action == 'invoice' || $expense_action == 'expense' || $expense_action == 'all')
            {
                //Invoice Query
            $invoice_query = Invoice::select(TBL_INVOICE.".*",TBL_CLIENT.".name as client_name",TBL_CLIENT.".client_type as client_type",TBL_CLIENT.".gstn_no as gstn_no")
                        ->join(TBL_CLIENT,TBL_CLIENT.".id","=",TBL_INVOICE.".client_id");

            $invoice_query = Invoice::listFilter($invoice_query);
            $invoice_query = $invoice_query->orderBy('invoice_date');
            $invoice_data = $invoice_query->get();

            //Expense Query
            $expense_query = Expense::query();
            $expense_query = Expense::listFilter($expense_query);
            $expense_query = $expense_query->orderBy('date');
            $expense_data = $expense_query->get();

            $filename = 'InvoiceExpense';
            if(true)
            {
                $expense_sheet = Excel::create($filename, function($excel) use ($expense_data,$invoice_data){

                $expense_action = request()->get("expense_action");
                $first_sheet_name = 'Sales';
                $second_sheet_name = 'Expense';
                $invoice_record = [];
                $expense_record = [];
                if(true)
                {
                    if(true)
                    {
                        $amount_recevied = '';
                        $payment_recevied_date = '';
                        $cflag = '';
                        $total_cgst = '';
                        $total_sgst = '';
                        $total_igst = '';
                        $bill_name = '';
                        $party_gstn_no = '';
                        $i = 1;
                        $inv_border = 1;
                        $row_manipulate = array();
                        /*Calculation for invoice data*/
                        $invoice_title[] = array("No./month","Company Name.","","Party GSTN No.","Invoice No.","Invoice Date","Taxable Value","CGST 9% Tax","SGST 9% Tax","IGST 18% Tax","Total Value (rs.)","Total Value (usd)","Rate","Amount Received (INR)","Payment Received Date");
                        foreach ($invoice_data as $row)
                        {
                            $total_taxable_value = '';
                            $total_tax = '';
                            $total_value_rs = '';
                            $total_value_usd = '';
                            $party_gstn_no = $row->gstn_no;
                            if($row->client_type == 2){
                                $bill_name = 'EXPORT BILL';
                            }
                            else{
                                $bill_name = 'LOCAL BILL';
                            }

                            if($row->currency == 'in_rs')
                            {
                                $total_taxable_value = floatval($row->total_without_gst);
                                $total_tax = floatval($row->total_with_gst);
                                $total_value_rs = floatval($row->total_amount);
                                $total_cgst = floatval($row->cgst_amount);
                                $total_sgst = floatval($row->sgst_amount);
                                $total_igst = floatval($row->igst_amount);
                            }
                            else{
                                $total_value_usd = $row->total_amount;
                                $total_value_usd = floatval($total_value_usd);
                            }

                            $invoice_exp = InvoiceExpense::where('invoice_id',$row->id)->orderBy('payment_date','desc');
                            $invoice_exp_data = $invoice_exp->get();
                        
                            if(count($invoice_exp_data) > 0)
                            {
                                $amount_recevied = $invoice_exp->sum('amount');
                                $payment_recevied_date = $invoice_exp->first();
                                $payment_recevied_date = date('d-m-Y',strtotime($payment_recevied_date->payment_date));
                            }
                            $invoice_month = date('M-y',strtotime($row->invoice_date));
                            $invoice_date = date('d-m-Y',strtotime($row->invoice_date));
                            $client_name = ucfirst($row->client_name);
                            
                            if($cflag != $invoice_month)
                            {
                                $row_manipulate[] = $inv_border+1;
                                $invoice_record[] = [$invoice_month,'','','','','','','','','','','','','',''];
                                $cflag = date('M-y',strtotime($row->invoice_date));  
                                $inv_border++;
                                $i = 1;
                            }
                               
                            $invoice_record[] = [$i, $client_name, $bill_name, $party_gstn_no, $row->invoice_no, $invoice_date, $total_taxable_value,$total_cgst,$total_sgst,$total_igst,$total_value_rs,$total_value_usd,'',$amount_recevied,$payment_recevied_date];
                            $i++;
                            $inv_border++;
                        }
                        /*Calaculation for expense data*/
                        $j = 1;
                        $exp_border = 1;
                        $expense_title[] = array("Sr. No.","Description","Amount"," TDS ","Date","Invoice No.");
                        foreach ($expense_data as $expense_row)
                        {
                            $exp_desc = $expense_row->description_bill;
                            $exp_amount = floatval($expense_row->amount);
                            $exp_tds = floatval($expense_row->gst_amount);
                            $exp_date = date('d-m-Y',strtotime($expense_row->date));
                            $exp_invoice_no = $expense_row->invoice_no;
                            
                            $expense_record[] = [$j, $exp_desc, $exp_amount, $exp_tds, $exp_date, $exp_invoice_no];
                            $j++;
                            $exp_border++;
                        }

                        if($expense_action == 'invoice' || $expense_action == 'all')
                        {
                            $excel->sheet($first_sheet_name, function($sheet) use($invoice_title,$invoice_record,$inv_border,$row_manipulate) {
                                
                                $sheet->cell('A1:O1', function($cell) {
                                    $cell->setAlignment('center');
                                    $cell->setBackground('#dde3e6');
                                    $cell->setFont(array('family'=> 'Calibri','size'=>'11','bold'=>true));
                                });
                                foreach ($row_manipulate as $key)
                                {
                                    $sheet->row($key, function($row) {
                                        $row->setAlignment('center');
                                        $row->setFont(array('family'=> 'Calibri','size'=>'11','bold'=>true));
                                    });
                                }
                                $sheet->setColumnFormat(array(
                                    'F' => 'dd-mm-yyyy',
                                    'G' => '#,##0.00',
                                    'H' => '#,##0.00',
                                    'I' => '#,##0.00',
                                    'J' => '#,##0.00',
                                    'K' => '#,##0.00',
                                    'L' => '#,##0.00',
                                    'N' => '#,##0.00',
                                    'O' => 'dd-mm-yyyy',
                                ));
                                $sheet->freezeFirstRow();
                                $sheet->setAutoSize(true);
                                //$sheet->setBorder('A1:M'.$inv_border, 'thin');
                                $sheet->fromArray($invoice_title, null, 'A1', false, false);
                                $sheet->fromArray($invoice_record, null, 'A1', true, false);
                            });
                        }
                        if($expense_action == 'expense' || $expense_action == 'all')
                        {
                            $excel->sheet($second_sheet_name, function($sheet) use($expense_title,$expense_record,$exp_border) {
                    
                                $sheet->cell('A1:F1', function($cell) {
                                    $cell->setAlignment('center');
                                    $cell->setBackground('#dde3e6');
                                    $cell->setFont(array('family'=> 'Calibri','size'=>'11','bold'=>true));
                                });
                                
                                $sheet->setColumnFormat(array(
                                    'C' => '#,##0.00',
                                    'D' => '#,##0.00',
                                    'E' => 'dd-mm-yyyy',
                                ));
                                $sheet->freezeFirstRow();
                                //$sheet->setAutoSize(true);
                                $sheet->setSize(array(
                                    'A1' => array('width'=> 10,'height'=> 15),
                                    'B1' => array('width'=> 40,'height' => 15),
                                    'C1' => array('width'=> 20,'height' => 15),
                                    'D1' => array('width'=> 20,'height' => 15),
                                    'E1' => array('width'=> 20,'height' => 15),
                                    'F1' => array('width'=> 35,'height' => 15),
                                ));

                                //$sheet->setBorder('A1:F'.$exp_border, 'thin');
                                $sheet->fromArray($expense_title, null, 'A1', false, false);
                                $sheet->fromArray($expense_record, null, 'A1', false, false);
                            });
                        }
                    }
                }
                });
                $expense_sheet->download('xlsx');
            }
            }else
            {
                session()->flash('error_message','Please enter valid action data !');
                return redirect()->back();       
            }
            
        }
        else
        {
            session()->flash('error_message','Please enter valid data !');
            return redirect()->back();
        }
        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
