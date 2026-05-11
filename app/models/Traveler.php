<?php
require_once __DIR__ . '/../models/DB.php';
require_once __DIR__.'/User.php'; 

class Traveler extends User
{
    
    /* CREATE TRAVELER */
    public function createTraveler($travelerID, $preferredCurrency)
    {
        $sql = "INSERT INTO traveler (TravelerID, PreferredCurrency) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $travelerID, $preferredCurrency);

        return $stmt->execute();
    }

    /* GET ONE TRAVELER */
    public function getTravelerById($id)
    {
        $sql = "SELECT * FROM traveler WHERE TravelerID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL TRAVELERS */
    public function getAllTravelers()
    {
        $sql = "SELECT * FROM traveler ORDER BY TravelerID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE TRAVELER */
    public function updateTraveler($id, $preferredCurrency)
    {
        $sql = "UPDATE traveler
                SET PreferredCurrency = ?
                WHERE TravelerID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $preferredCurrency, $id);

        return $stmt->execute();
    }

    /* DELETE TRAVELER */
    public function deleteTraveler($id)
    {
        $sql = "DELETE FROM traveler WHERE TravelerID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}