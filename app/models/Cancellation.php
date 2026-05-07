<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class Cancellation
{
    private $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE */
    public function createCancellation($bookingId, $refundedPercentage, $cancellationFee, $reason)
    {
        $sql = "INSERT INTO cancellation (BookingId, CancellationDate, RefundedPercentage, CancellationFee, ReasonOfCancellation) 
        VALUES (?, NOW(), ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("idds", $bookingId, $refundedPercentage, $cancellationFee, $reason);

        return $stmt->execute();
    }

    /* GET ONE */
    public function getCancellationById($cancellationId)
    {
        $sql = "SELECT * FROM cancellation WHERE CancellationId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cancellationId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET BY BOOKING */
    public function getCancellationByBooking($bookingId)
    {
        $sql = "SELECT * FROM cancellation WHERE BookingId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL */
    public function getAllCancellations()
    {
        $sql = "SELECT * FROM cancellation ORDER BY CancellationId DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE */
    public function updateCancellation($cancellationId, $refundedPercentage, $cancellationFee, $reason)
    {
        $sql = "UPDATE cancellation 
                SET RefundedPercentage = ?, CancellationFee = ?, ReasonOfCancellation = ? 
                WHERE CancellationId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ddsi", $refundedPercentage, $cancellationFee, $reason, $cancellationId);

        return $stmt->execute();
    }

    /* DELETE */
    public function deleteCancellation($cancellationId)
    {
        $sql = "DELETE FROM cancellation WHERE CancellationId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cancellationId);

        return $stmt->execute();
    }
}