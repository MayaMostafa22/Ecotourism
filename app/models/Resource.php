<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class Resource
{
   private $db;

    public function __construct()
    {
        // Use the Singleton
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE RESOURCE */
    public function createResource($resourceName, $totallNum)
    {
        $sql = "INSERT INTO resource (ResourceName, TotallNum) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
         $stmt->bind_param("si", $resourceName, $totallNum);

        return $stmt->execute();
    }

    /* GET ONE RESOURCE */
    public function getResourceById($id)
    {
        $sql = "SELECT * FROM resource WHERE ResourceID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL RESOURCES */
    public function getAllResources()
    {
        $sql = "SELECT * FROM resource ORDER BY ResourceID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* FIND BY NAME (Support Method like findByEmail) */
    public function getResourceByName($name)
    {
        $sql = "SELECT * FROM resource WHERE ResourceName = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $name);

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /* UPDATE RESOURCE */
    public function updateResource($id, $resourceName, $totallNum)
    {
        $sql = "UPDATE resource 
                SET ResourceName = ?, TotallNum = ?
                WHERE ResourceID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sii", $resourceName, $totallNum, $id);

        return $stmt->execute();
    }

    /* DELETE RESOURCE */
    public function deleteResource($id)
    {
        $sql = "DELETE FROM resource WHERE ResourceID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}