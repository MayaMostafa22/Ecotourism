<?php
require_once __DIR__ . '/../models/DB.php';
class SeasonalRule
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    public function createRule($expId, $reason, $actionType, $startDate, $endDate)
    {
        $sql = "INSERT INTO seasonalrule (ExpID, Reason, ActionType, StartDate, EndDate) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("issss", $expId, $reason, $actionType, $startDate, $endDate);
        
        return $stmt->execute();
    }

    public function getRuleById($ruleId)
    {
        $sql = "SELECT * FROM seasonalrule WHERE RuleID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $ruleId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    public function getRulesByExperience($expId)
    {
        $sql = "SELECT * FROM seasonalrule WHERE ExpID = ? ORDER BY StartDate ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $expId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateRule($ruleId, $reason, $actionType, $startDate, $endDate)
    {
        $sql = "UPDATE seasonalrule 
                SET Reason = ?, ActionType = ?, StartDate = ?, EndDate = ? 
                WHERE RuleID = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssi", $reason, $actionType, $startDate, $endDate, $ruleId);
        
        return $stmt->execute();
    }

    public function deleteRule($ruleId)
    {
        $sql = "DELETE FROM seasonalrule WHERE RuleID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $ruleId);
        
        return $stmt->execute();
    }
}