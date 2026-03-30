<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: admin/login.php");
    exit;
}

$staff_id = $_SESSION['staff_id'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['project_name'];
    $desc = $_POST['description'];

    $client_name   = $_POST['client_name'] ?? '';
    $domain_name   = $_POST['domain_name'] ?? '';
    $client_email  = $_POST['client_email'] ?? '';
    $client_mobile = $_POST['client_mobile'] ?? '';
    $address       = $_POST['address'] ?? '';
    $city          = $_POST['city'] ?? '';
    $state         = $_POST['state'] ?? '';
    $pincode       = $_POST['pincode'] ?? '';
    $project_amount = (float) ($_POST['project_amount'] ?? 0);
    $paid_amount    = (float) ($_POST['paid_amount'] ?? 0);
    $domain_amount =  (float) ($_POST['domain_amount'] ?? 0);

    /* AUTO CALCULATIONS */
    $pending_amount = $project_amount - $paid_amount;

    if($pending_amount <= 0){
        $pending_amount = 0;
        $payment_status = 'Paid';
    } elseif($paid_amount > 0){
        $payment_status = 'Partial';
    } else {
        $payment_status = 'Pending';
    }

    $stmt = $conn->prepare("
    INSERT INTO projects 
    (project_name, description, staff_id, client_name, domain_name, client_email, client_mobile, address, city, state, pincode, project_amount, paid_amount, pending_amount, payment_status, domain_amount) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
    "ssissssssssddddd",
    $name,
    $desc,
    $staff_id,
    $client_name,
    $domain_name,
    $client_email,
    $client_mobile,
    $address,
    $city,
    $state,
    $pincode,
    $project_amount,
    $paid_amount,
    $pending_amount,
    $payment_status,
    $domain_amount
    );
    $stmt->execute();

    header("Location: staff_panel.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Project | Staff Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .form-card {
            background: rgba(255, 255, 255, 0.95);
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
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900">

<div class="flex h-screen overflow-hidden">

    <aside id="sidebar" class="w-72 bg-slate-950 text-white hidden md:flex flex-col border-r border-slate-800 transition-all duration-300 z-50">
        <div class="p-8 flex items-center justify-between">
            <div class="flex items-center space-x-3 group cursor-default">
                <div class="bg-blue-600 p-2.5 rounded-xl shadow-lg shadow-blue-500/20">
                    <i data-lucide="layers" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-xl font-bold tracking-tight text-white">Billing Pro</span>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400"><i data-lucide="x"></i></button>
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
    </aside>

    <main class="flex-1 overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-6 md:px-10 py-6 sticky top-0 z-20 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button onclick="toggleSidebar()" class="md:hidden p-2 bg-slate-100 rounded-lg text-slate-600">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800">Launch Project</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Initiation Portal</p>
                </div>
            </div>
        </header>

        <div class="p-4 md:p-10 flex justify-center">
            <div class="form-card w-full max-w-2xl rounded-[2rem] shadow-2xl shadow-slate-200/50 overflow-hidden p-6 md:p-10 mb-10">
                
                <div class="mb-8 flex items-center space-x-4">
                    <div class="bg-blue-50 p-3.5 rounded-2xl">
                        <i data-lucide="rocket" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">New Project Setup</h3>
                        <p class="text-xs text-slate-400">Enter details to begin tracking and billing</p>
                    </div>
                </div>

                <form method="POST" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2 px-1">Project Name</label>
                            <input type="text" name="project_name" placeholder="e.g. Corporate Website Redesign" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-sm input-focus outline-none" 
                                   required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2 px-1">Description</label>
                            <textarea name="description" placeholder="Scope of work and key milestones..." rows="3"
                                      class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-sm input-focus outline-none"></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2 px-1">Project Amount (INR)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                                <input type="number" step="0.01" name="project_amount" placeholder="0.00"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-4 py-3.5 text-sm input-focus outline-none font-semibold text-blue-600">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2 px-1">
                                Domain Amount (Optional)
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                                <input type="number" step="0.01" name="domain_amount" placeholder="0.00"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-4 py-3.5 text-sm input-focus outline-none font-semibold text-purple-600">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2 px-1">
                                Paid Amount (Initial Payment)
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                                <input type="number" step="0.01" name="paid_amount" placeholder="0.00"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-4 py-3.5 text-sm input-focus outline-none font-semibold text-green-600">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">
                                Leave 0 if no payment received yet
                            </p>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-100">
                        <div class="flex items-center space-x-2 mb-6">
                            <i data-lucide="user-check" class="w-4 h-4 text-blue-600"></i>
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-tight">Client Contact Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <input type="text" name="client_name" placeholder="Contact Person Name"
                                     class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>

                            <input type="text" name="domain_name" placeholder="Domain (e.g. client.com)"
                                 class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">

                            <input type="email" name="client_email" placeholder="Email Address"
                                 class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">

                            <input type="text" name="client_mobile" placeholder="Mobile / WhatsApp"
                                 class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">

                            <input type="text" name="pincode" placeholder="Pincode"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">

                            <div class="md:col-span-2">
                                <textarea name="address" placeholder="Physical / Billing Address" rows="2"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none"></textarea>
                            </div>

                            <input type="text" name="city" placeholder="City"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">

                            <input type="text" name="state" placeholder="State"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-2xl font-bold shadow-xl shadow-blue-500/30 transition-all active:scale-[0.98] flex items-center justify-center space-x-3">
                            <i data-lucide="plus-circle" class="w-5 h-5"></i>
                            <span>Initialize Project</span>
                        </button>
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
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('flex');
        sidebar.classList.toggle('absolute');
        sidebar.classList.toggle('h-full');
    }
</script>

</body>
</html>