<?php
require_once __DIR__ . '/../models/GuideIdentity.php';
require_once __DIR__ . '/../models/GuideLanguage.php';
require_once __DIR__ . '/../models/AuditLog.php'; 

class AuditorController
{
    private $guideIdentityModel;
    private $guideLanguageModel;
    private $auditLogModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->guideIdentityModel = new GuideIdentity();
        $this->guideLanguageModel = new GuideLanguage();
        $this->auditLogModel = new AuditLog();
    }

    // 1. Biometric Identity Verification 

    public function verifyGuideIdentity($identityID, $verificationStatus)
    {
        if (!isset($_SESSION["UserRole"]) || ($_SESSION["UserRole"] !== 'Auditor' && $_SESSION["UserRole"] !== 'SuperAdmin')) {
            $_SESSION["errMsg"] = "Unauthorized access. Only Auditors can verify identity.";
            return false;
        }

        if (empty($identityID) || empty($verificationStatus)) {
            $_SESSION["errMsg"] = "Identity ID and Verification Status are required.";
            return false;
        }

        $success = $this->guideIdentityModel->updateStatus($identityID, $verificationStatus);

        if ($success) {
            $_SESSION["successMsg"] = "Guide identity marked as: $verificationStatus.";
            $currentAuditor = $_SESSION["UserID"];
            $this->auditLogModel->logAction($currentAuditor, "IDENTITY_VERIFICATION", "Updated IdentityID: $identityID to Status: $verificationStatus");
            
            return true;
        }

        $_SESSION["errMsg"] = "Failed to update identity status.";
        return false;
    }

    // 2. Language Proficiency Verification 

    public function verifyLanguageProficiency($guideId, $langId, $verificationStatus)
    {
        if (!isset($_SESSION["UserRole"]) || ($_SESSION["UserRole"] !== 'Auditor' && $_SESSION["UserRole"] !== 'SuperAdmin')) {
            $_SESSION["errMsg"] = "Unauthorized access. Only Auditors can verify languages.";
            return false;
        }

        if (empty($guideId) || empty($langId) || !isset($verificationStatus)) {
            $_SESSION["errMsg"] = "Missing required information for language verification.";
            return false;
        }

        $success = $this->guideLanguageModel->updateVerificationStatus($guideId, $langId, $verificationStatus);

        if ($success) {
            $_SESSION["successMsg"] = "Language verification status updated successfully.";
            $currentAuditor = $_SESSION["UserID"];
            $this->auditLogModel->logAction($currentAuditor, "LANGUAGE_VERIFICATION", "Set Verification Status: $verificationStatus for LangID: $langId, GuideID: $guideId");
            
            return true;
        }

        $_SESSION["errMsg"] = "Failed to update language verification status.";
        return false;
    }
}
?>