<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class Complaint
{
    private $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    public function fileComplaint($bookingId, $travelerId, $guideId, $adminId, $photosUrl, $description, $status, $resolution, $createdDate, $resolvedDate)
    {
        
        $sql = "INSERT INTO complaint (BookingID, TravelerID, GuideID, AdminID, PhotosURL, Description, Status, Resolution, CreatedDate, ResolvedDate) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        // i (BookingID), i (TravelerID), i (GuideID), i (AdminID), s (PhotosURL), s (Description), s (Status), s (Resolution), s (CreatedDate), s (ResolvedDate) -> iiisssssss
        $stmt->bind_param("iiiissssss", $bookingId, $travelerId, $guideId, $adminId, $photosUrl, $description, $status, $resolution, $createdDate, $resolvedDate);
        
        return $stmt->execute();
    }


    public function resolveComplaint($complaintId, $adminId, $status, $resolution, $resolvedDate)
    {
        $sql = "UPDATE complaint 
                SET AdminID = ?, Status = ?, Resolution = ?, ResolvedDate = ? 
                WHERE ComplaintID = ?";
        
        $stmt = $this->db->prepare($sql);
        
        // i (AdminID), s (Status), s (Resolution), s (Date), i (ComplaintID) -> isssi
        $stmt->bind_param("isssi", $adminId, $status, $resolution, $resolvedDate, $complaintId);
        
        return $stmt->execute();
    }

    public function getAllComplaints()
    {
        $sql = "SELECT * FROM complaint ORDER BY CreatedDate DESC";
        $result = $this->db->query($sql);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getComplaintById($id)
    {
        $sql = "SELECT * FROM complaint WHERE ComplaintID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    
    public function getComplaintsByTraveler($travelerId)
    {
        $sql = "SELECT * FROM complaint WHERE TravelerID = ? ORDER BY CreatedDate DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $travelerId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

   
    public function deleteComplaint($id)
    {
        $sql = "DELETE FROM complaint WHERE ComplaintID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}