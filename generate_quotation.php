<?php
require __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;

/* ================= INPUT ================= */

$client_name = $_POST['client_name'];
$subject = $_POST['subject'];
$introduction = nl2br($_POST['introduction']);
$features = explode("\n", $_POST['features']);
$technical = explode("\n", $_POST['technical_features']);
$payment_terms = nl2br($_POST['payment_terms']);
$project_cost = floatval($_POST['project_cost']);

$date = date("d-m-Y");
$year = date("Y");

/* ================= QUOTATION NUMBER ================= */

$counter_file = "quotation_counter.txt";

if(!file_exists($counter_file)){
    file_put_contents($counter_file, "15");
}

$current = intval(file_get_contents($counter_file));
$new = $current + 1;
file_put_contents($counter_file, $new);

$quotation_number = "WD-$year-" . str_pad($new,4,"0",STR_PAD_LEFT);

/* ================= LOGO ================= */

$logoPath = __DIR__ . '/logos/company_logo.png';
$logoData = '';

if(file_exists($logoPath)){
    $imageType = pathinfo($logoPath, PATHINFO_EXTENSION);
    $imageData = base64_encode(file_get_contents($logoPath));
    $logoData = 'data:image/'.$imageType.';base64,'.$imageData;
}

/* ================= BUILD FEATURE LIST ================= */

$featureList = "";
foreach($features as $f){
    $featureList .= "<li>".trim($f)."</li>";
}

$technicalList = "";
foreach($technical as $t){
    $technicalList .= "<li>".trim($t)."</li>";
}

/* ================= HTML ================= */

$html = '
<html>
<head>
<style>
@page {
    margin: 20px;
}
body {
    font-family: DejaVu Sans, sans-serif;
    margin: 40px;
    font-size: 13px;
    border: 2px solid #000;
    padding: 20px;
    box-sizing: border-box;
}

.header-table {
    width:100%;
}

.logo {
    max-height:80px;
}

.info-table {
    width:100%;
    border-collapse: collapse;
    margin-top:20px;
}

.info-table td {
    border:1px solid #000;
    padding:6px;
}

.section {
    margin-top:20px;
}

ul {
    margin-top:5px;
}

.bold {
    font-weight:bold;
}

</style>
</head>

<body>

<table class="header-table">
<tr>
<td>';

if($logoData != ''){
    $html .= '<img src="'.$logoData.'" class="logo">';
}

$html .= '
</td>
<td align="right">
Address : 1/206,SUHAGNAGAR,<br>
Firozabad, Uttar Pradesh, India<br>
Phone: +91 9105190096, +91 9456614612<br>
Email: webfiredegitech@gmail.com<br>
Website: webfiredegitech.com
</td>
</tr>
</table>

<table class="info-table">
<tr>
<td><strong>Company/Client</strong></td>
<td>'.$client_name.'</td>
<td><strong>Date</strong></td>
<td>'.$date.'</td>
</tr>
<tr>
<td><strong>Subject</strong></td>
<td>'.$subject.'</td>
<td><strong>Quotation No.</strong></td>
<td>'.$quotation_number.'</td>
</tr>
</table>

<div class="section">
Dear Client,<br><br>
'.$introduction.'
</div>

<div class="section bold">Features:</div>
<ul>
'.$featureList.'
</ul>

<div class="section bold">Technical Features:</div>
<ul>
'.$technicalList.'
</ul>

<div class="section bold">Pricing & Payment Terms</div>
<div>
Total Project Cost: ₹'.number_format($project_cost,2).'/-<br><br>
'.$payment_terms.'
</div>

<div class="section">
Note:  Domain charges are not included and will be charged extra as per actual cost.<br><br>
<p>We assure you of quality work, timely delivery, and complete support throughout the project. Kindly review
the proposal and let us know your approval so we can proceed further.</p><br>
Looking forward to working with your esteemed organization.<br><br>
</div>
<div class="section">
Note: We provide 3-month free support on any web development project & will charge if any addition of services required.<br><br>
Need More Information, Call us at 9105190096, 9456614612 or Email:
info@webfiredegitech.com , webfiredegitech@gmail.com
</div>

</body>
</html>
';

/* ================= PDF ================= */

$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();

$file_name = __DIR__."/quotations/".$quotation_number.".pdf";
file_put_contents($file_name, $dompdf->output());

require 'config.php';

$stmt = $conn->prepare("INSERT INTO quotations 
(quotation_number, client_name, date, total_amount, file_path) 
VALUES (?, ?, ?, ?, ?)");


$stmt->bind_param("sssds", 
    $quotation_number, 
    $client_name, 
    $date, 
    $total_amount, 
    $file_name
);

$stmt->execute();


$dompdf->stream($quotation_number.".pdf", ["Attachment"=>true]);
exit;
