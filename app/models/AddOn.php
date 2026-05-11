<?php
require_once __DIR__ . '/../models/DB.php';

class AddOn
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    /* CREATE ADDON */
    public function createAddOn($name, $price)
    {
        $sql = "INSERT INTO addon (Name, Price) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sd", $name, $price);

        return $stmt->execute();
    }

    /* GET ONE ADDON */
    public function getAddOnById($id)
    {
        $sql = "SELECT * FROM addon WHERE AddOnID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL ADDONS */
    public function getAllAddOns()
    {
        $sql = "SELECT * FROM addon ORDER BY AddOnID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE ADDON */
    public function updateAddOn($id, $name, $price)
    {
        $sql = "UPDATE addon
                SET Name = ?, Price = ?
                WHERE AddOnID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sdi", $name, $price, $id);

        return $stmt->execute();
    }

    /* DELETE ADDON */
    public function deleteAddOn($id)
    {
        $sql = "DELETE FROM addon WHERE AddOnID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}