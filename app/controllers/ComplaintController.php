<?php
require_once '../../Models/Complaint.php';

class ComplaintController
{
    public function createNewComplaint($bookingId, $travelerId, $guideId, $adminId, $photosUrl, $description, $status, $resolution, $createdDate, $resolvedDate)
    {
        $complaintModel = new Complaint();
        return $complaintModel->fileComplaint($bookingId, $travelerId, $guideId, $adminId, $photosUrl, $description, $status, $resolution, $createdDate, $resolvedDate);
    }

    public function takeAction($complaintId, $adminId, $status, $resolution, $resolvedDate)
    {
        $complaintModel = new Complaint();
        return $complaintModel->resolveComplaint($complaintId, $adminId, $status, $resolution, $resolvedDate);
    }

    public function listAll()
    {
        $complaintModel = new Complaint();
        return $complaintModel->getAllComplaints();
    }

    public function getDetails($id)
    {
        $complaintModel = new Complaint();
        return $complaintModel->getComplaintById($id);
    }

    public function getTravelerHistory($travelerId)
    {
        $complaintModel = new Complaint();
        return $complaintModel->getComplaintsByTraveler($travelerId);
    }

    public function removeComplaint($id)
    {
        $complaintModel = new Complaint();
        return $complaintModel->deleteComplaint($id);
    }
}