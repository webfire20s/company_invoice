<?php
require 'auth.php';
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

    <nav class="flex-1 px-4 space-y-1">
        <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Main Menu</p>
        
        <a href="../index.php" 
           class="flex items-center space-x-3 text-slate-400 hover:bg-slate-900 hover:text-white px-4 py-3 rounded-xl transition-all duration-200">
            <i data-lucide="house" class="w-5 h-5"></i>
            <span class="font-medium">Home</span>
        </a>

        <a href="dashboard.php" 
           class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="invoice_form.php" 
           class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'invoice_form.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
            <i data-lucide="file-plus" class="w-5 h-5"></i>
            <span class="font-medium">Create Invoice</span>
        </a>

        <a href="quotation_form.php" 
           class="flex items-center space-x-3 <?php echo (basename($_SERVER['PHP_SELF']) == 'quotation_form.php') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white'; ?> px-4 py-3 rounded-xl transition-all duration-200">
            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
            <span class="font-medium">Create Quotation</span>
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

        <header class="bg-white border-b border-slate-200 px-10 py-6 sticky top-0 z-20 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Generate New Invoice</h2>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Financial Documents / Invoicing</p>
            </div>
            <a href="dashboard.php" class="text-slate-400 hover:text-slate-600 p-2">
                <i data-lucide="x" class="w-6 h-6"></i>
            </a>
        </header>

        <div class="p-10 max-w-5xl mx-auto">

            <div class="form-card rounded-3xl shadow-xl overflow-hidden">
                <form method="POST" action="../generate_invoice.php" class="divide-y divide-slate-100">

                    <div class="p-8 space-y-6">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">01</span>
                            <h3 class="text-lg font-bold text-slate-800">Client Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Client Name</label>
                                <input type="text" name="client_name" placeholder="Acme Corp"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Email Address</label>
                                <input type="email" name="client_email" placeholder="billing@acme.com"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Mobile Number</label>
                                <input type="text" name="client_mobile" placeholder="+91 00000 00000"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Payment Status</label>
                                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none appearance-none">
                                    <option value="Unpaid">Unpaid / Pending</option>
                                    <option value="Paid">Paid / Cleared</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Billing Address</label>
                                <textarea name="client_address" rows="3" placeholder="Street name, City, State, ZIP..."
                                          class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 space-y-6">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">02</span>
                            <h3 class="text-lg font-bold text-slate-800">Services & Pricing</h3>
                        </div>

                        <div class="md:col-span-2">
                            <input type="hidden" name="description" id="description">
                            
                            <div class="border border-slate-100 rounded-2xl overflow-hidden shadow-sm">
                                <table class="w-full text-left" id="itemsTable">
                                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                        <tr>
                                            <th class="px-6 py-4">Service / Product Description</th>
                                            <th class="px-6 py-4 w-48 text-right">Price (₹)</th>
                                            <th class="px-6 py-4 w-20 text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody" class="divide-y divide-slate-50">
                                        <tr class="group">
                                            <td class="px-4 py-3">
                                                <input type="text" class="service w-full bg-transparent px-3 py-2 rounded-lg text-sm input-focus outline-none"
                                                placeholder="Example: UI/UX Design Project">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" class="price w-full bg-transparent px-3 py-2 text-right rounded-lg text-sm font-bold text-slate-700 input-focus outline-none"
                                                placeholder="0.00" oninput="calculateTotal()">
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

                            <button type="button" onclick="addRow()"
                                    class="mt-4 inline-flex items-center space-x-2 text-sm font-bold text-blue-600 hover:text-blue-700 transition">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                <span>Add Another Line Item</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Discount (₹)</label>
                                <input type="number" step="0.01" name="discount" value="0" oninput="calculateTotal()"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-red-600 input-focus outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Tax (GST %)</label>
                                <select id="gst_toggle" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none" onchange="toggleGST()">
                                    <option value="0">No GST</option>
                                    <option value="5">5% GST</option>
                                    <option value="12">12% GST</option>
                                    <option value="18">18% GST</option>
                                    <option value="28">28% GST</option>
                                </select>
                                <input type="hidden" name="gst_rate" id="gst_rate" value="0">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Renewal (₹)</label>
                                <input type="number" step="0.01" name="renewal_charge" value="0"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2 text-blue-600">Total Payable</label>
                                <input type="number" step="0.01" name="amount" readonly
                                       class="w-full bg-blue-50 border border-blue-100 text-blue-700 rounded-xl px-4 py-3 text-lg font-black outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="p-8 space-y-6 bg-slate-50/50">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">03</span>
                            <h3 class="text-lg font-bold text-slate-800">Compliance & GST (Optional)</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Client GSTIN</label>
                                <input type="text" name="gstin" placeholder="Ex: 22AAAAA0000A1Z5"
                                       class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none uppercase">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Your Company GSTIN</label>
                                <input type="text" name="company_gstin" placeholder="Ex: 22BBBBB0000B1Z5"
                                       class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none uppercase">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Place of Supply</label>
                                <input type="text" name="place_of_supply" placeholder="Ex: Uttar Pradesh"
                                       class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>
                            <div class="lg:col-span-3">
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">HSN / SAC Code</label>
                                <input type="text" name="hsn_sac" placeholder="998311"
                                       class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm input-focus outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="p-8 flex items-center justify-between bg-white rounded-b-3xl">
                        <p class="text-xs text-slate-400 max-w-xs">By generating this invoice, it will be saved to your database and a PDF file will be created automatically.</p>
                        <div class="flex space-x-4">
                            <a href="dashboard.php" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-800 transition">Discard</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all active:scale-95">
                                Generate Invoice
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

    function addRow(){
        const table = document.getElementById("itemsBody");
        const row = `
        <tr class="group">
            <td class="px-4 py-3">
                <input type="text" class="service w-full bg-transparent px-3 py-2 rounded-lg text-sm input-focus outline-none" placeholder="Service name">
            </td>
            <td class="px-4 py-3">
                <input type="number" class="price w-full bg-transparent px-3 py-2 text-right rounded-lg text-sm font-bold text-slate-700 input-focus outline-none" placeholder="0.00" oninput="calculateTotal()">
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeRow(this)" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        </tr>`;
        table.insertAdjacentHTML("beforeend", row);
        lucide.createIcons(); // Refresh icons for new row
    }

    function removeRow(btn){
        const rows = document.querySelectorAll("#itemsBody tr");
        if(rows.length > 1) {
            btn.closest("tr").remove();
            calculateTotal();
        }
    }

    function toggleGST(){
        let rate = document.getElementById("gst_toggle").value;
        document.getElementById("gst_rate").value = rate;
        calculateTotal();
    }

    function calculateTotal(){
        let subtotal = 0;
        document.querySelectorAll(".price").forEach(input => {
            subtotal += parseFloat(input.value) || 0;
        });

        let discount = parseFloat(document.querySelector('input[name="discount"]').value) || 0;
        let gst_rate = parseFloat(document.getElementById("gst_rate").value) || 0;
        
        let after_discount = subtotal - discount;
        let gst_amount = after_discount * (gst_rate / 100);
        let total = after_discount + gst_amount;

        document.querySelector('input[name="amount"]').value = total.toFixed(2);
    }

    function prepareDescription(){
        let services = document.querySelectorAll(".service");
        let prices = document.querySelectorAll(".price");
        let lines = [];

        services.forEach((service, i)=>{
            if(service.value.trim() !== ""){
                let price = prices[i].value || 0;
                lines.push(service.value.trim() + "|" + price);
            }
        });
        document.getElementById("description").value = lines.join("\n");
    }

    document.querySelector("form").addEventListener("submit", prepareDescription);
</script>

</body>
</html>