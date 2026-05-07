<?php
require_once '../../Models/CommunityConsent.php';

class CommunityConsentController
{
    public function addConsent($status, $contractURL)
    {
        $consentModel = new CommunityConsent();
        return $consentModel->createConsent($status, $contractURL);
    }

    public function getConsentDetails($id)
    {
        $consentModel = new CommunityConsent();
        return $consentModel->getConsentById($id);
    }

    public function listAllConsents()
    {
        $consentModel = new CommunityConsent();
        return $consentModel->getAllConsents();
    }

    public function editConsent($id, $status, $contractURL)
    {
        $consentModel = new CommunityConsent();
        return $consentModel->updateConsent($id, $status, $contractURL);
    }

    public function removeConsent($id)
    {
        $consentModel = new CommunityConsent();
        return $consentModel->deleteConsent($id);
    }
}