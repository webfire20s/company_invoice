<?php
require 'auth.php';
require '../config.php';

$result = $conn->query("SELECT * FROM staff ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Directory | Billing Pro</title>
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
            <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Main Menu</p>
            
            <a href="../index.php" 
               class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="house" class="w-5 h-5"></i>
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
                class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'create_staff.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
                <span class="font-medium">Create Staff</span>
            </a>

            <a href="staff_list.php"
                class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'staff_list.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span class="font-medium">Staff List</span>
            </a>

            <a href="create_project.php"
                class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'create_project.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="folder-plus" class="w-5 h-5"></i>
                <span class="font-medium">Create Project</span>
            </a>

            <a href="projects_list.php"
                class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'projects_list.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="folder" class="w-5 h-5"></i>
                <span class="font-medium">Projects</span>
            </a>

            <div class="pt-8 mt-8 border-t border-slate-900">
                <a href="logout.php" 
                class="flex items-center space-x-3 text-red-400 hover:bg-red-500/10 px-4 py-3 rounded-xl transition-all duration-200">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </nav>

        <!-- <div class="p-6">
            <div class="bg-slate-900/50 rounded-2xl p-4 border border-slate-800">
                <p class="text-[10px] text-slate-500 font-bold uppercase mb-1">Status</p>
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-xs text-slate-300 font-medium">System Online</span>
                </div>
            </div>
        </div> -->
    </aside>

    <main class="flex-1 overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-10 py-6 sticky top-0 z-20 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Staff Directory</h2>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Internal Team / Access Management</p>
            </div>
            <a href="create_staff.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Add Member</span>
            </a>
        </header>

        <div class="p-10">
            <div class="data-card rounded-3xl shadow-xl overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">Team Member</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">Contact Details</th>
                            <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr onclick="window.location='staff_details.php?id=<?= $row['id'] ?>'"
                            class="transition-colors cursor-pointer hover:bg-slate-50">
                            <td class="px-8 py-5">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm border border-slate-200">
                                        <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-700 block"><?= $row['name'] ?></span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">Member</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center space-x-2 text-slate-600">
                                    <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                                    <span class="text-sm font-medium"><?= $row['email'] ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">
                                    Active
                                </span>
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