<?php
session_start();

/* 🔒 BLOCK ACCESS */
if (!isset($_SESSION['user_id'])) {
    header("Location: /login_page.php");
    exit;
}

/* 🔒 ANTI-BACK CACHE HEADERS */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/checkups.php";
require_once "../class/billings.php";
require_once "../class/stafflog.php";

// Database connection
$database = new Database();
$db = $database->connect();

// Initialize classes
$checkup = new Checkup($db);
$billing = new Billing($db);
$log = new StaffActivityLog($db);

// ====== COUNT CARDS ======
$patient_count = $db->query("SELECT COUNT(*) AS total FROM patients")->fetch(PDO::FETCH_ASSOC)['total'];
$today_checkups_count = $db->query("SELECT COUNT(*) AS total FROM checkups WHERE checkup_date = CURDATE()")->fetch(PDO::FETCH_ASSOC)['total'];
$unpaid_bills_count = $db->query("SELECT COUNT(*) AS total FROM billing WHERE payment_status='Unpaid'")->fetch(PDO::FETCH_ASSOC)['total'];
$low_stock_count = $db->query("SELECT COUNT(*) AS total FROM medicine_inventory WHERE quantity <= reorder_level")->fetch(PDO::FETCH_ASSOC)['total'];

// ====== TABLE DATA ======
$today_checkups = $checkup->filter(null, date('Y-m-d'));
$recent_billings = $billing->viewAll();
$recent_logs = $log->viewAll();
?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Welcome, Super Admin!</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <!-- ====== CARDS ====== -->
    <div class="row">
      <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
          <div class="card-body"><h5>Total Patients</h5><h2><?= $patient_count ?></h2></div>
          <div class="card-footer d-flex justify-content-between align-items-center">
            <a class="small text-white stretched-link" href="../public/patient.php">View Details</a>
            <i class="fas fa-user-injured"></i>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
          <div class="card-body"><h5>Today's Checkups</h5><h2><?= $today_checkups_count ?></h2></div>
          <div class="card-footer d-flex justify-content-between align-items-center">
            <a class="small text-white stretched-link" href="../public/checkups_dashboard.php">View Details</a>
            <i class="fas fa-calendar-check"></i>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white mb-4">
          <div class="card-body"><h5>Unpaid Bills</h5><h2><?= $unpaid_bills_count ?></h2></div>
          <div class="card-footer d-flex justify-content-between align-items-center">
            <a class="small text-white stretched-link" href="../public/billings_dashboard.php">View Details</a>
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="card bg-danger text-white mb-4">
          <div class="card-body"><h5>Low Stock Medicines</h5><h2><?= $low_stock_count ?></h2></div>
          <div class="card-footer d-flex justify-content-between align-items-center">
            <a class="small text-white stretched-link" href="../public/inventory_dashboard.php">View Details</a>
            <i class="fas fa-pills"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- ====== TODAY'S CHECKUPS TABLE ====== -->
    <div class="card mb-4">
      <div class="card-header"><i class="fas fa-table me-1"></i>Today's Checkups</div>
      <div class="card-body table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Checkup ID</th>
              <th>Patient Name</th>
              <th>Doctor</th>
              <th>Date</th>
              <th>Diagnosis</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($today_checkups as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['checkup_id']) ?></td>
                <td><?= htmlspecialchars($c['patient_name']) ?></td>
                <td><?= htmlspecialchars($c['doc_fullname']) ?></td>
                <td><?= htmlspecialchars($c['checkup_date']) ?></td>
                <td><?= htmlspecialchars($c['diagnosis']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ====== RECENT BILLINGS TABLE ====== -->
    <div class="card mb-4">
      <div class="card-header"><i class="fas fa-table me-1"></i>Recent Billing</div>
      <div class="card-body table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Bill ID</th>
              <th>Patient</th>
              <th>Doctor</th>
              <th>Checkup Date</th>
              <th>Total Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($recent_billings as $b): ?>
              <tr>
                <td><?= htmlspecialchars($b['bill_id']) ?></td>
                <td><?= htmlspecialchars($b['patient_name']) ?></td>
                <td><?= htmlspecialchars($b['doctor_name']) ?></td>
                <td><?= htmlspecialchars($b['checkup_date'] ?? '-') ?></td>
                <td><?= htmlspecialchars($b['total_amount']) ?></td>
                <td><?= htmlspecialchars($b['payment_status']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ====== RECENT STAFF ACTIONS TABLE ====== -->
    <div class="card mb-4">
      <div class="card-header"><i class="fas fa-table me-1"></i>Recent Staff Actions</div>
      <div class="card-body table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Log ID</th>
              <th>Staff</th>
              <th>Action</th>
              <th>Module</th>
              <th>Reference ID</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($recent_logs as $l): ?>
              <tr>
                <td><?= htmlspecialchars($l['log_id']) ?></td>
                <td><?= htmlspecialchars($l['staff_first_name'] . ' ' . $l['staff_last_name']) ?></td>
                <td><?= htmlspecialchars($l['action']) ?></td>
                <td><?= htmlspecialchars($l['module']) ?></td>
                <td><?= htmlspecialchars($l['reference_id'] ?? '-') ?></td>
                <td><?= htmlspecialchars($l['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<?php require_once "../includes/footer.php"; ?>
