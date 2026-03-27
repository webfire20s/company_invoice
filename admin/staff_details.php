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

/* TOTAL domain amount */
$total_domain = $conn->query("
SELECT SUM(domain_amount) as total 
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
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .shadow-xl { shadow: none; }
        }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900 pb-20">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 pt-6 sm:pt-10">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <a href="staff_list.php" class="no-print inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-700 transition-all uppercase tracking-wider">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Directory
            </a>
            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 mt-2 tracking-tight">
                <?= htmlspecialchars($staff['name']) ?>
            </h2>
            <div class="flex items-center gap-3 mt-1">
                <span class="text-sm text-slate-500 font-medium">
                    <?= htmlspecialchars($staff['email']) ?>
                </span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">ID: #STAFF-<?= $staff['id'] ?></span>
            </div>
        </div>
        <div class="flex items-center gap-2 no-print">
            <button onclick="window.print()" class="p-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                <i data-lucide="printer" class="w-5 h-5"></i>
            </button>
            <span class="px-4 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-500/20">
                Staff Performance Profile
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2 sm:gap-6">

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:border-blue-200 transition-colors group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 rounded-lg group-hover:bg-blue-600 transition-colors">
                    <i data-lucide="layers" class="w-4 h-4 text-blue-600 group-hover:text-white"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Projects</p>
            </div>
            <h2 class="text-3xl font-black text-slate-800">
                <?= $total_projects ?> <span class="text-xs text-slate-400 font-normal ml-1 tracking-normal italic">Assignments</span>
            </h2>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:border-purple-200 transition-colors group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-50 rounded-lg group-hover:bg-purple-600 transition-colors">
                    <i data-lucide="database" class="w-4 h-4 text-purple-600 group-hover:text-white"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Portfolio Value</p>
            </div>
            <h2 class="text-3xl font-black text-slate-800">
                ₹<?= number_format($total_value, 0) ?>
            </h2>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:border-emerald-200 transition-colors group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-50 rounded-lg group-hover:bg-emerald-600 transition-colors">
                    <i data-lucide="trending-up" class="w-4 h-4 text-emerald-600 group-hover:text-white"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Cleared</p>
            </div>
            <h2 class="text-3xl font-black text-emerald-600">
                ₹<?= number_format($total_revenue, 0) ?>
            </h2>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 border-b-4 border-b-red-500 hover:border-red-200 transition-colors group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-red-50 rounded-lg group-hover:bg-red-600 transition-colors">
                    <i data-lucide="clock" class="w-4 h-4 text-red-600 group-hover:text-white"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Outstanding</p>
            </div>
            <h2 class="text-3xl font-black text-red-600">
                ₹<?= number_format($total_pending, 0) ?>
            </h2>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:border-emerald-200 transition-colors group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-50 rounded-lg group-hover:bg-emerald-600 transition-colors">
                    <i data-lucide="trending-up" class="w-4 h-4 text-emerald-600 group-hover:text-white"></i>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Domain Amount</p>
            </div>
            <h2 class="text-3xl font-black text-emerald-600">
                ₹<?= number_format($total_domain, 0) ?>
            </h2>
        </div>

    </div>

    <div class="mt-10 bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">

        <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <div>
                <h3 class="font-bold text-slate-800 text-lg flex items-center">
                    <i data-lucide="briefcase" class="w-5 h-5 mr-3 text-blue-600"></i>
                    Project Engagement
                </h3>
                <p class="text-xs text-slate-500 mt-1">Detailed breakdown of current and past responsibilities</p>
            </div>
        </div>

        <div class="lg:hidden px-8 py-3 bg-amber-50/50 text-[10px] text-amber-700 font-bold uppercase tracking-wider flex items-center">
            <i data-lucide="move-horizontal" class="w-3 h-3 mr-2"></i> Swipe horizontally to view project financials
        </div>

        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[950px]">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Project Description</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Client Name</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Total Valuation</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Amount Paid</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Dues</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Activity Log</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    <?php if($projects->num_rows > 0): ?>
                        <?php while($row = $projects->fetch_assoc()): ?>
                        <tr class="hover:bg-blue-50/30 transition-all group">
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-800 text-sm group-hover:text-blue-600 transition-colors uppercase tracking-tight">
                                    <?= htmlspecialchars($row['project_name']) ?>
                                </div>
                                <div class="text-[10px] text-slate-400 font-medium mt-1">Assigned on <?= !empty($row['created_at']) 
                                    ? date('d M Y', strtotime($row['created_at'])) 
                                    : 'N/A' ?>
                                </div>
                            </td>

                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-2 text-sm font-semibold text-slate-600">
                                    <i data-lucide="user" class="w-3 h-3 text-slate-300"></i>
                                    <span><?= htmlspecialchars($row['client_name'] ?? 'Guest Client') ?></span>
                                </div>
                            </td>

                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-slate-900 tracking-tighter">₹<?= number_format($row['project_amount'], 2) ?></span>
                            </td>

                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-emerald-600 tracking-tighter">₹<?= number_format($row['paid_amount'], 2) ?></span>
                                    <div class="w-16 bg-slate-100 h-1 rounded-full mt-2 overflow-hidden">
                                        <?php $perc = ($row['project_amount'] > 0) ? ($row['paid_amount'] / $row['project_amount']) * 100 : 0; ?>
                                        <div class="bg-emerald-500 h-full rounded-full" style="width: <?= $perc ?>%"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold <?= $row['payment_status']=='Paid' ? 'text-slate-300' : 'text-red-600' ?> tracking-tighter">
                                        ₹<?= number_format($row['pending_amount'], 2) ?>
                                    </span>
                                    <span class="text-[8px] font-black uppercase <?= $row['payment_status']=='Paid' ? 'text-emerald-500' : 'text-red-400' ?> mt-1">
                                        <?= $row['payment_status'] ?>
                                    </span>
                                </div>
                            </td>

                            <td class="px-8 py-6">
                                <div class="text-[11px] text-slate-500 italic max-w-[220px] line-clamp-2 leading-relaxed" title="<?= htmlspecialchars($row['notes']) ?>">
                                    <?= !empty($row['notes']) ? '"' . htmlspecialchars($row['notes']) . '"' : 'No logs available' ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="folder-open" class="w-12 h-12 text-slate-200 mb-4"></i>
                                    <p class="text-slate-400 font-bold text-sm">No projects assigned yet.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
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