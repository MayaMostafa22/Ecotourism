<?php
require_once __DIR__ . '/../models/DB.php';
class Payment
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    /* CREATE PAYMENT */
    public function createPayment($bookingId, $amount, $currency, $exchangeRate)
    {
       
        $sql = "INSERT INTO payment (BookingId, Amount, Currency, ExchangeRate) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        // i (BookingId), d (Amount), s (Currency), d (ExchangeRate) -> idsd
        
        $stmt->bind_param("idsd", $bookingId, $amount, $currency, $exchangeRate);

        return $stmt->execute();
    }

    /* GET ONE PAYMENT BY TRANSACTION ID */
    public function getPaymentById($transactionId)
    {
        $sql = "SELECT * FROM payment WHERE TransactionId = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $transactionId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET PAYMENT DETAILS BY BOOKING ID (Very Important for Logic) */
    public function getPaymentByBookingId($bookingId)
    {
        $sql = "SELECT * FROM payment WHERE BookingId = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $bookingId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL PAYMENTS */
    public function getAllPayments()
    {
        $sql = "SELECT * FROM payment ORDER BY PaymentDate DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* DELETE PAYMENT (Use with caution in real financial systems) */
    public function deletePayment($transactionId)
    {
        $sql = "DELETE FROM payment WHERE TransactionId = ?";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("i", $transactionId);
        
        return $stmt->execute();
    }
}