<?php
require_once '../../Models/Complaint.php';

class ComplaintController
{
    public function __construct()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /* 1. Traveler submits a new complaint */
    // Reduced parameters: Controller handles the automated data!
    public function createNewComplaint($bookingId, $guideId, $photosUrl, $description)
    {
        // Security: Must be logged in and a Traveler
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Traveler') {
            $_SESSION["errMsg"] = "You must be logged in as a Traveler to submit a complaint.";
            return false;
        }

        // Auto-filling Data
        $travelerId = $_SESSION["UserID"]; // Pulled from session
        $adminId = null; // No admin assigned yet
        $status = "Pending"; // Default status
        $resolution = null; // No resolution yet
        $createdDate = date("Y-m-d H:i:s"); // Current timestamp
        $resolvedDate = null; // Not resolved yet

        // Sanitize input to prevent XSS attacks
        $description = htmlspecialchars(trim($description));

        // Call the model using your exact parameter structure
        $complaintModel = new Complaint();
        $isCreated = $complaintModel->fileComplaint(
            $bookingId, $travelerId, $guideId, $adminId, 
            $photosUrl, $description, $status, $resolution, 
            $createdDate, $resolvedDate
        );

        if ($isCreated) {
            $_SESSION["successMsg"] = "Your complaint has been submitted successfully.";
            return true;
        }
        
        $_SESSION["errMsg"] = "An error occurred while submitting your complaint.";
        return false;
    }

    /* 2. Admin resolves and closes the complaint */
    // No need to pass $adminId or $resolvedDate, Controller handles them
    public function takeAction($complaintId, $status, $resolution)
    {
        // Security: Must be an Admin
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Admin') {
            $_SESSION["errMsg"] = "Only Administrators can resolve complaints.";
            return false;
        }

        $adminId = $_SESSION["UserID"]; // Identify which admin resolved it
        $resolvedDate = date("Y-m-d H:i:s"); // Timestamp of resolution
        $resolution = htmlspecialchars(trim($resolution));

        $complaintModel = new Complaint();
        $isUpdated = $complaintModel->resolveComplaint($complaintId, $adminId, $status, $resolution, $resolvedDate);

        if ($isUpdated) {
            $_SESSION["successMsg"] = "Complaint status updated successfully.";
            return true;
        }
        return false;
    }

    /* 3. List all complaints (Admins Only) */
    public function listAll()
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Admin') {
            return []; // Return empty array if not an Admin
        }

        $complaintModel = new Complaint();
        return $complaintModel->getAllComplaints();
    }

    /* 4. Get details of a specific complaint */
    public function getDetails($id)
    {
        if (!isset($_SESSION["UserID"])) return false;

        $complaintModel = new Complaint();
        $complaint = $complaintModel->getComplaintById($id);

        if (!$complaint) return false;

        // Security: If Traveler, ensure this complaint belongs to them!
        if ($_SESSION["UserRole"] === 'Traveler' && $complaint['TravelerId'] != $_SESSION["UserID"]) {
            $_SESSION["errMsg"] = "Unauthorized access to this complaint.";
            return false;
        }

        return $complaint;
    }

    /* 5. View traveler's complaint history */
    // Removed $travelerId parameter for security, pulling it from Session instead
    public function getTravelerHistory()
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Traveler') {
            return [];
        }

        $travelerId = $_SESSION["UserID"];
        $complaintModel = new Complaint();
        return $complaintModel->getComplaintsByTraveler($travelerId);
    }

    /* 6. Delete a complaint (Admins Only) */
    public function removeComplaint($id)
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Admin') {
            $_SESSION["errMsg"] = "Only Administrators can delete complaints.";
            return false;
        }

        $complaintModel = new Complaint();
        $isDeleted = $complaintModel->deleteComplaint($id);

        if ($isDeleted) {
            $_SESSION["successMsg"] = "Complaint deleted successfully.";
            return true;
        }
        return false;
    }
}
?>