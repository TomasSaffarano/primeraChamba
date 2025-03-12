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

    // Método para registrar un turno
    public function registrar() {
        if (empty($_POST['nombre']) || empty($_POST['dni']) || empty($_POST['telefono']) || 
            empty($_POST['modelo']) || empty($_POST['patente']) || empty($_POST['estado']) || 
            empty($_POST['descripcion']) || empty($_POST['kilometros']) || 
            empty($_POST['entrega']) || empty($_POST['ingreso']) || empty($_POST['hora'])) {
            
            $this->error->showError("Todos los campos son obligatorios.", "registro");
            return;
        }

        // ✅ Obtener datos del formulario
        $nombre = $_POST['nombre'];
        $dni = $_POST['dni'];
        $telefono = $_POST['telefono'];
        $modelo = $_POST['modelo'];
        $patente = $_POST['patente'];
        $estado = $_POST['estado'];
        $descripcion = $_POST['descripcion'];
        $observaciones = $_POST['observaciones'] ?? ''; // Observaciones opcionales
        $kilometros = $_POST['kilometros'];
        $ingreso = $_POST['ingreso'];
        $hora = $_POST['hora'];
        $fechaHora = $ingreso . ' ' . $hora . ':00'; // Agregamos los segundos
        $entrega = $_POST['entrega'];

        // ✅ Verificar si la moto ya tiene un turno en la misma fecha y hora
        if ($this->modelTurno->existsTurno($ingreso, $hora, $patente)) {
            $this->error->showError("Esta moto ya tiene un turno asignado para la misma fecha y hora.", "registro");
            return;
        }

        // ✅ Verificar si el cliente ya existe
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

        // ✅ Insertar moto
        $idMoto = $this->modelMoto->insertMoto($modelo, $patente, $estado, $descripcion, $observaciones, $kilometros, $dni);
        if (!$idMoto) {
            $this->error->showError("Error al registrar la moto.", "registro");
            return;
        }

        // ✅ Insertar turno
        $idTurno = $this->modelTurno->createTurno($ingreso,$fechaHora, $entrega, $patente);
        if (!$idTurno) {
            $this->error->showError("Error al registrar el turno.", "registro");
            return;
        }

        // ✅ Redirigir con éxito
        header('Location: ' . BASE_URL . 'turnos?success=1');
        exit();
    }

    // Si no es un POST, mostrar el formulario con la fecha seleccionada
    public function mostrarFormulario($fechaIngreso) {
        // Mostrar el formulario y pasar la fecha de ingreso para precargarla
        $this->view->mostrarFormulario($fechaIngreso); 
    }

    public function mostrarcalendario() {
        // Mostrar el calendario
        $this->view->mostrarCalendario();
    }
}
?>
