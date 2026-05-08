<?php
require_once __DIR__ . '/../core/Controller.php'; 
require_once __DIR__ . '/../core/Auth.php';      
require_once __DIR__ . '/../core/Validator.php';  

require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Complaint.php';
require_once __DIR__ . '/../models/SentimentFlag.php';

class EngagementController extends Controller
{
    private $reviewModel;
    private $complaintModel;
    private $sentimentFlagModel;

    public function __construct()
    {
        $this->reviewModel = new Review();
        $this->complaintModel = new Complaint();
        $this->sentimentFlagModel = new SentimentFlag();
    }

    // REVIEW MANAGEMENT

    public function submitReview()
    {
        Auth::redirectIfNotLogged();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new Validator();

            $bookingID = $_POST['bookingID'] ?? '';
            $rating = $_POST['rating'] ?? '';
            $comment = $_POST['comment'] ?? '';
            $culturalRespectScore = $_POST['culturalRespectScore'] ?? 0;
            $ecoScore = $_POST['ecoScore'] ?? 0;

            //اValidation
            $validator->required('bookingID', $bookingID, 'Booking ID is missing.');
            $validator->required('rating', $rating, 'Please provide a rating.');
            $validator->required('comment', $comment, 'Comment cannot be empty.');
            $validator->minLength('comment', $comment, 10, 'Comment must be at least 10 characters.');

            if ($validator->passes()) {
                $travelerID = Auth::user()['id'];

                $success = $this->reviewModel->createReview($bookingID, $travelerID, $rating, $culturalRespectScore, $comment, $ecoScore);
                
                if ($success) {
                    return $this->view('reviews/success', ['message' => 'Review submitted successfully.']);
                }
            }

            return $this->view('reviews/create', ['errors' => $validator->getErrors()]);
        }
    }

    public function getReviewsForBooking($bookingID)
    {
        $reviews = $this->reviewModel->getReviewsByBooking($bookingID);
        return $this->view('reviews/list', ['reviews' => $reviews]);
    }

    public function listFlaggedReviews()
    {
        Auth::forbidIfNotRole('admin');

        $flaggedReviews = $this->reviewModel->getFlaggedReviews();
        return $this->view('admin/reviews/flagged', ['reviews' => $flaggedReviews]);
    }

    public function approveFlaggedReview($reviewID)
    {
        Auth::forbidIfNotRole('admin');

        $success = $this->reviewModel->unflagReview($reviewID);
        
        if ($success) {
            return $this->view('admin/reviews/flagged', ['success' => 'Review unflagged successfully.']);
        }
        return $this->view('admin/reviews/flagged', ['error' => 'Failed to unflag review.']);
    }

    // COMPLAINT MANAGEMENT

    public function submitComplaint()
    {
        Auth::redirectIfNotLogged();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new Validator();

            $bookingId = $_POST['bookingId'] ?? '';
            $guideId = $_POST['guideId'] ?? '';
            $description = $_POST['description'] ?? '';
            $photosUrl = $_POST['photosUrl'] ?? ''; 
            $adminId = NULL;

            $validator->required('bookingId', $bookingId);
            $validator->required('description', $description);
            $validator->minLength('description', $description, 20, 'Please explain the issue in detail (min 20 chars).');

            if ($validator->passes()) {
                $travelerId = Auth::user()['id'];
                $status = 'Pending'; 
                $resolution = '';
                $createdDate = date('Y-m-d H:i:s');
                $resolvedDate = NULL;

                $success = $this->complaintModel->fileComplaint($bookingId, $travelerId, $guideId, $adminId, $photosUrl, $description, $status, $resolution, $createdDate, $resolvedDate);

                if ($success) {
                    return $this->view('complaints/success', ['message' => 'Complaint filed successfully.']);
                }
            }

            return $this->view('complaints/create', ['errors' => $validator->getErrors()]);
        }
    }

    public function getTravelerComplaints()
    {
        Auth::redirectIfNotLogged();
        $travelerId = Auth::user()['id']; 
        $complaints = $this->complaintModel->getComplaintsByTraveler($travelerId);
        
        return $this->view('traveler/complaints_history', ['complaints' => $complaints]);
    }

    public function processComplaintResolution()
    {
        Auth::forbidIfNotRole('admin');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $complaintId = $_POST['complaintId'];
            $status = $_POST['status'];
            $resolution = $_POST['resolution'];
            $adminId = Auth::user()['id']; 
            $resolvedDate = date('Y-m-d H:i:s');
            
            $success = $this->complaintModel->resolveComplaint($complaintId, $adminId, $status, $resolution, $resolvedDate);

            $complaints = $this->complaintModel->getAllComplaints();
            return $this->view('admin/complaints/list', ['complaints' => $complaints, 'message' => 'Complaint resolved.']);
        }
    }

    public function listAllComplaints()
    {
        Auth::forbidIfNotRole('admin');
        $complaints = $this->complaintModel->getAllComplaints();
        return $this->view('admin/complaints/list', ['complaints' => $complaints]);
    }

    // SENTIMENT FLAG MANAGEMENT

    public function flagReviewSentiment($reviewID, $keywordDetected, $flagType, $severity)
    {
        $success = $this->sentimentFlagModel->createSentimentFlag($reviewID, $keywordDetected, $flagType, $severity);
        return $success;
    }

    public function listAllSentimentFlags()
    {
        Auth::forbidIfNotRole('admin');
        $flags = $this->sentimentFlagModel->getAllSentimentFlags();
        return $this->view('admin/sentiment/list', ['flags' => $flags]);
    }
}
?>