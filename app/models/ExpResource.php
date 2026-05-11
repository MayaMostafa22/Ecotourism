<?php
require_once __DIR__ . '/../models/D.php';

class ExpResource
{
    private $db;

    public function __construct()
    {
        // Use the Singleton
        $this->db = DB::getInstance()->getConnection();
    }

    /* CREATE:*/
    public function createExpResource($expID, $resourceID, $quantityPerPerson)
    {
        $sql = "INSERT INTO exp_resource (ExpID, ResourceID, QuantityPerPerson) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("iii", $expID, $resourceID, $quantityPerPerson);

        return $stmt->execute();
    }

    /* READ  */
    /*
    public function getResourcesByExp($expID)
    {
        $sql = "SELECT * FROM exp_resource WHERE ExpID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $expID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }*/

    /* READ (PRO):*/
    public function getFullResourceDetailsByExp($expID)
    {
        $sql = "SELECT 
                    er.QuantityPerPerson, 
                    r.ResourceName, 
                    r.TotallNum
                FROM exp_resource er
                JOIN resource r ON er.ResourceID = r.ResourceID
                WHERE er.ExpID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $expID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE: */
    public function updateQuantity($expID, $resourceID, $newQuantity)
    {
        $sql = "UPDATE exp_resource SET QuantityPerPerson = ? 
                WHERE ExpID = ? AND ResourceID = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iii", $newQuantity, $expID, $resourceID);

        return $stmt->execute();
    }

    /* DELETE: */
    public function deleteExpResource($expID, $resourceID)
    {

        $sql = "DELETE FROM exp_resource WHERE ExpID = ? AND ResourceID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $expID, $resourceID);

        return $stmt->execute();
    }
}