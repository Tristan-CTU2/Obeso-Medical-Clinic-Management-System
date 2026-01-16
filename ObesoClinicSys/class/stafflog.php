<?php
class StaffActivityLog {
    private $conn;
    private $table = "staff_activity_logs";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch all logs
    public function viewAll() {
        $query = "SELECT l.log_id, s.staff_first_name, s.staff_last_name, l.action, l.module, l.reference_id, l.created_at
                  FROM " . $this->table . " l
                  JOIN staff s ON l.staff_id = s.staff_id
                  ORDER BY l.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch logs filtered by staff or date
    public function filterLogs($staff_id = null, $start_date = null, $end_date = null) {
        $conditions = [];
        $params = [];

        if ($staff_id) {
            $conditions[] = "l.staff_id = :staff_id";
            $params[':staff_id'] = $staff_id;
        }

        if ($start_date) {
            $conditions[] = "DATE(l.created_at) >= :start_date";
            $params[':start_date'] = $start_date;
        }

        if ($end_date) {
            $conditions[] = "DATE(l.created_at) <= :end_date";
            $params[':end_date'] = $end_date;
        }

        $where = "";
        if (!empty($conditions)) {
            $where = "WHERE " . implode(" AND ", $conditions);
        }

        $query = "SELECT l.log_id, s.staff_first_name, s.staff_last_name, l.action, l.module, l.reference_id, l.created_at
                  FROM " . $this->table . " l
                  JOIN staff s ON l.staff_id = s.staff_id
                  $where
                  ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
