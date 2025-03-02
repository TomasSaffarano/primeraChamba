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
    
        usort($motos, function($a, $b) {
            if ($a->estado == 'en_reparacion' && $b->estado != 'en_reparacion') {
                return -1;
            } elseif ($a->estado != 'en_reparacion' && $b->estado == 'en_reparacion') {
                return 1;
            }
            return 0;
        });
    
        $this->view->showMotos($motos);
    }

    public function agregarMoto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $modelo = htmlspecialchars(trim($_POST['modelo']));
            $patente = htmlspecialchars(trim($_POST['patente']));
            $estado = htmlspecialchars(trim($_POST['estado']));
            $kilometros = isset($_POST['kilometros']) ? (int) $_POST['kilometros'] : NULL;
            $descripcion = isset($_POST['descripcion']) ? htmlspecialchars(trim($_POST['descripcion'])) : NULL;
            $observaciones = isset($_POST['observaciones']) ? htmlspecialchars(trim($_POST['observaciones'])) : NULL;
            $dni = htmlspecialchars(trim($_POST['dni']));
    
            if (empty($modelo) || empty($patente) || empty($estado) || empty($dni)) {
                echo "Por favor, complete todos los campos requeridos.";
                return;
            }

            $cliente = $this->clienteModel->obtenerClientePorDNI($dni);
    
            if ($cliente) {
                $this->model->insertMoto($modelo, $patente, $estado, $kilometros, $descripcion, $observaciones, $dni);
                header("Location: " . BASE_URL . "motos");
                exit();
            } else {
                echo "Cliente no encontrado con el DNI: $dni";
            }
        }
    }

    public function mostrarMotosModelo($modelo) {
        $modelo = htmlspecialchars(trim($modelo));
        $motos = $this->model->getMotosByModelo($modelo);
        if (!empty($motos)) {
            $this->view->showMotos($motos);
        } else {
            $this->view->showError("No se encontraron motos con el modelo $modelo.");
        }
    }

    public function borrarMoto($params = []) {
        $id = (int) $params[':ID'];
        $this->model->eliminarMoto($id);
        header("Location: " . BASE_URL . "motos");
        exit();
    }

    public function editarMoto($id) {
        $id = (int) $id;
        $motoAEditar = $this->model->getMotoById($id);
        $motos = $this->model->getMotos();
    
        if ($motoAEditar) {
            $this->view->showMotos($motos, $motoAEditar);
        } else {
            $this->view->showError("Moto no encontrada.");
        }
    }

    public function actualizarMoto($id) {
        $id = (int) $id;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $modelo = htmlspecialchars(trim($_POST['modelo']));
            $patente = htmlspecialchars(trim($_POST['patente']));
            $estado = htmlspecialchars(trim($_POST['estado']));
            $dni = htmlspecialchars(trim($_POST['dni']));
            $kilometros = isset($_POST['kilometros']) ? (int) $_POST['kilometros'] : 0;
            $descripcion = isset($_POST['descripcion']) ? htmlspecialchars(trim($_POST['descripcion'])) : '';
            $observaciones = isset($_POST['observaciones']) ? htmlspecialchars(trim($_POST['observaciones'])) : '';
    
            if (empty($modelo) || empty($patente) || empty($estado) || empty($dni)) {
                $this->view->showError("Faltan datos obligatorios.");
                return;
            }
    
            $this->model->updateMoto($id, $modelo, $patente, $estado, $dni, $kilometros, $descripcion, $observaciones);
            header("Location: " . BASE_URL . "motos");
        }
    }

    public function verMoto($id) {
        $id = (int) $id;
        $moto = $this->model->getMotoByIdInfo($id);
        if ($moto) {
            $this->view->showMoto($moto);
        } else {
            $this->view->showError("No se encontraron motos $moto.");
        }
    }
}

?>