<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class BookingOffsetProject
{
    private $db; 
    public function __construct()
    {
        // Use the Singleton
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE: */
    public function createBookingOffset($projectID, $bookingID, $offsetAmount)
    {
        $sql = "INSERT INTO booking_offset_projects (ProjectID, BookingID, offsetAmounts) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("iid", $projectID, $bookingID, $offsetAmount);

        return $stmt->execute();
    }

    /* READ: */
    public function getOffsetsByBooking($bookingID)
    {
        $sql = "SELECT * FROM booking_offset_projects WHERE BookingID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* READ:*/
    public function getBookingsByProject($projectID)
    {
        $sql = "SELECT * FROM booking_offset_projects WHERE ProjectID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $projectID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE*/
    public function updateOffsetAmount($projectID, $bookingID, $newAmount)
    {
        $sql = "UPDATE booking_offset_projects SET offsetAmounts = ? 
                WHERE ProjectID = ? AND BookingID = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("dii", $newAmount, $projectID, $bookingID);

        return $stmt->execute();
    }

    /* DELETE: */
    public function deleteBookingOffset($projectID, $bookingID)
    {
        $sql = "DELETE FROM booking_offset_projects WHERE ProjectID = ? AND BookingID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $projectID, $bookingID);

        return $stmt->execute();
    }
}