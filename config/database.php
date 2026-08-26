<?php

class Database
{
    private $host;
    private $dbname;
    private $username;
    private $password;

    public $conn;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->dbname = getenv('DB_NAME') ?: 'stajbilgisistem';
        $this->username = getenv('DB_USERNAME') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
    }

    public function connect()
    {
        $this->conn = null;

        try
        {
            $this->conn = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->dbname.";charset=utf8mb4",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        }
        catch(PDOException $e)
        {
            echo "Bağlantı hatası: ".$e->getMessage();
        }

        return $this->conn;
    }
}
?>