<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class BriefingDocument
{
    private $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE BRIEFING DOCUMENT */
    public function createBriefingDocument($expID, $url)
    {
        $sql = "INSERT INTO briefingdocument (ExpID, URL) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $expID, $url);

        return $stmt->execute();
    }

    /* GET ONE BRIEFING DOCUMENT */
    public function getBriefingDocumentById($id)
    {
        $sql = "SELECT * FROM briefingdocument WHERE DocID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL BRIEFING DOCUMENTS */
    public function getAllBriefingDocuments()
    {
        $sql = "SELECT * FROM briefingdocument ORDER BY DocID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE BRIEFING DOCUMENT */
    public function updateBriefingDocument($id, $expID, $url)
    {
        $sql = "UPDATE briefingdocument
                SET ExpID = ?, URL = ?
                WHERE DocID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isi", $expID, $url, $id);

        return $stmt->execute();
    }

    /* DELETE BRIEFING DOCUMENT */
    public function deleteBriefingDocument($id)
    {
        $sql = "DELETE FROM briefingdocument WHERE DocID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}