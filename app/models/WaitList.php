<?php
require_once __DIR__ . '/../models/DB.php';
class Waitlist
{
    protected $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    /* ADD TO WAITLIST */
    public function addToWaitlist($expID, $travelerID, $queuePosition)
    {
        $sql = "INSERT INTO waitlist (ExpID, TravelerID, QueuePosition, JoinedDate) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bind_param("iii", $expID, $travelerID, $queuePosition);

        return $stmt->execute();
    }

    /* GET ONE WAITLIST ENTRY BY ID */
    public function getWaitlistById($id)
    {
        $sql = "SELECT * FROM waitlist WHERE WaitListID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* GET ALL ENTRIES FOR A SPECIFIC EXPERIENCE */
    public function getWaitlistByExperience($expID)
    {
        $sql = "SELECT * FROM waitlist WHERE ExpID = ? ORDER BY QueuePosition ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $expID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE QUEUE POSITION */
    public function updatePosition($waitListID, $newPosition)
    {
        $sql = "UPDATE waitlist SET QueuePosition = ? WHERE WaitListID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $newPosition, $waitListID);

        return $stmt->execute();
    }

    /* DELETE FROM WAITLIST (REMOVE) */
    public function removeFromWaitlist($id)
    {
        $sql = "DELETE FROM waitlist WHERE WaitListID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}