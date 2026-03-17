<?php
session_start();
require 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM staff WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($user = $result->fetch_assoc()){
        if(password_verify($password,$user['password'])){
            $_SESSION['staff_id'] = $user['id'];
            $_SESSION['staff_name'] = $user['name'];

            header("Location: staff_panel.php");
            exit;
        }
    }

    $error = "Invalid credentials";
}
?>

<!DOCTYPE html>

<html>
<head>
<title>Staff Login</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 flex items-center justify-center h-screen">

    <form method="POST" class="bg-white p-6 rounded-xl shadow w-80 space-y-4">

        <h2 class="text-lg font-semibold text-center">Staff Login</h2>

        <?php if(isset($error)): ?>

        <p class="text-red-500 text-sm"><?= $error ?></p>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" required class="w-full border p-2 rounded">

        <input type="password" name="password" placeholder="Password" required class="w-full border p-2 rounded">

        <button class="w-full bg-blue-600 text-white py-2 rounded">Login</button>

    </form>

</body>
</html>
