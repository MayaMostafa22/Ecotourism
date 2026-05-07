<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class Review
{
    protected $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE REVIEW */
    public function createReview($bookingID, $travelerID, $rating, $culturalRespectScore, $comment, $ecoScore)
    {
        $sql = "INSERT INTO review (BookingID, TravelerID, Rating, CulturalRespectScore, Comment, Date, EcoScore) 
                VALUES (?, ?, ?, ?, ?, NOW(), ?)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("iiddsd", $bookingID, $travelerID, $rating, $culturalRespectScore, $comment, $ecoScore);

        return $stmt->execute();
    }

    /* GET ONE REVIEW BY ID */
    public function getReviewById($id)
    {
        $sql = "SELECT * FROM review WHERE ReviewID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL REVIEWS */
    public function getAllReviews()
    {
        $sql = "SELECT * FROM review ORDER BY ReviewID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* GET REVIEWS BY BOOKING */
    public function getReviewsByBooking($bookingID)
    {
        $sql = "SELECT * FROM review WHERE BookingID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE REVIEW */
    public function updateReview($reviewID, $rating, $culturalRespectScore, $comment, $ecoScore)
    {
        $sql = "UPDATE review 
                SET Rating = ?, CulturalRespectScore = ?, Comment = ?, EcoScore = ?
                WHERE ReviewID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ddsdi", $rating, $culturalRespectScore, $comment, $ecoScore, $reviewID);

        return $stmt->execute();
    }

    /* DELETE REVIEW */
    public function deleteReview($id)
    {
        $sql = "DELETE FROM review WHERE ReviewID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}