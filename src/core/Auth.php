<?php
namespace core;

use Exception;
use PDO;
use PDOException;

final class Auth {
    private static ?PDO $conn = null;

    /**
     * @throws Exception
     */
    private static function getConnection(): PDO
    {
        if (self::$conn === null) {
            $database = new Database();
            self::$conn = $database->getConnection();
        }
        return self::$conn;
    }

    /**
     * @throws Exception
     */
    public static function login($username, $password): bool
    {
        try {
            $query = "SELECT id, username, password_hash FROM " . env('AUTH_TABLE', 'users') . " WHERE username = :username LIMIT 1";
            $stmt = self::getConnection()->prepare($query);
        
            $username = Security::sanitizeInput($username);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (Security::verifyPassword($password, $row['password_hash'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $row['id'];
                    $_SESSION['admin_username'] = $row['username'];
                    return true;
                }
            }
            return false;
        } catch (PDOException|Exception $exception) {
            error_log("Login error: " . $exception->getMessage());
            return false;
        }
    }

    public static function logout(): void
    {
        session_destroy();
        session_start();
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    private function __clone() {}
}