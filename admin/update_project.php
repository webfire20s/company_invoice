<?php
require 'auth.php';
require '../config.php';

$id = $_GET['id'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $progress = $_POST['progress'];

    $stmt = $conn->prepare("UPDATE projects SET progress=? WHERE id=?");
    $stmt->bind_param("ii",$progress,$id);
    $stmt->execute();

    header("Location: projects_list.php");
}

$project = $conn->query("SELECT * FROM projects WHERE id=$id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Progress | Billing Pro</title>
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
        /* Custom Range Slider Styling */
        input[type=range] {
            -webkit-appearance: none;
            width: 100%;
            background: transparent;
        }
        input[type=range]::-webkit-slider-runnable-track {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 24px;
            width: 24px;
            border-radius: 50%;
            background: #2563eb;
            cursor: pointer;
            margin-top: -8px;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
            border: 3px solid white;
            transition: all 0.2s ease;
        }
        input[type=range]:active::-webkit-slider-thumb {
            transform: scale(1.2);
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
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
            <a href="dashboard.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="projects_list.php" class="flex items-center space-x-3 bg-blue-600 text-white shadow-lg shadow-blue-900/20 px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="folder" class="w-5 h-5"></i>
                <span class="font-medium">Projects</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-6 md:px-10 py-6 sticky top-0 z-20">
            <div class="flex items-center space-x-4">
                <a href="projects_list.php" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-400">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800">Update Project Progress</h2>
                    <p class="text-xs text-slate-500 font-medium uppercase tracking-wider italic">ID: #<?= $project['id'] ?></p>
                </div>
            </div>
        </header>

        <div class="p-6 md:p-10 flex justify-center items-start pt-12 md:pt-20">
            <div class="form-card w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden">
                <div class="p-8 md:p-10">
                    <form method="POST" class="space-y-10">
                        
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl mb-4">
                                <i data-lucide="rocket" class="w-8 h-8"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 leading-tight mb-1"><?= $project['project_name'] ?></h3>
                            <p class="text-sm text-slate-400 font-medium italic">Update the current milestone status</p>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-8 border border-slate-100 text-center relative overflow-hidden">
                            <div id="progressFill" class="absolute inset-y-0 left-0 bg-blue-600/5 transition-all duration-500" style="width: <?= $project['progress'] ?>%"></div>
                            
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-6 relative z-10">Completion Level</label>
                            
                            <div class="flex items-center justify-center space-x-2 mb-8 relative z-10">
                                <input type="number" name="progress" id="numInput" 
                                       value="<?= $project['progress'] ?>" min="0" max="100"
                                       class="w-24 bg-transparent text-5xl font-black text-blue-600 outline-none text-center">
                                <span class="text-3xl font-bold text-blue-300">%</span>
                            </div>

                            <div class="px-4 relative z-10">
                                <input type="range" id="rangeInput" min="0" max="100" value="<?= $project['progress'] ?>" class="cursor-pointer">
                                <div class="flex justify-between mt-4">
                                    <span class="text-[10px] font-bold text-slate-400 tracking-tighter uppercase">Starting</span>
                                    <span class="text-[10px] font-bold text-slate-400 tracking-tighter uppercase">Completed</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="projects_list.php" class="flex-1 text-center px-6 py-4 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">Cancel</a>
                            <button type="submit" class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-xl font-bold shadow-lg shadow-blue-500/25 transition-all active:scale-[0.98] flex items-center justify-center space-x-2">
                                <i data-lucide="zap" class="w-5 h-5"></i>
                                <span>Save Changes</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();

    const range = document.getElementById('rangeInput');
    const num = document.getElementById('numInput');
    const fill = document.getElementById('progressFill');

    // Sync Slider to Number
    range.addEventListener('input', (e) => {
        num.value = e.target.value;
        updateFill(e.target.value);
    });

    // Sync Number to Slider
    num.addEventListener('input', (e) => {
        let val = e.target.value;
        if(val > 100) val = 100;
        if(val < 0) val = 0;
        range.value = val;
        updateFill(val);
    });

    function updateFill(val) {
        fill.style.width = val + '%';
    }
</script>

</body>
</html>
