<?php
require_once '../../models/Payment.php';

class PaymentController
{
    public function addPayment($bookingId, $amount, $currency, $exchangeRate)
    {
        $paymentModel = new Payment();
        return $paymentModel->createPayment($bookingId, $amount, $currency, $exchangeRate);
    }

    public function getDetails($transactionId)
    {
        $paymentModel = new Payment();
        return $paymentModel->getPaymentById($transactionId);
    }

    public function getDetailsByBooking($bookingId)
    {
        $paymentModel = new Payment();
        return $paymentModel->getPaymentByBookingId($bookingId);
    }

    public function listAll()
    {
        $paymentModel = new Payment();
        return $paymentModel->getAllPayments();
    }

    public function removePayment($transactionId)
    {
        $paymentModel = new Payment();
        return $paymentModel->deletePayment($transactionId);
    }
}