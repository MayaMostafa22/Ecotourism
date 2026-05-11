<?php
require_once __DIR__ . '/../models/DB.php';
class ExperienceTier
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    public function createTier($expId, $tierName, $maxCapacity)
    {
        $sql = "INSERT INTO experiencetier (ExpID, TierName, MaxCapacity) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("isi", $expId, $tierName, $maxCapacity);
        
        return $stmt->execute();
    }

    public function getTierById($tierId)
    {
        $sql = "SELECT * FROM experiencetier WHERE TierID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $tierId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    public function getTiersByExperience($expId)
    {
        $sql = "SELECT * FROM experiencetier WHERE ExpID = ? ORDER BY MaxCapacity DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $expId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateTier($tierId, $tierName, $maxCapacity)
    {
        $sql = "UPDATE experiencetier 
                SET TierName = ?, MaxCapacity = ? 
                WHERE TierID = ?";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("sii", $tierName, $maxCapacity, $tierId);
        
        return $stmt->execute();
    }

    public function deleteTier($tierId)
    {
        $sql = "DELETE FROM experiencetier WHERE TierID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $tierId);
        
        return $stmt->execute();
    }
}