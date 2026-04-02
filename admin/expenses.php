<?php
require 'auth.php';
require '../config.php';

/* ================= DELETE ================= */
if(isset($_GET['delete'])){
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM expenses WHERE id = $id");
    header("Location: expenses.php");
    exit;
}

/* ================= EXPORT CSV ================= */
$where = "1";

if(!empty($_GET['from']) && !empty($_GET['to'])){
    $from = $_GET['from'];
    $to   = $_GET['to'];
    $where = "expense_date BETWEEN '$from' AND '$to'";
}

if(isset($_GET['export'])){
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="expenses.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Expense Name', 'Amount', 'Date']);

    $rows = $conn->query("SELECT * FROM expenses WHERE $where");

    while($r = $rows->fetch_assoc()){
        fputcsv($output, [
            $r['expense_name'],
            $r['amount'],
            $r['expense_date']
        ]);
    }

    fclose($output);
    exit;
}

/* ================= UPDATE ================= */
if(isset($_POST['update_id'])){
    $id     = (int) $_POST['update_id'];
    $name   = $_POST['expense_name'];
    $amount = $_POST['amount'];
    $date   = $_POST['expense_date'];

    $stmt = $conn->prepare("
        UPDATE expenses 
        SET expense_name=?, amount=?, expense_date=? 
        WHERE id=?
    ");
    $stmt->bind_param("sdsi", $name, $amount, $date, $id);
    $stmt->execute();

    header("Location: expenses.php");
    exit;
}

/* ================= INSERT ================= */
if($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['update_id'])){

    $names   = $_POST['expense_name'] ?? [];
    $amounts = $_POST['amount'] ?? [];
    $dates   = $_POST['expense_date'] ?? [];

    // 🔥 FIX: force arrays
    $names   = is_array($names)   ? $names   : [$names];
    $amounts = is_array($amounts) ? $amounts : [$amounts];
    $dates   = is_array($dates)   ? $dates   : [$dates];

    for($i = 0; $i < count($names); $i++){
        if(isset($names[$i], $amounts[$i], $dates[$i]) &&
           !empty($names[$i]) && !empty($amounts[$i]) && !empty($dates[$i])){

            $stmt = $conn->prepare("
                INSERT INTO expenses (expense_name, amount, expense_date)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sds", $names[$i], $amounts[$i], $dates[$i]);
            $stmt->execute();
        }
    }

    header("Location: expenses.php");
    exit;
}
/* ================= MONTH FILTER ================= */
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : date('Y');

/* COMMON CONDITION */
$where = "MONTH(expense_date) = $month AND YEAR(expense_date) = $year";
/* ================= FETCH ================= */
$expenses = $conn->query("
SELECT * FROM expenses
WHERE $where
ORDER BY expense_date DESC
");

/* ================= KPI (NOW FILTER-BASED) ================= */
$total_expense = $conn->query("
SELECT SUM(amount) as total
FROM expenses
WHERE $where
")->fetch_assoc()['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Expenses | Billing Pro</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }

.data-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.sidebar-overlay {
    background: rgba(0,0,0,0.5);
    display: none;
}
.sidebar-overlay.active { display:block; }
</style>

</head>

<body class="bg-[#f8fafc] text-slate-900">

<div class="flex h-screen overflow-hidden relative">

    <!-- MOBILE OVERLAY -->
    <div id="sidebarOverlay" onclick="toggleMobileMenu()" class="fixed inset-0 z-40 md:hidden sidebar-overlay"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 text-white transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0 md:flex flex-col border-r border-slate-800">

        <div class="p-8 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 p-2.5 rounded-xl shadow-lg shadow-blue-500/20">
                    <i data-lucide="layers" class="w-6 h-6"></i>
                </div>
                <span class="text-xl font-bold">Billing Pro</span>
            </div>
            <button onclick="toggleMobileMenu()" class="md:hidden text-slate-400">
                <i data-lucide="x"></i>
            </button>
        </div>

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto no-scrollbar">
            <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Main Menu</p>

            <a href="../index.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 px-4 py-3 rounded-xl">
                <i data-lucide="house"></i><span>Home</span>
            </a>

            <a href="dashboard.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 px-4 py-3 rounded-xl">
                <i data-lucide="layout-dashboard"></i><span>Dashboard</span>
            </a>

            <a href="expenses.php" class="flex items-center space-x-3 bg-blue-600 text-white px-4 py-3 rounded-xl shadow-lg">
                <i data-lucide="wallet"></i><span>Expenses</span>
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

            <!-- <a href="projects_list.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="folder" class="w-5 h-5"></i>
                <span>Projects</span>
            </a> -->

            <div class="pt-8 mt-8 border-t border-slate-900">
                <a href="logout.php" class="flex items-center space-x-3 text-red-400 hover:bg-red-500/10 px-4 py-3 rounded-xl">
                    <i data-lucide="log-out"></i><span>Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="flex-1 overflow-y-auto">

        <!-- HEADER -->
        <header class="bg-white border-b px-6 md:px-10 py-6 sticky top-0 z-20 flex justify-between items-center">
            <div class="flex items-center">
                <button onclick="toggleMobileMenu()" class="md:hidden mr-4 p-2">
                    <i data-lucide="menu"></i>
                </button>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold">Expense Management</h2>
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Company Expense Tracking</p>
                </div>
            </div>
        </header>

        <div class="p-4 md:p-10 space-y-6">

            <!-- MONTH BOX -->
            <div class="bg-white p-6 rounded-2xl shadow border">
                <p class="text-xs text-slate-500">This Month Expense</p>
                <h2 class="text-2xl font-bold text-red-600">
                    ₹<?= number_format($total_expense,2) ?>
                </h2>
            </div>

            <!-- FORM -->
            <div class="data-card rounded-2xl shadow p-6">
                <h3 class="font-bold mb-4">Add Expenses</h3>

                <form method="POST">
                    <div id="expense-wrapper">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <input type="text" name="expense_name[]" placeholder="Expense Name" class="border rounded-xl p-3" required>
                            <input type="number" step="0.01" name="amount[]" placeholder="Amount" class="border rounded-xl p-3" required>
                            <input type="date" name="expense_date[]" class="border rounded-xl p-3" required>
                        </div>

                    </div>

                    <button type="button" onclick="addRow()" class="text-sm bg-slate-100 px-4 py-2 rounded-lg mb-4">
                        + Add More
                    </button>

                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold">
                        Save Expenses
                    </button>
                </form>
            </div>

            <!-- FILTER -->
            <div class="flex gap-4">
                <div class="bg-white p-6 rounded-2xl shadow border flex items-center justify-between">

                    <div>
                        <p class="text-xs text-slate-500">Selected Period</p>
                        <h2 class="text-lg font-bold">
                            <?= date('F Y', mktime(0,0,0,$month,1,$year)) ?>
                        </h2>
                    </div>

                    <form method="GET" class="flex gap-2">

                        <select name="month" class="border rounded-lg p-2 text-sm">
                            <?php for($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= ($m == $month) ? 'selected' : '' ?>>
                                    <?= date('M', mktime(0,0,0,$m,1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>

                        <select name="year" class="border rounded-lg p-2 text-sm">
                            <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                                <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>

                        <button class="bg-blue-600 text-white px-4 rounded-lg text-sm">
                            Apply
                        </button>

                    </form>

                </div>
            </div>
            <a href="expenses.php?export=1&month=<?= $month ?>&year=<?= $year ?>" 
                class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                Export CSV
            </a>

            <?php if(isset($_GET['edit'])): 
                $edit_id = (int) $_GET['edit'];
                $edit = $conn->query("SELECT * FROM expenses WHERE id=$edit_id")->fetch_assoc();
            ?>

            <div class="data-card p-6 rounded-2xl">
                <h3 class="font-bold mb-4">Edit Expense</h3>

                <form method="POST">
                    <input type="hidden" name="update_id" value="<?= $edit['id'] ?>">

                    <input type="text" name="expense_name" value="<?= $edit['expense_name'] ?>" class="border p-3 rounded-xl w-full mb-3">
                    <input type="number" step="0.01" name="amount" value="<?= $edit['amount'] ?>" class="border p-3 rounded-xl w-full mb-3">
                    <input type="date" name="expense_date" value="<?= $edit['expense_date'] ?>" class="border p-3 rounded-xl w-full mb-3">

                    <button class="bg-blue-600 text-white px-4 py-2 rounded-xl">Update</button>
                </form>
            </div>

            <?php endif; ?>

            <!-- TABLE -->
            <div class="data-card rounded-3xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="expenseTable" class="w-full text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-xs uppercase">Expense</th>
                                <th class="px-6 py-4 text-xs uppercase">Amount</th>
                                <th class="px-6 py-4 text-xs uppercase">Date</th>
                                <th class="px-6 py-4 text-xs uppercase">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php while($row = $expenses->fetch_assoc()): ?>
                        <tr class="border-t hover:bg-slate-50">
                            <td class="px-6 py-4"><?= $row['expense_name'] ?></td>
                            <td class="px-6 py-4 text-red-600 font-semibold">
                                ₹<?= number_format($row['amount'],2) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= date('d M Y', strtotime($row['expense_date'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <a href="expenses.php?delete=<?= $row['id'] ?>" 
                                onclick="return confirm('Delete this expense?')"
                                class="text-red-500 font-bold text-sm">
                                Delete
                                </a>
                                <a href="expenses.php?edit=<?= $row['id'] ?>" 
                                    class="text-blue-500 text-sm font-bold mr-2">
                                    Edit
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

function toggleMobileMenu(){
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.toggle('active');
}

function addRow(){
    let row = `
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <input type="text" name="expense_name[]" placeholder="Expense Name" class="border rounded-xl p-3" required>
        <input type="number" step="0.01" name="amount[]" placeholder="Amount" class="border rounded-xl p-3" required>
        <input type="date" name="expense_date[]" class="border rounded-xl p-3" required>
    </div>`;
    document.getElementById('expense-wrapper').insertAdjacentHTML('beforeend', row);
}

$(document).ready(function(){
    $('#expenseTable').DataTable({
        pageLength: 5,
        order: [[2,'desc']]
    });
});
</script>

</body>
</html>