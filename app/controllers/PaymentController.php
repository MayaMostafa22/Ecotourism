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

    /**
     * 1. Process payment matching the SQL schema
     */
    public function processPayment($bookingID, $amount, $currency, $exchangeRate)
    {
        // Security check
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

        // Logic: Check if the amount (after considering exchange rate if needed) is correct
        // For simplicity, we assume 'amount' passed is what will be stored.
        if ($booking['Status'] === 'Confirmed') {
            $_SESSION["errMsg"] = "This booking is already paid.";
            return false;
        }

        // Validate Currency enum values matching your SQL: '$Dollar' or 'EGP'
        $allowedCurrencies = ['$Dollar', 'EGP'];
        if (!in_array($currency, $allowedCurrencies)) {
            $_SESSION["errMsg"] = "Invalid currency selected.";
            return false;
        }

        $paymentModel = new Payment();
        // Matching your table columns: BookingId, Amount, Currency, ExchangeRate
        // Note: PaymentDate is handled by current_timestamp() in MySQL
        $isPaymentRecorded = $paymentModel->createPayment(
            $bookingID, 
            $amount, 
            $currency, 
            $exchangeRate
        );

        if ($isPaymentRecorded) {
            // Update booking status to Confirmed
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

    /**
     * 2. Get payment history for current user
     */
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
            // Match column name 'BookingId' from your SQL
            if (in_array($payment['BookingId'], $userBookingIDs)) {
                $myPayments[] = $payment;
            }
        }

        return $myPayments;
    }
}