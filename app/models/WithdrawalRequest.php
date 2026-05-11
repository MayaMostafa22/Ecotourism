<?php
require_once __DIR__ . '/../models/DB.php';
class WithdrawalRequest
{
    private $db;

    public function __construct()
    {
        // Use the Singleton
        $this->db = DB::getInstance()->getConnection();
    }
    /* CREATE: */
    public function createRequest($amount, $status, $guideID)
    {
        $sql = "INSERT INTO withdrawalrequest (Amount, Status, GuideID) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("dsi", $amount, $status, $guideID);

        return $stmt->execute();
    }
    /* READ BY GUIDE: */
    public function getRequestsByGuide($guideID)
    {

        $sql = "SELECT * FROM withdrawalrequest WHERE GuideID = ? ORDER BY TimeStamp DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $guideID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    /* READ ALL:  */
    public function getAllRequests()
    {
        $sql = "SELECT * FROM withdrawalrequest ORDER BY TimeStamp DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE STATUS:  */
    public function updateRequestStatus($id, $status)
    {
     
        $sql = "UPDATE withdrawalrequest SET Status = ? WHERE RequestID = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $status, $id);

        return $stmt->execute();
    }

    /* DELETE: */
    public function deleteRequest($id)
    {
        $sql = "DELETE FROM withdrawalrequest WHERE RequestID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}