<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class GuideSchedule
{
    private $db;

    public function __construct()
    {

        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE SCHEDULE */
    public function createSchedule($expId, $guideId)
    {

        $sql = "INSERT INTO guideschedule (ExpID, GuideID) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        

        $stmt->bind_param("ii", $expId, $guideId);

        return $stmt->execute();
    }

    /* GET SCHEDULE BY ID */
    public function getScheduleById($id)
    {
        $sql = "SELECT * FROM guideschedule WHERE ScheduleID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL SCHEDULES FOR A SPECIFIC GUIDE */
    public function getSchedulesByGuide($guideId)
    {

        $sql = "SELECT gs.*, e.Title as ExperienceTitle 
                FROM guideschedule gs 
                JOIN experiences e ON gs.ExpID = e.ExperienceID 
                WHERE gs.GuideID = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $guideId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE SCHEDULE */
    public function updateSchedule($id, $expId, $guideId)
    {
        $sql = "UPDATE guideschedule SET ExpID = ?, GuideID = ? WHERE ScheduleID = ?";
        $stmt = $this->db->prepare($sql);

 
        $stmt->bind_param("iii", $expId, $guideId, $id);

        return $stmt->execute();
    }

    /* DELETE SCHEDULE */
    public function deleteSchedule($id)
    {
        $sql = "DELETE FROM guideschedule WHERE ScheduleID = ?";
        $stmt = $this->db->prepare($sql);

        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}