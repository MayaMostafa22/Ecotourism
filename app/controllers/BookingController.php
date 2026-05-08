<?php
require_once '../../models/Booking.php';
require_once '../../models/Experience.php'; 
require_once '../../models/Cancellation.php';

class BookingController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /* 1. Create a new booking */
    public function addBooking($expID, $groupSize, $discountID = null, $discountAmount = 0)
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Traveler') {
            $_SESSION["errMsg"] = "You must be logged in as a Traveler to complete the booking.";
            return false;
        }

        $travelerID = $_SESSION["UserID"];

        if ($groupSize < 1) {
            $_SESSION["errMsg"] = "Group size must be at least 1 person.";
            return false;
        }

        $experienceModel = new Experience();
        $experienceData = $experienceModel->getExperienceById($expID);

        if (!$experienceData) {
            $_SESSION["errMsg"] = "The selected experience is currently unavailable.";
            return false;
        }

        $basePrice = $experienceData['basePrice']; 
        $finalPrice = ($basePrice * $groupSize) - $discountAmount;
        if ($finalPrice < 0) { $finalPrice = 0; }

        $bookingDate = date("Y-m-d H:i:s");
        $status = "Pending";
        $isWaitListAccepted = 0;

        $bookingModel = new Booking();
        $isCreated = $bookingModel->createBooking(
            $travelerID, $expID, $bookingDate, $status, 
            $isWaitListAccepted, $groupSize, $discountID, 
            $discountAmount, $basePrice, $finalPrice
        );

        if ($isCreated) {
            $_SESSION["successMsg"] = "Booking registered successfully, pending payment.";
            return true;
        } else {
            $_SESSION["errMsg"] = "An error occurred while registering the booking, please try again.";
            return false;
        }
    }

    /* 2. Get details of a specific booking */
    public function getDetails($id)
    {
        if (!isset($_SESSION["UserID"])) return false;
        $bookingModel = new Booking();
        return $bookingModel->getBookingById($id);
    }

    /* 3. List all bookings for the current traveler */
    public function listMyBookings()
    {
        if (!isset($_SESSION["UserID"])) return [];

        $bookingModel = new Booking();
        $allBookings = $bookingModel->getAllBookings();
        
        $myBookings = [];
        foreach ($allBookings as $booking) {
            if ($booking['TravelerID'] == $_SESSION["UserID"]) {
                $myBookings[] = $booking;
            }
        }
        return $myBookings;
    }

    /* 4. Cancel booking with Dynamic Refund Policy */
    public function cancelBooking($bookingID, $reason)
    {
        // 1. Check permissions
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Traveler') {
            $_SESSION["errMsg"] = "You are not authorized to cancel this booking.";
            return false;
        }

        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingById($bookingID);

        // 2. Ensure the booking belongs to the user and is not already cancelled
        if (!$booking || $booking['TravelerID'] != $_SESSION["UserID"]) {
            $_SESSION["errMsg"] = "Booking not found or you do not have permission to access it.";
            return false;
        }

        if ($booking['Status'] === 'Cancelled') {
            $_SESSION["errMsg"] = "This booking is already cancelled.";
            return false;
        }

        // 3. Dynamic Application Logic: Calculate Refund based on Experience StartTime
        $experienceModel = new Experience();
        // Assuming the column name in booking table is 'ExpID'
        $experienceData = $experienceModel->getExperienceById($booking['ExpID']); 
        
        if (!$experienceData) {
            $_SESSION["errMsg"] = "Error retrieving experience details for cancellation.";
            return false;
        }

        // Convert dates to timestamps for math
        $tripDate = strtotime($experienceData['StartTime']);
        $today = time(); 

        // Calculate difference in days
        $daysDifference = ($tripDate - $today) / (60 * 60 * 24);

        // Apply Refund Policy
        if ($daysDifference >= 7) {
            // Cancelled 7 or more days before trip: 100% Refund
            $refundedPercentage = 100.00;
            $cancellationFee = 0.00;
        } elseif ($daysDifference >= 3) {
            // Cancelled 3 to 6 days before trip: 80% Refund, 20% Fee
            $refundedPercentage = 80.00;
            $cancellationFee = ($booking['FinalPrice'] * 20) / 100;
        } else {
            // Cancelled less than 3 days before (or already passed): 0% Refund, 100% Fee
            $refundedPercentage = 0.00;
            $cancellationFee = $booking['FinalPrice'];
        }

        // 4. Change the primary booking status to Cancelled
        $isStatusUpdated = $bookingModel->updateStatus($bookingID, 'Cancelled');

        if ($isStatusUpdated) {
            // 5. Record cancellation details in the cancellation table
            $cancellationModel = new Cancellation();
            $isRecordCreated = $cancellationModel->createCancellation(
                $bookingID, 
                $refundedPercentage, 
                $cancellationFee, 
                $reason
            );

            if ($isRecordCreated) {
                $_SESSION["successMsg"] = "Booking cancelled successfully. Refund percentage: {$refundedPercentage}%";
                return true;
            } else {
                $_SESSION["errMsg"] = "Cancelled, but an error occurred while recording the refund invoice.";
                return false;
            }
        }

        $_SESSION["errMsg"] = "An error occurred while trying to cancel the booking.";
        return false;
    }

    /* 5. Delete booking record permanently (for Admin) */
    public function removeBooking($id)
    {
        if (isset($_SESSION["UserRole"]) && $_SESSION["UserRole"] === 'Admin') {
            $bookingModel = new Booking();
            return $bookingModel->deleteBooking($id);
        }
        return false;
    }
}
?>