<?php
require 'auth.php';
require '../config.php';

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM invoices WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

if(!$invoice){
die("Invoice not found");
}

if($_SERVER['REQUEST_METHOD']=="POST"){

$client_name = $_POST['client_name'];
$client_email = $_POST['client_email'];
$client_mobile = $_POST['client_mobile'];
$client_address = $_POST['client_address'];
$description = $_POST['description'];

$amount = isset($_POST['amount']) ? (float) $_POST['amount'] : 0;
$discount = isset($_POST['discount']) ? (float) $_POST['discount'] : 0;
$renewal_charge = isset($_POST['renewal_charge']) ? (float) $_POST['renewal_charge'] : 0;

$status = $_POST['status'];
$date = $_POST['date'];

$total = ($amount) - $discount;

$stmt = $conn->prepare("UPDATE invoices SET
client_name=?,
client_email=?,
client_mobile=?,
client_address=?,
description=?,
amount=?,
discount=?,
renewal_charge=?,
status=?,
date=?,
total_amount=?
WHERE id=?");

$stmt->bind_param(
"sssssdddssdi",
$client_name,
$client_email,
$client_mobile,
$client_address,
$description,
$amount,
$discount,
$renewal_charge,
$status,
$date,
$total,
$id
);

$stmt->execute();

header("Location: regenerate_invoice.php?id=".$id);
exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Invoice</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-3xl mx-auto mt-10 bg-white p-8 rounded shadow">

<h2 class="text-xl font-bold mb-6">Edit Invoice</h2>

<form method="POST" class="grid grid-cols-2 gap-4">

<input type="text" name="client_name" value="<?= $invoice['client_name']?>" placeholder="Client Name" class="border p-2">

<input type="email" name="client_email" value="<?= $invoice['client_email']?>" placeholder="Client Email" class="border p-2">

<input type="text" name="client_mobile" value="<?= $invoice['client_mobile']?>" placeholder="Mobile" class="border p-2">

<input type="date" name="date" value="<?= $invoice['date']?>" class="border p-2">

<textarea name="client_address"placeholder="Client Address" class="border p-2 col-span-2"><?= $invoice['client_address']?></textarea>

<textarea name="description" placeholder="Description" class="border p-2 col-span-2"><?= $invoice['description']?></textarea>

<input type="number"  name="amount" value="<?= $invoice['amount']?>" placeholder="Amount" class="border p-2">

<input type="number"  name="discount" value="<?= $invoice['discount']?>" placeholder="Discount" class="border p-2">

<input type="number"  name="renewal_charge" value="<?= $invoice['renewal_charge']?>" placeholder="Renewal Charge" class="border p-2">

<input type="text" name="status" value="<?= $invoice['status']?>" placeholder="Status" class="border p-2">

<div class="col-span-2 flex gap-4 mt-4">

<button class="bg-blue-600 text-white px-5 py-2 rounded">
Update Invoice
</button>

<a href="dashboard.php" class="bg-gray-400 text-white px-5 py-2 rounded">
Cancel
</a>

</div>

</form>

</div>

</body>
</html>