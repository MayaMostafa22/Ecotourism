<?php
require_once "../app/models/Guid.php";
require_once "../app/models/GuideIdentity.php";
require_once "../app/models/GuideSchedule.php";
require_once "../app/models/GuideLanguage.php";
require_once "../app/models/GuideWallet.php";
require_once "../app/models/FieldReports.php";

class GuideController
{
    private $guideModel;
    private $identityModel;
    private $scheduleModel;
    private $langModel;
    private $walletModel;
    private $FieldReports;

    public function __construct()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'guide') {
            header("Location: /login");
            exit();
        }

        $this->guideModel = new Guid();
        $this->identityModel = new GuideIdentity();
        $this->scheduleModel = new GuideSchedule();
        $this->langModel = new GuideLanguage();
        $this->walletModel = new GuideWallet();
        $this->FieldReports = new FieldReports();
    }

    /**
     * 12. Language Proficiency Verification
     */
    public function verifyLanguage($langId)
    {
        $guideId = $_SESSION["UserID"];

        $isUpdated = $this->langModel->updateVerificationStatus($guideId, $langId, 1);

        if ($isUpdated) {
            $_SESSION["successMsg"] = "Language skills verified successfully.";
            return true;
        } else {
            $_SESSION["errMsg"] = "Failed to verify language proficiency.";
            return false;
        }
    }

    /**
     * 13. Guide "Local Cred" Algorithm
     */
    public function updateLocalCred()
    {
        $guideId = $_SESSION["UserID"];
        $guideData = $this->guideModel->getGuideById($guideId);

        if (!$guideData) {
            $_SESSION["errMsg"] = "Guide data not found.";
            return false;
        }

        //algo:(snwat el2kama *10)+ 5 key points for each beginner guide
        $score = ($guideData['yearsOfResidency'] * 10) + 5;

        $isUpdated = $this->guideModel->updateLocalCredScore($guideId, $score);

        if ($isUpdated) {
            $_SESSION["successMsg"] = "Local Credibility Score updated: $score";
            return $score;
        }
        return false;
    }

    /**
     * 15. Guide Availability Conflict Resolver
     */
    public function checkScheduleConflict($requestedDate)
    {
        $guideId = $_SESSION["UserID"];
        $existingSchedules = $this->scheduleModel->getSchedulesByGuide($guideId);

        foreach ($existingSchedules as $schedule) {
            if ($schedule['date'] == $requestedDate) {
                $_SESSION["errMsg"] = "Conflict: You are already booked on $requestedDate.";
                return true;
            }
        }
        return false;
    }

    /**
     * 18. Mobile "In-Field" Profile Updater
     */
    public function storeFieldReport($tourId, $reportContent)
    {
        $guideId = $_SESSION["UserID"];
        $mediaURL = "";

        if (empty($reportContent)) {
            $_SESSION["errMsg"] = "Report content cannot be empty.";
            return false;
        }

        $isSaved = $this->FieldReports->createFieldReport($mediaURL, $reportContent, $guideId, $tourId);

        if ($isSaved) {
            $_SESSION["successMsg"] = "Field report posted successfully!";
            return true;
        }
        return false;
    }

    /**
     * 19. Guide Revenue Split Logic
     */
    public function getRevenueDetails($experiencePrice)
    {
        $platformFee = 0.15;
        $communityTax = 0.05;

        $netAmount = $experiencePrice * (1 - ($platformFee + $communityTax));

        return [
            'total' => $experiencePrice,
            'fee' => $experiencePrice * $platformFee,
            'tax' => $experiencePrice * $communityTax,
            'net' => $netAmount
        ];
    }

    /**
     * 20. Withdrawal Request Processor
     */
    public function withdrawFunds($amount)
    {
        $guideId = $_SESSION["UserID"];
        $wallet = $this->walletModel->getWalletByGuideId($guideId);

        if (!$wallet || $wallet['AvailableBalance'] < $amount) {
            $_SESSION["errMsg"] = "Insufficient balance for withdrawal.";
            return false;
        }

        $newBalance = $wallet['AvailableBalance'] - $amount;
        $isProcessed = $this->walletModel->updateBalances($guideId, $newBalance, $wallet['PendingBalance']);

        if ($isProcessed) {
            $_SESSION["successMsg"] = "Withdrawal request of $amount processed.";
            return true;
        }
        return false;
    }

    /**
     * 21. Performance-Based Visibility Boost
     */
    public function boostVisibility()
    {
        $guideId = $_SESSION["UserID"];
        $guide = $this->guideModel->getGuideById($guideId);

        if ($guide) {

            $boostScore = ($guide['SustainabilityScore'] * 0.8) - ($guide['CancellationRate'] * 0.2);
            $this->guideModel->updateVisibilityScore($guideId, $boostScore);
            return $boostScore;
        }
        return false;
    }

    /**
     * 22. Biometric Identity Verification Simulation
     */
    public function verifyBiometric()
    {

        $identityId = $_SESSION["IdentityID"] ?? null;

        if (!$identityId) {
            $_SESSION["errMsg"] = "Identity ID not found in session.";
            return false;
        }

        $isVerified = $this->identityModel->verifyIdentity($identityId);

        if ($isVerified) {
            $_SESSION["successMsg"] = "Biometric check passed. Identity is now Active.";
            return true;
        }
        return false;
    }
}