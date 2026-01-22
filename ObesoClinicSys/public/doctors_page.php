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

$database = new Database();
$db = $database->connect();

/* =======================
   VALIDATE DOCTOR ID
======================= */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No doctor selected'); window.location='doctor_dashboard.php';</script>";
    exit;
}

$doc_id = intval($_GET['id']);

/* =======================
   GET DOCTOR INFO
======================= */
$doctorStmt = $db->prepare("
    SELECT * FROM doctors WHERE doc_id = :doc_id
");
$doctorStmt->execute([':doc_id' => $doc_id]);
$doctor = $doctorStmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    echo "<script>alert('Doctor not found'); window.location='doctor_dashboard.php';</script>";
    exit;
}

/* =======================
   CHECKUPS (PAST / TODAY / FUTURE)
======================= */
$today = date('Y-m-d');

function fetchCheckups($db, $doc_id, $condition) {
    $sql = "
        SELECT c.*, p.full_name
        FROM checkups c
        JOIN patients p ON c.patient_id = p.patient_id
        WHERE c.doc_id = :doc_id AND $condition
        ORDER BY c.checkup_date DESC
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute([':doc_id' => $doc_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$todayCheckups  = fetchCheckups($db, $doc_id, "c.checkup_date = CURDATE()");
$futureCheckups = fetchCheckups($db, $doc_id, "c.checkup_date > CURDATE()");
$pastCheckups   = fetchCheckups($db, $doc_id, "c.checkup_date < CURDATE()");

/* =======================
   PATIENTS HANDLED
======================= */
$patientsStmt = $db->prepare("
    SELECT DISTINCT p.patient_id, p.full_name, p.sex, p.age
    FROM patients p
    JOIN checkups c ON p.patient_id = c.patient_id
    WHERE c.doc_id = :doc_id
");
$patientsStmt->execute([':doc_id' => $doc_id]);
$patients = $patientsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
<div class="container-fluid px-4 mt-4">

<!-- =======================
     DOCTOR PROFILE
======================= -->
<div class="card shadow-sm mb-4">
  <div class="card-body d-flex align-items-center">
    <img src="../assets/images/dafultimage.jpg" width="120" class="rounded me-4">
    <div>
      <h3 class="text-primary mb-1">Dr. <?= htmlspecialchars($doctor['doc_fullname']) ?></h3>
      <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($doctor['doc_email']) ?></p>
      <p class="mb-0"><strong>Contact:</strong> <?= htmlspecialchars($doctor['doc_contact_num']) ?></p>
    </div>
  </div>
</div>

<!-- =======================
     CHECKUPS
======================= -->
<div class="row">

<!-- TODAY -->
<div class="col-md-4">
  <div class="card border-success mb-4">
    <div class="card-header bg-success text-white">
      Today's Checkups
    </div>
    <div class="card-body">
      <?php if (empty($todayCheckups)): ?>
        <p class="text-muted text-center">No checkups today</p>
      <?php else: ?>
        <?php foreach ($todayCheckups as $c): ?>
          <div class="mb-2">
            <strong><?= htmlspecialchars($c['full_name']) ?></strong><br>
            <small><?= htmlspecialchars($c['diagnosis']) ?></small>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- FUTURE -->
<div class="col-md-4">
  <div class="card border-info mb-4">
    <div class="card-header bg-info text-white">
      Future Checkups
    </div>
    <div class="card-body">
      <?php if (empty($futureCheckups)): ?>
        <p class="text-muted text-center">No upcoming checkups</p>
      <?php else: ?>
        <?php foreach ($futureCheckups as $c): ?>
          <div class="mb-2">
            <strong><?= htmlspecialchars($c['full_name']) ?></strong><br>
            <small><?= htmlspecialchars($c['checkup_date']) ?></small>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- PAST -->
<div class="col-md-4">
  <div class="card border-secondary mb-4">
    <div class="card-header bg-secondary text-white">
      Past Checkups
    </div>
    <div class="card-body">
      <?php if (empty($pastCheckups)): ?>
        <p class="text-muted text-center">No past checkups</p>
      <?php else: ?>
        <?php foreach ($pastCheckups as $c): ?>
          <div class="mb-2">
            <strong><?= htmlspecialchars($c['full_name']) ?></strong><br>
            <small><?= htmlspecialchars($c['diagnosis']) ?></small>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

</div>

<!-- =======================
     PATIENTS HANDLED
======================= -->
<div class="card mt-4">
  <div class="card-header bg-dark text-white">
    Patients Handled by Dr. <?= htmlspecialchars($doctor['doc_fullname']) ?>
  </div>
  <div class="card-body">
    <?php if (empty($patients)): ?>
      <p class="text-muted text-center">No patients yet</p>
    <?php else: ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Sex</th>
            <th>Age</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($patients as $p): ?>
            <tr>
              <td><?= $p['patient_id'] ?></td>
              <td><?= htmlspecialchars($p['full_name']) ?></td>
              <td><?= htmlspecialchars($p['sex']) ?></td>
              <td><?= htmlspecialchars($p['age']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

</div>
</main>

<?php require_once "../includes/footer.php"; ?>
