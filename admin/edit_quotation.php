<?php
require 'auth.php';
require '../config.php';

if(!isset($_GET['id'])){
header("Location: dashboard.php");
exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM quotations WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
header("Location: dashboard.php");
exit;
}

$q = $result->fetch_assoc();

/* UPDATE */

if($_SERVER['REQUEST_METHOD']=="POST"){

$client_name = $_POST['client_name'];
$date = $_POST['date'];

$features = $_POST['features'];
$technical_features = $_POST['technical_features'];
$notes = $_POST['notes'];

$total_amount = floatval($_POST['total_amount']);

$stmt = $conn->prepare("UPDATE quotations SET
client_name=?,
date=?,
features=?,
technical_features=?,
notes=?,
total_amount=?
WHERE id=?");

$stmt->bind_param(
"sssssdi",
$client_name,
$date,
$features,
$technical_features,
$notes,
$total_amount,
$id
);

$stmt->execute();

/* regenerate quotation */

header("Location: regenerate_quotation.php?id=".$id);
exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Quotation</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-3xl mx-auto mt-10 bg-white p-8 rounded shadow">

<h2 class="text-2xl font-bold mb-6">Edit Quotation</h2>

<form method="POST" class="space-y-6">

<div>
<label class="font-semibold">Quotation Number</label>
<input type="text"
value="<?= $q['quotation_number'] ?>"
disabled
class="border p-2 w-full bg-gray-100">
</div>

<div>
<label class="font-semibold">Client Name</label>
<input type="text"
name="client_name"
value="<?= htmlspecialchars($q['client_name']) ?>"
required
class="border p-2 w-full">
</div>

<div>
<label class="font-semibold">Date</label>
<input type="date"
name="date"
value="<?= $q['date'] ?>"
required
class="border p-2 w-full">
</div>

<div>
<label class="font-semibold">Features</label>
<textarea
name="features"
rows="5"
class="border p-2 w-full"><?= htmlspecialchars($q['features'] ?? '') ?></textarea>
</div>

<div>
<label class="font-semibold">Technical Features</label>
<textarea
name="technical_features"
rows="5"
class="border p-2 w-full"><?= htmlspecialchars($q['technical_features'] ?? '') ?></textarea>
</div>

<div>
<label class="font-semibold">Notes</label>
<textarea
name="notes"
rows="4"
class="border p-2 w-full"><?= htmlspecialchars($q['notes'] ?? '') ?></textarea>
</div>

<div>
<label class="font-semibold">Total Amount</label>
<input type="number"
step="0.01"
name="total_amount"
value="<?= $q['total_amount'] ?>"
required
class="border p-2 w-full">
</div>

<div class="flex gap-4 pt-4">

<button class="bg-blue-600 text-white px-6 py-2 rounded">
Update Quotation
</button>

<a href="dashboard.php"
class="bg-gray-400 text-white px-6 py-2 rounded">
Cancel
</a>

</div>

</form>

</div>

</body>
</html>