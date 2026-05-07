<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class RevenueSplitting
{
   private $db;
    public function __construct()
    {
        // Use the Singleton
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE REVENUE SPLIT */
    public function createSplit($communityTax, $guideEarning, $platformFee, $bookingID, $walletID)
    {
        $sql = "INSERT INTO revenuespliting (CommunityTax, GuideEarning, PlatFormFee, BookingID, WalletID) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
       
        $stmt->bind_param("dddii", $communityTax, $guideEarning, $platformFee, $bookingID, $walletID);

        return $stmt->execute();
    }

    /* GET ONE SPLIT BY ID */
    public function getSplitById($id)
    {
        $sql = "SELECT * FROM revenuespliting WHERE SplitID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function getSplitByBooking($bookingId)
    {
        $sql = "SELECT * FROM revenuespliting WHERE BookingID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL SPLITS */
    public function getAllSplits()
    {
        $sql = "SELECT * FROM revenuespliting ORDER BY TransactionDate DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE SPLIT*/
    public function updateSplit($id, $communityTax, $guideEarning, $platformFee)
    {
        $sql = "UPDATE revenuespliting 
                SET CommunityTax = ?, GuideEarning = ?, PlatFormFee = ?
                WHERE SplitID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("dddi", $communityTax, $guideEarning, $platformFee, $id);

        return $stmt->execute();
    }

    /* DELETE SPLIT */
    public function deleteSplit($id)
    {
        $sql = "DELETE FROM revenuespliting WHERE SplitID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}