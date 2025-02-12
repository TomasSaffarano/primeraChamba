<?php

require_once 'app/views/ViewMoto.php';
require_once 'app/models/ModelMoto.php';
require_once 'app/models/ModelCliente.php';

class ControllerMoto {
    private $clienteModel;
    private $view;
    private $model;

    public function __construct() {
        $this->view = new ViewMoto();
        $this->model = new ModelMoto();
        $this->clienteModel = new ModelCliente();
    }

    public function getAllMotos() {
        $motos = $this->model->getMotos();
        $this->view->showMotos($motos);
    }

    public function agregarMoto() {
        if (!empty($_POST['modelo']) && !empty($_POST['patente']) && !empty($_POST['estado'])) {
            $modelo = $_POST['modelo'];
            $patente = $_POST['patente'];
            $estado = $_POST['estado'];
            $kilometros = $_POST['kilometros'] ?? NULL;
            $descripcion = $_POST['descripcion'] ?? NULL;
            $observaciones = $_POST['observaciones'] ?? NULL;

            // Llamada al modelo para insertar la moto
            $this->model->insertMoto($modelo, $patente, $estado, $kilometros, $descripcion, $observaciones);
            
            // Redirigir al listado de motos
            header("Location: " . BASE_URL . "motos");
            exit();
        }
    }

    public function mostrarMotosCliente($dni_cliente) {
        $motosCliente = $this->model->getMotosByDNI($dni_cliente);
        
        if (!empty($motosCliente)) {
            $this->view->showMotos($motosCliente);  // Pasamos solo las motos filtradas
        } else {
            $this->view->showError("No se encontraron motos para el cliente con DNI $dni_cliente.");
        }
    }
}
?>

