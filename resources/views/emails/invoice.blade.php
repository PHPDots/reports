<h3>Hi {{ ucfirst($client_name) }}, </h3>
<p>Please find your attached invoice. We appreciate your prompt payment.</p>
<p>Invoice Number : {{ $invoice_no }}</p>
<p>Invoice Date : {{ date('d-M-y',strtotime($invoice_date)) }}</p>