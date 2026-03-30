<?php
require 'auth.php';
require '../config.php';
// CHECK IF CLIENT EXISTS
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $client_name = $_POST['client_name'];
    $client_mobile = $_POST['client_mobile'];

    $check = $conn->query("
    SELECT id FROM clients
    WHERE client_name = '$client_name'
    AND client_mobile = '$client_mobile'
    ");

    if($check->num_rows == 0){

        $client_code = 'CLT' . date('Y') . rand(1000,9999);

        $stmt = $conn->prepare("
        INSERT INTO clients 
        (client_code, client_name, email, mobile, address, city, state, pincode)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssssss",
            $client_code,
            $client_name,
            $client_email,
            $client_mobile,
            $address,
            $city,
            $state,
            $pincode
        );

        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Invoice | Billing Pro</title>
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

            <a href="expenses.php" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-3 rounded-xl transition-all duration-200">
                <i data-lucide="wallet"></i><span>Expenses</span>
            </a>

            <a href="invoice_form.php" class="flex items-center space-x-3 bg-blue-600 text-white px-4 py-3 rounded-xl shadow-lg">
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
                <a href="logout.php" class="flex items-center space-x-3 text-red-400 hover:bg-red-500/10 px-4 py-3 rounded-xl transition-all duration-200">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-6 md:px-10 py-4 md:py-6 sticky top-0 z-20 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden p-2 bg-slate-50 border border-slate-200 rounded-lg">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800">New Invoice</h2>
                    <p class="hidden sm:block text-xs text-slate-500 font-medium uppercase tracking-wider">Financial Documents</p>
                </div>
            </div>
            <!-- <a href="dashboard.php" class="text-slate-400 hover:text-slate-600 p-2">
                <i data-lucide="x" class="w-6 h-6"></i>
            </a> -->
        </header>

        <div class="p-4 md:p-10 max-w-5xl mx-auto">
            <div class="mb-4 relative">
                <label class="text-xs font-bold text-slate-500">Search Client</label>

                <input type="text" id="client_search"
                    placeholder="Type name or ID..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm">

                <!-- Dropdown -->
                <div id="client_results"
                    class="absolute bg-white border w-full mt-1 rounded-xl shadow hidden z-50 max-h-40 overflow-y-auto">
                </div>
            </div>
            <div class="form-card rounded-2xl md:rounded-3xl shadow-xl overflow-hidden">
                <form method="POST" action="../generate_invoice.php" class="divide-y divide-slate-100">

                    <div class="p-5 md:p-8 space-y-6">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex-shrink-0 flex items-center justify-center font-bold text-sm">01</span>
                            <h3 class="text-lg font-bold text-slate-800">Client Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Client Name</label>
                                <input type="text" name="client_name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Email</label>
                                <input type="email" name="client_email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Mobile</label>
                                <input type="text" name="client_mobile" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Status</label>
                                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                                    <option value="Unpaid">Unpaid</option>
                                    <option value="Paid">Paid</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Address</label>
                                <textarea name="client_address" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 md:p-8 space-y-6">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex-shrink-0 flex items-center justify-center font-bold text-sm">02</span>
                            <h3 class="text-lg font-bold text-slate-800">Services & Pricing</h3>
                        </div>

                        <input type="hidden" name="description" id="description">
                        
                        <div class="border border-slate-100 rounded-2xl overflow-hidden shadow-sm overflow-x-auto">
                            <table class="w-full text-left min-w-[500px]" id="itemsTable">
                                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="px-6 py-4">Service Description</th>
                                        <th class="px-6 py-4 w-40 text-right">Price (₹)</th>
                                        <th class="px-6 py-4 w-16 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody" class="divide-y divide-slate-50">
                                    <tr class="group">
                                        <td class="px-4 py-3">
                                            <input type="text" class="service w-full bg-transparent px-3 py-2 rounded-lg text-sm input-focus outline-none" placeholder="Service Name">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" class="price w-full bg-transparent px-3 py-2 text-right rounded-lg text-sm font-bold input-focus outline-none" placeholder="0.00" oninput="calculateTotal()">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" onclick="removeRow(this)" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" onclick="addRow()" class="mt-2 inline-flex items-center space-x-2 text-sm font-bold text-blue-600 hover:text-blue-700">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span>Add Item</span>
                        </button>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Discount (₹)</label>
                                <input type="number" name="discount" value="0" oninput="calculateTotal()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold text-red-600 input-focus outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">GST (%)</label>
                                <select id="gst_toggle" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm input-focus outline-none" onchange="toggleGST()">
                                    <option value="0">No GST</option>
                                    <option value="18">18% GST</option>
                                    <option value="12">12% GST</option>
                                    <option value="5">5% GST</option>
                                </select>
                                <input type="hidden" name="gst_rate" id="gst_rate" value="0">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Renewal (₹)</label>
                                <input type="number" name="renewal_charge" value="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm input-focus outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-blue-600 mb-1">Total Payable</label>
                                <input type="number" name="amount" readonly class="w-full bg-blue-50 border border-blue-100 text-blue-700 rounded-xl px-4 py-2 text-lg font-black outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="p-5 md:p-8 space-y-6 bg-slate-50/50">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex-shrink-0 flex items-center justify-center font-bold text-sm">03</span>
                            <h3 class="text-lg font-bold text-slate-800">Compliance</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">Client GSTIN</label>
                                <input type="text" name="gstin" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none uppercase">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">My GSTIN</label>
                                <input type="text" name="company_gstin" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none uppercase">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-2">HSN/SAC</label>
                                <input type="text" name="hsn_sac" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="p-5 md:p-8 flex flex-col sm:flex-row items-center justify-between bg-white rounded-b-3xl gap-4">
                        <p class="text-[11px] text-slate-400 text-center sm:text-left order-2 sm:order-1">Database and PDF will be updated upon generation.</p>
                        <div class="flex w-full sm:w-auto space-x-3 order-1 sm:order-2">
                            <a href="dashboard.php" class="flex-1 sm:flex-none text-center px-6 py-3 text-sm font-bold text-slate-500">Discard</a>
                            <button type="submit" class="flex-1 sm:flex-none bg-blue-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/30">
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

    function addRow(){
        const table = document.getElementById("itemsBody");
        const row = `
        <tr class="group">
            <td class="px-4 py-3"><input type="text" class="service w-full bg-transparent px-3 py-2 rounded-lg text-sm input-focus outline-none" placeholder="Service Name"></td>
            <td class="px-4 py-3"><input type="number" class="price w-full bg-transparent px-3 py-2 text-right rounded-lg text-sm font-bold input-focus outline-none" oninput="calculateTotal()"></td>
            <td class="px-4 py-3 text-center"><button type="button" onclick="removeRow(this)" class="p-2 text-slate-300 hover:text-red-500"><i data-lucide="trash-2" class="w-4 h-4"></i></button></td>
        </tr>`;
        table.insertAdjacentHTML("beforeend", row);
        lucide.createIcons();
    }

    function removeRow(btn){
        const rows = document.querySelectorAll("#itemsBody tr");
        if(rows.length > 1) { btn.closest("tr").remove(); calculateTotal(); }
    }

    function toggleGST(){
        document.getElementById("gst_rate").value = document.getElementById("gst_toggle").value;
        calculateTotal();
    }

    function calculateTotal(){
        let subtotal = 0;
        document.querySelectorAll(".price").forEach(input => subtotal += parseFloat(input.value) || 0);
        let discount = parseFloat(document.querySelector('input[name="discount"]').value) || 0;
        let gst_rate = parseFloat(document.getElementById("gst_rate").value) || 0;
        let total = (subtotal - discount) * (1 + gst_rate / 100);
        document.querySelector('input[name="amount"]').value = total.toFixed(2);
    }

    document.querySelector("form").addEventListener("submit", function(){
        let lines = [];
        document.querySelectorAll("#itemsBody tr").forEach(tr => {
            let s = tr.querySelector(".service").value;
            let p = tr.querySelector(".price").value;
            if(s) lines.push(s + "|" + (p || 0));
        });
        document.getElementById("description").value = lines.join("\n");
    });

</script>
<script>
const searchInput = document.getElementById('client_search');
const resultsBox = document.getElementById('client_results');

searchInput.addEventListener('input', function(){
    let query = this.value;

    if(query.length < 2){
        resultsBox.classList.add('hidden');
        return;
    }

    fetch('../get_client.php?q=' + query)
    .then(res => res.json())
    .then(data => {

        resultsBox.innerHTML = '';

        if(data.length === 0){
            resultsBox.classList.add('hidden');
            return;
        }

        data.forEach(client => {

            let div = document.createElement('div');
            div.className = "p-3 hover:bg-slate-100 cursor-pointer text-sm";
            div.innerHTML = `<strong>${client.client_name}</strong> (${client.mobile})`;

            div.onclick = () => {
                // Autofill
                document.querySelector('[name="client_name"]').value = client.client_name;
                document.querySelector('[name="client_email"]').value = client.email;
                document.querySelector('[name="client_mobile"]').value = client.mobile;
                document.querySelector('[name="client_address"]').value = client.address;
                document.querySelector('[name="city"]').value = client.city;
                document.querySelector('[name="state"]').value = client.state;
                document.querySelector('[name="pincode"]').value = client.pincode;

                // Hide dropdown properly
                resultsBox.classList.add('hidden');
                resultsBox.innerHTML = '';
                searchInput.blur(); // 🔥 important
            };

            resultsBox.appendChild(div);
        });

        resultsBox.classList.remove('hidden');
    });
});


// ✅ HIDE WHEN CLICKING OUTSIDE
document.addEventListener('click', function(e){
    if(!searchInput.contains(e.target) && !resultsBox.contains(e.target)){
        resultsBox.classList.add('hidden');
    }
});


// ✅ HIDE ON INPUT BLUR (WITH DELAY FOR CLICK)
searchInput.addEventListener('blur', function(){
    setTimeout(() => {
        resultsBox.classList.add('hidden');
    }, 150);
});
</script>

</body>
</html>