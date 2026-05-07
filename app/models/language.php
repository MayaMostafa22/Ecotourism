<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class Language
{
    private $db;

    public function __construct()
    {

        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE LANGUAGE */
    public function createLanguage($langName)
    {

        $sql = "INSERT INTO languages (LangName) VALUES (?)";
        $stmt = $this->db->prepare($sql);


        $stmt->bind_param("s", $langName);

        return $stmt->execute();
    }

    /* GET ONE LANGUAGE */
    public function getLanguageById($id)
    {
        $sql = "SELECT * FROM languages WHERE LangID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL LANGUAGES */
    public function getAllLanguages()
    {
        $sql = "SELECT * FROM languages ORDER BY LangID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE LANGUAGE */
    public function updateLanguage($id, $langName)
    {
        $sql = "UPDATE languages SET LangName = ? WHERE LangID = ?";
        $stmt = $this->db->prepare($sql);


        $stmt->bind_param("si", $langName, $id);

        return $stmt->execute();
    }

    /* DELETE LANGUAGE */
    public function deleteLanguage($id)
    {
        $sql = "DELETE FROM languages WHERE LangID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}