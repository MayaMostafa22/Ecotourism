<?php

class DB
{
    private static $instance = null;
    private $connection;

    private $dbHost = "localhost";
    private $dbUser = "root";
    private $password = "";
    // Write database name here
    private $dbname = "eco-tourism";


    // Private constructor → prevent direct creation
    private function __construct()
    {
        $this->connection = new mysqli(
            $this->dbHost,
            $this->dbUser,
            $this->password,
            $this->dbname
        );

        if ($this->connection->connect_error) {
            die("Database Connection Failed: " . $this->connection->connect_error);
        }

        // Important for Arabic or UTF8 text
        $this->connection->set_charset("utf8");
    }

    // Singleton static method → returns same instance always
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    // Getter for mysqli connection
    public function getConnection()
    {
        return $this->connection;
    }
  
}
