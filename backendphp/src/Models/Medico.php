<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Medico
{
    private ?PDO $conn;
    private string $table_name = "medicos";

    public ?int $id = null;
    public ?string $nome = null;
    public ?string $CRM = null;
    public ?string $UFCRM = null;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function readAll(): array
    {
        $query = "SELECT id, nome, CRM, UFCRM FROM " . $this->table_name . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function create(): bool
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nome=:nome, CRM=:CRM, UFCRM=:UFCRM";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->nome = htmlspecialchars(strip_tags((string)$this->nome));
        $this->CRM = htmlspecialchars(strip_tags((string)$this->CRM));
        $this->UFCRM = htmlspecialchars(strip_tags((string)$this->UFCRM));

        // bind
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":CRM", $this->CRM);
        $stmt->bindParam(":UFCRM", $this->UFCRM);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function update(): bool
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome=:nome, CRM=:CRM, UFCRM=:UFCRM
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags((string)$this->nome));
        $this->CRM = htmlspecialchars(strip_tags((string)$this->CRM));
        $this->UFCRM = htmlspecialchars(strip_tags((string)$this->UFCRM));
        $this->id = htmlspecialchars(strip_tags((string)$this->id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":CRM", $this->CRM);
        $stmt->bindParam(":UFCRM", $this->UFCRM);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public function delete(): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags((string)$this->id));
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }
}
