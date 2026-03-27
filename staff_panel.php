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
notes , created_at
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
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        tr:hover td { background-color: #f8fafc; }
        .stat-card:hover { transform: translateY(-2px); transition: all 0.2s ease; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900">

<div class="flex h-screen overflow-hidden">

    <aside id="sidebar" class="w-72 bg-slate-950 text-white hidden md:flex flex-col border-r border-slate-800 z-50 transition-all duration-300">
        <div class="p-8 flex items-center justify-between">
            <div class="flex items-center space-x-3 group cursor-default">
                <div class="bg-blue-600 p-2.5 rounded-xl shadow-lg shadow-blue-500/20">
                    <i data-lucide="layers" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-xl font-bold tracking-tight text-white">Billing Pro</span>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
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

        <div class="p-6 mt-auto">
            <div class="bg-slate-900/50 rounded-2xl p-4 border border-slate-800">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-xs text-slate-300 font-medium tracking-tight">System Online</span>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto relative">
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-6 md:px-10 py-6 sticky top-0 z-40 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button onclick="toggleSidebar()" class="md:hidden p-2 bg-slate-100 rounded-lg">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800 tracking-tight">Welcome, <?= $_SESSION['staff_name'] ?></h2>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Workspace / Dashboard</p>
                </div>
            </div>
            <a href="create_project_staff.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Create Project</span>
            </a>
        </header>

        <div class="p-6 md:p-10 space-y-8">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 stat-card flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Monthly Projects</p>
                        <h2 class="text-3xl font-bold text-slate-800"><?= $total_projects ?></h2>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-2xl text-blue-600"><i data-lucide="folder" class="w-6 h-6"></i></div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 stat-card flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Revenue</p>
                        <h2 class="text-3xl font-bold text-emerald-600">₹<?= number_format($total_revenue,0) ?></h2>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-2xl text-emerald-600"><i data-lucide="banknote" class="w-6 h-6"></i></div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 stat-card flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pending Amount</p>
                        <h2 class="text-3xl font-bold text-red-500">₹<?= number_format($total_pending,0) ?></h2>
                    </div>
                    <div class="bg-red-50 p-3 rounded-2xl text-red-500"><i data-lucide="clock-alert" class="w-6 h-6"></i></div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 stat-card flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Domain Amount</p>
                        <h2 class="text-3xl font-bold text-emerald-600">₹<?= number_format($total_domain,0) ?></h2>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-2xl text-emerald-600"><i data-lucide="banknote" class="w-6 h-6"></i></div>
                </div>
            </div>

            <div class="bg-slate-900 rounded-3xl p-8 border border-slate-800 shadow-xl shadow-slate-200">
                <div class="flex items-center space-x-3 mb-6">
                    <i data-lucide="download" class="w-5 h-5 text-blue-400"></i>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Export Custom Data</h3>
                </div>

                <form method="GET" action="export_projects.php" class="flex flex-col md:flex-row gap-6 items-end">
                    <div class="w-full md:w-auto flex-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase px-1 mb-2 block">From Date</label>
                        <input type="date" name="from_date" required
                               class="w-full bg-slate-800 border-slate-700 text-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div class="w-full md:w-auto flex-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase px-1 mb-2 block">To Date</label>
                        <input type="date" name="to_date" required
                               class="w-full bg-slate-800 border-slate-700 text-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <button type="submit"
                        class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-xl text-sm font-bold transition-all shadow-lg shadow-blue-500/20">
                        Generate CSV
                    </button>
                </form>
            </div>

            <div class="data-card rounded-[2.5rem] shadow-xl overflow-hidden mb-10">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h3 class="font-bold text-slate-800">Assigned Projects</h3>
                    <div class="flex items-center space-x-2 text-[10px] font-bold text-blue-600 uppercase tracking-tighter bg-blue-50 px-3 py-1.5 rounded-full">
                        <span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></span>
                        <span>Active Tracking</span>
                    </div>
                </div>
                
                <div class="overflow-x-auto no-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-bold uppercase text-slate-400">Project Details</th>
                                <th class="px-8 py-5 text-[10px] font-bold uppercase text-slate-400">Client Contacts</th>
                                <!-- <th class="px-8 py-5 text-[10px] font-bold uppercase text-slate-400">Domain ₹</th> -->
                                <th class="px-8 py-5 text-[10px] font-bold uppercase text-slate-400">Internal Notes</th>
                                <th class="px-8 py-5 text-[11px] font-bold uppercase text-slate-400">Created</th>
                                <th class="px-8 py-5 text-[10px] font-bold uppercase text-slate-400 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="group">
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-800 text-sm group-hover:text-blue-600 transition-colors">
                                        <?= $row['project_name'] ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-medium mt-0.5">
                                        <i data-lucide="globe" class="w-3 h-3 inline mr-1 opacity-60"></i><?= $row['domain_name'] ?? 'No Domain' ?>
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <div class="text-xs font-bold text-slate-700"><?= $row['client_name'] ?? 'N/A' ?></div>
                                    <div class="flex flex-col space-y-0.5 mt-1">
                                        <span class="text-[10px] text-slate-400"><?= $row['client_email'] ?? '' ?></span>
                                        <span class="text-[10px] text-slate-500 font-bold tracking-tight italic"><?= $row['client_mobile'] ?? '' ?></span>
                                    </div>
                                </td>

                                <!-- <td class="px-8 py-5">
                                    ₹<?= number_format($row['domain_amount'] ?? 0, 2) ?>
                                </td> -->

                                <td class="px-8 py-6">
                                    <div class="text-[11px] text-slate-500 max-w-[200px] leading-relaxed italic bg-slate-50/50 p-2 rounded-lg border border-dashed">
                                        <?= !empty($row['notes']) ? $row['notes'] : 'No updates recorded' ?>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-xs text-slate-500">
                                    <?= !empty($row['created_at']) 
                                    ? date('d M Y', strtotime($row['created_at'])) 
                                    : 'N/A' ?>
                                </td>

                                <td class="px-8 py-6 text-right">
                                    <a href="update_project_staff.php?id=<?= $row['id'] ?>"
                                       class="inline-flex items-center px-4 py-2 bg-slate-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl font-bold text-xs transition-all duration-200 border border-slate-100 shadow-sm">
                                        <span>Sync Progress</span>
                                        <i data-lucide="refresh-cw" class="w-3 h-3 ml-2"></i>
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

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('flex');
        sidebar.classList.toggle('absolute');
        sidebar.classList.toggle('h-full');
    }
</script>

</body>
</html>