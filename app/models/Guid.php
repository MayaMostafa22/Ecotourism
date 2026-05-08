<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';
//require_once 'User.php';
require_once __DIR__ . '/User.php';

class Guid extends User
{

    /* CREATE GUIDE */
    public function createGuide(
        $identityId,
        $localCredScore,
        $yearsOfExperience,
        $isVerified,
        $baseCountry,
        $status,
        $yearsOfResidency,
        $cancellationRate,
        $visibilityScore
    ) {
        $sql = "INSERT INTO guides (IdentityID, localCredScore, yearsOfExperience, isVerified, baseCountry, status, yearsOfResidency, CancellationRate, VisibilityScore) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        // i: integer, s: string
        $stmt->bind_param(
            "iiiissiii",
            $identityId,
            $localCredScore,
            $yearsOfExperience,
            $isVerified,
            $baseCountry,
            $status,
            $yearsOfResidency,
            $cancellationRate,
            $visibilityScore
        );

        return $stmt->execute();
    }

    /* GET ONE GUIDE */
    public function getGuideById($id)
    {
        $sql = "SELECT * FROM guides WHERE GuideID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL GUIDES */
    public function getAllGuides()
    {
        $sql = "SELECT * FROM guides ORDER BY GuideID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE GUIDE */
    public function updateGuide(
        $id,
        $identityId,
        $localCredScore,
        $yearsOfExperience,
        $isVerified,
        $baseCountry,
        $status,
        $yearsOfResidency,
        $cancellationRate,
        $visibilityScore
    ) {
        $sql = "UPDATE guides 
            SET IdentityID = ?, localCredScore = ?, yearsOfExperience = ?, isVerified = ?, baseCountry = ?, status = ?, yearsOfResidency = ?, CancellationRate = ?, VisibilityScore = ?
            WHERE GuideID = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->bind_param(
            "iiiiissiii",
            $identityId,
            $localCredScore,
            $yearsOfExperience,
            $isVerified,
            $baseCountry,
            $status,
            $yearsOfResidency,
            $cancellationRate,
            $visibilityScore,
            $id
        );

        return $stmt->execute();
    }

    /* DELETE GUIDE */
    public function deleteGuide($id)
    {
        $sql = "DELETE FROM guides WHERE GuideID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
    /* 13. Update Local Cred Score */
    public function updateLocalCredScore($guideId, $score)
    {
        // بناءً على الصورة: العمود اسمه localCredScore
        $sql = "UPDATE guides SET localCredScore = ? WHERE GuideID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $score, $guideId);
        return $stmt->execute();
    }

    /* 21. Update Visibility Score */
    public function updateVisibilityScore($guideId, $score)
    {
        // بناءً على الصورة: العمود اسمه VisibilityScore
        $sql = "UPDATE guides SET VisibilityScore = ? WHERE GuideID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $score, $guideId); // استخدمنا i لأن النوع في الصورة int(11)
        return $stmt->execute();
    }

    // /* دالة لجلب بيانات المرشد كاملة */
// public function getGuideById($guideId)
// {
//     $sql = "SELECT * FROM guides WHERE GuideID = ?";
//     $stmt = $this->db->prepare($sql);
//     $stmt->bind_param("i", $guideId);
//     $stmt->execute();
//     return $stmt->get_result()->fetch_assoc();
// }
}