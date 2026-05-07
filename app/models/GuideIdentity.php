<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class GuideIdentity
{
    protected $db;

    public function __construct()
    {

        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE IDENTITY RECORD */
    public function createIdentity($status = 'Active')
    {

        $sql = "INSERT INTO guideidentity (Status) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        

        $stmt->bind_param("s", $status);

        if ($stmt->execute()) {

            return $this->db->insert_id;
        }
        return false;
    }

    /* GET IDENTITY BY ID */
    public function getIdentityById($id)
    {
        $sql = "SELECT * FROM guideidentity WHERE IdentityID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /* UPDATE STATUS */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE guideidentity SET Status = ? WHERE IdentityID = ?";
        $stmt = $this->db->prepare($sql);
        

        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    /* DELETE IDENTITY */
    public function deleteIdentity($id)
    {
        $sql = "DELETE FROM guideidentity WHERE IdentityID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}