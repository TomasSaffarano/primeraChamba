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
            empty($_POST['ingreso']) || empty($_POST['hora'])) {
            
            $this->error->showError("Todos los campos obligatorios deben ser completados.", "registro");
            return;
        }
    
        // Campos obligatorios
        $nombre = $_POST['nombre'];
        $dni = $_POST['dni'];
        $telefono = $_POST['telefono'];
        $modelo = $_POST['modelo'];
        $patente = $_POST['patente'];
        $estado = $_POST['estado'];
        $ingreso = $_POST['ingreso'];
        $hora = $_POST['hora'];
        $fechaHora = $ingreso . ' ' . $hora . ':00';
    
        // Campos opcionales (se asigna un valor por defecto si no están definidos)
        $descripcion = $_POST['descripcion'] ?? ''; 
        $observaciones = $_POST['observaciones'] ?? ''; 
        $kilometros = $_POST['kilometros'] ?? NULL; 
        $entrega = $_POST['entrega'] ?? NULL;
    
        if ($this->modelTurno->existsTurno($ingreso, $hora, $patente)) {
            $this->error->showError("Esta moto ya tiene un turno asignado para la misma fecha y hora.", "registro");
            return;
        }
    
        // Manejo del cliente
        $cliente = $this->modelCliente->obtenerClientePorDNI($dni);
        if (!$cliente) {
            $idCliente = $this->modelCliente->insertClient($nombre, $dni, $telefono);
            if (!$idCliente) {
                error_log("Error al registrar el cliente.");
                $this->error->showError("Error al registrar el cliente.", "registro");
                return;
            }
        } else {
            $idCliente = $cliente->id;
        }
    
        // Registrar la moto
        $idMoto = $this->modelMoto->insertMoto($modelo, $patente, $estado, $kilometros, $descripcion, $observaciones, $dni);
        var_dump("🟠 ID Moto:", $idMoto);
        if (!$idMoto) {
            error_log("Error al registrar la moto.");
            $this->error->showError("Error al registrar la moto.", "registro");
            return;
        }
    
        // Registrar el turno
        $idTurno = $this->modelTurno->createTurno($ingreso, $fechaHora, $entrega, $patente);
        var_dump("🟠 ID Turno:", $idTurno);
        if (!$idTurno) {
            error_log("Error al registrar el turno.");
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
        if (!$cliente || !$moto) {
            echo "Error: La moto o el cliente no existen.";
            return;
        }
    
        $this->view->showForm("editar", [
            "turno" => $turno,
            "cliente" => $cliente,
            "moto" => $moto
        ]);
    }
    

    public function actualizarTurno()
{
    echo "<pre>";
    
    // 📌 Mostrar todo el contenido recibido por POST
    var_dump("🟢 Datos recibidos:", $_POST);

    // 📌 Validar datos recibidos
   // Depuración: Verificar qué datos está recibiendo el método
$turnoData = $this->validarDatosTurno();


    if (!$turnoData) {
        $this->error->showError("Todos los campos son obligatorios.", "calendario");
        die("🔴 Error: Datos inválidos");
    }

    // 📌 Extraer variables
    extract($turnoData);
    $fechaHora = $ingreso . ' ' . $hora . ':00';

    // 📌 Mostrar los datos que vamos a procesar
    var_dump("📌 Datos procesados:", $turnoData);

    // 📌 Verificar si la moto ya tiene un turno en ese horario (excluir turno actual)
    if ($this->modelTurno->existsTurno($ingreso, $hora, $patente, $idTurno)) {
        $this->error->showError("Esta moto ya tiene un turno asignado para la misma fecha y hora.", "calendario");
        die("🔴 Error: Turno ya existe");
    }

    // 📌 Actualizar Cliente
    $cliente = $this->modelCliente->obtenerClientePorDNI($dni);
    var_dump("👤 Cliente encontrado:", $cliente);

    if ($cliente) {
        $idCliente = $cliente->id;
        $this->modelCliente->updateClient($idCliente, $nombre, $dni, $telefono);
    } else {
        $this->error->showError("El cliente no existe.", "calendario");
        die("🔴 Error: Cliente no encontrado");
    }

    // 📌 Obtener Moto
    $moto = $this->modelMoto->getMotoByPatente($patente);
    var_dump("🏍️ Moto encontrada:", $moto);

    if ($moto) {
        $idMoto = $moto->id;
        $this->modelMoto->updateMoto($idMoto, $modelo, $patente, $estado, $dni, $kilometros, $descripcion, $observaciones);
    } else {
        $this->error->showError("La moto no existe.", "calendario");
        die("🔴 Error: Moto no encontrada");
    }

    // 📌 Actualizar Turno
    $turnoActualizado = $this->modelTurno->updateTurn($idTurno, $ingreso, $entrega, $patente, $hora);
    var_dump("⏳ Turno actualizado:", $turnoActualizado);

    if (!$turnoActualizado) {
        $this->error->showError("Error al actualizar el turno.", "calendario");
        die("🔴 Error: No se pudo actualizar el turno");
    }

    // 📌 Redirigir si todo fue exitoso
    echo "✅ Todo correcto, redirigiendo...";
    echo "</pre>";
    header('Location: ' . BASE_URL . 'calendario');
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
            'id' => $_POST['id'] ?? null, // Asegurar que exista la ID del turno
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
    
