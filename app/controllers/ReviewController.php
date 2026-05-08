<?php
require_once '../../models/Review.php';
require_once '../../models/Booking.php'; // Needed to verify the booking belongs to the traveler

class ReviewController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * 1. Traveler submits a new review
     */
    public function submitReview($bookingID, $rating, $culturalRespectScore, $comment, $ecoScore)
    {
        // Security check: Must be a logged-in Traveler
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Traveler') {
            $_SESSION["errMsg"] = "You must be logged in as a Traveler to submit a review.";
            return false;
        }

        $travelerID = $_SESSION["UserID"];

        // Ensure the booking belongs to this specific traveler
        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingById($bookingID);

        if (!$booking || $booking['TravelerID'] != $travelerID) {
            $_SESSION["errMsg"] = "You cannot review a booking that does not belong to you.";
            return false;
        }

        // Sanitize text input (XSS protection)
        $comment = htmlspecialchars(trim($comment));

        // Validate ratings are within a logical range (e.g., 1 to 5)
        if ($rating < 1 || $rating > 5 || $ecoScore < 1 || $ecoScore > 5) {
            $_SESSION["errMsg"] = "Ratings must be between 1 and 5.";
            return false;
        }

        $reviewModel = new Review();
        $isCreated = $reviewModel->createReview(
            $bookingID, 
            $travelerID, 
            $rating, 
            $culturalRespectScore, 
            $comment, 
            $ecoScore
        );

        if ($isCreated) {
            $_SESSION["successMsg"] = "Your review has been submitted successfully. Thank you for your feedback!";
            return true;
        }

        $_SESSION["errMsg"] = "An error occurred while saving your review.";
        return false;
    }

    /**
     * 2. View all reviews for a specific booking
     */
    public function getBookingReviews($bookingID)
    {
        $reviewModel = new Review();
        return $reviewModel->getReviewsByBooking($bookingID);
    }

    /**
     * 3. Edit a review (Traveler can edit their own review)
     */
    public function editReview($reviewID, $rating, $culturalRespectScore, $comment, $ecoScore)
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Traveler') {
            return false;
        }

        $reviewModel = new Review();
        $review = $reviewModel->getReviewById($reviewID);

        // Ensure the review belongs to the user trying to edit it
        if (!$review || $review['TravelerID'] != $_SESSION["UserID"]) {
            $_SESSION["errMsg"] = "You are not authorized to edit this review.";
            return false;
        }

        $comment = htmlspecialchars(trim($comment));
        
        $isUpdated = $reviewModel->updateReview($reviewID, $rating, $culturalRespectScore, $comment, $ecoScore);

        if ($isUpdated) {
            $_SESSION["successMsg"] = "Review updated successfully.";
            return true;
        }
        return false;
    }

    /**
     * 4. Remove a review (Traveler can delete their own, or Admin can delete any)
     */
    public function removeReview($reviewID)
    {
        if (!isset($_SESSION["UserID"])) {
            return false;
        }

        $reviewModel = new Review();
        $review = $reviewModel->getReviewById($reviewID);

        if (!$review) {
            return false;
        }

        // Allow deletion if the user is the review owner or an Admin
        if ($_SESSION["UserRole"] === 'Admin' || ($_SESSION["UserRole"] === 'Traveler' && $review['TravelerID'] == $_SESSION["UserID"])) {
            
            $isDeleted = $reviewModel->deleteReview($reviewID);
            
            if ($isDeleted) {
                $_SESSION["successMsg"] = "Review deleted successfully.";
                return true;
            }
        }

        $_SESSION["errMsg"] = "You are not authorized to delete this review.";
        return false;
    }
}