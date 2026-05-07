<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class CarbonOffsetProject
{
    private $db;

    public function __construct()
    {
        // Use the Singleton
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE PROJECT */
    public function createProject($projectName, $description, $costPerUnit)
    {
        // ProjectID is Auto Increment
        $sql = "INSERT INTO carbon_offset_projects (ProjectName, Description, CostOfCarbonUnit) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssd", $projectName, $description, $costPerUnit);

        return $stmt->execute();
    }

    /* GET ONE PROJECT */
    public function getProjectById($id)
    {
        $sql = "SELECT * FROM carbon_offset_projects WHERE ProjectID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL PROJECTS */
    public function getAllProjects()
    {
        $sql = "SELECT * FROM carbon_offset_projects ORDER BY ProjectID DESC";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* FIND BY NAME (Support Method like findByEmail) */
    public function getProjectByName($name)
    {
        $sql = "SELECT * FROM carbon_offset_projects WHERE ProjectName = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $name);

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /* UPDATE PROJECT */
    public function updateProject($id, $projectName, $description, $costPerUnit)
    {
        $sql = "UPDATE carbon_offset_projects 
                SET ProjectName = ?, Description = ?, CostOfCarbonUnit = ?
                WHERE ProjectID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssdi", $projectName, $description, $costPerUnit, $id);

        return $stmt->execute();
    }

    /* DELETE PROJECT */
    public function deleteProject($id)
    {
        $sql = "DELETE FROM carbon_offset_projects WHERE ProjectID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}