<?php
require_once __DIR__ . '/../models/DB.php';
class GuideWallet
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }


    public function createWallet($guideId, $initialAvailable = 0.0, $initialPending = 0.0)
    {
        $sql = "INSERT INTO guidewallet (AvailableBalance, PendingBalance, GuideID) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);


        $stmt->bind_param("ddi", $initialAvailable, $initialPending, $guideId);

        return $stmt->execute();
    }


    public function getWalletByGuideId($guideId)
    {
        $sql = "SELECT * FROM guidewallet WHERE GuideID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $guideId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }


    public function updateBalances($guideId, $available, $pending)
    {
        $sql = "UPDATE guidewallet SET AvailableBalance = ?, PendingBalance = ? WHERE GuideID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("ddi", $available, $pending, $guideId);

        return $stmt->execute();
    }

    public function releasePendingBalance($guideId, $amount)
    {

        $sql = "UPDATE guidewallet 
                SET AvailableBalance = AvailableBalance + ?, 
                    PendingBalance = PendingBalance - ? 
                WHERE GuideID = ? AND PendingBalance >= ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ddid", $amount, $amount, $guideId, $amount);

        return $stmt->execute();
    }
}