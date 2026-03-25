<?php
require 'auth.php';
require '../config.php';

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM invoices WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

if(!$invoice){
die("Invoice not found");
}

if($_SERVER['REQUEST_METHOD']=="POST"){

$client_name = $_POST['client_name'];
$client_email = $_POST['client_email'];
$client_mobile = $_POST['client_mobile'];
$client_address = $_POST['client_address'];
$description = $_POST['description'];

$amount = isset($_POST['amount']) ? (float) $_POST['amount'] : 0;
$discount = isset($_POST['discount']) ? (float) $_POST['discount'] : 0;
$renewal_charge = isset($_POST['renewal_charge']) ? (float) $_POST['renewal_charge'] : 0;

$status = $_POST['status'];
$date = $_POST['date'];

$total = ($amount) - $discount;

$stmt = $conn->prepare("UPDATE invoices SET
client_name=?,
client_email=?,
client_mobile=?,
client_address=?,
description=?,
amount=?,
discount=?,
renewal_charge=?,
status=?,
date=?,
total_amount=?
WHERE id=?");

$stmt->bind_param(
"sssssdddssdi",
$client_name,
$client_email,
$client_mobile,
$client_address,
$description,
$amount,
$discount,
$renewal_charge,
$status,
$date,
$total,
$id
);

$stmt->execute();

header("Location: regenerate_invoice.php?id=".$id);
exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Invoice | Billing Pro</title>
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
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900">

<div class="flex h-screen overflow-hidden relative">

    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden md:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 w-72 bg-slate-950 text-white z-40 -translate-x-full md:translate-x-0 md:static md:flex flex-col border-r border-slate-800">
        <div class="p-8 flex items-center justify-between">
            <div class="flex items-center space-x-3 group">
                <div class="bg-blue-600 p-2.5 rounded-xl shadow-lg shadow-blue-500/20">
                    <i data-lucide="layers" class="w-6 h-6 text-white"></i>
                </div>
                <span class="text-xl font-bold tracking-tight">Billing Pro</span>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <nav class="flex-1 px-4 space-y-1">
            <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Editing Mode</p>
            <a href="dashboard.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-3 rounded-xl transition-all">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                <span class="font-medium">Back to Dashboard</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-6 md:px-10 py-6 sticky top-0 z-20 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden p-2 bg-slate-50 border border-slate-200 rounded-lg">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800">Edit Invoice</h2>
                    <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Reference: #<?= $invoice['id'] ?? 'N/A' ?></p>
                </div>
            </div>
            <div class="hidden sm:flex items-center px-4 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse mr-2"></span>
                <span class="text-xs font-bold uppercase">Update Mode</span>
            </div>
        </header>

        <div class="p-4 md:p-10 max-w-4xl mx-auto">
            <div class="form-card rounded-2xl md:rounded-3xl shadow-xl overflow-hidden">
                <form method="POST" class="divide-y divide-slate-100">
                    
                    <div class="p-6 md:p-8 space-y-6">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                            <h3 class="text-lg font-bold text-slate-800">Client Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Full Name</label>
                                <input type="text" name="client_name" value="<?= $invoice['client_name']?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none font-medium">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Email Address</label>
                                <input type="email" name="client_email" value="<?= $invoice['client_email']?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Mobile Number</label>
                                <input type="text" name="client_mobile" value="<?= $invoice['client_mobile']?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Billing Address</label>
                                <textarea name="client_address" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none"><?= $invoice['client_address']?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 md:p-8 space-y-6 bg-slate-50/30">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Invoice Date</label>
                                <input type="date" name="date" value="<?= $invoice['date']?>" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Payment Status</label>
                                <select name="status" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                                    <option value="Unpaid" <?= $invoice['status'] == 'Unpaid' ? 'selected' : '' ?>>Unpaid / Pending</option>
                                    <option value="Paid" <?= $invoice['status'] == 'Paid' ? 'selected' : '' ?>>Paid / Completed</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Project Description</label>
                                <textarea name="description" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none font-mono text-xs"><?= $invoice['description']?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 md:p-8 bg-slate-900 text-white rounded-b-3xl">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Base Amount (₹)</label>
                                <input type="number" name="amount" value="<?= $invoice['amount']?>" class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-3 text-lg font-bold outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Discount (₹)</label>
                                <input type="number" name="discount" value="<?= $invoice['discount']?>" class="w-full bg-slate-800 border border-slate-700 text-red-400 rounded-xl px-4 py-3 text-lg font-bold outline-none focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Renewal (₹)</label>
                                <input type="number" name="renewal_charge" value="<?= $invoice['renewal_charge']?>" class="w-full bg-slate-800 border border-slate-700 text-emerald-400 rounded-xl px-4 py-3 text-lg font-bold outline-none focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="mt-8 pt-8 border-t border-slate-800 flex flex-col sm:flex-row gap-4 items-center justify-between">
                            <p class="text-xs text-slate-500 max-w-xs text-center sm:text-left">Changes will be updated immediately in the database. Ensure all financial figures are double-checked.</p>
                            <div class="flex gap-3 w-full sm:w-auto">
                                <a href="dashboard.php" class="flex-1 sm:flex-none text-center px-8 py-3 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 transition font-bold text-sm">Cancel</a>
                                <button type="submit" class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                                    Update Invoice
                                </button>
                            </div>
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