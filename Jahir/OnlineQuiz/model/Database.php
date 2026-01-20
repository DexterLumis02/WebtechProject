<?php
declare(strict_types=1);

class Database
{
    private static ?Database $instance = null;

    private mysqli $connection;

    private function __construct()
    {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->connection->connect_error) {
            die('Database connection failed');
        }
        $this->connection->set_charset(DB_CHARSET);
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    public function prepare(string $sql): mysqli_stmt
    {
        $stmt = $this->connection->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException('Database error');
        }
        return $stmt;
    }
}

