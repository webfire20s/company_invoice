<?php

require '../config.php';

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM invoices WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header("Location: dashboard.php");
    exit;
}

$invoice = $result->fetch_assoc();

/* simulate POST request for generator */

$_POST['invoice_number'] = $invoice['invoice_number'];
$_POST['client_name']    = $invoice['client_name'];
$_POST['client_email']   = $invoice['client_email'];
$_POST['client_mobile']  = $invoice['client_mobile'];
$_POST['client_address'] = $invoice['client_address'];
$_POST['description']    = $invoice['description'];
$_POST['amount']         = $invoice['amount'];
$_POST['discount']       = $invoice['discount'];
$_POST['renewal_charge'] = $invoice['renewal_charge'];
$_POST['status']         = $invoice['status'];

/* run original generator */

require '../generate_invoice.php';