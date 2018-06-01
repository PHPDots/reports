<!DOCTYPE html
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Offer Letter</title>
<!-- Bootstrap -->
<!-- <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=;:300italic,400italic|Poppins:400,300,500,600,700"> -->
<!-- <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet"> -->
<style>
table, tr, td {
  color:#000;
  font-size:15px;
  /*font-family: 'Poppins', Arial, Helvetica, sans-serif;*/
font-family:Arial, Helvetica, sans-serif;
  border:none;
  line-height:1.1;
  padding:0;
  margin:0;
}
td {
  padding:2px
}
strong {
  font-weight:600;
}
p{line-height:1.5; font-size:14px;}
</style>
<style type="text/css">
<!--
-->
</style>
</head>
<body style="padding:20px 10px 10px; margin:5px; font-size:14px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="color:#000; font-size:14px;"><tr><td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="color:#000; font-size:15px;">
  <tr>
    <td align="center" valign="top" height="40"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
      <tr>
        <td width="75%" align="left" valign="top" style="padding:4px;"><img src="http://reports.phpdots.com/images/pd-logo-lg.png" alt="PhpDots Technology" width="" height=""></td>
        <td width="25%" align="right" valign="middle" style="padding:4px;"><a href="www.phpdots.com" style="color:#1586d4; text-decoration:none;">www.phpdots.com</a></td>
      </tr>
    </table></td>
  </tr>
  <hr/>
  <tr>
    <td align="center" valign="middle"><h2 style="padding:0; margin:5px 0 0;">JOB OFFER LETTER</h2></td>
  </tr>
  <tr>
    <td height="20" align="left" valign="top">&nbsp;</td>
    </tr>
  <tr>
    <td height="20" align="left" valign="top">
    <p><strong><span style="color:#383838;">Dear Mr. {{ $record['name'] }},</span></strong></p>
      <p>Congratulations! We are pleased to confirm that you have  been selected to work for PHPDots Technologies. We are delighted to make you  the following job offer. </p>
      <p>The position we are offering you is that of {{ $record['designation'] }} at a monthly salary of Rs. {{ number_format($record['salary']) }} with an annual cost to  company Rs {{ number_format($record['annual']) }} CTC. Your working  hours will be from 9:30 AM to 7:00 PM, Monday to Saturday. We have alternate  Saturday week off. Benefits for the position include, Paid Leave 1 per month after  3 months’ probation period.</p>
      <p>We would like you to start work on Joining Date at 9:30 AM.  Please report to Jitendra Rathod on start date, for documentation and  orientation. If this date is not acceptable, please contact me immediately. </p>
      <p>Please sign the enclosed copy of this letter and return it  to me to indicate your acceptance of this offer. </p>
      <p>We are confident you will be able to make a  significant contribution to the success of our PHPDots Technologies and look  forward to working with you.</p> </td>
  </tr>
  <tr>
    <td height="80" align="center" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="top">
  <p style="font-size:14px; color:#383838"><strong>Thanking You.</strong></p>
    <p style="font-size:14px; color:#383838"><strong>Yours Faithfully</strong><br>
  <strong>For PHPDots Technologies</strong></p></td>
  </tr>
  <hr/>
  <tr>
    <td align="left" valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td width="68%" align="left" valign="middle"><p style="font-size:13px; color:#666666">PHPDots Technologies</p></td>
      <td width="32%" align="left" valign="middle"></td>
    </tr>

  </table></td>
    </tr>
  <tr>
    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="68%" align="left" valign="middle">
        <p style="font-size:13px; color:#666666">B/206, Dev  Aurum, Opposite Commerce House, Anandnagar Road</p>
      </td>
      <td width="32%" align="right" valign="middle">
        <p style="font-size:13px; color:#0a6eb7; text-decoration:none;">jitendra.rathod@phpdots.com</p>
      </td>
    </tr>
    <tr>
      <td width="68%" align="left" valign="middle">
        <p>Prahlad  Nagar, Ahmedabad, Gujarat 380015, India</p>
      </td>
      <td width="32%" align="right" valign="middle">
        <a href="www.phpdots.com" style="font-size:13px; color:#0a6eb7; text-decoration:none;">www.phpdots.com</a>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="right">
        <p>9825096687</p>
      </td>
    </tr>
  </table></td>
    </tr>
  <tr>
    <td align="left" valign="top"></td>
    </tr>
  <tr>
    <td align="left" valign="top">&nbsp;</td>
  </tr>
  
  <tr>
    <td align="left" valign="top"></td>
  </tr>
  <tr>
    <td align="left" valign="top"> </td>
  </tr>
  
</table>  
<em style="line-height:1"></em></td>
  </tr>
</table>
</body>
</html>