<?php

require '../config.php';

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM quotations WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header("Location: dashboard.php");
    exit;
}

$quotation = $result->fetch_assoc();

/* simulate POST so generator works normally */

$_POST['quotation_number'] = $quotation['quotation_number'];
$_POST['client_name'] = $quotation['client_name'];
$_POST['date'] = $quotation['date'];
$_POST['features'] = $quotation['features'];
$_POST['technical_features'] = $quotation['technical_features'];
$_POST['project_cost'] = $quotation['total_amount'];
$_POST['notes'] = $quotation['notes'];

/* run original quotation generator */

require '../generate_quotation.php';