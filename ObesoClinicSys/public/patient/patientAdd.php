<?php
require_once "../config/db.php";
require_once "../class/patient.php";
require_once "../class/user.php";

$database = new Database();
$db = $database->connect();
$patient = new Patient($db);
$user = new User($db);

$rows = $patient->viewAll(); // fetch all patients
$lastPatientID = null;

// -------------------------
// ADD PATIENT
// -------------------------
if (isset($_POST['add_patient'])) {
    $full_name         = trim($_POST['full_name']);
    $address           = trim($_POST['address']);
    $birthday          = trim($_POST['birthday']);
    $age               = intval($_POST['age']);
    $sex               = trim($_POST['sex']);
    $civil_status      = trim($_POST['civil_status']);
    $religion          = trim($_POST['religion']);
    $occupation        = trim($_POST['occupation']);
    $contact_person    = trim($_POST['contact_person']);
    $contact_person_age= intval($_POST['contact_person_age']);
    $contact_number    = trim($_POST['contact_number']);

    if ($patient->add(
        $full_name, $address, $birthday, $age, $sex,
        $civil_status, $religion, $occupation,
        $contact_person, $contact_person_age, $contact_number
    )) {
        $lastPatientID = $db->lastInsertId();
        $rows = $patient->viewAll();

        // Show account creation modal automatically
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var accountModal = new bootstrap.Modal(document.getElementById('accountModal'));
                accountModal.show();
            });
        </script>";
    } else {
        echo "<script>alert('❌ Error adding patient.'); window.location='../public/patient.php';</script>";
    }
}

// -------------------------
// CREATE ACCOUNT
// -------------------------
if (isset($_POST["create_account"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $pat_id = $_POST["pat_id"];

    if ($user->create($username, $password, $pat_id, null, null, false)) {
        $rows = $patient->viewAll();
        echo "<script>alert('✅ Account created successfully!'); window.location='../public/patient.php';</script>";
    } else {
        echo "<script>alert('❌ Error creating account.'); window.location='../public/patient.php';</script>";
    }
}
?>


<div class="mb-3">
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="fa-solid fa-user-plus"></i> Add New Patient
  </button>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Patient</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Address</label>
              <input type="text" class="form-control" name="address" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Birthday</label>
              <input type="date" class="form-control" name="birthday" required>
            </div>
            <div class="col-md-2">
              <label class="form-label">Age</label>
              <input type="number" class="form-control" name="age" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Sex</label>
              <select class="form-select" name="sex" required>
                <option value="">-- Select --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Civil Status</label>
              <input type="text" class="form-control" name="civil_status">
            </div>
            <div class="col-md-4">
              <label class="form-label">Religion</label>
              <input type="text" class="form-control" name="religion">
            </div>
            <div class="col-md-4">
              <label class="form-label">Occupation</label>
              <input type="text" class="form-control" name="occupation">
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Person</label>
              <input type="text" class="form-control" name="contact_person">
            </div>
            <div class="col-md-3">
              <label class="form-label">Contact Person Age</label>
              <input type="number" class="form-control" name="contact_person_age">
            </div>
            <div class="col-md-3">
              <label class="form-label">Contact Number</label>
              <input type="text" class="form-control" name="contact_number" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_patient" class="btn btn-success">
            <i class="fas fa-forward me-1"></i>Next
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="accountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-user-cog me-2"></i>Create Staff Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="pat_id" value="<?= $lastPatientID ?>">

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="create_account" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Create Account
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
