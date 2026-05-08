<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';
require_once __DIR__ . '/User.php';

class Admin extends User
{
    /* CREATE ADMIN */
     public function createAdmin($adminType, $userID)
    {
        $sql  = "INSERT INTO admin (AdminID, AdminType, CreatedAt) VALUES (?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $userID, $adminType);
        return $stmt->execute();
    }

    /* GET ONE ADMIN */
    public function getAdminById($id)
    {
        $sql = "SELECT * FROM admin WHERE AdminID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL ADMINS */
    public function getAllAdmins()
    {
        $sql = "SELECT * FROM admin ORDER BY AdminID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE ADMIN */
    public function updateAdmin($id, $adminType)
    {
        $sql = "UPDATE admin SET AdminType = ? WHERE AdminID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $adminType, $id);

        return $stmt->execute();
    }

   
}