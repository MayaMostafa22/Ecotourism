<?php
require_once __DIR__ . '/../models/DB.php';

class FieldReports
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    /* CREATE FIELD REPORT */
    public function createFieldReport($mediaURL, $reportText, $guideID, $expID)
    {
        $sql = "INSERT INTO fieldreports (MediaURL, ReportText, GuideID, ExpID) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssii", $mediaURL, $reportText, $guideID, $expID);

        return $stmt->execute();
    }

    /* GET ONE FIELD REPORT */
    public function getFieldReportById($id)
    {
        $sql = "SELECT * FROM fieldreports WHERE ReportID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL FIELD REPORTS */
    public function getAllFieldReports()
    {
        $sql = "SELECT * FROM fieldreports ORDER BY ReportID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE FIELD REPORT */
    public function updateFieldReport($id, $mediaURL, $reportText, $guideID, $expID)
    {
        $sql = "UPDATE fieldreports
                SET MediaURL = ?, ReportText = ?, GuideID = ?, ExpID = ?
                WHERE ReportID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssiii", $mediaURL, $reportText, $guideID, $expID, $id);

        return $stmt->execute();
    }

    /* DELETE FIELD REPORT */
    public function deleteFieldReport($id)
    {
        $sql = "DELETE FROM fieldreports WHERE ReportID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}