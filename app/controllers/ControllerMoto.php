    <?php

    require_once 'app/views/ViewMoto.php';
    require_once 'app/models/ModelMoto.php';
    require_once 'app/models/ModelCliente.php';
    require_once 'app/controllers/errorController.php';
    
    class ControllerMoto {
        private $view;
        private $model;
        private $clienteModel;
        private $error;
    
        public function __construct() {
            $this->view = new ViewMoto();
            $this->model = new ModelMoto();
            $this->clienteModel = new ModelCliente();
            $this->error = new ErrorController();
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
                $motoData = $this->getValidatedMotoData();
                
                if (!$motoData) {
                    $error = "Error: completar todos los campos obligatorios";
                    $redir = "motos";
                    $this->error->showError($error, $redir);
                    return;
                }
                
                $cliente = $this->clienteModel->obtenerClientePorDNI($motoData['dni']);
                if (!$cliente) {
                    $error = "Error: Cliente no encontrado con el DNI: " . $motoData['dni'];
                    $redir = "motos";
                    $this->error->showError($error, $redir);
                    return;
                }
                if ($this->model->existePatente($motoData['patente'])) {
                    $error = "Error: La patente '" . $motoData['patente'] . "' ya está registrada.";
                    $this->error->showError($error, "motos");
                    return;
                }
                
                $result = $this->model->insertMoto(
                    $motoData['modelo'], 
                    $motoData['patente'], 
                    $motoData['estado'], 
                    $motoData['kilometros'], 
                    $motoData['descripcion'], 
                    $motoData['observaciones'], 
                    $motoData['dni']
                );
                
                if ($result) {
                    header('Location: ' . BASE_URL . 'motos');
                } else {
                    $this->error->showError('Error en la base de datos', 'motos');
                }
                return;
            } else {
                $this->view->addMoto();
            }
        }
    
        public function getValidatedMotoData() {
            $modelo = htmlspecialchars(trim($_POST['modelo'] ?? ''));
            $patente = htmlspecialchars(trim($_POST['patente'] ?? ''));
            $estado = htmlspecialchars(trim($_POST['estado'] ?? ''));
            $kilometros = isset($_POST['kilometros']) ? (int) $_POST['kilometros'] : NULL;
            $descripcion = isset($_POST['descripcion']) ? htmlspecialchars(trim($_POST['descripcion'])) : NULL;
            $observaciones = isset($_POST['observaciones']) ? htmlspecialchars(trim($_POST['observaciones'])) : NULL;
            $dni = htmlspecialchars(trim($_POST['dni'] ?? ''));
            
            if (empty($modelo) || empty($patente) || empty($estado) || empty($dni)) {
                return false;
            }
            
            return [
                'modelo' => $modelo,
                'patente' => $patente,
                'estado' => $estado,
                'kilometros' => $kilometros,
                'descripcion' => $descripcion,
                'observaciones' => $observaciones,
                'dni' => $dni
            ];
        }
    

    public function mostrarMotosModelo($modelo) {
        $modelo = htmlspecialchars(trim($modelo));
        $motos = $this->model->getMotosByModelo($modelo);
        if (!empty($motos)) {
            $this->view->showMotos($motos);
        } else {
            $error = "No existen motos con el modelo=$modelo";
            $redir = "motos";
            $this->error->showError($error, $redir);
        }
    }

    public function borrarMoto($id) {
        if ($this->model->getMotoByID($id)) {
            $result = $this->model->eliminarMoto($id);
            if ($result) {
                header('Location: ' . BASE_URL . 'motos');
                exit;
            } else {
                $this->error->showError('Error en la base de datos', 'motos');
            }
        } else {
            $error = "No existe moto con el =$id";
            $redir = "motos";
            $this->error->showError($error, $redir);
        }
    }
    
    

    public function editarMoto($id) {
        $id = (int) $id;
        $motoAEditar = $this->model->getMotoById($id);
        $motos = $this->model->getMotos();
    
        if ($motoAEditar) {
            $this->view->showMotos($motos, $motoAEditar);
        } else {
            $error = "No existe moto con el =$id";
            $redir = "motos";
            $this->error->showError($error, $redir);
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
            $error = "No existe moto con el =$id";
            $redir = "motos";
            $this->error->showError($error, $redir);
        }
    }
}
?>