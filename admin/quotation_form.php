<?php
require 'auth.php';
$page_title = "Create Quotation";
?>

<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div class="content-wrapper">

  <div class="row">
    <div class="col-md-12 grid-margin">
      <div class="card">
        <div class="card-body">

          <h4 class="card-title">Create Quotation</h4>

          <form method="POST" action="../generate_quotation.php">

            <!-- Basic Details -->
            <div class="row">

              <div class="col-md-6 mb-3">
                <label class="form-label">Client Company Name</label>
                <input type="text" 
                       name="client_name" 
                       class="form-control" 
                       required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Subject</label>
                <input type="text" 
                       name="subject" 
                       class="form-control" 
                       required>
              </div>

            </div>

            <!-- Proposal Introduction -->
            <div class="mb-3">
              <label class="form-label">Proposal Introduction</label>
              <textarea name="introduction" 
                        rows="4" 
                        class="form-control" 
                        required></textarea>
            </div>

            <!-- Feature List -->
            <div class="mb-3">
              <label class="form-label">
                Feature List (One per line)
              </label>
              <textarea name="features" 
                        rows="6" 
                        class="form-control" 
                        required></textarea>
            </div>

            <!-- Technical Features -->
            <div class="mb-3">
              <label class="form-label">
                Technical Features (One per line)
              </label>
              <textarea name="technical_features" 
                        rows="6" 
                        class="form-control" 
                        required></textarea>
            </div>

            <!-- Payment Terms -->
            <div class="mb-3">
              <label class="form-label">
                Pricing & Payment Terms
              </label>
              <textarea name="payment_terms" 
                        rows="5" 
                        class="form-control" 
                        required></textarea>
            </div>

            <!-- Project Cost -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Total Project Cost (₹)</label>
                <input type="number" 
                       name="project_cost" 
                       class="form-control" 
                       required>
              </div>
            </div>

            <!-- Buttons -->
            <div class="mt-4">
              <button type="submit" class="btn btn-primary">
                Generate Quotation
              </button>

              <a href="admin/dashboard.php" class="btn btn-light">
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
