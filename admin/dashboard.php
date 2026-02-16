<?php
require 'auth.php';
require '../config.php';

$search = $_GET['search'] ?? '';

?>

<?php $base_path = '';
include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div class="main-panel">
  <div class="content-wrapper">

    <!-- Page Header -->
    <div class="row">
      <div class="col-md-12 grid-margin">
        <div class="card">
          <div class="card-body d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Admin Dashboard</h4>
            <div>
              <a href="index.php" class="btn btn-success btn-sm">Create Invoice</a>
              <a href="quotation_form.php" class="btn btn-info btn-sm">Create Quotation</a>
              <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Card -->
    <div class="row">
      <div class="col-md-12 grid-margin">
        <div class="card">
          <div class="card-body">
            <form method="GET" class="d-flex">
              <input type="text" 
                     name="search" 
                     class="form-control me-2"
                     placeholder="Search by Client Name"
                     value="<?= htmlspecialchars($search) ?>">
              <button type="submit" class="btn btn-primary">Search</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Invoices Section -->
    <div class="row">
      <div class="col-md-12 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Invoices</h4>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Invoice No</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

<?php
$stmt = $conn->prepare("SELECT * FROM invoices 
                        WHERE client_name LIKE CONCAT('%', ?, '%')
                        ORDER BY id DESC");
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['invoice_number']}</td>
            <td>{$row['client_name']}</td>
            <td>{$row['date']}</td>
            <td>₹{$row['total_amount']}</td>
            <td>
              <a href='../{$row['file_path']}' 
                 target='_blank' 
                 class='btn btn-sm btn-outline-primary'>
                 View
              </a>
            </td>
          </tr>";
}
?>

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quotations Section -->
    <div class="row">
      <div class="col-md-12 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Quotations</h4>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Quotation No</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

<?php
$stmt = $conn->prepare("SELECT * FROM quotations 
                        WHERE client_name LIKE CONCAT('%', ?, '%')
                        ORDER BY id DESC");
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['quotation_number']}</td>
            <td>{$row['client_name']}</td>
            <td>{$row['date']}</td>
            <td>₹{$row['total_amount']}</td>
            <td>
              <a href='../{$row['file_path']}' 
                 target='_blank' 
                 class='btn btn-sm btn-outline-info'>
                 View
              </a>
            </td>
          </tr>";
}
?>

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include 'layout/footer.php'; ?>
