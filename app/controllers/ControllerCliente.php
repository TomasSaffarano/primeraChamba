<?php

require_once 'app/views/ViewCliente.php';
require_once 'app/models/ModelCliente.php';
require_once 'app/controllers/errorController.php';

class ControllerCliente {
    private $view;
    private $model;
    private $error;

    public function __construct() {
        $this->view = new ViewCliente();
        $this->model = new ModelCliente();
        $this->error = new ErrorControler();
    }

    public function getAllClientes() {
        $clients = $this->model->getClients();
        $this->view->showClients($clients);
    }

    public function addClient()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientData = $this->getValidatedClientData();

            // Si la validación falla, manejar el error
            if (!$clientData) {
                $error = "Error: completar todos los campos obligatorios";
                $redir = "clientes";
                $this->error->showError($error, $redir);
            } else {
                $result = $this->model->insertClient( $clientData['name'],  $clientData['dni'],  $clientData['telefono']);
                if($result)
                header('Location: ' . BASE_URL . 'realizado');
            else
                $this->error->showError('Error en la base de datos', 'clientes');
            return;
            }
        } else {
            $this->view->addClient();
        }
    }

    private function getValidatedClientData()
    {
        // Verificar campos obligatorios
        if (
            !isset($_POST['name']) || empty($_POST['name']) ||
            !isset($_POST['dni']) || empty($_POST['dni']) ||
            !isset($_POST['cellphone']) || empty($_POST['cellphone'])
        ) {
            return false;
        }
        
        
        // Si todos los datos son válidos, devolver un array con los datos
        return [
            'name' => htmlspecialchars($_POST['name']),
            'dni' => htmlspecialchars($_POST['dni']),
            'telefono' => htmlspecialchars($_POST['cellphone']),
        ];
    }


}
