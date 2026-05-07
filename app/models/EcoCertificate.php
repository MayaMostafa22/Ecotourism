<?php
require_once __DIR__ . '/../controllers/DBcontroller.php';

class EcoCertificate
{
    private $db;

    public function __construct()
    {
        // Use the Singleton
        $this->db = DBcontroller::getInstance()->getConnection();
    }

    /* CREATE CERTIFICATE:*/
    public function createCertificate($url, $name, $guideID)
    {
        // CertificateID: Auto Increment
        $sql = "INSERT INTO ecocertificate (CertificateURL, CertificateName, GuideID) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $url, $name, $guideID);

        return $stmt->execute();
    }

    /* READ:*/
    /*
    public function getCertificateById($id)
    {
        $sql = "SELECT * FROM ecocertificate WHERE CertificateID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
*/
    /* READ:  */
    public function getCertificatesByGuide($guideID)
    {
        $sql = "SELECT * FROM ecocertificate WHERE GuideID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $guideID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* UPDATE:  */
    public function updateCertificate($id, $url, $name)
    {
        $sql = "UPDATE ecocertificate 
                SET CertificateURL = ?, CertificateName = ? 
                WHERE CertificateID = ?";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $url, $name, $id);
        return $stmt->execute();
    }

    /* DELETE: */
    public function deleteCertificate($id)
    {
        $sql = "DELETE FROM ecocertificate WHERE CertificateID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}