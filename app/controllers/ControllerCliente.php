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

    
    public function deleteClient($id)
    {
        if($this->model->checkIDExists($id)){
            $result = $this->model->eraseClient($id);
            if($result)
            header('Location: ' . BASE_URL . 'realizado');
        else
            $this->error->showError('Error en la base de datos', 'clientes');
        return;
        } else {
            $error = "No existe el cliente con el id=$id";
            $redir = "clientes";
            $this->error->showError($error, $redir);
        }
    }

    public function updateClient($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $client = $this->model->getClient($id);
            if (!$client) {
                $error = "No existe el cliente con el id=$id";
                $redir = "clientes";
                $this->error->showError($error, $redir);
                return;
            }
            $this->view->showClientForm($client, true);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientData = $this->getValidatedClientData();
            if (!$clientData) {
                $error = "Error: completar todos los campos obligatorios";
                $redir = "clientes";
                $this->error->showError($error, $redir);
            } else {
                // Actualizo el producto
                $result = $this->model->updateClient($id, $clientData['name'],  $clientData['dni'],  $clientData['telefono']);
                if($result)
                header('Location: ' . BASE_URL . 'realizado');
            else
                $this->error->showError('Error en la base de datos', 'clientes');
            return;
            }
        }
    }
}
