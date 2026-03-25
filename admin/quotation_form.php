<?php
require 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Quotation | Billing Pro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .form-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .input-focus {
            transition: all 0.2s ease-in-out;
        }
        .input-focus:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            border-color: #2563eb;
        }
        #sidebar { transition: transform 0.3s ease-in-out; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
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

    <main class="flex-1 overflow-y-auto flex flex-col">
        <header class="bg-white border-b border-slate-200 px-4 md:px-10 py-4 md:py-6 sticky top-0 z-20 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden p-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-600">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800">Draft Quotation</h2>
                    <p class="hidden sm:block text-xs text-slate-500 font-medium uppercase tracking-wider">Business Proposals</p>
                </div>
            </div>
            <!-- <a href="dashboard.php" class="text-slate-400 hover:text-slate-600 p-2">
                <i data-lucide="x" class="w-6 h-6"></i>
            </a> -->
        </header>

        <div class="p-4 md:p-10 max-w-5xl mx-auto w-full">
            <div class="form-card rounded-2xl md:rounded-3xl shadow-xl overflow-hidden">
                <form method="POST" action="../generate_quotation.php" class="divide-y divide-slate-100">

                    <div class="p-5 md:p-8 space-y-6">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex-shrink-0 flex items-center justify-center font-bold text-sm">01</span>
                            <h3 class="text-lg font-bold text-slate-800">Proposal Identity</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Client Company</label>
                                <input type="text" name="client_name" placeholder="Client Name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Subject</label>
                                <input type="text" name="subject" placeholder="Project Proposal" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Introduction</label>
                            <textarea name="introduction" placeholder="Briefly describe the proposal purpose..." rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required></textarea>
                        </div>
                    </div>

                    <div class="p-5 md:p-8 space-y-6 bg-slate-50/30">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex-shrink-0 flex items-center justify-center font-bold text-sm">02</span>
                            <h3 class="text-lg font-bold text-slate-800">Scope & Deliverables</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Feature List</label>
                                <textarea name="features" rows="6" placeholder="• Custom UI Design&#10;• User Dashboard&#10;• API Integration..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required></textarea>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Technical Specs</label>
                                <textarea name="technical_features" placeholder="• Responsive Framework&#10;• Secure Database&#10;• Cloud Hosting..." rows="6" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 md:p-8 space-y-6">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex-shrink-0 flex items-center justify-center font-bold text-sm">03</span>
                            <h3 class="text-lg font-bold text-slate-800">Financials</h3>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Payment Terms</label>
                                <textarea name="payment_terms" placeholder="E.g., 50% Upfront, 50% on completion..." rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required></textarea>
                            </div>
                            <div class="flex flex-col justify-end">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2 text-blue-600">Cost (₹)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                                    <input type="number" name="project_cost" placeholder="0.00" class="w-full bg-blue-50 border border-blue-100 rounded-xl pl-10 pr-4 py-4 text-xl font-black text-blue-700 input-focus outline-none" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 md:p-8 flex flex-col sm:flex-row items-center justify-between bg-white rounded-b-3xl gap-4">
                        <p class="text-[11px] text-slate-400 text-center sm:text-left order-2 sm:order-1">PDF proposal will be generated automatically.</p>
                        <div class="flex w-full sm:w-auto space-x-3 order-1 sm:order-2">
                            <a href="dashboard.php" class="flex-1 sm:flex-none text-center px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-800 transition">Discard</a>
                            <button type="submit" class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-6 md:px-10 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all active:scale-95 text-sm">
                                Generate
                            </button>
                        </div>
                    </div>
                </form>
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