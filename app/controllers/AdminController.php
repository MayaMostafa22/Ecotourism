<?php
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/AuditLog.php';

class AdminCoreController
{
    private $adminModel;
    private $auditLogModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->adminModel = new Admin();
        $this->auditLogModel = new AuditLog();
    }

    // Role-Based Access Control 

    public function assignAdminRole($userID, $adminType)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'SuperAdmin') {
            $_SESSION["errMsg"] = "Unauthorized access. Only Super Admins can assign roles.";
            return false;
        }

        if (empty($userID) || empty($adminType)) {
            $_SESSION["errMsg"] = "User ID and Admin Type are required.";
            return false;
        }

        if ($adminType !== 'Auditor') { 
            $_SESSION["errMsg"] = "Super Admins are only allowed to add an 'Auditor'.";
            return false;
        }

        $allAdmins = $this->adminModel->getAllAdmins();
        foreach ($allAdmins as $admin) {
            if ($admin['AdminType'] === 'Auditor') {
                $_SESSION["errMsg"] = "An Auditor already exists in the system. Only one is allowed.";
                return false;
            }
        }
        $success = $this->adminModel->createAdmin($adminType, $userID);
        
        if ($success) {
            $_SESSION["successMsg"] = "Auditor role assigned successfully.";            $currentAdmin = $_SESSION["UserID"];
            $this->auditLogModel->logAction($currentAdmin, "ASSIGN_ROLE", "Assigned $adminType role to UserID: $userID");
            
            return true;
        }

        $_SESSION["errMsg"] = "Failed to assign admin role.";
        return false;
    }

    public function listAllAdmins()
    {
        if (!isset($_SESSION["UserRole"]) || ($_SESSION["UserRole"] !== 'SuperAdmin' && $_SESSION["UserRole"] !== 'RegionalAuditor')) {
            $_SESSION["errMsg"] = "Unauthorized access.";
            return [];
        }

        return $this->adminModel->getAllAdmins();
    }

    public function updateAdminRole($adminID, $newAdminType)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'SuperAdmin') {
            $_SESSION["errMsg"] = "Unauthorized access.";
            return false;
        }

        if ($newAdminType === 'Auditor') {
            $allAdmins = $this->adminModel->getAllAdmins();
            foreach ($allAdmins as $admin) {
                if ($admin['AdminType'] === 'Auditor' && $admin['AdminID'] != $adminID) {
                    $_SESSION["errMsg"] = "An Auditor already exists in the system. Cannot promote to Auditor.";
                    return false;
                }
            }
        }

        $success = $this->adminModel->updateAdmin($adminID, $newAdminType);
        
        if ($success) {
            $_SESSION["successMsg"] = "Admin role updated successfully.";
            
            $currentAdmin = $_SESSION["UserID"];
            $this->auditLogModel->logAction($currentAdmin, "UPDATE_ROLE", "Updated AdminID: $adminID to $newAdminType");
            
            return true;
        }

        $_SESSION["errMsg"] = "Failed to update admin role.";
        return false;
    }

    // 2. System Audit Trail 
    public function viewSystemAuditLogs()
    {
        if (!isset($_SESSION["UserRole"]) || ($_SESSION["UserRole"] !== 'SuperAdmin' && $_SESSION["UserRole"] !== 'RegionalAuditor')) {
            $_SESSION["errMsg"] = "Unauthorized access to audit logs.";
            return [];
        }

        return $this->auditLogModel->getAllLogs();
    }

    public function logExperienceChange($expID, $filedType, $oldValue, $newValue)
    {
        if (!isset($_SESSION["UserID"])) {
            return false;
        }
        
        $adminID = $_SESSION["UserID"];
        return $this->auditLogModel->createLog($expID, $filedType, $adminID, $oldValue, $newValue);
    }

    // 3. Experience "Vetting" Queue - [Task #38]

    public function approveExperience($expID)
    {
        if (!isset($_SESSION["UserRole"]) || ($_SESSION["UserRole"] !== 'SuperAdmin' && $_SESSION["UserRole"] !== 'RegionalAuditor')) {
            $_SESSION["errMsg"] = "Unauthorized access. Only Admins or Auditors can vet experiences.";
            return false;
        }

        $adminID = $_SESSION["UserID"];
        
        $success = true;

        if ($success) {
            $this->auditLogModel->createLog($expID, 'Status', $adminID, 'Pending', 'Approved');
            $_SESSION["successMsg"] = "Experience approved and logged in the audit trail.";
            return true;
        }

        $_SESSION["errMsg"] = "Failed to approve experience.";
        return false;
    }
}
?>