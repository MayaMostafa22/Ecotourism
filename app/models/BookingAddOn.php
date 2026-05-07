<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class BookingAddOn
{
    private $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE BOOKING ADDON */
    public function createBookingAddOn($bookingID, $quantity)
    {
        $sql = "INSERT INTO bookingaddon (BookingID, Quantity) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $bookingID, $quantity);

        return $stmt->execute();
    }

    /* GET ONE BOOKING ADDON */
    public function getBookingAddOnById($id)
    {
        $sql = "SELECT * FROM bookingaddon WHERE AddOnID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL BOOKING ADDONS */
    public function getAllBookingAddOns()
    {
        $sql = "SELECT * FROM bookingaddon ORDER BY AddOnID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE BOOKING ADDON */
    public function updateBookingAddOn($id, $bookingID, $quantity)
    {
        $sql = "UPDATE bookingaddon
                SET BookingID = ?, Quantity = ?
                WHERE AddOnID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iii", $bookingID, $quantity, $id);

        return $stmt->execute();
    }

    /* DELETE BOOKING ADDON */
    public function deleteBookingAddOn($id)
    {
        $sql = "DELETE FROM bookingaddon WHERE AddOnID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}