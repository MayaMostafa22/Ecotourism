<?php
require_once '../../models/Payment.php';
require_once '../../models/Booking.php';

class PaymentController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    
    public function processPayment($bookingID, $amount, $currency, $exchangeRate)
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Traveler') {
            $_SESSION["errMsg"] = "Unauthorized access. Please login.";
            return false;
        }

        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingById($bookingID);

        // Validation
        if (!$booking || $booking['TravelerID'] != $_SESSION["UserID"]) {
            $_SESSION["errMsg"] = "Booking not found or access denied.";
            return false;
        }

        if ($booking['Status'] === 'Confirmed') {
            $_SESSION["errMsg"] = "This booking is already paid.";
            return false;
        }

        $allowedCurrencies = ['$Dollar', 'EGP'];
        if (!in_array($currency, $allowedCurrencies)) {
            $_SESSION["errMsg"] = "Invalid currency selected.";
            return false;
        }

        $paymentModel = new Payment();
        $isPaymentRecorded = $paymentModel->createPayment(
            $bookingID, 
            $amount, 
            $currency, 
            $exchangeRate
        );

        if ($isPaymentRecorded) {
            $isBookingUpdated = $bookingModel->updateStatus($bookingID, 'Confirmed');

            if ($isBookingUpdated) {
                $_SESSION["successMsg"] = "Payment processed successfully in $currency.";
                return true;
            } else {
                $_SESSION["errMsg"] = "Payment recorded but status update failed.";
                return false;
            }
        }

        $_SESSION["errMsg"] = "Database error during payment processing.";
        return false;
    }

    public function getMyPaymentHistory()
    {
        if (!isset($_SESSION["UserID"])) return [];

        $paymentModel = new Payment();
        $allPayments = $paymentModel->getAllPayments();

        $bookingModel = new Booking();
        $allBookings = $bookingModel->getAllBookings();
        
        $userBookingIDs = [];
        foreach ($allBookings as $booking) {
            if ($booking['TravelerID'] == $_SESSION["UserID"]) {
                $userBookingIDs[] = $booking['BookingID'];
            }
        }

        $myPayments = [];
        foreach ($allPayments as $payment) {
            if (in_array($payment['BookingId'], $userBookingIDs)) {
                $myPayments[] = $payment;
            }
        }

        return $myPayments;
    }
}