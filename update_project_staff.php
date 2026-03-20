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
    $amount = isset($_POST['project_amount']) ? (float) $_POST['project_amount'] : 0;
    $status = $_POST['payment_status'] ?? 'Pending';

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
            payment_status=?
        WHERE id=? AND staff_id=?
    ");

    $stmt->bind_param(
        "sssssssssssidsii",
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
        $amount,
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
    <title>Update Progress | Staff Portal</title>
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
        /* Hide scrollbar while keeping functionality */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
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

    <main class="flex-1 overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-10 py-6 sticky top-0 z-20 flex items-center space-x-4">
            <a href="staff_panel.php" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-400">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Update Progress</h2>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Project Lifecycle / Milestone Update</p>
            </div>
        </header>

        <div class="p-10 flex justify-center pt-20">
            <div class="form-card w-full max-w-md rounded-3xl shadow-xl overflow-hidden p-8 text-center">
                <div class="mb-8">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="activity" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800"><?= $project['project_name'] ?></h3>
                </div>

                <form method="POST" class="space-y-6">
                    <!-- NOTES -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">
                            Project Notes (Work Done / Pending)
                        </label>
                        <textarea name="notes" rows="4"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none"><?= $project['notes'] ?? '' ?></textarea>
                    </div>
                    <!-- PROJECT NAME -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Project Name</label>
                        <input type="text" name="project_name"
                            value="<?= $project['project_name'] ?>"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                    </div>

                    <!-- DESCRIPTION -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none"><?= $project['description'] ?></textarea>
                    </div>
                    <hr>

                    <p class="text-xs font-bold text-slate-500 uppercase">Client Details</p>

                    <input type="text" name="client_name" placeholder="Client Name"
                    value="<?= $project['client_name'] ?? '' ?>"
                    class="w-full bg-slate-50 border rounded-xl px-4 py-3 text-sm">

                    <input type="text" name="domain_name" placeholder="Domain Name"
                    value="<?= $project['domain_name'] ?? '' ?>"
                    class="w-full bg-slate-50 border rounded-xl px-4 py-3 text-sm">

                    <input type="email" name="client_email" placeholder="Email"
                    value="<?= $project['client_email'] ?? '' ?>"
                    class="w-full bg-slate-50 border rounded-xl px-4 py-3 text-sm">

                    <input type="text" name="client_mobile" placeholder="Mobile"
                    value="<?= $project['client_mobile'] ?? '' ?>"
                    class="w-full bg-slate-50 border rounded-xl px-4 py-3 text-sm">

                    <textarea name="address" placeholder="Address"
                    class="w-full bg-slate-50 border rounded-xl px-4 py-3 text-sm"><?= $project['address'] ?? '' ?></textarea>

                    <div class="grid grid-cols-3 gap-2">
                        <input type="text" name="city" placeholder="City"
                        value="<?= $project['city'] ?? '' ?>" class="bg-slate-50 border rounded-xl px-3 py-2 text-sm">

                        <input type="text" name="state" placeholder="State"
                        value="<?= $project['state'] ?? '' ?>" class="bg-slate-50 border rounded-xl px-3 py-2 text-sm">

                        <input type="text" name="pincode" placeholder="Pincode"
                        value="<?= $project['pincode'] ?? '' ?>" class="bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-4">Completion Percentage</label>
                        <input type="number" name="progress" value="<?= $project['progress'] ?>" min="0" max="100"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-4 text-3xl font-bold text-center text-blue-600 input-focus outline-none">
                        <p class="mt-2 text-[10px] text-slate-400 italic">Adjust from 0% to 100%</p>
                    </div>

                    <!-- PROJECT AMOUNT -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">
                            Project Amount (₹)
                        </label>
                        <input type="number"
                            step="0.01"
                            name="project_amount"
                            value="<?= $project['project_amount'] ?? 0 ?>"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                    </div>

                    <!-- PAYMENT STATUS -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">
                            Payment Status
                        </label>
                        <select name="payment_status"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            
                            <option value="Pending" <?= ($project['payment_status'] == 'Pending') ? 'selected' : '' ?>>
                                Pending
                            </option>

                            <option value="Paid" <?= ($project['payment_status'] == 'Paid') ? 'selected' : '' ?>>
                                Paid
                            </option>

                        </select>
                    </div>


                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98] flex items-center justify-center space-x-2">
                        <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                        <span>Update Project</span>
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>