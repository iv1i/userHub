<?php
namespace core;

use core\exceptions\DatabaseException;
use Exception;
use PDO;
use PDOException;

final class Database
{
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    public ?PDO $conn;

    public function __construct()
    {
        
        $this->host = env('DB_HOST');
        $this->db_name = env('DB_NAME');
        $this->username = env('DB_USER');
        $this->password = env('DB_PASS');
    }

    /**
     * Get database connection
     * @return PDO|null
     * @throws Exception
     */
    public function getConnection(): ?PDO
    {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8";
            $this->conn = new PDO($dsn, $this->username, $this->password);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } catch (PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            throw new DatabaseException("Database connection failed. Please try again later.", $exception);
        }

        return $this->conn;
    }
}

