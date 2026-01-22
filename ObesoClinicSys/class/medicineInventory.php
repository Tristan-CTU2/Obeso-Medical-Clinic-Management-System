<?php
class MedicineInventory {
    private $conn;
    private $table_inventory = "medicine_inventory";

    public function __construct($db) {
        $this->conn = $db;
    }

    /* ======================
       ADD MEDICINE TO INVENTORY
    ====================== */
    public function addMedicine($medication_id, $quantity, $expiry_date, $reorder_level = 10) {
        $sql = "INSERT INTO {$this->table_inventory} 
                (medication_id, quantity, expiry_date, reorder_level) 
                VALUES (:medication_id, :quantity, :expiry_date, :reorder_level)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':medication_id' => $medication_id,
            ':quantity' => $quantity,
            ':expiry_date' => $expiry_date,
            ':reorder_level' => $reorder_level
        ]);
    }

    /* ======================
       UPDATE INVENTORY
    ====================== */
    public function updateInventory($inventory_id, $quantity, $expiry_date) {
        $sql = "UPDATE {$this->table_inventory}
                SET quantity = :quantity, expiry_date = :expiry_date, last_updated = NOW()
                WHERE inventory_id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':quantity' => $quantity,
            ':expiry_date' => $expiry_date,
            ':id' => $inventory_id
        ]);
    }

    /* ======================
       DELETE INVENTORY ITEM
    ====================== */
    public function deleteInventory($inventory_id) {
        $sql = "DELETE FROM {$this->table_inventory} WHERE inventory_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $inventory_id]);
    }

    /* ======================
       GET ALL INVENTORY ITEMS WITH MEDICATION DETAILS
    ====================== */
    public function viewAll() {
        $sql = "SELECT mi.*, m.generic_name, m.brand_name
                FROM {$this->table_inventory} mi
                JOIN medications m ON mi.medication_id = m.medication_id
                ORDER BY mi.expiry_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ======================
       GET INVENTORY ITEM BY ID
    ====================== */
    public function getById($inventory_id) {
        $sql = "SELECT * FROM {$this->table_inventory} WHERE inventory_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $inventory_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
