<?php
require_once "../class/staff.php";
require_once "../class/doctor.php";

$staff = new Staff($db);
$doctor = new Doctor($db);

$staffs = $staff->all();
$doctors = $doctor->getAllDoctors();

if (isset($_POST['add_user'])) {
    $staff_id = $_POST['staff_id'] ?: null;
    $doc_id   = $_POST['doc_id'] ?: null;

    $check = $user->checkRoleSelection($staff_id, $doc_id);

    if ($check === "BOTH_SELECTED") {
        echo "<script>alert('User can only be Staff OR Doctor, not both');</script>";
    } elseif ($check === "NONE_SELECTED") {
        echo "<script>alert('User must be linked to Staff or Doctor');</script>";
    } else {
        $result = $user->create(
            $_POST['username'],
            $_POST['password'],
            $staff_id,
            $doc_id
        );

        if ($result === "DUPLICATE_USERNAME") {
            echo "<script>alert('❌ Username already exists. Please choose another.');</script>";
        } elseif ($result === true) {
            echo "<script>alert('✅ User created successfully');</script>";
        }
    }
}


?>

<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUser">
    <i class="fas fa-user-plus"></i> Create User
</button>

<div class="modal fade" id="addUser">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5>Create User Account</h5>
                </div>

                <div class="modal-body">
                    <input class="form-control mb-2" name="username" placeholder="Username" required>
                    <input class="form-control mb-2" name="password" type="password" placeholder="Password" required>

                    <select class="form-select mb-2" name="staff_id">
                        <option value="">Assign Staff (optional)</option>
                        <?php foreach ($staffs as $s): ?>
                            <option value="<?= $s['staff_id'] ?>">
                                <?= $s['staff_first_name'] . " " . $s['staff_last_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select class="form-select" name="doc_id">
                        <option value="">Assign Doctor (optional)</option>
                        <?php foreach ($doctors as $d): ?>
                            <option value="<?= $d['doc_id'] ?>">
                                <?= $d['doc_fullname'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="modal-footer">
                    <button name="add_user" class="btn btn-success">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>