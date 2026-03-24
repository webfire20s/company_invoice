<?php
require 'auth.php';
require '../config.php';

if(!isset($_GET['id'])){
    header("Location: staff_list.php");
    exit;
}

$staff_id = (int) $_GET['id'];

/* STAFF INFO */
$staff = $conn->query("SELECT * FROM staff WHERE id=$staff_id")->fetch_assoc();

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

/* TOTAL VALUE */
$total_value = $conn->query("
SELECT SUM(project_amount) as total 
FROM projects 
WHERE staff_id = $staff_id 
AND MONTH(created_at) = $month 
AND YEAR(created_at) = $year
")->fetch_assoc()['total'] ?? 0;

/* TOTAL REVENUE (ACTUAL RECEIVED) */
$total_revenue = $conn->query("
SELECT SUM(paid_amount) as total 
FROM projects 
WHERE staff_id = $staff_id 
AND MONTH(created_at) = $month 
AND YEAR(created_at) = $year
")->fetch_assoc()['total'] ?? 0;

/* TOTAL PENDING */
$total_pending = $conn->query("
SELECT SUM(pending_amount) as total 
FROM projects 
WHERE staff_id = $staff_id 
AND MONTH(created_at) = $month 
AND YEAR(created_at) = $year
")->fetch_assoc()['total'] ?? 0;

/* ALL PROJECTS OF STAFF */
$projects = $conn->query("
SELECT 
    project_name,
    client_name,
    progress,
    project_amount,
    paid_amount,
    pending_amount,
    payment_status,
    notes,
    created_at
FROM projects
WHERE staff_id = $staff_id
ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Details | Billing Pro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        /* Hide scrollbar for cleaner look but allow scrolling */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900 pb-20">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 pt-6 sm:pt-10">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <a href="staff_list.php" class="inline-flex items-center text-sm font-bold text-blue-600 hover:gap-2 transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to List
            </a>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-800 mt-2 tracking-tight">
                <?= htmlspecialchars($staff['name']) ?>
            </h2>
            <p class="text-sm text-slate-500 font-medium">
                <?= htmlspecialchars($staff['email']) ?>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-4 py-2 bg-blue-50 text-blue-700 text-xs font-bold rounded-xl border border-blue-100">
                Staff Performance
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">This Month</p>
                <i data-lucide="layers" class="w-4 h-4 text-blue-500"></i>
            </div>
            <h2 class="text-2xl font-black text-blue-600">
                <?= $total_projects ?> <span class="text-xs text-slate-400 font-normal">Projects</span>
            </h2>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Value</p>
                <i data-lucide="database" class="w-4 h-4 text-purple-500"></i>
            </div>
            <h2 class="text-2xl font-black text-purple-600">
                ₹<?= number_format($total_value, 2) ?>
            </h2>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Revenue</p>
                <i data-lucide="trending-up" class="w-4 h-4 text-green-500"></i>
            </div>
            <h2 class="text-2xl font-black text-green-600">
                ₹<?= number_format($total_revenue, 2) ?>
            </h2>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 border-b-4 border-b-red-400">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pending</p>
                <i data-lucide="clock" class="w-4 h-4 text-red-500"></i>
            </div>
            <h2 class="text-2xl font-black text-red-600">
                ₹<?= number_format($total_pending, 2) ?>
            </h2>
        </div>

    </div>

    <div class="mt-10 bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">

        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <h3 class="font-bold text-slate-700 flex items-center">
                <i data-lucide="clipboard-list" class="w-4 h-4 mr-2 text-blue-600"></i>
                Projects assigned to <?= htmlspecialchars($staff['name']) ?>
            </h3>
        </div>

        <div class="lg:hidden px-6 py-2 bg-amber-50 text-[10px] text-amber-700 font-bold uppercase tracking-wider flex items-center">
            <i data-lucide="info" class="w-3 h-3 mr-2"></i> Scroll right to view all details
        </div>

        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Project</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Client</th>
                        <!-- <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Progress</th> -->
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Total</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Paid</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Pending</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 tracking-widest">Notes</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">

                <?php while($row = $projects->fetch_assoc()): ?>

                <tr class="hover:bg-slate-50/80 transition-colors">

                    <td class="px-6 py-5">
                        <div class="font-bold text-slate-800 text-sm italic group-hover:text-blue-600 transition-colors">
                            <?= htmlspecialchars($row['project_name']) ?>
                        </div>
                    </td>

                    <td class="px-6 py-5">
                        <div class="text-sm font-medium text-slate-600">
                            <?= htmlspecialchars($row['client_name'] ?? 'N/A') ?>
                        </div>
                    </td>

                    <!-- <td class="px-6 py-5">
                        <div class="flex items-center space-x-2">
                            <div class="w-12 bg-slate-100 h-1 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-full" style="width: <?= $row['progress'] ?>%"></div>
                            </div>
                            <span class="text-[10px] font-black text-blue-600"><?= $row['progress'] ?>%</span>
                        </div>
                    </td> -->

                    <td class="px-6 py-5">
                        <span class="text-sm font-bold text-slate-800 tracking-tight">₹<?= number_format($row['project_amount'], 2) ?></span>
                    </td>

                    <td class="px-6 py-5">
                        <span class="text-sm font-bold text-emerald-600 tracking-tight">₹<?= number_format($row['paid_amount'], 2) ?></span>
                    </td>

                    <td class="px-6 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold <?= $row['payment_status']=='Paid' ? 'text-slate-400' : 'text-red-600' ?> tracking-tight">
                                ₹<?= number_format($row['pending_amount'], 2) ?>
                            </span>
                            <span class="text-[8px] font-black uppercase <?= $row['payment_status']=='Paid' ? 'text-emerald-500' : 'text-red-400' ?>">
                                <?= $row['payment_status'] ?>
                            </span>
                        </div>
                    </td>

                    <td class="px-6 py-5">
                        <div class="text-[11px] text-slate-400 max-w-[200px] truncate leading-relaxed" title="<?= htmlspecialchars($row['notes']) ?>">
                            <?= !empty($row['notes']) ? htmlspecialchars($row['notes']) : '—' ?>
                        </div>
                    </td>

                </tr>

                <?php endwhile; ?>

                </tbody>
            </table>
        </div>

    </div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>