<?php
require_once "../app/helpers/Validator.php";
require_once "../app/helpers/Auth.php";


require_once "../app/models/Guid.php";
require_once "../app/models/GuideIdentity.php";
require_once "../app/models/GuideSchedule.php";
require_once "../app/models/GuideLanguage.php";
require_once "../app/models/GuideWallet.php";
require_once "../app/models/FieldReports.php";

class GuideController extends Controller
{
    private $guideModel;
    private $identityModel;
    private $scheduleModel;
    private $langModel;
    private $walletModel;
    private $FieldReports;

    public function __construct()
    {
        Auth::redirectIfNotLogged();
        Auth::forbidIfNotRole("guide");

      
        $this->guideModel    = new Guid();
        $this->identityModel = new GuideIdentity();
        $this->scheduleModel = new GuideSchedule();
        $this->langModel     = new GuideLanguage();
        $this->walletModel   = new GuideWallet();
        $this->FieldReports   = new FieldReports();
    }

    // 12. Language Proficiency Verification
    public function verifyLanguage()
    {
        $langId = $_POST['lang_id'];
        $guideId = Auth::user()->id;
     
        $this->langModel->updateVerificationStatus($guideId, $langId, 1);
       // header("Location: " . BASE_URL . "Guide/profile");
    }

    // 13. Guide "Local Cred" Algorithm
    public function updateLocalCred()
    {
        $guideId = Auth::user()->id;
        $guideData = $this->guideModel->getGuideById($guideId);
        
        // الخوارزمية: (سنوات الإقامة * 10) + (عدد الرحلات المحلية * 2)
        $score = ($guideData['yearsOfResidency'] * 10) + 5; 
        
        $this->guideModel->updateLocalCredScore($guideId, $score);
        echo "Local Credibility Score updated to: " . $score;
    }

    
    // 15. Guide Availability Conflict Resolver
    public function checkScheduleConflict($requestedDate)
    {
        $guideId = Auth::user()->id;
        $existingSchedules = $this->scheduleModel->getSchedulesByGuide($guideId);
        
        foreach ($existingSchedules as $schedule) {
            if ($schedule['date'] == $requestedDate) {
                return "Conflict Detected: You are already booked on this date!";
            }
        }
        return "Available";
    }

    // 18. Mobile "In-Field" Profile Updater
    
    public function storeFieldReport()
    {
        $guideId = Auth::user()->id; //
        $reportContent = $_POST['report_text']; //
        $tourId = $_POST['tour_id']; //

        // 1. لازم نبعت قيمة للميديا (حتى لو فاضية) لأن الموديل مستني 4 حاجات
        $mediaURL = "";

        // 2. استخدام نفس الاسم اللي عرفتيه في الـ constructor وهو $this->FieldReports
        // 3. ترتيب البيانات لازم يطابق الموديل: URL ثم Text ثم GuideID ثم ExpID
        $this->FieldReports->createFieldReport($mediaURL, $reportContent, $guideId, $tourId);

        //header("Location: " . BASE_URL . "Guide/dashboard"); //
    }
    // 19. Guide Revenue Split Logic
    public function getRevenueDetails($experiencePrice)
    {
        $platformFee = 0.15; // 15% عمولة الموقع
        $communityTax = 0.05; // 5% ضرائب مجتمع محلي
        
        $netAmount = $experiencePrice * (1 - ($platformFee + $communityTax));
        
        return [
            'total' => $experiencePrice,
            'fee' => $experiencePrice * $platformFee,
            'tax' => $experiencePrice * $communityTax,
            'net' => $netAmount
        ];
    }

    // 20. Withdrawal Request Processor
    public function withdrawFunds()
    {
        $guideId = Auth::user()->id;
        $amount = $_POST['amount'];
        $wallet = $this->walletModel->getWalletByGuideId($guideId);

        if ($wallet['AvailableBalance'] >= $amount) {
            // عملية سحب الأموال
            $newBalance = $wallet['AvailableBalance'] - $amount;
            $this->walletModel->updateBalances($guideId, $newBalance, $wallet['PendingBalance']);
            echo "Withdrawal successful!";
        } else {
            echo "Insufficient balance.";
        }
    }

    // 21. Performance-Based Visibility Boost
    public function boostVisibility()
    {
        $guideId = Auth::user()->id;
        $guide = $this->guideModel->getGuideById($guideId);
        
        // خوارزمية الظهور: تعتمد على تقييم الاستدامة ونسبة الإلغاء
        $boostScore = ($guide['SustainabilityScore'] * 0.8) - ($guide['CancellationRate'] * 0.2);
        
        $this->guideModel->updateVisibilityScore($guideId, $boostScore);
    }

    // 22. Biometric Identity Verification Simulation
    public function verifyBiometric()
    {
        $identityId = Auth::user()->IdentityID;
        // محاكاة التحقق الحيوي لتفعيل الحساب
        $this->identityModel->verifyIdentity($identityId);
        echo "Biometric check passed. Identity status updated to Active.";
    }
}