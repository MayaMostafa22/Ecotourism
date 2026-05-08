<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class Experience
{
    private $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    public function createExperience($name, $desc, $ecoRating, $status, $price, $locations, $start, $end)
    {
        $sql = "INSERT INTO experience (ExperienceName, Description, EcoLeafRating, Status, basePrice, locations, StartTime, EndTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("ssisdsss", $name, $desc, $ecoRating, $status, $price, $locations, $start, $end);
        
        return $stmt->execute();
    }

    public function getExperienceById($id)
    {
        $sql = "SELECT * FROM experience WHERE ExperienceID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllExperiences()
    {
        $sql = "SELECT * FROM experience ORDER BY ExperienceID DESC";
        $result = $this->db->query($sql);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getActiveExperiences()
    {
        $sql = "SELECT * FROM experience WHERE Status = 'Active' ORDER BY ExperienceID DESC";
        $result = $this->db->query($sql);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateExperience($id, $name, $desc, $ecoRating, $status, $price, $locations, $start, $end)
    {
        $sql = "UPDATE experience 
                SET ExperienceName = ?, Description = ?, EcoLeafRating = ?, Status = ?, basePrice = ?, locations = ?, StartTime = ?, EndTime = ?
                WHERE ExperienceID = ?";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("ssisdsssi", $name, $desc, $ecoRating, $status, $price, $locations, $start, $end, $id);
        
        return $stmt->execute();
    }
    
    public function updateExperienceRating($id, $ecoRating)
    {
    $sql = "UPDATE experience SET EcoLeafRating = ? WHERE ExperienceID = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ii", $ecoRating, $id);
    
    return $stmt->execute();
    }

    public function deleteExperience($id)
    {
        $sql = "DELETE FROM experience WHERE ExperienceID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    /*by yasmiiin */


public function getPendingExperiences()
{
    $sql = "SELECT * FROM experiences WHERE Status = 'Pending' ORDER BY CreatedAt DESC";
    $result = $this->db->query($sql);

    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

public function updateStatus($id, $status)
{
    $sql = "UPDATE experiences SET Status = ? WHERE ExperienceID = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("si", $status, $id);

    return $stmt->execute();
}
}
