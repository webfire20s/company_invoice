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

            <a href="index.php"
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
                                <label class="block text-sm font-medium mb-2">Description</label>
                                <textarea name="description"
                                          rows="3"
                                          class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                          required></textarea>
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

</body>
</html>
