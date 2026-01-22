<?php
class Inventory {
    private $conn;
    private $table="medicine_inventory";

    public function __construct($db){ $this->conn=$db; }

    public function viewAll(){
        return $this->conn->query("
            SELECT m.generic_name, i.*
            FROM {$this->table} i
            JOIN medications m ON i.medication_id=m.medication_id
        ")->fetchAll();
    }
}
