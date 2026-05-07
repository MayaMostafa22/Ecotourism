<?php
require_once '../../Models/SeasonalRule.php';

class SeasonalRuleController
{
    public function addRule($expId, $reason, $actionType, $startDate, $endDate)
    {
        $ruleModel = new SeasonalRule();
        return $ruleModel->createRule($expId, $reason, $actionType, $startDate, $endDate);
    }

    public function getRuleDetails($ruleId)
    {
        $ruleModel = new SeasonalRule();
        return $ruleModel->getRuleById($ruleId);
    }

    public function getExperienceRules($expId)
    {
        $ruleModel = new SeasonalRule();
        return $ruleModel->getRulesByExperience($expId);
    }

    public function editRule($ruleId, $reason, $actionType, $startDate, $endDate)
    {
        $ruleModel = new SeasonalRule();
        return $ruleModel->updateRule($ruleId, $reason, $actionType, $startDate, $endDate);
    }

    public function removeRule($ruleId)
    {
        $ruleModel = new SeasonalRule();
        return $ruleModel->deleteRule($ruleId);
    }
}