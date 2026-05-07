<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class Location
{
    private $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE LOCATION */
    public function createLocation($coordinates, $locationName, $isProtectedLand, $isIndigenousLand, $consentID)
    {
        $sql = "INSERT INTO location (coordinates, LocationName, IsProtectedLand, IsIndigenousLand, ConsentID) VALUES (ST_GeomFromText(?), ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssiii", $coordinates, $locationName, $isProtectedLand, $isIndigenousLand, $consentID);

        return $stmt->execute();
    }

    /* GET ONE LOCATION */
    public function getLocationById($id)
    {
        $sql = "SELECT LocationID, ST_AsText(coordinates) as coordinates, LocationName, IsProtectedLand, IsIndigenousLand, ConsentID FROM location WHERE LocationID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL LOCATIONS */
    public function getAllLocations()
    {
        $sql = "SELECT LocationID, ST_AsText(coordinates) as coordinates, LocationName, IsProtectedLand, IsIndigenousLand, ConsentID FROM location ORDER BY LocationID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE LOCATION */
    public function updateLocation($id, $coordinates, $locationName, $isProtectedLand, $isIndigenousLand, $consentID)
    {
        $sql = "UPDATE location
                SET coordinates = ST_GeomFromText(?), LocationName = ?, IsProtectedLand = ?, IsIndigenousLand = ?, ConsentID = ?
                WHERE LocationID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssiii", $coordinates, $locationName, $isProtectedLand, $isIndigenousLand, $consentID, $id);

        return $stmt->execute();
    }

    /* DELETE LOCATION */
    public function deleteLocation($id)
    {
        $sql = "DELETE FROM location WHERE LocationID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}