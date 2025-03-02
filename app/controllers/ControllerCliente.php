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
        $this->error = new ErrorController();
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
                $existe = $this->model->obtenerClientePorDNI($clientData['dni']);
                if($existe){
                $error = "Error: el cliente ya existe";
                $redir = "clientes";
                $this->error->showError($error, $redir);
                return;
                }
                $result = $this->model->insertClient( $clientData['name'],  $clientData['dni'],  $clientData['telefono']);
                if($result)
                header('Location: ' . BASE_URL . 'clientes');
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
            header('Location: ' . BASE_URL . 'clientes');
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
            $clients = $this->model->getClients();
            // Mostrar el formulario de edición, pasando el cliente y todos los clientes
            $this->view->showClientForm($client, true, $clients);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar los datos del cliente
            $clientData = $this->getValidatedClientData();
            
            if (!$clientData) {
                $error = "Error: completar todos los campos obligatorios";
                $redir = "clientes";
                $this->error->showError($error, $redir);
            } else {
                // Actualizar el cliente en la base de datos
                $result = $this->model->updateClient($id, $clientData['name'], $clientData['dni'], $clientData['telefono']);
                
                // Redirigir si la actualización es exitosa
                if ($result) {
                    header('Location: ' . BASE_URL . 'clientes');
                } else {
                    $this->error->showError('Error en la base de datos', 'clientes');
                }
            }
        }
    }
    
    public function mostrarCliente($client) {
        $client = htmlspecialchars(trim($client));
        $clients = $this->model->getClienteNombre($client);
        if (!empty($clients)) {
            $this->view->showClients($clients);
        } else {
            $error = "No existe el cliente con el nombre=$client";
            $redir = "clientes";
            $this->error->showError($error, $redir);
        }
    }
}
