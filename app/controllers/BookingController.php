<?php
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Waitlist.php';
require_once __DIR__ . '/../models/BookingAddOn.php';
require_once __DIR__ . '/../models/BookingEscrow.php';
require_once __DIR__ . '/../models/Cancellation.php';
require_once __DIR__ . '/../models/BookingOffsetProject.php';

class BookingController
{
    private $bookingModel;
    private $waitlistModel;
    private $addOnModel;
    private $escrowModel;
    private $cancellationModel;
    private $offsetModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->bookingModel      = new Booking();
        $this->waitlistModel     = new Waitlist();
        $this->addOnModel        = new BookingAddOn();
        $this->escrowModel       = new BookingEscrow();
        $this->cancellationModel = new Cancellation();
        $this->offsetModel       = new BookingOffsetProject();
    }

    /* =========================
       FUNCTION 24 + 25 + 28
       CREATE BOOKING
    ========================= */
    public function createBooking()
    {
        if (!isset($_SESSION['UserID']) || $_SESSION['UserRole'] !== 'traveler') {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        $travelerID  = $_SESSION['UserID'];
        $expID       = $_POST['expID'] ?? '';
        $bookingDate = $_POST['bookingDate'] ?? '';
        $groupSize   = $_POST['groupSize'] ?? '';
        $basePrice   = $_POST['basePrice'] ?? '';
        $discountID  = $_POST['discountID'] ?? 0;

        if (empty($expID) || empty($bookingDate) || empty($groupSize) || empty($basePrice)) {
            $_SESSION['errMsg'] = "All fields are required.";
            return false;
        }

        /* FUNCTION 28 - ITINERARY CONFLICT DETECTION */
        $allBookings = $this->bookingModel->getAllBookings();
        foreach ($allBookings as $booking) {
            if (
                $booking['TravelerID'] == $travelerID &&
                $booking['BookingDate'] == $bookingDate &&
                $booking['Status'] != 'Cancelled'
            ) {
                $_SESSION['errMsg'] = "You already have a booking at this time.";
                return false;
            }
        }

        /* FUNCTION 24 - GROUP DISCOUNT */
        if ($groupSize >= 10) {
            $discountPercent = 0.20;
        } elseif ($groupSize >= 5) {
            $discountPercent = 0.10;
        } else {
            $discountPercent = 0;
        }

        $discountAmount = $basePrice * $discountPercent;
        $finalPrice     = $basePrice - $discountAmount;

        $success = $this->bookingModel->createBooking(
            $travelerID,
            $expID,
            $bookingDate,
            'Pending',
            0,
            $groupSize,
            $discountID,
            $discountAmount,
            $basePrice,
            $finalPrice
        );

        if (!$success) {
            $_SESSION['errMsg'] = "Failed to create booking.";
            return false;
        }

        /* FUNCTION 25 - SECURE BOOKING ESCROW */
        $bookingID = $this->bookingModel->getAllBookings()[0]['BookingID'];
        $entryDate = date('Y-m-d H:i:s');
        $this->escrowModel->createEscrow($bookingID, 'Held', $finalPrice, $entryDate, null);

        $_SESSION['successMsg'] = "Booking created successfully.";
        return true;
    }

    /* =========================
       GET MY BOOKINGS
    ========================= */
    public function getMyBookings()
    {
        if (!isset($_SESSION['UserID'])) {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        $travelerID  = $_SESSION['UserID'];
        $allBookings = $this->bookingModel->getAllBookings();

        return array_values(array_filter(
            $allBookings,
            fn($b) => $b['TravelerID'] == $travelerID
        ));
    }

    /* =========================
       FUNCTION 26 - CANCELLATION PENALTY TIERING
    ========================= */
    public function cancelBooking($bookingID)
    {
        if (!isset($_SESSION['UserID']) || $_SESSION['UserRole'] !== 'traveler') {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        if (empty($bookingID)) {
            $_SESSION['errMsg'] = "Booking ID is required.";
            return false;
        }

        $booking = $this->bookingModel->getBookingById($bookingID);

        if (!$booking || $booking['TravelerID'] != $_SESSION['UserID']) {
            $_SESSION['errMsg'] = "Booking not found or access denied.";
            return false;
        }

        $tourDate = strtotime($booking['BookingDate']);
        $today    = time();
        $daysLeft = ($tourDate - $today) / (60 * 60 * 24);

        if ($daysLeft >= 7) {
            $refundPercent   = 100;
            $cancellationFee = 0;
        } elseif ($daysLeft >= 3) {
            $refundPercent   = 50;
            $cancellationFee = $booking['FinalPrice'] * 0.50;
        } else {
            $refundPercent   = 0;
            $cancellationFee = $booking['FinalPrice'];
        }

        $this->bookingModel->updateStatus($bookingID, 'Cancelled');
        $this->cancellationModel->createCancellation(
            $bookingID,
            $refundPercent,
            $cancellationFee,
            $_POST['reason'] ?? 'No reason provided'
        );

        /* FUNCTION 27 - WAITLIST PROMOTION */
        $this->promoteFromWaitlist($booking['ExpID']);

        $_SESSION['successMsg'] = "Booking cancelled. Refund: $refundPercent%.";
        return true;
    }

    /* =========================
       FUNCTION 27 - WAITLIST PROMOTION
    ========================= */
    private function promoteFromWaitlist($expID)
    {
        $waitlist = $this->waitlistModel->getWaitlistByExperience($expID);

        if (!empty($waitlist)) {
            $next = $waitlist[0];
            $this->bookingModel->updateStatus($next['BookingID'], 'Confirmed');
            $this->waitlistModel->removeFromWaitlist($next['WaitListID']);
            $_SESSION['successMsg'] .= " Next traveler on waitlist has been notified.";
        }
    }

    /* =========================
       FUNCTION 29 - ADD-ON SERVICE MANAGER
    ========================= */
    public function addAddOn($bookingID, $quantity)
    {
        if (!isset($_SESSION['UserID']) || $_SESSION['UserRole'] !== 'traveler') {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        if (empty($bookingID) || empty($quantity)) {
            $_SESSION['errMsg'] = "Booking ID and quantity are required.";
            return false;
        }

        $success = $this->addOnModel->createBookingAddOn($bookingID, $quantity);

        if ($success) {
            $_SESSION['successMsg'] = "Add-on added successfully.";
            return true;
        }

        $_SESSION['errMsg'] = "Failed to add add-on.";
        return false;
    }

    /* =========================
       FUNCTION 31 - VOUCHER GENERATION
    ========================= */
    public function generateVoucher($bookingID)
    {
        if (!isset($_SESSION['UserID']) || $_SESSION['UserRole'] !== 'traveler') {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        if (empty($bookingID)) {
            $_SESSION['errMsg'] = "Booking ID is required.";
            return false;
        }

        $booking = $this->bookingModel->getBookingById($bookingID);

        if (!$booking || $booking['TravelerID'] != $_SESSION['UserID']) {
            $_SESSION['errMsg'] = "Booking not found or access denied.";
            return false;
        }

        $voucherCode = strtoupper(uniqid('ECO-'));

        return [
            'booking'     => $booking,
            'voucherCode' => $voucherCode
        ];
    }

    /* =========================
       FUNCTION 32 - EMERGENCY REFUND
    ========================= */
    public function emergencyRefund($bookingID)
    {
        if (!isset($_SESSION['UserID'])) {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        if (empty($bookingID)) {
            $_SESSION['errMsg'] = "Booking ID is required.";
            return false;
        }

        $booking = $this->bookingModel->getBookingById($bookingID);

        if (!$booking) {
            $_SESSION['errMsg'] = "Booking not found.";
            return false;
        }

        $this->bookingModel->updateStatus($bookingID, 'Cancelled');
        $this->cancellationModel->createCancellation(
            $bookingID,
            100,
            0,
            'Force Majeure - Emergency Refund'
        );

        $escrow = $this->escrowModel->getEscrowByBookingId($bookingID);
        if ($escrow) {
            $this->escrowModel->updateEscrow(
                $escrow['EscrowId'],
                'Released',
                $escrow['AmountHeld'],
                date('Y-m-d H:i:s')
            );
        }

        $_SESSION['successMsg'] = "Emergency refund processed. Full refund will be issued.";
        return true;
    }

    /* =========================
       FUNCTION 5 - CARBON OFFSET INTEGRATION
    ========================= */
    public function addCarbonOffset($bookingID, $projectID, $offsetAmount)
    {
        if (!isset($_SESSION['UserID']) || $_SESSION['UserRole'] !== 'traveler') {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        if (empty($bookingID) || empty($projectID) || empty($offsetAmount)) {
            $_SESSION['errMsg'] = "All fields are required.";
            return false;
        }

        $success = $this->offsetModel->createBookingOffset($projectID, $bookingID, $offsetAmount);

        if ($success) {
            $_SESSION['successMsg'] = "Carbon offset added successfully.";
            return true;
        }

        $_SESSION['errMsg'] = "Failed to add carbon offset.";
        return false;
    }

    /* =========================
       GET OFFSET DETAILS
    ========================= */
    public function getOffsetDetails($bookingID)
    {
        if (empty($bookingID)) {
            $_SESSION['errMsg'] = "Booking ID is required.";
            return false;
        }

        return $this->offsetModel->getFullOffsetDetails($bookingID);
    }
}