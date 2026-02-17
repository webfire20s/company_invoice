<?php
session_start();
require '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Login Credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md">

        <!-- Card -->
        <div class="bg-white shadow-xl rounded-2xl p-8 border">

            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-slate-800">
                    Admin Login
                </h2>
                <p class="text-sm text-slate-500 mt-2">
                    Access your billing dashboard
                </p>
            </div>

            <?php if(isset($error)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
                    <?= $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">

                <div>
                    <label class="block text-sm font-medium mb-2">
                        Username
                    </label>
                    <input type="text"
                           name="username"
                           required
                           class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">
                        Password
                    </label>
                    <input type="password"
                           name="password"
                           required
                           class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition">
                    Login
                </button>

            </form>

        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            © <?= date('Y') ?> Billing System. All rights reserved.
        </p>

    </div>

</body>
</html>
