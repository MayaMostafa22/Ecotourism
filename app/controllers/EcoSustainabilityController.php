<?php
require_once '../../models/CarbonOffsetProject.php';
require_once '../../models/EcoCertificate.php';
require_once '../../models/CommunityConsent.php';

class EcoSustainabilityController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // 1. Carbon Offset Projects
    
    public function listCarbonProjects()
    {
        $projectModel = new CarbonOffsetProject();
        return $projectModel->getAllProjects();
    }

    public function addCarbonProject($projectName, $description, $costPerUnit)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            $_SESSION["errMsg"] = "Unauthorized access.";
            return false;
        }

        if (empty($projectName) || $costPerUnit < 0) {
            $_SESSION["errMsg"] = "Invalid project data.";
            return false;
        }

        $projectModel = new CarbonOffsetProject();
        $isCreated = $projectModel->createProject($projectName, $description, $costPerUnit);

        if ($isCreated) {
            $_SESSION["successMsg"] = "Carbon project added successfully.";
            return true;
        }
        return false;
    }

    public function editCarbonProject($id, $projectName, $description, $costPerUnit)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return false;
        }

        $projectModel = new CarbonOffsetProject();
        if ($projectModel->updateProject($id, $projectName, $description, $costPerUnit)) {
            $_SESSION["successMsg"] = "Project updated successfully.";
            return true;
        }
        return false;
    }

    public function removeCarbonProject($id)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return false;
        }

        $projectModel = new CarbonOffsetProject();
        return $projectModel->deleteProject($id);
    }

    // 2. Eco Certificates

    public function uploadEcoCertificate($url, $name)
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Guide') {
            $_SESSION["errMsg"] = "Unauthorized access.";
            return false;
        }

        $guideID = $_SESSION["UserID"];
        $certModel = new EcoCertificate();
        
        $isUploaded = $certModel->createCertificate($url, $name, $guideID);

        if ($isUploaded) {
            $_SESSION["successMsg"] = "Certificate uploaded successfully.";
            return true;
        }
        return false;
    }

    public function getMyCertificates()
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Guide') {
            return [];
        }

        $certModel = new EcoCertificate();
        return $certModel->getCertificatesByGuide($_SESSION["UserID"]);
    }

    public function removeEcoCertificate($certID)
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Guide') {
            return false;
        }

        $certModel = new EcoCertificate();
        $isDeleted = $certModel->deleteCertificate($certID);

        if ($isDeleted) {
            $_SESSION["successMsg"] = "Certificate deleted.";
            return true;
        }
        return false;
    }

    // 3. Community Consents

    public function listCommunityConsents()
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return [];
        }

        $consentModel = new CommunityConsent();
        return $consentModel->getAllConsents();
    }

    public function recordCommunityConsent($status, $contractURL)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return false;
        }

        $allowedStatuses = ['Approved', 'Refused'];
        if (!in_array($status, $allowedStatuses)) {
            $_SESSION["errMsg"] = "Invalid status.";
            return false;
        }

        $consentModel = new CommunityConsent();
        $isRecorded = $consentModel->createConsent($status, $contractURL);

        if ($isRecorded) {
            $_SESSION["successMsg"] = "Community consent recorded successfully.";
            return true;
        }
        return false;
    }

    public function editCommunityConsent($id, $status, $contractURL)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return false;
        }

        $allowedStatuses = ['Approved', 'Refused'];
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $consentModel = new CommunityConsent();
        return $consentModel->updateConsent($id, $status, $contractURL);
    }

    public function removeCommunityConsent($id)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return false;
        }

        $consentModel = new CommunityConsent();
        return $consentModel->deleteConsent($id);
    }
}
?>