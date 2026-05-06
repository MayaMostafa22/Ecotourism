<?php

class User
{
    private $UserID;
    private $UserName;
    private $UserRole;
    private $Email;
    private $Password;
 public function __construct($userName, $email, $password, $userRole)
    {
        $this->setUserName($userName);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->UserRole = $userRole;
    }
    public function getUserID()
    {
        return $this->UserID;
    }

    public function getUserName()
    {
        return $this->UserName;
    }

    public function setUserName($UserName)
    {
        $this->UserName = $UserName;
    }
    
    public function getUserRole()
    {
        return $this->UserRole;
    }

    public function setUserRole($UserRole)
    {
        $this->UserRole = $UserRole;
    }
    
    public function getEmail()
    {
        return $this->Email;
    }

    public function setEmail($Email)
    {
        $this->Email = $Email;
    }
    public function getPassword()
    {
        return $this->Password;
    }

    public function setPassword($Password)
    {
        $this->Password = $Password;
    }
}