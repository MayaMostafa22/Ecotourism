<?php
require_once '../../models/Booking.php';

class BookingController
{
    public function addBooking($travelerID, $expID, $bookingDate, $status, $isWaitListAccepted, $groupSize, $discountID, $discountAmount, $basePrice, $finalPrice)
    {
        $bookingModel = new Booking();
        return $bookingModel->createBooking($travelerID, $expID, $bookingDate, $status, $isWaitListAccepted, $groupSize, $discountID, $discountAmount, $basePrice, $finalPrice);
    }

    public function getDetails($id)
    {
        $bookingModel = new Booking();
        return $bookingModel->getBookingById($id);
    }

    public function listAll()
    {
        $bookingModel = new Booking();
        return $bookingModel->getAllBookings();
    }

    public function changeStatus($bookingID, $status)
    {
        $bookingModel = new Booking();
        return $bookingModel->updateStatus($bookingID, $status);
    }

    public function removeBooking($id)
    {
        $bookingModel = new Booking();
        return $bookingModel->deleteBooking($id);
    }
}