<?php
require_once 'app/models/ModelMoto.php';
require_once 'app/models/ModelTurno.php';
require_once 'app/models/ModelCliente.php';
require_once 'app/controllers/ErrorController.php';
require_once 'app/views/ViewAgregar.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');

class ControllerAgregar {
    private $view;
    private $modelCliente;
    private $modelMoto;
    private $modelTurno;
    private $error;

    public function __construct() {
        $this->view = new ViewAgregar();
        $this->modelCliente = new ModelCliente();
        $this->modelMoto = new ModelMoto();
        $this->modelTurno = new ModelTurno();
        $this->error = new ErrorController();
    }

    public function registrar() {
        if (empty($_POST['nombre']) || empty($_POST['dni']) || empty($_POST['telefono']) || 
            empty($_POST['modelo']) || empty($_POST['patente']) || empty($_POST['estado']) || 
            empty($_POST['descripcion']) || empty($_POST['kilometros']) || 
            empty($_POST['entrega']) || empty($_POST['ingreso']) || empty($_POST['hora'])) {
            
            $this->error->showError("Todos los campos son obligatorios.", "registro");
            return;
        }

        $nombre = $_POST['nombre'];
        $dni = $_POST['dni'];
        $telefono = $_POST['telefono'];
        $modelo = $_POST['modelo'];
        $patente = $_POST['patente'];
        $estado = $_POST['estado'];
        $descripcion = $_POST['descripcion'];
        $observaciones = $_POST['observaciones'] ?? '';
        $kilometros = $_POST['kilometros'];
        $ingreso = $_POST['ingreso'];
        $hora = $_POST['hora'];
        $fechaHora = $ingreso . ' ' . $hora . ':00';
        $entrega = $_POST['entrega'];

        if ($this->modelTurno->existsTurno($ingreso, $hora, $patente)) {
            $this->error->showError("Esta moto ya tiene un turno asignado para la misma fecha y hora.", "registro");
            return;
        }

        $cliente = $this->modelCliente->obtenerClientePorDNI($dni);
        if (!$cliente) {
            $idCliente = $this->modelCliente->insertClient($nombre, $dni, $telefono);
            if (!$idCliente) {
                $this->error->showError("Error al registrar el cliente.", "registro");
                return;
            }
        } else {
            $idCliente = $cliente->id;
        }

        $idMoto = $this->modelMoto->insertMoto($modelo, $patente, $estado, $descripcion, $observaciones, $kilometros, $dni);
        if (!$idMoto) {
            $this->error->showError("Error al registrar la moto.", "registro");
            return;
        }

        $idTurno = $this->modelTurno->createTurno($ingreso, $fechaHora, $entrega, $patente);
        if (!$idTurno) {
            $this->error->showError("Error al registrar el turno.", "registro");
            return;
        }

        header('Location: ' . BASE_URL . 'calendario');
        exit();
    }

    public function mostrarFormulario($fechaIngreso) {
        $this->view->mostrarFormulario($fechaIngreso); 
    }

    public function mostrarcalendario() {
        $this->view->mostrarCalendario();
    }
    
    public function getTurnosJson() {
        header('Content-Type: application/json');
        $turnos = $this->modelTurno->getTurnosForCalendar();
        echo json_encode($turnos);
    }

    public function editar($id) {
        $turno = $this->modelTurno->getTurnoById($id);
        if (!$turno) return;

        $moto = $this->modelMoto->getMotoByPatente($turno->patente);
        $cliente = $moto && property_exists($moto, 'dni') ? $this->modelCliente->obtenerClientePorDNI($moto->dni) : null;
        if (!$cliente || !$moto) return;

        $this->view->showForm("editar", [
            "turno" => $turno,
            "cliente" => $cliente,
            "moto" => $moto
        ]);
    }

    public function actualizarTurno()
    {
        // 📌 Validar datos recibidos
        $turnoData = $this->validarDatosTurno();
        if (!$turnoData) {
            $this->error->showError("Todos los campos son obligatorios.", "turnos");
            return;
        }
    
        // 📌 Extraer variables
        extract($turnoData);
        $fechaHora = $ingreso . ' ' . $hora . ':00';
    
        // 📌 Verificar si la moto ya tiene un turno en ese horario (excluir turno actual)
        if ($this->modelTurno->existsTurno($ingreso, $hora, $patente, $idTurno)) {
            $this->error->showError("Esta moto ya tiene un turno asignado para la misma fecha y hora.", "turnos");
            return;
        }
    
        // 📌 Actualizar Cliente
        $cliente = $this->modelCliente->obtenerClientePorDNI($dni);
        if ($cliente) {
            $idCliente = $cliente->id;
            $this->modelCliente->updateClient($idCliente, $nombre, $dni, $telefono);
        } else {
            $this->error->showError("El cliente no existe.", "turnos");
            return;
        }
    
        // 📌 Actualizar Moto
        $moto = $this->modelMoto->getMotoByPatente($patente);
        if ($moto) {
            $idMoto = $moto->id;
            $this->modelMoto->updateMoto($idMoto, $modelo, $patente, $estado, $dni, $kilometros, $descripcion, $observaciones);
        } else {
            $this->error->showError("La moto no existe.", "turnos");
            return;
        }
    
        // 📌 Actualizar Turno
        $turnoActualizado = $this->modelTurno->updateTurn($idTurno, $ingreso, $entrega, $patente, $hora);
        if (!$turnoActualizado) {
            $this->error->showError("Error al actualizar el turno.", "turnos");
            return;
        }
    
        // 📌 Redirigir si todo fue exitoso
        header('Location: ' . BASE_URL . 'clientes');
        exit();
    }
    
    // 🔹 Método para validar los datos del formulario
    private function validarDatosTurno()
    {
        if (empty($_POST['nombre']) || empty($_POST['dni']) || empty($_POST['telefono']) || 
            empty($_POST['modelo']) || empty($_POST['patente']) || empty($_POST['estado']) || 
            empty($_POST['descripcion']) || empty($_POST['kilometros']) || 
            empty($_POST['entrega']) || empty($_POST['ingreso']) || empty($_POST['hora'])) {
            return false;
        }
    
        return [
            'idTurno' => $_POST['idTurno'] ?? null, // Asegurar que exista la ID del turno
            'nombre' => $_POST['nombre'],
            'dni' => $_POST['dni'],
            'telefono' => $_POST['telefono'],
            'modelo' => $_POST['modelo'],
            'patente' => $_POST['patente'],
            'estado' => $_POST['estado'],
            'descripcion' => $_POST['descripcion'],
            'observaciones' => $_POST['observaciones'] ?? '',
            'kilometros' => $_POST['kilometros'],
            'ingreso' => $_POST['ingreso'],
            'hora' => $_POST['hora'],
            'entrega' => $_POST['entrega']
        ];
    }
    
    }
    

?> 
    
