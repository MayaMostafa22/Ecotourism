<?php
require_once __DIR__ . '/../models/DB.php';
class TranslatedDoc
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    /* CREATE TRANSLATED DOC */
    public function createTranslatedDoc($docID, $langID)
    {
        $sql = "INSERT INTO translateddoc (DocID, LangID) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $docID, $langID);

        return $stmt->execute();
    }

    /* GET ONE TRANSLATED DOC */
   /* public function getTranslatedDocById($docID, $langID)
    {
        $sql = "SELECT * FROM translateddoc WHERE DocID = ? AND LangID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("ii", $docID, $langID);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }*/
    

    /* GET ALL TRANSLATED DOCS */
    public function getAllTranslatedDocs()
    {
        $sql = "SELECT * FROM translateddoc ORDER BY DocID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* DELETE TRANSLATED DOC */
    public function deleteTranslatedDoc($docID, $langID)
    {
        $sql = "DELETE FROM translateddoc WHERE DocID = ? AND LangID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $docID, $langID);
        return $stmt->execute();
    }
}