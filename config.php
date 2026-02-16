<?php
$conn = new mysqli("localhost", "root", "", "billing_system");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
?>
