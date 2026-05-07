<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class BookingEscrow
{
    private $db;

    public function __construct()
    {
        // Use the Singleton
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE ESCROW */
    public function createEscrow($bookingId, $status, $amountHeld, $entryDate, $releasedDate)
    {
        $sql = "INSERT INTO bookingescrow (BookingId, Status, AmountHeld, EnteryDate, ReleasedDate) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isdss", $bookingId, $status, $amountHeld, $entryDate, $releasedDate);
        return $stmt->execute();
    }

    /* GET ONE ESCROW */
    public function getEscrowById($id)
    {
        $sql = "SELECT * FROM bookingescrow WHERE EscrowId = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }


    /* GET ESCROW BY BOOKING ID (Like findByEmail) */
    public function getEscrowByBookingId($bookingId)
    {
        $sql = "SELECT * FROM bookingescrow WHERE BookingId = ?";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    /* UPDATE ESCROW */
    public function updateEscrow($id, $status, $amountHeld, $releasedDate)
    {
        $sql = "UPDATE bookingescrow 
                SET Status = ?, AmountHeld = ?, ReleasedDate = ?
                WHERE EscrowId = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sdsi", $status, $amountHeld, $releasedDate, $id);

        return $stmt->execute();
    }

    /* DELETE ESCROW */
    public function deleteEscrow($id)
    {
        $sql = "DELETE FROM bookingescrow WHERE EscrowId = ?";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}