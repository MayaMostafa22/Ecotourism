<?php
require_once '../../models/Review.php';

class ReviewController
{
    public function addReview($bookingID, $travelerID, $rating, $culturalRespectScore, $comment, $ecoScore)
    {
        $reviewModel = new Review();
        return $reviewModel->createReview($bookingID, $travelerID, $rating, $culturalRespectScore, $comment, $ecoScore);
    }

    public function getDetails($id)
    {
        $reviewModel = new Review();
        return $reviewModel->getReviewById($id);
    }

    public function listAll()
    {
        $reviewModel = new Review();
        return $reviewModel->getAllReviews();
    }

    public function getBookingReviews($bookingID)
    {
        $reviewModel = new Review();
        return $reviewModel->getReviewsByBooking($bookingID);
    }

    public function editReview($reviewID, $rating, $culturalRespectScore, $comment, $ecoScore)
    {
        $reviewModel = new Review();
        return $reviewModel->updateReview($reviewID, $rating, $culturalRespectScore, $comment, $ecoScore);
    }

    public function removeReview($id)
    {
        $reviewModel = new Review();
        return $reviewModel->deleteReview($id);
    }
}