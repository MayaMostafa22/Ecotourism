<?php
require_once "../app/helpers/Validator.php";
require_once "../app/helpers/Auth.php";
require_once "../app/models/User.php";
require_once "../app/models/Experience.php";
require_once "../app/models/Review.php";
require_once "../app/models/AuditLog.php";

class AdminController extends Controller
{
    private $userModel;
    private $experienceModel;
    private $reviewModel;
    private $disputeModel;
    private $auditModel;

    public function __construct()
    {
        Auth::redirectIfNotLogged();
        Auth::forbidIfNotRole("admin");

        $this->userModel = new User();
        $this->experienceModel = new Experience();
        $this->reviewModel = new Review();
        $this->auditModel = new AuditLog();
    }
public function vettingQueue()
    {
        $pendingExperiences = $this->experienceModel->getPendingExperiences();
        $this->view("admin/vetting_queue", ['experiences' => $pendingExperiences]);
    }

    public function approveExperience($id)
    {
        $this->experienceModel->updateStatus($id, 'Approved');
        $this->auditModel->logAction(Auth::user()['id'], 'Approved Experience', "Experience ID: $id");
        
     /*   header("Location: " . BASE_URL . "Admin/vettingQueue");*/
    } 
    public function flaggedReviews()
    {
        $flaggedReviews = $this->reviewModel->getFlaggedReviews();
        $this->view("admin/flagged_reviews", ['reviews' => $flaggedReviews]);
    }

    public function moderateReview($reviewId)
    {
        $action = $_POST['action']; 
        if ($action == 'Delete') {
            $this->reviewModel->deleteReview($reviewId);
            $this->auditModel->logAction(Auth::user()['id'], 'Deleted Review', "Review ID: $reviewId for eco-violation");
        } else {
            $this->reviewModel->unflagReview($reviewId);
        }

       /* header("Location: " . BASE_URL . "Admin/flaggedReviews");*/
    }
/*
    public function disputes()
    {
        $activeDisputes = $this->disputeModel->getActiveDisputes();
        $this->view("admin/disputes_hub", ['disputes' => $activeDisputes]);
    }*/

    public function resolveDispute($disputeId)
    {
        $resolutionDetails = $_POST['resolution_details'];
        $winner = $_POST['winner']; // Guide أو Traveler

        $this->disputeModel->resolve($disputeId, $winner, $resolutionDetails);
        $this->auditModel->logAction(Auth::user()['id'], 'Resolved Dispute', "Dispute ID: $disputeId");

       /* header("Location: " . BASE_URL . "Admin/disputes");*/
    }

  
    public function auditLogs()
    {
        $logs = $this->auditModel->getAllLogs();
        $this->view("admin/audit_logs", ['logs' => $logs]);
    }

    /* نكنسل
    public function triggerIndexRebuild()
    {
        $this->experienceModel->rebuildSearchIndex();
        $this->auditModel->logAction(Auth::user()['id'], 'Triggered Index Rebuild', "Manual trigger");
        
        header("Location: " . BASE_URL . "Admin/index?msg=index_rebuilt");
    }

    public function generateEcoNewsletter()
    {
        $this->experienceModel->curateAndSendNewsletter();
        $this->auditModel->logAction(Auth::user()['id'], 'Generated Newsletter', "Manual trigger");

        header("Location: " . BASE_URL . "Admin/index?msg=newsletter_sent");
    }*/
}