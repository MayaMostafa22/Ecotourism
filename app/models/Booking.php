<?php
require_once __DIR__ . '/../models/DB.php';

class Booking
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    /* CREATE BOOKING */
    public function createBooking($travelerID, $expID, $bookingDate, $status, $isWaitListAccepted, $groupSize, $discountID, $discountAmount, $basePrice, $finalPrice)
    {
        $sql = "INSERT INTO booking (TravelerID, ExpID, BookingDate, Status, IsWaitListAccepted, GroupSize, DiscountID, DiscountAmount, BasePrice, FinalPrice) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iissiiiiii", $travelerID, $expID, $bookingDate, $status, $isWaitListAccepted, $groupSize, $discountID, $discountAmount, $basePrice, $finalPrice);

        return $stmt->execute();
    }

    /* GET ONE BOOKING */
    public function getBookingById($id)
    {
        $sql = "SELECT * FROM booking WHERE BookingID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL BOOKINGS */
    public function getAllBookings()
    {
        $sql = "SELECT * FROM booking ORDER BY BookingID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE STATUS */
    public function updateStatus($bookingID, $status)
    {
        $sql = "UPDATE booking SET Status = ? WHERE BookingID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $status, $bookingID);
        return $stmt->execute();
    }

    /* DELETE BOOKING */
    public function deleteBooking($id)
    {
        $sql = "DELETE FROM booking WHERE BookingID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}