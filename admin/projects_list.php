<?php
require 'auth.php';
require '../config.php';

$result = $conn->query("
SELECT p.*, s.name as staff_name
FROM projects p
JOIN staff s ON p.staff_id = s.id
ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Projects List | Billing Pro</title>
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
        #sidebar { transition: transform 0.3s ease-in-out; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900">

<div class="flex h-screen overflow-hidden relative">

    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden md:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 w-72 bg-slate-950 text-white z-40 -translate-x-full md:translate-x-0 md:static md:flex flex-col border-r border-slate-800">
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

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
            <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Main Menu</p>
            
            <a href="../index.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="house" class="w-5 h-5"></i>
                <span class="font-medium">Home</span>
            </a>

            <a href="dashboard.php" class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="invoice_form.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="file-plus" class="w-5 h-5"></i>
                <span class="font-medium">Create Invoice</span>
            </a>

            <a href="quotation_form.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                <span class="font-medium">Create Quotation</span>
            </a>

            <a href="create_staff.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
                <span>Create Staff</span>
            </a>

            <a href="staff_list.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Staff List</span>
            </a>

            <a href="create_project.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="folder-plus" class="w-5 h-5"></i>
                <span>Create Project</span>
            </a>

            <a href="projects_list.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="folder" class="w-5 h-5"></i>
                <span>Projects</span>
            </a>

            <div class="pt-8 mt-8 border-t border-slate-900">
                <a href="logout.php" class="flex items-center space-x-3 text-red-400 hover:bg-red-500/10 px-4 py-3 rounded-xl transition-all duration-200">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto flex flex-col w-full">
        <header class="bg-white border-b border-slate-200 px-4 md:px-10 py-4 md:py-6 sticky top-0 z-20 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden p-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-600">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800">Active Projects</h2>
                    <p class="hidden sm:block text-xs text-slate-500 font-medium uppercase tracking-wider">Project Lifecycle</p>
                </div>
            </div>
            <a href="create_project.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-2.5 rounded-xl font-bold text-xs md:text-sm shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span class="hidden xs:inline">New Project</span>
            </a>
        </header>

        <div class="p-4 md:p-10 w-full">
            <div class="data-card rounded-2xl md:rounded-3xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto no-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 md:px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">Project Name</th>
                                <th class="px-6 md:px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">Assigned Staff</th>
                                <th class="px-6 md:px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500">Completion</th>
                                <th class="px-6 md:px-8 py-5 text-[11px] font-bold uppercase tracking-widest text-slate-500 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="transition-colors">
                                <td class="px-6 md:px-8 py-5">
                                    <span class="font-bold text-slate-700 block text-sm md:text-base"><?= $row['project_name'] ?></span>
                                </td>
                                <td class="px-6 md:px-8 py-5">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex-shrink-0 flex items-center justify-center font-bold text-xs">
                                            <?= strtoupper(substr($row['staff_name'], 0, 1)) ?>
                                        </div>
                                        <span class="text-sm font-medium text-slate-600 truncate max-w-[100px]"><?= $row['staff_name'] ?></span>
                                    </div>
                                </td>
                                <td class="px-6 md:px-8 py-5">
                                    <div class="w-full max-w-[120px]">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-[10px] font-black text-slate-400"><?= $row['progress'] ?>%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-blue-600 h-full rounded-full" style="width: <?= $row['progress'] ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 md:px-8 py-5 text-right">
                                    <a href="update_project.php?id=<?= $row['id'] ?>" class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-800 font-bold text-sm transition-colors">
                                        <span class="hidden sm:inline">Manage</span>
                                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
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
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    document.getElementById('sidebarOverlay').addEventListener('click', toggleSidebar);
</script>

</body>
</html>