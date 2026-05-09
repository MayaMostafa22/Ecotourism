<?php
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Complaint.php';
require_once __DIR__ . '/../models/SentimentFlag.php';

class EngagementController
{
    private $reviewModel;
    private $complaintModel;
    private $sentimentFlagModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->reviewModel = new Review();
        $this->complaintModel = new Complaint();
        $this->sentimentFlagModel = new SentimentFlag();
    }

    // REVIEW MANAGEMENT

    public function submitReview($bookingID, $rating, $culturalRespectScore, $comment, $ecoScore)
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Traveler') {
            $_SESSION["errMsg"] = "Unauthorized access.";
            return false;
        }

        if (empty($bookingID) || empty($rating) || empty($comment) || strlen($comment) < 10) {
            $_SESSION["errMsg"] = "Invalid review data. Comment must be at least 10 characters.";
            return false;
        }

        $travelerID = $_SESSION["UserID"]; 
        $success = $this->reviewModel->createReview($bookingID, $travelerID, $rating, $culturalRespectScore, $comment, $ecoScore);
        
        if ($success) {
            $_SESSION["successMsg"] = "Review submitted successfully.";
            return true;
        }
        
        $_SESSION["errMsg"] = "Failed to submit review.";
        return false;
    }

    public function getReviewsForBooking($bookingID)
    {
        return $this->reviewModel->getReviewsByBooking($bookingID);
    }

    public function listFlaggedReviews()
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return [];
        }

        return $this->reviewModel->getFlaggedReviews();
    }

    public function approveFlaggedReview($reviewID)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            $_SESSION["errMsg"] = "Unauthorized access.";
            return false;
        }

        $success = $this->reviewModel->unflagReview($reviewID);
        
        if ($success) {
            $_SESSION["successMsg"] = "Review unflagged successfully.";
            return true;
        }

        $_SESSION["errMsg"] = "Failed to unflag review.";
        return false;
    }

    // COMPLAINT MANAGEMENT
    

    public function submitComplaint($bookingId, $guideId, $photosUrl, $description)
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Traveler') {
            $_SESSION["errMsg"] = "Unauthorized access.";
            return false;
        }

        if (empty($bookingId) || empty($description) || strlen($description) < 20) {
            $_SESSION["errMsg"] = "Please explain the issue in detail (min 20 chars).";
            return false;
        }

        $travelerId = $_SESSION["UserID"];
        $adminId = NULL;
        $status = 'Pending'; 
        $resolution = '';
        $createdDate = date('Y-m-d H:i:s');
        $resolvedDate = NULL;

        $success = $this->complaintModel->fileComplaint($bookingId, $travelerId, $guideId, $adminId, $photosUrl, $description, $status, $resolution, $createdDate, $resolvedDate);

        if ($success) {
            $_SESSION["successMsg"] = "Complaint filed successfully.";
            return true;
        }

        $_SESSION["errMsg"] = "Failed to file complaint.";
        return false;
    }

    public function getTravelerComplaints()
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Traveler') {
            return [];
        }

        $travelerId = $_SESSION["UserID"]; 
        return $this->complaintModel->getComplaintsByTraveler($travelerId);
    }

    public function processComplaintResolution($complaintId, $status, $resolution)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            $_SESSION["errMsg"] = "Unauthorized access.";
            return false;
        }

        $adminId = $_SESSION["UserID"];  
        $resolvedDate = date('Y-m-d H:i:s');
        
        $success = $this->complaintModel->resolveComplaint($complaintId, $adminId, $status, $resolution, $resolvedDate);

        if ($success) {
            $_SESSION["successMsg"] = "Complaint resolved successfully.";
            return true;
        }

        $_SESSION["errMsg"] = "Failed to resolve complaint.";
        return false;
    }

    public function listAllComplaints()
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return [];
        }

        return $this->complaintModel->getAllComplaints();
    }

    // SENTIMENT FLAG MANAGEMENT

    public function flagReviewSentiment($reviewID, $keywordDetected, $flagType, $severity)
    {
        return $this->sentimentFlagModel->createSentimentFlag($reviewID, $keywordDetected, $flagType, $severity);
    }

    public function listAllSentimentFlags()
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return [];
        }

        return $this->sentimentFlagModel->getAllSentimentFlags();
    }
}
?>