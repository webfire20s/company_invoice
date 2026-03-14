<?php
require 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col">
        <div class="p-6 flex items-center space-x-3">
            <div class="bg-blue-600 p-2 rounded-lg">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <span class="text-xl font-bold">Billing System</span>
        </div>

        <nav class="flex-1 px-4 space-y-2 mt-4">
            <a href="dashboard.php"
               class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>

            <a href="invoice_form.php"
               class="flex items-center space-x-3 bg-blue-600 px-4 py-3 rounded-xl">
                <i data-lucide="file-plus" class="w-5 h-5"></i>
                <span>Create Invoice</span>
            </a>

            <a href="quotation_form.php"
               class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-3 rounded-xl">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                <span>Create Quotation</span>
            </a>

            <div class="pt-8">
                <a href="logout.php"
                   class="flex items-center space-x-3 text-red-400 hover:bg-red-500/10 px-4 py-3 rounded-xl">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">

        <header class="bg-white border-b px-8 py-4">
            <h2 class="text-lg font-semibold">Create Invoice</h2>
        </header>

        <div class="p-8">

            <div class="bg-white rounded-2xl border shadow-sm p-8">

                <form method="POST" action="../generate_invoice.php" class="space-y-10">

                    <!-- Client Details -->
                    <div>
                        <h3 class="text-lg font-semibold mb-6 border-b pb-2">Client Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label class="block text-sm font-medium mb-2">Client Name</label>
                                <input type="text" name="client_name"
                                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Email</label>
                                <input type="email" name="client_email"
                                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Mobile</label>
                                <input type="text" name="client_mobile"
                                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Status</label>
                                <select name="status"
                                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="Unpaid">Unpaid</option>
                                    <option value="Paid">Paid</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-2">Address</label>
                                <textarea name="client_address"
                                          rows="3"
                                          class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                          required></textarea>
                            </div>

                        </div>
                    </div>

                    <!-- Invoice Details -->
                    <div>
                        <h3 class="text-lg font-semibold mb-6 border-b pb-2">Invoice Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="md:col-span-2">

                                <label class="block text-sm font-medium mb-4">Services / Products</label>

                                <input type="hidden" name="description" id="description">

                                <table class="w-full border rounded-lg overflow-hidden" id="itemsTable">

                                    <thead class="bg-slate-100 text-sm">
                                        <tr>
                                            <th class="text-left p-3 border">Service / Product</th>
                                            <th class="text-left p-3 border w-40">Price (₹)</th>
                                            <th class="text-center p-3 border w-20">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody id="itemsBody">

                                        <tr>
                                            <td class="border p-2">
                                                <input type="text" class="service w-full px-3 py-2 border rounded-md"
                                                placeholder="Example: Website Development">
                                            </td>

                                            <td class="border p-2">
                                                <input type="number" class="price w-full px-3 py-2 border rounded-md"
                                                placeholder="0"
                                                oninput="calculateTotal()">
                                            </td>

                                            <td class="border text-center">
                                                <button type="button"
                                                onclick="removeRow(this)"
                                                class="text-red-500 hover:text-red-700">
                                                ✕
                                                </button>
                                            </td>
                                        </tr>

                                    </tbody>

                                </table>

                                <button type="button"
                                onclick="addRow()"
                                class="mt-3 bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-slate-700 transition">
                                + Add Blank Line
                                </button>

                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Amount (₹)</label>
                                <input type="number"
                                       step="0.01"
                                       name="amount"
                                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Discount (₹)</label>
                                <input type="number"
                                       step="0.01"
                                       name="discount"
                                       value="0"
                                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Apply GST</label>

                                <select id="gst_toggle"
                                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                                        onchange="toggleGST()">

                                    <option value="0">No GST</option>
                                    <option value="5">5% GST</option>
                                    <option value="12">12% GST</option>
                                    <option value="18">18% GST</option>
                                    <option value="28">28% GST</option>

                                </select>

                                <input type="hidden" name="gst_rate" id="gst_rate" value="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Next Year Renewal Charge </label>
                                <input type="number"
                                       step="0.01"
                                       name="renewal_charge"
                                       value="0"
                                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <!-- GST Details (Optional) -->

                                <div>
                                    <h3 class="text-lg font-semibold mb-6 border-b pb-2">GST Details (Optional)</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                        <!-- <div>
                                            <label class="block text-sm font-medium mb-2">GST Rate (%)</label>
                                            <select name="gst_rate"
                                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">

                                                <option value="0">No GST</option>
                                                <option value="5">5%</option>
                                                <option value="12">12%</option>
                                                <option value="18">18%</option>
                                                <option value="28">28%</option>

                                            </select>
                                        </div> -->

                                        <div>
                                            <label class="block text-sm font-medium mb-2">Client GSTIN</label>
                                            <input type="text"
                                            name="gstin"
                                            placeholder="Optional"
                                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-2">Your GSTIN</label>
                                            <input type="text"
                                            name="company_gstin"
                                            placeholder="Optional"
                                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-2">Place of Supply</label>
                                            <input type="text"
                                            name="place_of_supply"
                                            placeholder="Optional"
                                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium mb-2">HSN / SAC Code</label>
                                            <input type="text"
                                            name="hsn_sac"
                                            placeholder="Optional"
                                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        </div>

                                    </div>
                                </div>

                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                            Generate Invoice
                        </button>

                        <a href="dashboard.php"
                           class="bg-slate-200 hover:bg-slate-300 px-6 py-2 rounded-lg font-medium transition">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </main>
</div>

<script>
    lucide.createIcons();
</script>
<script>

function addRow(){

    const table = document.getElementById("itemsBody");

    const row = `
    <tr>

    <td class="border p-2">
    <input type="text" class="service w-full px-3 py-2 border rounded-md"
    placeholder="Service name">
    </td>

    <td class="border p-2">
    <input type="number" class="price w-full px-3 py-2 border rounded-md"
    placeholder="0"
    oninput="calculateTotal()">
    </td>

    <td class="border text-center">
    <button type="button"
    onclick="removeRow(this)"
    class="text-red-500 hover:text-red-700">✕</button>
    </td>

    </tr>
    `;

    table.insertAdjacentHTML("beforeend", row);

}

function removeRow(btn){

    btn.closest("tr").remove();

    calculateTotal();

}

function calculateTotal(){

    let total = 0;

    document.querySelectorAll(".price").forEach(input => {

        total += parseFloat(input.value) || 0;

    });

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

<script>

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

</script>
</body>
</html>
