<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private string $host = '127.0.0.1';
    private string $db_name = 'aplis_test';
    private string $username = 'root';
    private string $password = '';
    private ?PDO $conn = null;

    public function getConnection(): ?PDO
    {
        $this->conn = null;

        try {
            // First connect without database to ensure it exists
            $pdo = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);
            
            // Reconnect to the database
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Automatically create table if not exists
            $this->createTablesIfNotExist();

        } catch (PDOException $exception) {
            // Only output generic message to avoid leaking credentials
            http_response_code(500);
            echo json_encode(["error" => "Connection Error: " . $exception->getMessage()]);
            exit;
        }

        return $this->conn;
    }

    private function createTablesIfNotExist(): void
    {
        if ($this->conn) {
            $query = "
                CREATE TABLE IF NOT EXISTS medicos (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nome VARCHAR(255) NOT NULL,
                    CRM VARCHAR(50) NOT NULL,
                    UFCRM VARCHAR(2) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";
            $this->conn->exec($query);
        }
    }
}
