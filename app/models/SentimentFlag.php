<?php
require_once __DIR__ . '/../models/DB.php';
class SentimentFlag
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    /* CREATE SENTIMENT FLAG */
    public function createSentimentFlag($reviewID, $keywordDetected, $flagType, $severity)
    {
        $sql = "INSERT INTO sentimentflag (ReviewID, KeywordDetected, FlagType, Severity) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isss", $reviewID, $keywordDetected, $flagType, $severity);

        return $stmt->execute();
    }

    /* GET ONE SENTIMENT FLAG */
    public function getSentimentFlagById($id)
    {
        $sql = "SELECT * FROM sentimentflag WHERE FlagID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL SENTIMENT FLAGS */
    public function getAllSentimentFlags()
    {
        $sql = "SELECT * FROM sentimentflag ORDER BY FlagID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    
    /* DELETE SENTIMENT FLAG */
    public function deleteSentimentFlag($id)
    {
        $sql = "DELETE FROM sentimentflag WHERE FlagID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}