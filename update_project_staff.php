<?php
session_start();
require 'config.php';

if(!isset($_SESSION['staff_id'])){
    header("Location: staff_login.php");
    exit;
}

$id = $_GET['id'];
$staff_id = $_SESSION['staff_id'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name = $_POST['project_name'];
    $desc = $_POST['description'];

    $client_name = $_POST['client_name'] ?? '';
    $domain_name = $_POST['domain_name'] ?? '';
    $client_email = $_POST['client_email'] ?? '';
    $client_mobile = $_POST['client_mobile'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $pincode = $_POST['pincode'] ?? '';

    $notes = $_POST['notes'] ?? '';

    $progress = (int) $_POST['progress'];

    $project_amount = (float) $_POST['project_amount'];
    $paid_amount = isset($_POST['paid_amount']) ? (float) $_POST['paid_amount'] : 0;

    // 🔥 AUTO CALCULATE PENDING
    $pending_amount = $project_amount - $paid_amount;

    // 🔥 AUTO STATUS
    $status = ($pending_amount <= 0) ? 'Paid' : 'Pending';

    $stmt = $conn->prepare("
        UPDATE projects 
        SET 
            project_name=?,
            description=?,
            client_name=?,
            domain_name=?,
            client_email=?,
            client_mobile=?,
            address=?,
            city=?,
            state=?,
            pincode=?,
            notes=?,
            progress=?,
            project_amount=?,
            paid_amount=?,
            pending_amount=?,
            payment_status=?
        WHERE id=? AND staff_id=?
    ");

    $stmt->bind_param(
        "sssssssssssiddsdii",
        $name,
        $desc,
        $client_name,
        $domain_name,
        $client_email,
        $client_mobile,
        $address,
        $city,
        $state,
        $pincode,
        $notes,
        $progress,
        $project_amount,
        $paid_amount,
        $pending_amount,
        $status,
        $id,
        $staff_id
    );

    $stmt->execute();

    header("Location: staff_panel.php");
    exit;
}
$project = $conn->query("
SELECT * FROM projects 
WHERE id=$id AND staff_id=$staff_id
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Project | Staff Portal</title>
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
               class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'staff_panel.php' || basename($_SERVER['PHP_SELF']) == 'update_project_staff.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="font-medium">My Projects</span>
            </a>

            <a href="create_project_staff.php" 
               class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'create_project_staff.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="folder-plus" class="w-5 h-5"></i>
                <span class="font-medium">New Project</span>
            </a>

            <div class="pt-8 mt-8 border-t border-slate-900">
                <a href="logout.php" 
                class="flex items-center space-x-3 text-red-400 hover:bg-red-500/10 px-4 py-3 rounded-xl transition-all duration-200">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-6 md:px-10 py-6 sticky top-0 z-20 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button onclick="toggleSidebar()" class="md:hidden p-2 bg-slate-100 rounded-lg text-slate-600">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <a href="staff_panel.php" class="hidden md:flex p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-400">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800">Update Project</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Editing: <?= htmlspecialchars($project['project_name']) ?></p>
                </div>
            </div>
        </header>

        <div class="p-4 md:p-10 flex justify-center">
            <div class="form-card w-full max-w-2xl rounded-[2.5rem] shadow-2xl shadow-slate-200/60 overflow-hidden p-6 md:p-10 mb-10">
                
                <div class="mb-10 text-center">
                    <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                        <i data-lucide="edit-3" class="w-10 h-10"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-800 tracking-tight">Modify Milestone & Billing</h3>
                </div>

                <form method="POST" class="space-y-8">
                    
                    <div class="space-y-5">
                        <div class="flex items-center space-x-2 pb-2 border-b border-slate-100">
                            <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Project Overview</span>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2 px-1">Project Name</label>
                            <input type="text" name="project_name" value="<?= htmlspecialchars($project['project_name']) ?>"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm input-focus outline-none font-medium">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2 px-1">Scope / Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm input-focus outline-none"><?= htmlspecialchars($project['description']) ?></textarea>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2 px-1 text-blue-600">Work Progress Notes</label>
                            <textarea name="notes" rows="4" placeholder="What's the current status of the work?"
                                      class="w-full bg-blue-50/30 border border-blue-100 rounded-2xl px-5 py-4 text-sm input-focus outline-none italic"><?= htmlspecialchars($project['notes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <div class="flex items-center space-x-2 pb-2 border-b border-slate-100">
                            <i data-lucide="users" class="w-4 h-4 text-blue-500"></i>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Client Information</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="client_name" placeholder="Client Name" value="<?= $project['client_name'] ?? '' ?>"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            
                            <input type="text" name="domain_name" placeholder="Domain" value="<?= $project['domain_name'] ?? '' ?>"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            
                            <input type="email" name="client_email" placeholder="Email" value="<?= $project['client_email'] ?? '' ?>"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            
                            <input type="text" name="client_mobile" placeholder="Mobile" value="<?= $project['client_mobile'] ?? '' ?>"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <input type="text" name="city" placeholder="City" value="<?= $project['city'] ?? '' ?>" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none">
                            <input type="text" name="state" placeholder="State" value="<?= $project['state'] ?? '' ?>" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none">
                            <input type="text" name="pincode" placeholder="Pin" value="<?= $project['pincode'] ?? '' ?>" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none">
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <div class="flex items-center space-x-2 pb-2 border-b border-slate-100">
                            <i data-lucide="credit-card" class="w-4 h-4 text-blue-500"></i>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Billing Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Total Project Amount (₹)</label>
                                <input type="number" step="0.01" name="project_amount" id="total_amt" value="<?= $project['project_amount'] ?? 0 ?>"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none font-bold text-slate-700">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Amount Paid (₹)</label>
                                <input type="number" step="0.01" name="paid_amount" id="paid_amt" value="<?= $project['paid_amount'] ?? 0 ?>"
                                       class="w-full bg-emerald-50/50 border border-emerald-100 rounded-xl px-4 py-3 text-sm input-focus outline-none font-bold text-emerald-600">
                            </div>
                        </div>

                        <div class="bg-slate-900 rounded-2xl p-5 flex justify-between items-center shadow-lg shadow-slate-200">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Balance Pending</p>
                                <h4 id="pending_display" class="text-2xl font-bold text-white tracking-tight">₹0.00</h4>
                            </div>
                            <div class="bg-white/10 p-3 rounded-xl">
                                <i data-lucide="wallet" class="w-6 h-6 text-blue-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-5 rounded-[1.5rem] font-bold shadow-xl shadow-blue-500/30 transition-all active:scale-[0.98] flex items-center justify-center space-x-3 text-lg">
                            <i data-lucide="save" class="w-6 h-6"></i>
                            <span>Sync & Save Updates</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();

    // Responsive Sidebar Toggle
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('flex');
        sidebar.classList.toggle('absolute');
        sidebar.classList.toggle('h-full');
    }

    // Real-time Calculation
    const totalInput = document.getElementById('total_amt');
    const paidInput = document.getElementById('paid_amt');
    const pendingDisplay = document.getElementById('pending_display');

    function calculatePending() {
        const total = parseFloat(totalInput.value) || 0;
        const paid = parseFloat(paidInput.value) || 0;
        const pending = total - paid;
        pendingDisplay.innerText = '₹' + pending.toLocaleString('en-IN', {minimumFractionDigits: 2});
        
        // Change color to red if there is a pending balance
        if(pending > 0) {
            pendingDisplay.classList.add('text-orange-400');
        } else {
            pendingDisplay.classList.remove('text-orange-400');
        }
    }

    totalInput.addEventListener('input', calculatePending);
    paidInput.addEventListener('input', calculatePending);
    window.onload = calculatePending; // Initial calc on load
</script>

</body>
</html>