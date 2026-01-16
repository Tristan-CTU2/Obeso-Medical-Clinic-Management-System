<?php
class Medications {
    private $conn;
    private $table = "medications";

    public function __construct($db) {
        $this->conn = $db;
    }

    /* ======================
       GET ALL MEDICATIONS
    ====================== */
    public function getAllMedications() {
        $sql = "SELECT * FROM {$this->table} ORDER BY generic_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ======================
       GET MEDICATION BY ID
    ====================== */
    public function getMedicationById($medication_id) {
        $sql = "SELECT * FROM {$this->table} WHERE medication_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $medication_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ======================
       ADD NEW MEDICATION
    ====================== */
    public function addMedication($generic_name, $brand_name = null) {
        $sql = "INSERT INTO {$this->table} (generic_name, brand_name) VALUES (:generic_name, :brand_name)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':generic_name' => $generic_name,
            ':brand_name' => $brand_name
        ]);
    }

    /* ======================
       UPDATE MEDICATION
    ====================== */
    public function updateMedication($medication_id, $generic_name, $brand_name = null) {
        $sql = "UPDATE {$this->table} 
                SET generic_name = :generic_name, brand_name = :brand_name 
                WHERE medication_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':generic_name' => $generic_name,
            ':brand_name' => $brand_name,
            ':id' => $medication_id
        ]);
    }

    /* ======================
       DELETE MEDICATION
    ====================== */
    public function deleteMedication($medication_id) {
        $sql = "DELETE FROM {$this->table} WHERE medication_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $medication_id]);
    }
}
?>
