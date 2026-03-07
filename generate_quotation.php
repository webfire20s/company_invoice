<?php
require __DIR__ . '/vendor/autoload.php';
$date = date("Y-m-d");

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

/* ================= INPUT ================= */

$client_name = $_POST['client_name'];
$subject = $_POST['subject'];
$introduction = nl2br($_POST['introduction']);
$features = explode("\n", $_POST['features']);
$technical = explode("\n", $_POST['technical_features']);
$payment_terms = nl2br($_POST['payment_terms']);
$project_cost = floatval($_POST['project_cost']);


/* ================= QUOTATION NUMBER ================= */

if(isset($_POST['quotation_number'])){

    /* when regenerating quotation */
    $quotation_number = $_POST['quotation_number'];

} else {

    $counter_file = "quotation_counter.txt";

    if(!file_exists($counter_file)){
        file_put_contents($counter_file, "15");
    }

    $current = intval(file_get_contents($counter_file));
    $new = $current + 1;
    file_put_contents($counter_file, $new);

    $quotation_number = "WD-$year-" . str_pad($new,4,"0",STR_PAD_LEFT);
}

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

/* FIX: This class now repeats the logo on every page because of position: fixed */
.watermark {
    position: fixed;
    top: 25%;
    left: 10%;
    width: 500px;
    opacity: 0.09;
    z-index: -1000;
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
.terms-section {
    margin-top: 25px;
    font-size: 13px;
    line-height: 1.8;
}

.terms-section p {
    margin: 8px 0;
}

.note-red {
    color: #cc0000;
    font-weight: bold;
}

.contact-info {
    margin-top: 15px;
    text-align: center;
}

.contact-info a {
    color: #0645AD;
    text-decoration: underline;
}

</style>
</head>

<body>
<img src="'.$logoData.'" class="watermark">

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

<div class="terms-section">

    <p>
        <span class="note-red">Note:</span>
        Domain charges are not included and will be charged extra as per actual cost.
    </p>

    <p>
        We assure you of <strong>quality work, timely delivery, and complete support</strong> throughout the project.
        Kindly review the proposal and let us know your approval so we can proceed further.
    </p>

    <p>
        Looking forward to working with your esteemed organization.
    </p>

    <p>
        <span class="note-red">Note:</span>
        We provide 3-month free support on any web development project & will charge if any addition of services required.
    </p>

    <div class="contact-info">
        Need More Information, Call us at <strong>9105190096, 9456614612</strong> or Email:<br>
        <a href="mailto:info@webfiredegitech.com">info@webfiredegitech.com</a>,
        <a href="mailto:webfiredegitech@gmail.com">webfiredegitech@gmail.com</a>
    </div>

</div>

</body>
</html>
';

/* ================= PDF GENERATION ================= */

// I merged the options and initialization to prevent duplicate headers error
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();

$filename = $quotation_number . ".pdf";

/* filesystem path (for saving) */
$save_path = __DIR__ . "/quotations/" . $filename;

// Check if directory exists before saving
if (!file_exists(__DIR__ . "/quotations/")) {
    mkdir(__DIR__ . "/quotations/", 0777, true);
}

file_put_contents($save_path, $dompdf->output());

/* public path (store in DB) */
$db_path = "quotations/" . $filename;

if(!isset($_POST['quotation_number'])){

require 'config.php';

$stmt = $conn->prepare("INSERT INTO quotations 
(quotation_number, client_name, date, features, technical_features, notes, total_amount, file_path) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
"ssssssds",
$quotation_number,
$client_name,
$date,
$features,
$technical_features,
$notes,
$project_cost,
$db_path
);

$stmt->execute();

}

$dompdf->stream($quotation_number.".pdf", ["Attachment"=>true]);
exit;