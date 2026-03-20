<?php
session_start();
require 'config.php';

// 🔒 ROLE-BASED SECURITY (NEW)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: admin/login.php");
    exit;
}

// ✅ KEEP EXISTING LOGIC SAFE
$staff_id = $_SESSION['staff_id'];

$result = $conn->query("
SELECT project_name, progress, id,
client_name, domain_name, client_email, client_mobile, city,
notes
FROM projects 
WHERE staff_id = $staff_id
ORDER BY id DESC
");
$month = date('m');
$year = date('Y');

/* TOTAL PROJECTS (MONTH) */
$total_projects = $conn->query("
SELECT COUNT(*) as total 
FROM projects 
WHERE staff_id = $staff_id 
AND MONTH(created_at) = $month 
AND YEAR(created_at) = $year
")->fetch_assoc()['total'];

/* TOTAL REVENUE (PAID) */
$total_revenue = $conn->query("
SELECT SUM(project_amount) as total 
FROM projects 
WHERE staff_id = $staff_id 
AND payment_status = 'Paid'
")->fetch_assoc()['total'] ?? 0;

/* TOTAL PENDING */
$total_pending = $conn->query("
SELECT SUM(project_amount) as total 
FROM projects 
WHERE staff_id = $staff_id 
AND payment_status = 'Pending'
")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Panel | Billing Pro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .data-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        /* Hide scrollbar while keeping functionality */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        tr:hover td { background-color: #f8fafc; }
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

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto no-scrollbar">
            <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Staff Menu</p>
            
            <a href="staff_panel.php" 
               class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'staff_panel.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="font-medium">My Projects</span>
            </a>

            <a href="create_project_staff.php" 
               class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'create_project_staff.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="folder-plus" class="w-5 h-5"></i>
                <span class="font-medium">New Project</span>
            </a>

            <div class="pt-8 mt-8 border-t border-slate-900">
                <a href="admin/logout.php" 
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
        <header class="bg-white border-b border-slate-200 px-10 py-6 sticky top-0 z-20 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Welcome, <?= $_SESSION['staff_name'] ?></h2>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Staff Workspace / Project Overview</p>
            </div>
            <a href="create_project_staff.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Create Project</span>
            </a>
        </header>
        <div class="p-10 pt-6 grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- TOTAL PROJECTS -->
            <div class="bg-white rounded-2xl shadow p-6 border">
                <p class="text-xs text-slate-500">This Month Projects</p>
                <h2 class="text-2xl font-bold text-blue-600">
                    <?= $total_projects ?>
                </h2>
            </div>

            <!-- REVENUE -->
            <div class="bg-white rounded-2xl shadow p-6 border">
                <p class="text-xs text-slate-500">Total Revenue</p>
                <h2 class="text-2xl font-bold text-green-600">
                    ₹<?= number_format($total_revenue,2) ?>
                </h2>
            </div>

            <!-- PENDING -->
            <div class="bg-white rounded-2xl shadow p-6 border">
                <p class="text-xs text-slate-500">Pending Amount</p>
                <h2 class="text-2xl font-bold text-red-600">
                    ₹<?= number_format($total_pending,2) ?>
                </h2>
            </div>

        </div>

        <div class="p-10">
            <div class="data-card rounded-3xl shadow-xl overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h3 class="font-bold text-slate-700">My Assigned Projects</h3>
                    <span class="text-[10px] bg-slate-100 px-3 py-1 rounded-full font-bold text-slate-500 uppercase">Live Tracking</span>
                </div>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[11px] font-bold uppercase text-slate-500">Project</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase text-slate-500">Client</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase text-slate-500">Progress</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase text-slate-500">Notes</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase text-slate-500 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>

                            <!-- PROJECT -->
                            <td class="px-8 py-5">
                                <span class="font-bold text-slate-700 block">
                                    <?= $row['project_name'] ?>
                                </span>
                                <span class="text-xs text-slate-400">
                                    <?= $row['domain_name'] ?? '' ?>
                                </span>
                            </td>

                            <!-- CLIENT -->
                            <td class="px-8 py-5">
                                <div class="text-sm font-semibold text-slate-700">
                                    <?= $row['client_name'] ?? 'N/A' ?>
                                </div>
                                <div class="text-xs text-slate-400">
                                    <?= $row['client_email'] ?? '' ?>
                                </div>
                                <div class="text-xs text-slate-400">
                                    <?= $row['client_mobile'] ?? '' ?>
                                </div>
                                <div class="text-xs text-slate-400">
                                    <?= $row['city'] ?? '' ?>
                                </div>
                            </td>

                            <!-- PROGRESS -->
                            <td class="px-8 py-5">
                                <div class="w-full max-w-[160px]">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-[10px] font-black text-blue-600">
                                            <?= $row['progress'] ?>%
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-1.5 rounded-full">
                                        <div class="bg-blue-600 h-full"
                                            style="width: <?= $row['progress'] ?>%">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <!-- NOTES -->
                            <td class="px-8 py-5">
                                <div class="text-xs text-slate-600 max-w-[250px] truncate">
                                    <?= !empty($row['notes']) ? $row['notes'] : 'No updates yet' ?>
                                </div>
                            </td>

                            <!-- ACTION -->
                            <td class="px-8 py-5 text-right">
                                <a href="update_project_staff.php?id=<?= $row['id'] ?>"
                                class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-800 font-bold text-sm">
                                    <span>Update</span>
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                            </td>

                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>