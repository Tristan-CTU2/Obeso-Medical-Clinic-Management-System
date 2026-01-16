<?php

require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../config/db.php";
require_once "../class/checkups.php";
require_once "../class/doctor.php";

$database = new Database();
$db = $database->connect();
$checkup = new Checkup($db);

// Fetch patients for dropdown
$patients = $db->query("SELECT patient_id, full_name FROM patients ORDER BY full_name")->fetchAll();

// Fetch doctors using class method
$doctorClass = new Doctor($db);
$doctors = $doctorClass->getAllDoctors();

// Fetch all checkups
$rows = $checkup->viewAll();

// ADD CHECKUP
if (isset($_POST['add_checkup'])) {
    $data = [
        ':pid'  => $_POST['patient_id'],
        ':did'  => $_POST['doc_id'],
        ':df'   => $_POST['doc_fullname'],
        ':cd'   => $_POST['checkup_date'],
        ':cc'   => $_POST['chief_complaint'],
        ':hpi'  => $_POST['history_present_illness'],
        ':dx'   => $_POST['diagnosis'],
        ':bp'   => $_POST['blood_pressure'],
        ':rr'   => $_POST['respiratory_rate'],
        ':wt'   => $_POST['weight'],
        ':hr'   => $_POST['heart_rate'],
        ':temp' => $_POST['temperature'],
    ];

    if ($checkup->add($data)) {
        $rows = $checkup->viewAll();
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var addModal = new bootstrap.Modal(document.getElementById('addCheckupModal'));
                addModal.hide();
            });
        </script>";
    } else {
        echo "<script>alert('❌ Error adding checkup');</script>";
    }
}
?>

<main>
        <!-- ADD CHECKUP BUTTON -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCheckupModal">
                <i class="fas fa-notes-medical"></i> Add New Check-Up
            </button>
        </div>

        <!-- ADD CHECKUP MODAL -->
        <div class="modal fade" id="addCheckupModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-notes-medical me-2"></i>Add Check-Up</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- PATIENT DROPDOWN -->
                                <div class="col-md-6">
                                    <label class="form-label">Patient</label>
                                    <select class="form-select" name="patient_id" required>
                                        <option value="">-- Select Patient --</option>
                                        <?php foreach ($patients as $p): ?>
                                            <option value="<?= $p['patient_id'] ?>">
                                                <?= htmlspecialchars($p['full_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- DOCTOR DROPDOWN -->
                                <div class="col-md-6">
                                    <label class="form-label">Doctor</label>
                                    <select class="form-select" name="doc_id" id="doctorSelect" required>
                                        <option value="">-- Select Doctor --</option>
                                        <?php foreach ($doctors as $d): ?>
                                            <option value="<?= $d['doc_id'] ?>" data-name="<?= htmlspecialchars($d['doc_fullname']) ?>">
                                                <?= htmlspecialchars($d['doc_fullname']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Hidden field for doctor fullname -->
                                <input type="hidden" name="doc_fullname" id="doc_fullname">

                                <div class="col-md-4">
                                    <label class="form-label">Check-Up Date</label>
                                    <input type="date" class="form-control" name="checkup_date" required>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">Chief Complaint</label>
                                    <input type="text" class="form-control" name="chief_complaint" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">History of Present Illness</label>
                                    <textarea class="form-control" name="history_present_illness"></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Diagnosis</label>
                                    <input type="text" class="form-control" name="diagnosis">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">BP</label>
                                    <input type="text" class="form-control" name="blood_pressure">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">RR</label>
                                    <input type="text" class="form-control" name="respiratory_rate">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Weight</label>
                                    <input type="text" class="form-control" name="weight">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">HR</label>
                                    <input type="text" class="form-control" name="heart_rate">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Temp</label>
                                    <input type="text" class="form-control" name="temperature">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" name="add_checkup" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Save Check-Up
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Checkup Table Here -->
        <!-- You can reuse your $rows table listing below -->

    </div>
</main>

<script>
    // Update hidden doctor fullname when selecting from dropdown
    document.getElementById('doctorSelect').addEventListener('change', function() {
        document.getElementById('doc_fullname').value =
            this.options[this.selectedIndex].getAttribute('data-name');
    });
</script>
