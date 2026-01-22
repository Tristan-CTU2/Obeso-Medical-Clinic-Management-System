<?php
class Patient {
    private $conn;
    private $table = "patients";

    public function __construct($db) {
        $this->conn = $db;
    }

    /* ==============================
       ADD NEW PATIENT (ADMIN / STAFF)
    ============================== */
    public function add(
        $full_name,
        $address,
        $birthday,
        $age,
        $sex,
        $civil_status,
        $religion,
        $occupation,
        $contact_person,
        $contact_person_age,
        $contact_number
    ) {
        try {
            $sql = "INSERT INTO {$this->table}
                    (full_name, address, birthday, age, sex,
                     civil_status, religion, occupation,
                     contact_person, contact_person_age, contact_number)
                    VALUES
                    (:name, :address, :birthday, :age, :sex,
                     :civil, :religion, :occupation,
                     :cperson, :cp_age, :contact)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ":name" => $full_name,
                ":address" => $address,
                ":birthday" => $birthday,
                ":age" => $age,
                ":sex" => $sex,
                ":civil" => $civil_status,
                ":religion" => $religion,
                ":occupation" => $occupation,
                ":cperson" => $contact_person,
                ":cp_age" => $contact_person_age,
                ":contact" => $contact_number
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /* ==============================
       GET LAST INSERTED PATIENT ID
    ============================== */
    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }

    /* ==============================
       VIEW ALL PATIENTS
    ============================== */
    public function viewAll() {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table}
             ORDER BY patient_id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==============================
       GET PATIENT BY ID
    ============================== */
    public function getById($patient_id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table}
             WHERE patient_id = :id"
        );
        $stmt->execute([":id" => $patient_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ==============================
       SEARCH PATIENT BY NAME
    ============================== */
    public function searchByName($keyword) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table}
             WHERE full_name LIKE :k
             ORDER BY full_name ASC"
        );
        $stmt->execute([":k" => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==============================
       UPDATE PATIENT
    ============================== */
    public function update(
        $patient_id,
        $full_name,
        $address,
        $birthday,
        $age,
        $sex,
        $civil_status,
        $religion,
        $occupation,
        $contact_person,
        $contact_person_age,
        $contact_number
    ) {
        $sql = "UPDATE {$this->table}
                SET full_name = :name,
                    address = :address,
                    birthday = :birthday,
                    age = :age,
                    sex = :sex,
                    civil_status = :civil,
                    religion = :religion,
                    occupation = :occupation,
                    contact_person = :cperson,
                    contact_person_age = :cp_age,
                    contact_number = :contact
                WHERE patient_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":name" => $full_name,
            ":address" => $address,
            ":birthday" => $birthday,
            ":age" => $age,
            ":sex" => $sex,
            ":civil" => $civil_status,
            ":religion" => $religion,
            ":occupation" => $occupation,
            ":cperson" => $contact_person,
            ":cp_age" => $contact_person_age,
            ":contact" => $contact_number,
            ":id" => $patient_id
        ]);
    }

}
?>
