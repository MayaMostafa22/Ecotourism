<?php 
require_once '../../models/User.php';

class AuthController
{
    public function login($email, $password)
    {
        $userModel = new User();
        $userData = $userModel->findByEmail($email);
        
        if($userData)
        {
            if(password_verify($password, $userData['Password']))
            {
                if(session_status() === PHP_SESSION_NONE) { session_start(); }
                $_SESSION["UserID"] = $userData["UserID"];
                $_SESSION["UserName"] = $userData["UserName"];
                $_SESSION["UserRole"] = $userData["UserRole"];
                return true;
            }
        }
        
        if(session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION["errMsg"] = "You have entered wrong email or password";
        return false;
    }

  public function register($name, $email, $password, $role)
{
    $userModel = new User();
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $isCreated = $userModel->createUser($name, $email, $hashedPassword, $role);
    
    if($isCreated)
    {
        $userData = $userModel->findByEmail($email);
        $userID = $userData["UserID"];

        
        if($role === "traveler") {
            $sql = "INSERT INTO traveler (TravelerID, PreferredCurrency) VALUES (?, 'USD')";
            $stmt = $userModel->db->prepare($sql);
            $stmt->bind_param("i", $userID);
            $stmt->execute();
        } 
        elseif($role === "guide") {
        $sql1 = "INSERT INTO guideidentity (Status, VerificationDate) VALUES ('Active', NOW())";
        $userModel->db->query($sql1);
        $identityID = $userModel->db->insert_id;

        $sql2 = "INSERT INTO guide (GuideID, IdentityID) VALUES (?, ?)";
        $stmt = $userModel->db->prepare($sql2);
        $stmt->bind_param("ii", $userID, $identityID);
        $stmt->execute();
        }

        if(session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION["UserID"] = $userData["UserID"];
        $_SESSION["UserName"] = $userData["UserName"];
        $_SESSION["UserRole"] = $userData["UserRole"];
        return true;
    }
    else
    {
        if(session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION["errMsg"] = "Something went wrong... try again later";
        return false;
    }
}
}
?>