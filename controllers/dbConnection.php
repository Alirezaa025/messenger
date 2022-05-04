<?php


class MySqlDatabaseConnection
{
    private static $instance = null;
    private PDO $conn;


    private function __construct()
    {
        $this->conn = new PDO("mysql:host=localhost;dbname=messenger", 'root', '');
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new MySqlDatabaseConnection();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }

    public function closeConnection()
    {
        $this->conn = null;
    }
}
