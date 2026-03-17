<?php
require 'admin/auth.php';
require 'config.php';

/* ===== KPI QUERIES (Logic Unchanged) ===== */
$q1 = $conn->query("SELECT COUNT(*) as total FROM invoices");
$total_invoices = $q1->fetch_assoc()['total'] ?? 0;

$q2 = $conn->query("SELECT COUNT(*) as total FROM quotations");
$total_quotations = $q2->fetch_assoc()['total'] ?? 0;

$q3 = $conn->query("SELECT SUM(total_amount) as revenue FROM invoices WHERE status='Paid'");
$revenue = $q3->fetch_assoc()['revenue'] ?? 0;

$q4 = $conn->query("SELECT SUM(total_amount) as pending FROM invoices WHERE status='Unpaid'");
$pending = $q4->fetch_assoc()['pending'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Billing System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .sidebar-link-active {
            background: linear-gradient(to right, #2563eb, #3b82f6);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
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
            
            <a href="index.php" 
            class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="house" class="w-5 h-5"></i>
                <span class="font-medium">Home</span>
            </a>

            <a href="admin/dashboard.php" 
            class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="admin/invoice_form.php" 
            class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'invoice_form.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="file-plus" class="w-5 h-5"></i>
                <span class="font-medium">Create Invoice</span>
            </a>

            <a href="admin/quotation_form.php" 
            class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'quotation_form.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                <span class="font-medium">Create Quotation</span>
            </a>
            <a href="admin/create_staff.php"
                class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
                <span>Create Staff</span>
            </a>

            <a href="admin/staff_list.php"
                class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Staff List</span>
            </a>

            <a href="admin/create_project.php"
                class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="folder-plus" class="w-5 h-5"></i>
                <span>Create Project</span>
            </a>

            <a href="admin/projects_list.php"
                class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="folder" class="w-5 h-5"></i>
                <span>Projects</span>
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
    <main class="flex-1 overflow-y-auto bg-slate-50">
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-10 sticky top-0 z-10">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Welcome Back</h1>
                <p class="text-xs text-slate-500 font-medium">Here's what's happening today.</p>
            </div>
            <div class="flex items-center space-x-4">
                <button class="p-2 text-slate-400 hover:bg-slate-100 rounded-full transition-colors">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>
                <div class="h-10 w-10 rounded-full bg-blue-100 border border-blue-200 flex items-center justify-center text-blue-600 font-bold">
                    AD
                </div>
            </div>
        </header>

        <div class="p-10">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold tracking-tight">Business Overview</h2>
                <div class="flex space-x-3">
                   <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span> System Live
                   </span>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">

                <a href="admin/dashboard.php" class="glass-card p-6 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Total Invoices</p>
                    <div class="flex items-baseline space-x-2 mt-1">
                        <p class="text-3xl font-extrabold text-slate-900"><?php echo $total_invoices; ?></p>
                        <span class="text-xs font-bold text-blue-600">Active</span>
                    </div>
                </a>

                <a href="admin/dashboard.php?type=quotation" class="glass-card p-6 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition-colors">
                            <i data-lucide="clipboard-check" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Total Quotations</p>
                    <div class="flex items-baseline space-x-2 mt-1">
                        <p class="text-3xl font-extrabold text-slate-900"><?php echo $total_quotations; ?></p>
                        <span class="text-xs font-bold text-purple-600">Estimates</span>
                    </div>
                </a>

                <a href="admin/dashboard.php?status=Paid" class="glass-card p-6 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <i data-lucide="trending-up" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Revenue</p>
                    <div class="flex items-baseline space-x-1 mt-1">
                        <p class="text-2xl font-extrabold text-slate-900">₹<?php echo number_format($revenue, 2); ?></p>
                    </div>
                </a>

                <a href="admin/dashboard.php?status=Unpaid" class="glass-card p-6 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Pending</p>
                    <div class="flex items-baseline space-x-1 mt-1">
                        <p class="text-2xl font-extrabold text-slate-900">₹<?php echo number_format($pending, 2); ?></p>
                    </div>
                </a>

            </div>

            <?php
            $projects = $conn->query("
            SELECT p.*, s.name as staff_name
            FROM projects p
            JOIN staff s ON p.staff_id = s.id
            ORDER BY p.id DESC LIMIT 5
            ");
            ?>

            <div class="bg-white p-6 rounded-xl mt-10 border">

                <h3 class="text-lg font-semibold mb-4">Projects Overview</h3>

                <?php while($p = $projects->fetch_assoc()): ?>

                <div class="mb-4">
                    <p class="font-medium"><?= $p['project_name'] ?> (<?= $p['staff_name'] ?>)</p>

                    <div class="w-full bg-gray-200 h-2 rounded">
                        <div class="bg-blue-600 h-2 rounded"
                            style="width: <?= $p['progress'] ?>%">
                        </div>
                    </div>

                    <p class="text-xs"><?= $p['progress'] ?>%</p>

                </div>

                <?php endwhile; ?>

            </div>

            <div class="mt-12 p-8 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 text-white flex items-center justify-between shadow-lg">
                <div>
                    <h3 class="text-lg font-bold">Quick Report Generation</h3>
                    <p class="text-blue-100 text-sm">Need a full financial summary? Head over to the Detailed Dashboard.</p>
                </div>
                <a href="admin/dashboard.php" class="px-6 py-3 bg-white text-blue-600 rounded-xl font-bold text-sm hover:bg-blue-50 transition-colors">
                    View Full Reports
                </a>
            </div>
        </div>
    </main>
</div>

<script>
    // Initialize Lucide Icons
    lucide.createIcons();
</script>

</body>
</html>