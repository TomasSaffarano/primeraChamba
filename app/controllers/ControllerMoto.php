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
        // Obtener todas las motos desde el modelo
        $motos = $this->model->getMotos();
    
        // Ordenar las motos de acuerdo al estado, donde 'en_reparacion' aparece primero
        usort($motos, function($a, $b) {
            if ($a->estado == 'en_reparacion' && $b->estado != 'en_reparacion') {
                return -1; // $a debe ir antes que $b
            } elseif ($a->estado != 'en_reparacion' && $b->estado == 'en_reparacion') {
                return 1; // $b debe ir antes que $a
            }
            return 0; // si ambos tienen el mismo estado, no cambiar el orden
        });
    
        // Pasar las motos ordenadas a la vista
        $this->view->showMotos($motos);
    }

    public function agregarMoto() {
        if (!empty($_POST['modelo']) && !empty($_POST['patente']) && !empty($_POST['estado']) && !empty($_POST['dni'])) {
            $modelo = $_POST['modelo'];
            $patente = $_POST['patente'];
            $estado = $_POST['estado'];
            $kilometros = $_POST['kilometros'] ?? NULL;
            $descripcion = $_POST['descripcion'] ?? NULL;
            $observaciones = $_POST['observaciones'] ?? NULL;
            $dni = $_POST['dni'];
    
            $cliente = $this->clienteModel->obtenerClientePorDNI($dni);
    
            if ($cliente) {
                $id_cliente = $cliente->id;
                $this->model->insertMoto($modelo, $patente, $estado, $kilometros, $descripcion, $observaciones, $dni);
    
                header("Location: " . BASE_URL . "motos");
                exit();
            } else {
                echo "Cliente no encontrado con el DNI: $dni";
            }
        } else {
            echo "Por favor, complete todos los campos requeridos.";
        }
    }    public function mostrarMotosCliente($dni) {
        $motos = $this->model->getMotosByDNI($dni);
        if (!empty($motos)) {
            $this->view->showMotos($motos);
        } else {
            $this->view->showError("No se encontraron motos para el cliente con DNI $dni.");
        }
    }
}
?>

