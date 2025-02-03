<?php

require_once 'app/views/ViewMoto.php';
require_once 'app/models/ModelMoto.php';

class ControllerMoto {
    private $view;
    private $model;

    public function __construct() {
        $this->view = new ViewMoto();
        $this->model = new ModelMoto();
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
    
            $this->model->insertMoto($modelo, $patente, $estado, $kilometros, $descripcion, $observaciones);
            header("Location: " . BASE_URL . "motos");
            exit();
        }
    }
}
