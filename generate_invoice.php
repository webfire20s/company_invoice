<?php
require __DIR__ . '/vendor/autoload.php';
require 'amount_in_words.php';
$date = date("Y-m-d");

use Dompdf\Dompdf;

/* ================= INPUT ================= */

$client_name    = $_POST['client_name'];
$client_email   = $_POST['client_email'];
$client_mobile  = $_POST['client_mobile'];
$client_address = $_POST['client_address'];
$description    = $_POST['description'];
$amount         = floatval($_POST['amount']);
$discount       = floatval($_POST['discount']);
$status         = $_POST['status'];

$total = $amount - $discount;
$amount_words = numberToWords($total);

/* ================= SEQUENTIAL INVOICE NUMBER ================= */

$invoice_file = "invoice_counter.txt";

if(!file_exists($invoice_file)){
    file_put_contents($invoice_file, "132"); // starting from WD000132
}

$current_number = intval(file_get_contents($invoice_file));
$new_number = $current_number + 1;

file_put_contents($invoice_file, $new_number);

$invoice_number = "WD" . str_pad($new_number,6,"0",STR_PAD_LEFT);
$invoice_date = date("D, d F Y");

/* ================= LOGO UPLOAD ================= */

$logoPath = __DIR__ . '/logos/company_logo.png';

$logoData = '';

if(file_exists($logoPath)){
    $imageType = pathinfo($logoPath, PATHINFO_EXTENSION);
    $imageData = base64_encode(file_get_contents($logoPath));
    $logoData = 'data:image/'.$imageType.';base64,'.$imageData;
}


/* ================= WATERMARK ================= */

$watermarkColor = ($status == "Paid") ? "rgba(0,150,0,0.15)" : "rgba(200,0,0,0.15)";

/* ================= HTML TEMPLATE ================= */

$html = '
<html>
<head>
<style>

body {
    font-family: DejaVu Sans, sans-serif;
    margin: 40px;
    font-size: 13px;
}
img {
    max-height:80px;
    width:auto;
    object-fit:contain;
}


.header-table {
    width: 100%;
}

.logo {
    height: 80px;
}

.invoice-title {
    text-align: center;
    font-size: 26px;
    font-weight: bold;
    margin: 20px 0;
}

.section {
    margin-top: 20px;
}

.two-column {
    width: 100%;
}

.left {
    width: 55%;
    float: left;
}

.right {
    width: 40%;
    float: right;
}

.clear {
    clear: both;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.table th, .table td {
    border: 1px solid #000;
    padding: 8px;
}

.table th {
    text-align: left;
}

.total-row {
    font-weight: bold;
}

.watermark {
    position: fixed;
    top: 40%;
    left: 20%;
    font-size: 100px;
    transform: rotate(-30deg);
    color: '.$watermarkColor.';
    z-index: 9999;
    opacity: 0.5;
    pointer-events: none;
}

.footer {
    margin-top: 60px;
    text-align: right;
    font-weight: bold;
}

</style>
</head>

<body>

<div class="watermark">'.strtoupper($status).'</div>

<div class="invoice-title">Invoice</div>


<table style="width:100%; margin-bottom:20px;">
<tr>
<td style="vertical-align:top; background-color:#F5F5F5;">
    <h2 style="margin:0;">Invoice-'.$invoice_number.'</h2>
    <div style="margin-top:5px;">
        Invoice Number: '.$invoice_number.'<br>
        Invoice Date: '.$invoice_date.'
    </div>
</td>

<td style="text-align:right; vertical-align:top;">';
if($logoData != ''){
    $html .= '<img src="'.$logoData.'" style="max-height:80px; width:auto;">';
}


$html .= '
</td>
</tr>
</table>



<div class="section two-column">
<div class="left">
<strong>Branch Address :</strong><br>
1/206, SUHAGNAGAR,<br>
Firozabad, Uttar Pradesh, India<br>
webfiredegitech@gmail.com<br>
Uttar Pradesh State Code: 09
</div>

<div class="right">
<strong>Bank Details :</strong><br>
Bank Name: CANARA BANK<br>
Account Name: Webfire Degitech<br>
Account Number: 110046974278<br>
IFSC Code: CNRB0006341<br>
UPI ID: 8434636013@hdfcbank
</div>

<div class="clear"></div>
</div>

<div class="section">
<strong>Invoiced To</strong><br>
'.$client_name.'<br>
Mob. : '.$client_mobile.'<br>
'.$client_email.'<br>
'.$client_address.'
</div>

<table class="table">
<tr style=background-color:#F5F5F5;>
<th>Description</th>
<th style="text-align:right;">Total</th>
</tr>

<tr>
<td>'.$description.'</td>
<td style="text-align:right;">Rs.'.number_format($amount,2).' INR</td>
</tr>

<tr style=background-color:#F5F5F5;>
<td>Discount</td>
<td style="text-align:right;background-color:#F5F5F5;">Rs.'.number_format($discount,2).' INR</td>
</tr>

<tr class="total-row" style=background-color:#F5F5F5;>
<td>Total Amount</td>
<td style="text-align:right; background-color:#F5F5F5;">Rs.'.number_format($total,2).' INR</td>
</tr>
</table>

<div class="section" style=background-color:#F5F5F5;>
<strong>Total Amount (in words):</strong><br>
'.$amount_words.'
</div>

<div class="section">
<strong>Next Year Renewal</strong><br>
Website Renewal Charge : 1200
</div>

<div class="footer">
WEBFIRE DEGITECH
</div>

</body>
</html>
';

/* ================= PDF GENERATION ================= */

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();

$file_name = __DIR__."/invoices/".$invoice_number.".pdf";
file_put_contents($file_name, $dompdf->output());

require 'config.php';

$stmt = $conn->prepare("INSERT INTO invoices 
(invoice_number, client_name, date, total_amount, file_path) 
VALUES (?, ?, ?, ?, ?)");

$stmt->bind_param("sssds", 
    $invoice_number, 
    $client_name, 
    $date, 
    $total_amount, 
    $file_name
);

$stmt->execute();


$dompdf->stream($invoice_number.".pdf", ["Attachment" => true]);
exit;
