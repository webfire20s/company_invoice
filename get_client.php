<?php
require 'config.php';

$q = $_GET['q'] ?? '';

$result = $conn->query("
SELECT * FROM clients 
WHERE client_name LIKE '%$q%' 
OR client_code LIKE '%$q%'
LIMIT 5
");

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);