<?php
require 'auth.php';
require '../config.php';

$search = $_GET['search'] ?? '';

/* ===== NEW FILTERS FROM INDEX ===== */
$type   = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
        }
        .table-container {
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900">

<div class="flex h-screen overflow-hidden">

    <aside class="w-72 bg-slate-950 text-white hidden md:flex flex-col border-r border-slate-800">
        <div class="p-8">
            <div class="flex items-center space-x-3 group cursor-default">
                <div class="bg-blue-600 p-2.5 rounded-xl shadow-lg shadow-blue-500/20">
                    <i data-lucide="layers" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-xl font-bold tracking-tight text-white">Billing Pro</span>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-1">
            <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Main Menu</p>
            
            <a href="../index.php" 
            class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="house   " class="w-5 h-5"></i>
                <span class="font-medium">Home</span>
            </a>

            <a href="dashboard.php" 
            class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="invoice_form.php" 
            class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'invoice_form.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="file-plus" class="w-5 h-5"></i>
                <span class="font-medium">Create Invoice</span>
            </a>

            <a href="quotation_form.php" 
            class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'quotation_form.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                <span class="font-medium">Create Quotation</span>
            </a>
            <a href="create_staff.php"
                class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
                <span>Create Staff</span>
            </a>

            <a href="staff_list.php"
                class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Staff List</span>
            </a>

            <a href="create_project.php"
                class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="folder-plus" class="w-5 h-5"></i>
                <span>Create Project</span>
            </a>

            <a href="projects_list.php"
                class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="folder" class="w-5 h-5"></i>
                <span>Projects</span>
            </a>

            <div class="pt-8 mt-8 border-t border-slate-900">
                <a href="logout.php" 
                class="flex items-center space-x-3 text-red-400 hover:bg-red-500/10 px-4 py-3 rounded-xl transition-all duration-200">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </nav>

        <div class="p-6">
            <div class="bg-slate-900/50 rounded-2xl p-4 border border-slate-800">
                <p class="text-[10px] text-slate-500 font-bold uppercase mb-1">Status</p>
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-xs text-slate-300 font-medium">System Online</span>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        
        <header class="glass-header sticky top-0 z-20 border-b border-slate-200 px-10 py-5 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Data Management</h2>
                <p class="text-xs text-slate-500 font-medium">Viewing results for: <span class="text-blue-600"><?= $type ?: 'All Records' ?></span></p>
            </div>

            <form method="GET" class="relative group">
                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">

                <input type="text" 
                       name="search" 
                       value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search client name..." 
                       class="pl-11 pr-4 py-2.5 bg-slate-100 border-transparent border focus:border-blue-500 focus:bg-white rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:outline-none w-72 transition-all">
                
                <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 group-focus-within:text-blue-500 transition-colors"></i>
            </form>
        </header>

        <div class="p-10 max-w-7xl mx-auto space-y-12">

            <?php if($type != "quotation" || $type == ""): ?>
            <div class="bg-white rounded-3xl table-container overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h4 class="font-bold text-slate-800 flex items-center gap-3">
                        <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </span>
                        Invoices
                    </h4>
                    <span class="text-xs font-semibold px-3 py-1 bg-blue-50 text-blue-700 rounded-full">Recent Transactions</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/50 text-[11px] uppercase tracking-wider font-bold text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-4">Invoice No</th>
                                <th class="px-8 py-4">Client Details</th>
                                <th class="px-8 py-4">Issue Date</th>
                                <th class="px-8 py-4">Amount</th>
                                <th class="px-8 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php
                            $query = "SELECT * FROM invoices WHERE client_name LIKE CONCAT('%', ?, '%')";
                            if($status == "Paid") $query .= " AND status='Paid'";
                            if($status == "Unpaid") $query .= " AND status='Unpaid'";
                            $query .= " ORDER BY id DESC";

                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("s", $search);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()):
                            ?>
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-8 py-5 font-mono text-sm font-semibold text-blue-600">
                                    #<?= $row['invoice_number'] ?>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm font-bold text-slate-700"><?= $row['client_name'] ?></span>
                                </td>
                                <td class="px-8 py-5 text-sm text-slate-500"><?= $row['date'] ?></td>
                                <td class="px-8 py-5">
                                    <span class="text-sm font-extrabold text-slate-900">₹<?= number_format($row['total_amount'], 2) ?></span>
                                </td>
                                <td class="px-8 py-5 text-right space-x-2">
                                    <a href="../<?= $row['file_path'] ?>" target="_blank" 
                                       class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="edit_invoice.php?id=<?= $row['id'] ?>" 
                                       class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all shadow-sm">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <?php if($type == "quotation" || ($type == "" && $status == "")): ?>
            <div class="bg-white rounded-3xl table-container overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h4 class="font-bold text-slate-800 flex items-center gap-3">
                        <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                        </span>
                        Quotations
                    </h4>
                    <span class="text-xs font-semibold px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full">Pending Proposals</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/50 text-[11px] uppercase tracking-wider font-bold text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-4">Quotation No</th>
                                <th class="px-8 py-4">Client Name</th>
                                <th class="px-8 py-4">Generated Date</th>
                                <th class="px-8 py-4">Estimated Total</th>
                                <th class="px-8 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM quotations WHERE client_name LIKE CONCAT('%', ?, '%') ORDER BY id DESC");
                            $stmt->bind_param("s", $search);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()):
                            ?>
                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="px-8 py-5 font-mono text-sm font-semibold text-indigo-600">
                                    QT-<?= $row['quotation_number'] ?>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm font-bold text-slate-700"><?= $row['client_name'] ?></span>
                                </td>
                                <td class="px-8 py-5 text-sm text-slate-500"><?= $row['date'] ?></td>
                                <td class="px-8 py-5 font-extrabold text-slate-900 text-sm">₹<?= number_format($row['total_amount'], 2) ?></td>
                                <td class="px-8 py-5 text-right space-x-2">
                                    <a href="../<?= $row['file_path'] ?>" target="_blank" 
                                       class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="edit_quotation.php?id=<?= $row['id'] ?>" 
                                       class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all shadow-sm">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </main>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>