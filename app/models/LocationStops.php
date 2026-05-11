<?php
require_once __DIR__ . '/../models/DB.php';
class LocationStops
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    /* CREATE LOCATION STOP */
    public function createLocationStop($locationID, $expID, $distanceToNext, $transportMode)
    {
        $sql = "INSERT INTO locationstops (LocationID, ExpID, DistanceToNext, TransportMode) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iids", $locationID, $expID, $distanceToNext, $transportMode);

        return $stmt->execute();
    }

    /* GET ONE LOCATION STOP */
    public function getLocationStopById($locationID, $expID)
    {
        $sql = "SELECT * FROM locationstops WHERE LocationID = ? AND ExpID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("ii", $locationID, $expID);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
    /* GET STOPS BY EXPERIENCE ID */
    public function getStopsByExperience($expID)
    {
        $sql = "SELECT 
                    ls.*, 
                    l.LocationName 
                FROM locationstops ls
                JOIN location l ON ls.LocationID = l.LocationID
                WHERE ls.ExpID = ?
                ORDER BY ls.LocationID ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $expID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* GET ALL LOCATION STOPS */
    public function getAllLocationStops()
    {
        $sql = "SELECT * FROM locationstops ORDER BY LocationID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE LOCATION STOP */
    public function updateLocationStop($locationID, $expID, $distanceToNext, $transportMode)
    {
        $sql = "UPDATE locationstops
                SET DistanceToNext = ?, TransportMode = ?
                WHERE LocationID = ? AND ExpID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("dsii", $distanceToNext, $transportMode, $locationID, $expID);

        return $stmt->execute();
    }

    /* DELETE LOCATION STOP */
    public function deleteLocationStop($locationID, $expID)
    {
        $sql = "DELETE FROM locationstops WHERE LocationID = ? AND ExpID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $locationID, $expID);
        return $stmt->execute();
    }
}