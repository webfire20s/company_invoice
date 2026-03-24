<?php
require 'config.php';

if(!isset($_GET['id'])){
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

/* FETCH PROJECT */
$project = $conn->query("
SELECT p.*, s.name as staff_name
FROM projects p
LEFT JOIN staff s ON p.staff_id = s.id
WHERE p.id = $id
")->fetch_assoc();

if(!$project){
    echo "Project not found";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Details | Billing Pro</title>
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
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900 pb-20">

<div class="max-w-6xl mx-auto px-6 md:px-10 pt-10">

    <div class="flex items-center justify-between mb-8">
        <a href="index.php" class="flex items-center space-x-2 text-slate-500 hover:text-blue-600 transition-colors group">
            <div class="p-2 group-hover:bg-blue-50 rounded-lg transition-colors">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </div>
            <span class="font-bold text-sm tracking-tight">Back to Dashboard</span>
        </a>

        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold uppercase tracking-widest rounded-full">
                ID: #<?= str_pad($project['id'] ?? '0', 4, '0', STR_PAD_LEFT) ?>
            </span>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-8 items-start mb-10">
        <div class="md:col-span-2">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                <?= htmlspecialchars($project['project_name']) ?>
            </h1>
            <div class="flex items-center mt-3 space-x-4">
                <div class="flex items-center text-slate-500 text-sm">
                    <i data-lucide="user" class="w-4 h-4 mr-2 text-blue-500"></i>
                    <span class="font-medium">Lead: <?= htmlspecialchars($project['staff_name'] ?? 'Unassigned') ?></span>
                </div>
                <div class="h-1 w-1 bg-slate-300 rounded-full"></div>
                <div class="flex items-center text-slate-500 text-sm">
                    <i data-lucide="calendar" class="w-4 h-4 mr-2 text-blue-500"></i>
                    <span class="font-medium">Started: <?= date('M d, Y', strtotime($project['created_at'] ?? 'now')) ?></span>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-end">
             <?php if(($project['payment_status'] ?? '') == 'Paid' || ($project['project_amount'] - ($project['paid_amount'] ?? 0)) <= 0): ?>
                <div class="bg-emerald-500 text-white px-6 py-2 rounded-xl font-bold flex items-center shadow-lg shadow-emerald-200">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i> Fully Paid
                </div>
            <?php else: ?>
                <div class="bg-orange-500 text-white px-6 py-2 rounded-xl font-bold flex items-center shadow-lg shadow-orange-200">
                    <i data-lucide="clock" class="w-5 h-5 mr-2"></i> Payment Pending
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-8">
            
            <div class="glass-card p-8 rounded-[2rem] shadow-xl shadow-slate-200/50">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Delivery Progress</h3>
                        <p class="text-xs text-slate-400">Current development milestone</p>
                    </div>
                    <!-- <span class="text-4xl font-black text-blue-600 tracking-tighter"><?= $project['progress'] ?? 0 ?>%</span> -->
                </div>
                
                <!-- <div class="w-full bg-slate-100 h-4 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-1000" style="width: <?= $project['progress'] ?? 0 ?>%"></div>
                </div> -->

                <div class="mt-8 grid grid-cols-3 gap-4 border-t border-slate-50 pt-8">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Budget</p>
                        <p class="text-xl font-bold text-slate-800">₹<?= number_format($project['project_amount'] ?? 0, 2) ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Paid Received</p>
                        <p class="text-xl font-bold text-emerald-600">₹<?= number_format($project['paid_amount'] ?? 0, 2) ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Balance Due</p>
                        <p class="text-xl font-bold text-red-500">₹<?= number_format(($project['project_amount'] ?? 0) - ($project['paid_amount'] ?? 0), 2) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-2xl">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-blue-600 rounded-lg">
                        <i data-lucide="sticky-note" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold">Latest Internal Notes</h3>
                </div>
                <div class="bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50">
                    <p class="text-slate-300 leading-relaxed text-sm italic whitespace-pre-line">
                        <?= !empty($project['notes']) ? htmlspecialchars($project['notes']) : 'No specific updates have been logged for this project yet.' ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass-card p-8 rounded-[2rem] shadow-xl shadow-slate-200/50">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                    <i data-lucide="briefcase" class="w-5 h-5 mr-3 text-blue-600"></i>
                    Client Profile
                </h3>

                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Full Name</label>
                        <p class="font-bold text-slate-800"><?= htmlspecialchars($project['client_name'] ?? 'N/A') ?></p>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Digital Identity</label>
                        <p class="text-sm font-medium text-blue-600"><?= htmlspecialchars($project['domain_name'] ?? 'No Domain') ?></p>
                        <p class="text-sm text-slate-600 mt-1"><?= htmlspecialchars($project['client_email'] ?? '') ?></p>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Mobile Contact</label>
                        <p class="text-sm font-bold text-slate-800"><?= htmlspecialchars($project['client_mobile'] ?? 'N/A') ?></p>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Billing Address</label>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            <?= htmlspecialchars($project['address'] ?? '') ?><br>
                            <?= htmlspecialchars($project['city'] ?? '') ?>, <?= htmlspecialchars($project['state'] ?? '') ?> - <?= htmlspecialchars($project['pincode'] ?? '') ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button onclick="window.print()" class="flex flex-col items-center justify-center p-4 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 transition-colors">
                    <i data-lucide="printer" class="w-6 h-6 text-slate-400 mb-2"></i>
                    <span class="text-[11px] font-bold text-slate-600 uppercase">Print Summary</span>
                </button>
                <a href="update_project_staff.php?id=<?= $project['id'] ?>" class="flex flex-col items-center justify-center p-4 bg-blue-600 rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    <i data-lucide="edit-3" class="w-6 h-6 text-white mb-2"></i>
                    <span class="text-[11px] font-bold text-white uppercase">Edit Project</span>
                </a>
            </div>
        </div>

    </div>

</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>