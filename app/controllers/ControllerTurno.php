<?
require_once 'app/models/ModelTurno.php';
require_once 'app/views/ViewTurno.php';
class ControllerTurno {
    private $view;
    private $model;

    public function __construct() {
        $this->view = new ViewTurno();
        $this->model = new ModelTurno();
    }

    // Muestra el formulario de creación de turno
    public function showCreateTurnoForm() {
        $this->view->showCreateTurnoForm();
    }

    // Crea un nuevo turno
    public function createTurno() {
        // Obtener datos del formulario
        $fechaIngreso = $_POST['fecha_ingreso'];
        $fechaEntrega = $_POST['fecha_entrega'];
        $idCliente = $_POST['id_cliente'];

        // Validación simple de los datos
        if (empty($fechaIngreso) || empty($fechaEntrega) || empty($idCliente)) {
            $this->view->showError("Todos los campos son obligatorios.");
            return;
        }

        // Crear el turno en la base de datos
        $turnoId = $this->model->createTurno($fechaIngreso, $fechaEntrega, $idCliente);

        // Redirigir a la vista de turnos
       //header("Location: " . BASE_URL . "turnos/$turnoId");
    }

    // Obtener turnos de un cliente
    public function getTurnosByCliente($idCliente) {
        $turnos = $this->model->getTurnosByCliente($idCliente);
        $this->view->showTurnosByCliente($turnos);
    }

    // Mostrar un turno por ID
    public function showTurnoById($id) {
        $turno = $this->model->getTurnoById($id);
        if (!$turno) {
            $this->view->showError("Turno no encontrado.");
            return;
        }
        $this->view->showTurnoDetails($turno);
    }
}
?>