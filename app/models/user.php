<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';
class User
{
    private $db;

    public function __construct()
    {

        // Use the Singleton
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE USER */
    public function createUser($name, $email, $password, $role)
    {
        $sql = "INSERT INTO users (UserName, Email, Password, UserRole) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        return $stmt->execute();
    }

    /* GET ONE USER */
    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE UserID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL USERS */
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users ORDER BY UserID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* LOGIN SUPPORT */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE Email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /* UPDATE USER */
    public function updateUser($id, $name, $email, $role)
    {
        $sql = "UPDATE users 
            SET UserName = ?, Email = ?, UserRole = ?
            WHERE UserID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $role, $id);

        return $stmt->execute();
    }

    /* DELETE USER */
    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE UserID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
