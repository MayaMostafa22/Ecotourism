<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class AuditLog
{
    private $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE LOG */
    public function createLog($expID, $filedType, $adminID, $oldValue, $newValue)
    {
        $sql = "INSERT INTO auditlog (ExpID, FiledType, AdminID, OldValue, NewValue) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isiss", $expID, $filedType, $adminID, $oldValue, $newValue);

        return $stmt->execute();
    }

    /* GET ONE LOG */
    public function getLogById($id)
    {
        $sql = "SELECT * FROM auditlog WHERE LogID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL LOGS */
    public function getAllLogs()
    {
        $sql = "SELECT * FROM auditlog ORDER BY LogID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE LOG */
    public function updateLog($id, $expID, $filedType, $adminID, $oldValue, $newValue)
    {
        $sql = "UPDATE auditlog
                SET ExpID = ?, FiledType = ?, AdminID = ?, OldValue = ?, NewValue = ?
                WHERE LogID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isissi", $expID, $filedType, $adminID, $oldValue, $newValue, $id);

        return $stmt->execute();
    }

    /* DELETE LOG */
    public function deleteLog($id)
    {
        $sql = "DELETE FROM auditlog WHERE LogID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
   

    public function logAction($adminId, $action, $details)
    {
        $sql = "INSERT INTO audit_logs (AdminID, Action, Details) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("iss", $adminId, $action, $details);

        return $stmt->execute();
    }

   
}
