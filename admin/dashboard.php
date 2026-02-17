<?php
require 'auth.php';
require '../config.php';

$search = $_GET['search'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col">
        <div class="p-6 flex items-center space-x-3">
            <div class="bg-blue-600 p-2 rounded-lg">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <span class="text-xl font-bold">Billing System</span>
        </div>

        <nav class="flex-1 px-4 space-y-2 mt-4">
            <a href="dashboard.php"
               class="flex items-center space-x-3 bg-blue-600 px-4 py-3 rounded-xl">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>

            <a href="index.php"
               class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="file-plus" class="w-5 h-5"></i>
                <span>Create Invoice</span>
            </a>

            <a href="quotation_form.php"
               class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                <span>Create Quotation</span>
            </a>

            <div class="pt-8">
                <a href="logout.php"
                   class="flex items-center space-x-3 text-red-400 hover:bg-red-500/10 px-4 py-3 rounded-xl">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">

        <!-- Top Bar -->
        <header class="bg-white border-b px-8 py-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold">Admin Dashboard</h2>

            <form method="GET" class="relative">
                <input type="text"
                       name="search"
                       value="<?= htmlspecialchars($search) ?>"
                       placeholder="Search client name..."
                       class="pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-64">
                <i data-lucide="search"
                   class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            </form>
        </header>

        <div class="p-8 space-y-10">

            <!-- Invoices -->
            <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                <div class="p-6 border-b flex justify-between items-center">
                    <h4 class="font-bold text-slate-800 flex items-center">
                        <i data-lucide="file-text" class="w-5 h-5 mr-2 text-blue-600"></i>
                        Invoices
                    </h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Invoice No</th>
                                <th class="px-6 py-4">Client</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">

<?php
$stmt = $conn->prepare("SELECT * FROM invoices 
                        WHERE client_name LIKE CONCAT('%', ?, '%')
                        ORDER BY id DESC");
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()):
?>

<tr class="hover:bg-slate-50">
    <td class="px-6 py-4 font-mono text-blue-600">
        <?= $row['invoice_number'] ?>
    </td>
    <td class="px-6 py-4"><?= $row['client_name'] ?></td>
    <td class="px-6 py-4"><?= $row['date'] ?></td>
    <td class="px-6 py-4 font-semibold">
        ₹<?= $row['total_amount'] ?>
    </td>
    <td class="px-6 py-4 text-right">
        <a href="../<?= $row['file_path'] ?>"
           target="_blank"
           class="inline-flex items-center bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-100">
            <i data-lucide="eye" class="w-3.5 h-3.5 mr-1"></i>
            View
        </a>
    </td>
</tr>

<?php endwhile; ?>

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quotations -->
            <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                <div class="p-6 border-b">
                    <h4 class="font-bold text-slate-800 flex items-center">
                        <i data-lucide="clipboard-list" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Quotations
                    </h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Quotation No</th>
                                <th class="px-6 py-4">Client</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">

<?php
$stmt = $conn->prepare("SELECT * FROM quotations 
                        WHERE client_name LIKE CONCAT('%', ?, '%')
                        ORDER BY id DESC");
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()):
?>

<tr class="hover:bg-slate-50">
    <td class="px-6 py-4 font-mono text-indigo-600">
        <?= $row['quotation_number'] ?>
    </td>
    <td class="px-6 py-4"><?= $row['client_name'] ?></td>
    <td class="px-6 py-4"><?= $row['date'] ?></td>
    <td class="px-6 py-4 font-semibold">
        ₹<?= $row['total_amount'] ?>
    </td>
    <td class="px-6 py-4 text-right">
        <a href="../<?= $row['file_path'] ?>"
           target="_blank"
           class="inline-flex items-center bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-indigo-100">
            <i data-lucide="eye" class="w-3.5 h-3.5 mr-1"></i>
            View
        </a>
    </td>
</tr>

<?php endwhile; ?>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>
