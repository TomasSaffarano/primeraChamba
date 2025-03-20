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
    
    public function getTurnosJson() {
        header('Content-Type: application/json');

        // Obtener turnos del modelo
        $turnos = $this->modelTurno->getTurnosForCalendar();
        
        // Convertir el resultado a JSON y enviarlo
        echo json_encode($turnos);
    }
public function editar($id) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $turno = $this->modelTurno->getTurnoById($id);
        if (!$turno) {
            return;
        }

        $moto = $this->modelMoto->getMotoByPatente($turno->patente);

        if ($moto && property_exists($moto, 'dni') && !empty($moto->dni)) {
            $cliente = $this->modelCliente->obtenerClientePorDNI($moto->dni);
        } else {
            $cliente = null; // Manejo en caso de que no se encuentre
        }
        if (!$cliente || !$moto) {
            return;
        }

        // Pasar datos a la vista para mostrar el formulario de edición
        $this->view->showForm("editar", [
            "turno" => $turno,
            "cliente" => $cliente,
            "moto" => $moto
        ]);
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['nombre']) || empty($_POST['dni']) || empty($_POST['telefono']) || 
            empty($_POST['modelo']) || empty($_POST['patente']) || empty($_POST['estado']) || 
            empty($_POST['descripcion']) || empty($_POST['kilometros']) || 
            empty($_POST['entrega']) || empty($_POST['ingreso']) || empty($_POST['hora'])) {
            
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
        $observaciones = $_POST['observaciones'] ?? ''; // Opcional
        $kilometros = $_POST['kilometros'];
        $ingreso = $_POST['ingreso'];
        $hora = $_POST['hora'];
        $fechaHora = $ingreso . ' ' . $hora . ':00';
        $entrega = $_POST['entrega'];

        // ✅ Verificar si el turno existe
        $turnoExistente = $this->modelTurno->getTurnoById($id);
        if (!$turnoExistente) {
            return;
        }

        // ✅ Verificar si la nueva fecha/hora está ocupada por otro turno
        if ($turnoExistente->patente !== $patente || $turnoExistente->hora !== $fechaHora) {
            if ($this->modelTurno->existsTurno($ingreso, $hora, $patente)) {
                return;
            }
        }

        // ✅ Verificar si el cliente ya existe
        $cliente = $this->modelCliente->obtenerClientePorDNI($dni);
        if (!$cliente) {
            $idCliente = $this->modelCliente->insertClient($nombre, $dni, $telefono);
            if (!$idCliente) {
                return;
            }
        } else {
            $idCliente = $cliente->id;

            // 🔹 **Actualizar cliente**
            $clienteActualizado = $this->modelCliente->updateClient($idCliente, $nombre, $dni, $telefono);
            if (!$clienteActualizado) {
                return;
            }
        }

        // ✅ Obtener la moto asociada a la patente
        $moto = $this->modelMoto->getMotoByPatente($patente);
        if (!$moto) {
            return;
        }

        // ✅ Ahora tenemos el ID de la moto
        $idMoto = $moto->id;

        // ✅ Actualizar moto
        $motoActualizada = $this->modelMoto->updateMoto($idMoto, $modelo, $patente, $estado, $dni, $kilometros, $descripcion, $observaciones);
        if (!$motoActualizada) {
            return;
        }

        // ✅ Actualizar turno
        $turnoActualizado = $this->modelTurno->updateTurn($id, $ingreso, $fechaHora, $entrega, $patente);
        if (!$turnoActualizado) {
            return;
        }

        // ✅ Redirigir con éxito
        header('Location: ' . BASE_URL . 'turnos?success=2');
        exit();
    }
}

    
    
}
?>
