<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class LocationStops
{
    private $db;

    public function __construct()
    {
        $this->db = DBcontroller::getInstance()->getConnection();
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