<?php

namespace App\Controllers;

use App\Models\Medico;

class MedicoController
{
    private Medico $medicoModel;

    public function __construct()
    {
        $this->medicoModel = new Medico();
    }

    public function index(): void
    {
        header("Content-Type: application/json; charset=UTF-8");
        
        $medicos = $this->medicoModel->readAll();
        
        // Return exactly as the challenge asks
        echo json_encode($medicos);
    }

    public function store(): void
    {
        header("Content-Type: application/json; charset=UTF-8");
        
        // Get raw posted data
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->nome) && !empty($data->CRM) && !empty($data->UFCRM)) {
            
            $this->medicoModel->nome = $data->nome;
            $this->medicoModel->CRM = $data->CRM;
            $this->medicoModel->UFCRM = $data->UFCRM;

            if ($this->medicoModel->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Médico criado com sucesso"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Não foi possível criar o médico"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos"]);
        }
    }

    public function update($id): void
    {
        header("Content-Type: application/json; charset=UTF-8");
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->nome) && !empty($data->CRM) && !empty($data->UFCRM) && $id) {
            $this->medicoModel->id = $id;
            $this->medicoModel->nome = $data->nome;
            $this->medicoModel->CRM = $data->CRM;
            $this->medicoModel->UFCRM = $data->UFCRM;

            if ($this->medicoModel->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Médico atualizado com sucesso"]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Médico não encontrado ou não atualizado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos"]);
        }
    }

    public function delete($id): void
    {
        header("Content-Type: application/json; charset=UTF-8");
        
        if ($id) {
            $this->medicoModel->id = $id;

            if ($this->medicoModel->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Médico deletado com sucesso"]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Médico não encontrado ou já deletado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID não fornecido"]);
        }
    }
}
