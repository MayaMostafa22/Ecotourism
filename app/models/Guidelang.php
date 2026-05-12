<?php
require_once __DIR__ . '/../models/DB.php';
class GuideLanguage
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }


    public function addLanguageToGuide($guideId, $langId, $proficiencyLevel, $verificationStatus = 0)
    {

        $sql = "INSERT INTO guidelang (GuideID, LangID, proficiencyLevel, VerificationStatus) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
    
        $stmt->bind_param("iisi", $guideId, $langId, $proficiencyLevel, $verificationStatus);

        return $stmt->execute();
    }


    public function getLanguagesByGuide($guideId)
    {
        $sql = "SELECT gl.*, l.LangName 
                FROM guidelang gl 
                JOIN languages l ON gl.LangID = l.LangID 
                WHERE gl.GuideID = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $guideId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    public function updateVerificationStatus($guideId, $langId, $status)
    {
        $sql = "UPDATE guidelang SET VerificationStatus = ? WHERE GuideID = ? AND LangID = ?";
        $stmt = $this->db->prepare($sql);
        

        $stmt->bind_param("iii", $status, $guideId, $langId);

        return $stmt->execute();
    }
}