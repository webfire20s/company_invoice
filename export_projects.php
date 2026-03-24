<?php
session_start();
require 'config.php';

if(!isset($_SESSION['staff_id'])){
    die("Unauthorized");
}

$staff_id = $_SESSION['staff_id'];

$from = $_GET['from_date'] ?? '';
$to   = $_GET['to_date'] ?? '';

if(empty($from) || empty($to)){
    die("Invalid date range");
}

/* HEADERS FOR DOWNLOAD */
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="projects_export_'.$from.'_to_'.$to.'.csv"');

$output = fopen("php://output", "w");

/* CSV HEADERS */
fputcsv($output, [
    'Project Name',
    'Description',
    'Client Name',
    'Domain',
    'Email',
    'Mobile',
    'City',
    'State',
    'Pincode',
    'Total Amount',
    'Paid Amount',
    'Pending Amount',
    'Payment Status',
    'Progress (%)',
    'Notes',
    'Created At'
]);

/* FETCH DATA */
$stmt = $conn->prepare("
    SELECT * FROM projects
    WHERE staff_id = ?
    AND DATE(created_at) BETWEEN ? AND ?
    ORDER BY created_at DESC
");

$stmt->bind_param("iss", $staff_id, $from, $to);
$stmt->execute();

$result = $stmt->get_result();

/* OUTPUT ROWS */
while($row = $result->fetch_assoc()){
    fputcsv($output, [
        $row['project_name'],
        $row['description'],
        $row['client_name'],
        $row['domain_name'],
        $row['client_email'],
        $row['client_mobile'],
        $row['city'],
        $row['state'],
        $row['pincode'],
        $row['project_amount'],
        $row['paid_amount'],
        $row['pending_amount'],
        $row['payment_status'],
        $row['progress'],
        $row['notes'],
        $row['created_at']
    ]);
}

fclose($output);
exit;