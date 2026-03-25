<?php
require 'auth.php';
require '../config.php';

if(!isset($_GET['id'])){
header("Location: dashboard.php");
exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM quotations WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
header("Location: dashboard.php");
exit;
}

$q = $result->fetch_assoc();

/* UPDATE */

if($_SERVER['REQUEST_METHOD']=="POST"){

$client_name = $_POST['client_name'];
$date = $_POST['date'];

$features = $_POST['features'];
$technical_features = $_POST['technical_features'];
$notes = $_POST['notes'];

$total_amount = floatval($_POST['total_amount']);

$stmt = $conn->prepare("UPDATE quotations SET
client_name=?,
date=?,
features=?,
technical_features=?,
notes=?,
total_amount=?
WHERE id=?");

$stmt->bind_param(
"sssssdi",
$client_name,
$date,
$features,
$technical_features,
$notes,
$total_amount,
$id
);

$stmt->execute();

/* regenerate quotation */

header("Location: regenerate_quotation.php?id=".$id);
exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Quotation | Billing Pro</title>
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
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800">Edit Quotation</h2>
                    <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Ref No: <?= $q['quotation_number'] ?></p>
                </div>
            </div>
            <div class="hidden sm:flex items-center px-4 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-100">
                <i data-lucide="file-edit" class="w-3.5 h-3.5 mr-2"></i>
                <span class="text-xs font-bold uppercase tracking-tight">Draft Revision</span>
            </div>
        </header>

        <div class="p-4 md:p-10 max-w-5xl mx-auto">
            <div class="form-card rounded-2xl md:rounded-3xl shadow-xl overflow-hidden">
                <form method="POST" class="divide-y divide-slate-100">
                    
                    <div class="p-6 md:p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="opacity-75">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Quotation No</label>
                                <div class="flex items-center bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-600 font-mono">
                                    <i data-lucide="lock" class="w-3.5 h-3.5 mr-2 text-slate-400"></i>
                                    <?= $q['quotation_number'] ?>
                                </div>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Client Name</label>
                                <input type="text" name="client_name" value="<?= htmlspecialchars($q['client_name']) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none font-medium">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Quotation Date</label>
                                <input type="date" name="date" value="<?= $q['date'] ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="p-6 md:p-8 space-y-6 bg-slate-50/30">
                        <div class="flex items-center space-x-2 mb-2">
                            <i data-lucide="scroll-text" class="w-5 h-5 text-blue-600"></i>
                            <h3 class="font-bold text-slate-800">Proposal Scope & Details</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Features & Services</label>
                                <textarea name="features" rows="6" placeholder="List the primary project features..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none resize-none"><?= htmlspecialchars($q['features'] ?? '') ?></textarea>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Technical Specifications</label>
                                <textarea name="technical_features" rows="6" placeholder="Server specs, languages, frameworks..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none resize-none"><?= htmlspecialchars($q['technical_features'] ?? '') ?></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Additional Terms / Notes</label>
                                <textarea name="notes" rows="3" placeholder="Payment milestones, validity period, etc." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none"><?= htmlspecialchars($q['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 md:p-8 bg-slate-900 text-white rounded-b-3xl">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="w-full md:w-72">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Estimated Total (₹)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">₹</span>
                                    <input type="number" step="0.01" name="total_amount" value="<?= $q['total_amount'] ?>" required class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl pl-8 pr-4 py-4 text-2xl font-black outline-none focus:border-blue-500">
                                </div>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                                <a href="dashboard.php" class="px-8 py-4 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 transition font-bold text-sm text-center">Discard Changes</a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-12 py-4 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all active:scale-95 text-center">
                                    Update Quotation
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