<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class Voucher
{
    protected $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE VOUCHER  */
    public function createVoucher($status, $expireDate, $qrCode, $bookingID)
    {
        $sql = "INSERT INTO voucher (Status, GeneratedDate, ScannedDate, ExpireDate, QRCode, BookingID) 
                VALUES (?, NOW(), NULL, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("sssi", $status, $expireDate, $qrCode, $bookingID);

        return $stmt->execute();
    }

    /* MARK VOUCHER AS SCANNED  */
    public function scanVoucher($id)
    {
        $sql = "UPDATE voucher SET ScannedDate = NOW(), Status = 'Expired' WHERE VoucherID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    /* GET ONE VOUCHER BY ID  */
    public function getVoucherById($id)
    {
        $sql = "SELECT * FROM voucher WHERE VoucherID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET VOUCHERS BY BOOKING */
    public function getVouchersByBooking($bookingID)
    {
        $sql = "SELECT * FROM voucher WHERE BookingID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE VOUCHER STATUS */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE voucher SET Status = ? WHERE VoucherID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $status, $id);

        return $stmt->execute();
    }

    /* DELETE VOUCHER */
    public function deleteVoucher($id)
    {
        $sql = "DELETE FROM voucher WHERE VoucherID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}