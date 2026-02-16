<?php
require 'auth.php';
$page_title = "Create Invoice";
?>

<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div class="content-wrapper">

  <div class="row">
    <div class="col-md-12 grid-margin">
      <div class="card">
        <div class="card-body">

          <h4 class="card-title">Create Invoice</h4>

          <form method="POST" action="../generate_invoice.php">

            <!-- Client Details -->
            <h5 class="mt-4 mb-3">Client Details</h5>

            <div class="row">

              <div class="col-md-6 mb-3">
                <label class="form-label">Client Name</label>
                <input type="text" name="client_name" class="form-control" required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="client_email" class="form-control" required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Mobile</label>
                <input type="text" name="client_mobile" class="form-control">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                  <option value="Unpaid">Unpaid</option>
                  <option value="Paid">Paid</option>
                </select>
              </div>

              <div class="col-md-12 mb-3">
                <label class="form-label">Address</label>
                <textarea name="client_address" 
                          class="form-control" 
                          rows="3" 
                          required></textarea>
              </div>

            </div>

            <!-- Invoice Details -->
            <h5 class="mt-4 mb-3">Invoice Details</h5>

            <div class="row">

              <div class="col-md-12 mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" 
                          class="form-control" 
                          rows="3" 
                          required></textarea>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Amount (₹)</label>
                <input type="number" 
                       step="0.01" 
                       name="amount" 
                       class="form-control" 
                       required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Discount (₹)</label>
                <input type="number" 
                       step="0.01" 
                       name="discount" 
                       value="0"
                       class="form-control">
              </div>

            </div>

            <!-- Submit -->
            <div class="mt-4">
              <button type="submit" class="btn btn-primary">
                Generate Invoice
              </button>

              <a href="dashboard.php" class="btn btn-light">
                Cancel
              </a>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>

</div>

<?php include 'admin/layout/footer.php'; ?>
