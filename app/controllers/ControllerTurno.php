<?php

require_once 'app/views/ViewTurno.php';
require_once 'app/models/ModelTurno.php';
require_once 'app/controllers/errorController.php';
require_once 'app/models/modelMoto.php';
require_once 'app/models/modelCliente.php';

class ControllerTurno {
    private $view;
    private $model;
    private $error;
    private $motoModel;
    private $clienteModel;

    public function __construct() {
        $this->view = new ViewTurno();
        $this->model = new ModelTurno();
        $this->error = new ErrorController();
        $this->motoModel = new ModelMoto();
        $this->clienteModel = new ModelCliente();
    }

    public function showHome() {
        $this->view->showHome();
    }

    public function getAllTurnos() {
        $turns = $this->model->getTurns();
        $this->view->showTurns($turns);
    }

    public function addTurn(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $turnData = $this->getValidatedTurnData();

        // Primero verificamos si la validación falló
        if (!$turnData) {
            $error = "Error: completar todos los campos obligatorios";
            $redir = "historial";
            $this->error->showError($error, $redir);
            return;
        }

        // Ahora podemos acceder con seguridad a ['patente']
        $patente = $this->motoModel->existePatente($turnData['patente']);

        if (!$patente) {
            $error = "Error: no existe la patente";
            $redir = "historial";
            $this->error->showError($error, $redir);
            return;
        }

        // Insertar en la base de datos
        $result = $this->model->insertTurn($turnData['ingreso'], $turnData['entrega'], $turnData['patente']);

        if ($result) {
            header('Location: ' . BASE_URL . 'historial');
        } else {
            $this->error->showError('Error en la base de datos', 'historial');
        }
        return;

    } else {
        $this->view->showTurn();
    }
}

    private function getValidatedTurnData()
    {
        // Verificar campos obligatorios
        if (
            !isset($_POST['ingreso']) || empty($_POST['ingreso']) ||
            !isset($_POST['entrega']) || empty($_POST['entrega']) ||
            !isset($_POST['patente']) || empty($_POST['patente'])
        ) {
            return false;
        }
        
        
        // Si todos los datos son válidos, devolver un array con los datos
        return [
            'ingreso' => htmlspecialchars($_POST['ingreso']),
            'entrega' => htmlspecialchars($_POST['entrega']),
            'patente' => htmlspecialchars($_POST['patente']),
        ];
    }

    
    public function deleteTurn($id)
    {
        if($this->model->checkIDExists($id)){
            $result = $this->model->eraseTurn($id);
            if($result)
            header('Location: ' . BASE_URL . 'historial');
        else
            $this->error->showError('Error en la base de datos', 'historial');
        return;
        } else {
            $error = "No existe el turno con el id=$id";
            $redir = "historial";
            $this->error->showError($error, $redir);
        }
    }

    
    public function updateTurn($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $turn = $this->model->getTurn($id);
            if (!$turn) {
                $error = "No existe el turno con el id=$id";
                $redir = "historial";
                $this->error->showError($error, $redir);
                return;
            }
            $turns = $this->model->getTurns();
            // Mostrar el formulario de edición, pasando el cliente y todos los clientes
            $this->view->showTurnForm($turn, true, $turns);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar los datos del cliente
            $turnData = $this->getValidatedTurnData();
            
            if (!$turnData) {
                $error = "Error: completar todos los campos obligatorios";
                $redir = "historial";
                $this->error->showError($error, $redir);
            } else {

            $patente = $this->motoModel->existePatente($turnData['patente']);

            if (!$patente) {
                $error = "Error: no existe la patente";
                $redir = "historial";
                $this->error->showError($error, $redir);
                return;
            }
                // Actualizar el cliente en la base de datos
                $result = $this->model->updateTurn($id, $turnData['ingreso'], $turnData['entrega'], $turnData['patente']);
                
                // Redirigir si la actualización es exitosa
                if ($result) {
                    header('Location: ' . BASE_URL . 'historial');
                } else {
                    $this->error->showError('Error en la base de datos', 'historial');
                }
            }
        }
    }

    public function mostrarTurno($turn) {
        $patent = htmlspecialchars(trim($turn));
        $turns = $this->model->getTurnPatent($patent);
        if (!empty($turns)) {
            $this->view->showTurns($turns);
        } else {
            $error = "No existe el turno con la patente=$patent";
            $redir = "historial";
            $this->error->showError($error, $redir);
        }
    }

    public function verTurno($id){
        $id = (int) $id;
        $turno = $this->model->getTurn($id);
        if (!$turno) {
            $error = "No existe el turno con el id=$id";
            $redir = "historial";
            $this->error->showError($error, $redir);
            return;
        } 
        
        $moto = $this->motoModel->getMotoByPatente($turno->patente);

        if (!$moto) {
            $error = "No existe la moto con la patente=$turno->patente";
            $redir = "historial";
            $this->error->showError($error, $redir);
            return;
        } 

        $cliente = $this->clienteModel->obtenerClientePorDNI($moto->dni);

        if (!$cliente) {
            $error = "No existe el cliente con el dni=$$moto->dni";
            $redir = "historial";
            $this->error->showError($error, $redir);
            return;
        } 

        $this->view->showTurn($turno,$moto,$cliente);

    }
    
}

