<?php
require_once __DIR__ . '/../models/Experience.php';
require_once __DIR__ . '/../models/ExperienceTier.php';
require_once __DIR__ . '/../models/AddOn.php';
require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/LocationStops.php';
require_once __DIR__ . '/../models/SeasonalRule.php';
require_once __DIR__ . '/../models/Resource.php';
require_once __DIR__ . '/../models/ExpResource.php';

class ExperienceController extends Controller
{
    private $experienceModel;
    private $tierModel;
    private $addOnModel;
    private $locationModel;
    private $locationStopsModel;
    private $seasonalRuleModel;
    private $resourceModel;
    private $expResourceModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->experienceModel    = new Experience();
        $this->tierModel          = new ExperienceTier();
        $this->addOnModel         = new AddOn();
        $this->locationModel      = new Location();
        $this->locationStopsModel = new LocationStops();
        $this->seasonalRuleModel  = new SeasonalRule();
        $this->resourceModel      = new Resource();
        $this->expResourceModel   = new ExpResource();
    }

    public function index()
    {
        $experiences = $this->experienceModel->getAllExperiences();
        $this->view("experience/index", ['experiences' => $experiences]);
    }

    public function create()
    {
        $locations = $this->locationModel->getAllLocations();
        $resources = $this->resourceModel->getAllResources();
        $this->view("experience/create", ['locations' => $locations, 'resources' => $resources]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            if (!isset($_SESSION["UserRole"]) ||
                !in_array($_SESSION["UserRole"], ['Admin', 'Guide'])) {
                header("Location: /Experience/create?error=unauthorized");
                exit();
            }

            $result = $this->experienceModel->createExperience(
                $_POST['name'], $_POST['desc'], $_POST['ecoRating'],
                'Pending', $_POST['price'], $_POST['locations'],
                $_POST['start'], $_POST['end']
            );
            if ($result) {
                header("Location: /Experience/index?success=created");
            } else {
                header("Location: /Experience/create?error=failed");
            }
            exit();
        }
    }

    public function show(int $id)
    {
        $experience = $this->experienceModel->getExperienceById($id);
        if (!$experience) {
            header("Location: /Experience/index?error=not_found");
            exit();
        }

        $this->view("experience/show", [
            'experience'   => $experience,
            'tiers'        => $this->tierModel->getTiersByExperience($id),
            'locations'    => $this->locationStopsModel->getStopsByExperience($id),
            'resources'    => $this->expResourceModel->getFullResourceDetailsByExp($id),
            'rules'        => $this->seasonalRuleModel->getRulesByExperience($id),
            'allLocations' => $this->locationModel->getAllLocations(),
            'allResources' => $this->resourceModel->getAllResources()
        ]);
    }

    public function update(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
                $_SESSION["errMsg"] = "Unauthorized: Only Admins can edit experiences.";
                header("Location: /Experience/show/$id?error=unauthorized");
                exit();
            }

            $result = $this->experienceModel->updateExperience(
                $id, $_POST['name'], $_POST['desc'], $_POST['ecoRating'],
                $_POST['status'], $_POST['price'], $_POST['locations'],
                $_POST['start'], $_POST['end']
            );
            header("Location: /Experience/show/$id" . ($result ? "?success=updated" : "?error=update_failed"));
            exit();
        }
    }

    public function delete(int $id)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            $_SESSION["errMsg"] = "Unauthorized: Only Admins can delete experiences.";
            header("Location: /Experience/show/$id?error=unauthorized");
            exit();
        }

        $this->experienceModel->deleteExperience($id);
        header("Location: /Experience/index?success=deleted");
        exit();
    }

    public function approveExperience(int $id)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            $_SESSION["errMsg"] = "Unauthorized.";
            return false;
        }

        return $this->experienceModel->updateStatus($id, 'Approved');
    }

    public function calculateEcoLeafRating($expID)
    {
        $stops      = $this->locationStopsModel->getStopsByExperience($expID);
        $totalScore = 0;
        $stopCount  = count($stops);

        if ($stopCount == 0) return 0;

        foreach ($stops as $stop) {
            switch ($stop['TransportMode']) {
                case 'Walking':
                case 'Mountain Bicycles':
                    $totalScore += 35;
                    break;
                case 'Sailboat / Felucca':
                    $totalScore += 30;
                    break;
                case 'Camel / Horse':
                    $totalScore += 25;
                    break;
                case 'Electric Vehicles':
                    $totalScore += 20;
                    break;
                default:
                    $totalScore += 5;
                    break;
            }
        }

        $finalScore = min(($totalScore / $stopCount) + 20, 100);
        $this->experienceModel->updateExperienceRating($expID, $finalScore);
        return round($finalScore);
    }

    public function getOptimizedAvailability($expID)
    {
        $rules = $this->seasonalRuleModel->getRulesByExperience($expID);
        $currentDate = date('Y-m-d');
        foreach ($rules as $rule) {
            if ($currentDate >= $rule['StartDate'] && $currentDate <= $rule['EndDate']) {
                return ["status" => $rule['ActionType'], "reason" => $rule['Reason']];
            }
        }
        return ["status" => "Available"];
    }

    public function checkResourceInventory($expID, $groupSize)
    {
        $required = $this->expResourceModel->getFullResourceDetailsByExp($expID);
        foreach ($required as $item) {
            $needed = $item['QuantityPerPerson'] * $groupSize;
            if ($needed > $item['TotallNum']) return false;
        }
        return true;
    }

    public function verifyLandConsent($expID)
    {
        $stops = $this->locationStopsModel->getStopsByExperience($expID);
        foreach ($stops as $stop) {
            $loc = $this->locationModel->getLocationById($stop['LocationID']);
            if ($loc['IsIndigenousLand'] && empty($loc['ConsentID'])) {
                return ["allowed" => false, "location" => $loc['LocationName']];
            }
        }
        return ["allowed" => true];
    }

    public function getSustainableRoute($expID)
    {
        return $this->locationStopsModel->getStopsByExperience($expID);
    }

    public function enforceCapacityLimits($tierID, $requestedSize)
    {
        $tier = $this->tierModel->getTierById($tierID);
        return $requestedSize <= $tier['MaxCapacity'];
    }

    public function getPendingVettingQueue()
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return [];
        }
        return $this->experienceModel->getPendingExperiences();
    }

    public function storeTier(int $expId)
    {
        $this->tierModel->createTier($expId, $_POST['tierName'], $_POST['maxCapacity']);
        header("Location: /Experience/show/$expId?success=tier_added");
        exit();
    }

    public function updateTier(int $expId, int $tierId)
    {
        $this->tierModel->updateTier($tierId, $_POST['tierName'], $_POST['maxCapacity']);
        header("Location: /Experience/show/$expId?success=tier_updated");
        exit();
    }

    public function deleteTier(int $expId, int $tierId)
    {
        if (!isset($_SESSION['UserRole']) || $_SESSION['UserRole'] !== 'Admin') {
            header("Location: /Experience/show/$expId?error=not_authorized");
            exit();
        }
        $this->tierModel->deleteTier($tierId);
        header("Location: /Experience/show/$expId?success=tier_deleted");
        exit();
    }

    public function storeStop(int $expId)
    {
        $this->locationStopsModel->createLocationStop(
            $_POST['locationID'], $expId, $_POST['distance'], $_POST['transport']
        );
        header("Location: /Experience/show/$expId?success=stop_added");
        exit();
    }

    public function updateStop(int $expId, int $locId)
    {
        $this->locationStopsModel->updateLocationStop($locId, $expId, $_POST['distance'], $_POST['transport']);
        header("Location: /Experience/show/$expId?success=stop_updated");
        exit();
    }

    public function deleteStop(int $expId, int $locId)
    {
        $this->locationStopsModel->deleteLocationStop($locId, $expId);
        header("Location: /Experience/show/$expId?success=stop_deleted");
        exit();
    }

    public function storeResource(int $expId)
    {
        $resId    = $_POST['resourceID'];
        $qty      = $_POST['quantityPerPerson'];
        $resource = $this->resourceModel->getResourceById($resId);

        if ($resource && $qty <= $resource['TotallNum']) {
            $this->expResourceModel->createExpResource($expId, $resId, $qty);
            header("Location: /Experience/show/$expId?success=resource_added");
        } else {
            header("Location: /Experience/show/$expId?error=insufficient_stock");
        }
        exit();
    }

    public function deleteResource(int $expId, int $resId)
    {
        $this->expResourceModel->deleteExpResource($expId, $resId);
        header("Location: /Experience/show/$expId?success=resource_removed");
        exit();
    }

    public function storeRule(int $expId)
    {
        $this->seasonalRuleModel->createRule($expId, $_POST['reason'], $_POST['action'], $_POST['start'], $_POST['end']);
        header("Location: /Experience/show/$expId?success=rule_added");
        exit();
    }

    public function updateRule(int $expId, int $ruleId)
    {
        $this->seasonalRuleModel->updateRule($ruleId, $_POST['reason'], $_POST['action'], $_POST['start'], $_POST['end']);
        header("Location: /Experience/show/$expId?success=rule_updated");
        exit();
    }

    public function deleteRule(int $expId, int $ruleId)
    {
        $this->seasonalRuleModel->deleteRule($ruleId);
        header("Location: /Experience/show/$expId?success=rule_deleted");
        exit();
    }

    public function storeAddon(int $expId)
    {
        $this->addOnModel->createAddOn($_POST['name'], $_POST['price']);
        header("Location: /Experience/show/$expId?success=addon_added");
        exit();
    }
}
