<?php
require_once '../../models/ExperienceTier.php';

class ExperienceTierController
{
    public function addTier($expId, $tierName, $maxCapacity)
    {
        $tierModel = new ExperienceTier();
        return $tierModel->createTier($expId, $tierName, $maxCapacity);
    }

    public function getDetails($tierId)
    {
        $tierModel = new ExperienceTier();
        return $tierModel->getTierById($tierId);
    }

    public function getExperienceTiers($expId)
    {
        $tierModel = new ExperienceTier();
        return $tierModel->getTiersByExperience($expId);
    }

    public function editTier($tierId, $tierName, $maxCapacity)
    {
        $tierModel = new ExperienceTier();
        return $tierModel->updateTier($tierId, $tierName, $maxCapacity);
    }

    public function removeTier($tierId)
    {
        $tierModel = new ExperienceTier();
        return $tierModel->deleteTier($tierId);
    }
}