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
require_once "../class/stafflog.php";
require_once "../class/staff.php";



$database = new Database();
$db = $database->connect();

$log = new StaffActivityLog($db);
$staff = new Staff($db);

// Fetch staff for filter dropdown
$staffList = $staff->all();

// Handle filter form submission
$filterStaff = $_POST['filter_staff'] ?? null;
$filterStart = $_POST['filter_start'] ?? null;
$filterEnd   = $_POST['filter_end'] ?? null;

$logs = $log->filterLogs($filterStaff, $filterStart, $filterEnd);
?>

<main>
<div class="container-fluid px-4">
    <h1 class="mt-4">Staff Activity Logs</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Logs</li>
    </ol>

    <!-- Filter Form -->
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Staff</label>
            <select name="filter_staff" class="form-select">
                <option value="">-- All Staff --</option>
                <?php foreach ($staffList as $s): ?>
                    <option value="<?= $s['staff_id'] ?>" <?= ($filterStaff == $s['staff_id']) ? 'selected' : '' ?>>
                        <?= $s['staff_first_name'] . ' ' . $s['staff_last_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="filter_start" class="form-control" value="<?= $filterStart ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="filter_end" class="form-control" value="<?= $filterEnd ?>">
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter Logs</button>
        </div>
    </form>

    <!-- Logs Table -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="fas fa-list me-2"></i>Activity Logs
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Staff Name</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Reference ID</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($logs)): ?>
                        <?php foreach($logs as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= $row['staff_first_name'] . ' ' . $row['staff_last_name'] ?></td>
                                <td><?= $row['action'] ?></td>
                                <td><?= $row['module'] ?></td>
                                <td><?= $row['reference_id'] ?? '-' ?></td>
                                <td><?= $row['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-danger">No logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>
