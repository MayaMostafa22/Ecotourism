<?php
require_once '../../models/Experience.php';

class ExperienceController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * 1. List all experiences
     */
    public function listAll()
    {
        $experienceModel = new Experience();
        return $experienceModel->getAllExperiences();
    }

    /**
     * 2. Get details of a specific experience
     */
    public function getDetails($id)
    {
        $experienceModel = new Experience();
        $experience = $experienceModel->getExperienceById($id);

        if (!$experience) {
            $_SESSION["errMsg"] = "Experience not found.";
            return false;
        }
        return $experience;
    }

    /**
     * 3. Create a new experience
     */
    public function createNewExperience($name, $desc, $ecoRating, $status, $price, $locations, $start, $end)
    {
        if (!isset($_SESSION["UserRole"]) || ($_SESSION["UserRole"] !== 'Admin' && $_SESSION["UserRole"] !== 'Guide')) {
            $_SESSION["errMsg"] = "Unauthorized: You cannot add experiences.";
            return false;
        }

        $experienceModel = new Experience();
        $isCreated = $experienceModel->createExperience($name, $desc, $ecoRating, $status, $price, $locations, $start, $end);

        if ($isCreated) {
            $_SESSION["successMsg"] = "Experience created successfully.";
            return true;
        }
        
        $_SESSION["errMsg"] = "Failed to create experience.";
        return false;
    }

    /**
     * 4. Update an experience
     */
    public function updateExperience($id, $name, $desc, $ecoRating, $status, $price, $locations, $start, $end)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            $_SESSION["errMsg"] = "Unauthorized: Only Admins can edit experiences.";
            return false;
        }

        $experienceModel = new Experience();
        $isUpdated = $experienceModel->updateExperience($id, $name, $desc, $ecoRating, $status, $price, $locations, $start, $end);

        if ($isUpdated) {
            $_SESSION["successMsg"] = "Experience updated successfully.";
            return true;
        }
        return false;
    }

    /**
     * 5. Remove an experience
     */
    public function removeExperience($id)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            $_SESSION["errMsg"] = "Unauthorized: Only Admins can delete experiences.";
            return false;
        }

        $experienceModel = new Experience();
        if ($experienceModel->deleteExperience($id)) {
            $_SESSION["successMsg"] = "Experience deleted successfully.";
            return true;
        }
        return false;
    }

    /**
     * 6. Vetting Queue (For Admin Dashboard)
     */
    public function vettingQueue()
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return [];
        }

        $experienceModel = new Experience();
        return $experienceModel->getPendingExperiences();
    }

    /**
     * 7. Approve Experience (Action)
     */
    public function approveExperience($id)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return false;
        }

        $experienceModel = new Experience();
        // Uses the updateStatus from the model
        return $experienceModel->updateStatus($id, 'Approved');
    }
}