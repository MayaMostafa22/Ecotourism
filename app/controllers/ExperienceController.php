<?php
require_once '../../Models/Experience.php';

class ExperienceController
{
    public function addExperience($name, $desc, $ecoRating, $status, $price, $locations, $start, $end)
    {
        $expModel = new Experience();
        return $expModel->createExperience($name, $desc, $ecoRating, $status, $price, $locations, $start, $end);
    }

    public function editExperience($id, $name, $desc, $ecoRating, $status, $price, $locations, $start, $end)
    {
        $expModel = new Experience();
        return $expModel->updateExperience($id, $name, $desc, $ecoRating, $status, $price, $locations, $start, $end);
    }

    public function removeExperience($id)
    {
        $expModel = new Experience();
        return $expModel->deleteExperience($id);
    }

    public function getExperience($id)
    {
        $expModel = new Experience();
        return $expModel->getExperienceById($id);
    }

    public function showAllExperiences()
    {
        $expModel = new Experience();
        return $expModel->getAllExperiences();
    }

    public function showActiveExperiences()
    {
        $expModel = new Experience();
        return $expModel->getActiveExperiences();
    }
}
