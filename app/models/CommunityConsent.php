<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class CommunityConsent
{
    private $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    public function createConsent($status, $contractURL)
    {
        $sql = "INSERT INTO communityconsent (Status, contractURL) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $status, $contractURL);
        
        return $stmt->execute();
    }

    public function getConsentById($id)
    {
        $sql = "SELECT * FROM communityconsent WHERE consentID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllConsents()
    {
        $sql = "SELECT * FROM communityconsent";
        $result = $this->db->query($sql);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateConsent($id, $status, $contractURL)
    {
        $sql = "UPDATE communityconsent SET Status = ?, contractURL = ? WHERE consentID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $status, $contractURL, $id);
        
        return $stmt->execute();
    }

    public function deleteConsent($id)
    {
        $sql = "DELETE FROM communityconsent WHERE consentID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}