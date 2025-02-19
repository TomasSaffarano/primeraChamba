<?php

require_once 'app/controllers/ControllerTurno.php';
require_once 'app/controllers/ControllerCliente.php';
require_once 'app/controllers/errorController.php';
require_once 'app/controllers/succesController.php';
require_once 'app/controllers/ControllerMoto.php';

define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

if (isset($_GET['action']) && !empty($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = 'home';
}

$params = explode('/', $action);

switch ($params[0]) {
    case 'home':
        $controller = new ControllerTurno(); 
        $controller->showHome();
        break;

    case 'clientes':
        $controller = new ControllerCliente(); 
        $controller->getAllClientes();
        break;
    case 'motos':
        $controller = new ControllerMoto(); 
        $controller->getAllMotos();
        break; 
    case 'agregarMoto':
            $controller = new ControllerMoto(); 
            $controller->agregarMoto();
            break; 
    case 'motosPorDni':
            $controller = new ControllerMoto();
            if (isset($_GET['dni'])) {
                $dni = $_GET['dni'];
                $controller->mostrarMotosCliente($dni);
            } else {
                echo "DNI no especificado";
            }
            break;
    case 'borrarMoto':
            if (!empty($params[1])) { // Si hay un segundo parámetro en la URL
                $controller = new ControllerMoto();
                $controller->borrarMoto([':ID' => $params[1]]);
            } else {
                echo "Error: Falta el ID de la moto";
            }
            break;

 case 'agregarCliente':
        $controller = new ControllerCliente(); 
        $controller->addClient();
        break;

    case 'modificarCliente':
        $controller = new ControllerCliente();
        $controller->updateClient($params[1]);
        break;

    case 'realizado': 
        $controller = new SuccessControler();
        $controller->showSuccess();
        break; 

    case 'eliminarCliente':
        $controller = new ControllerCliente();
        $controller->deleteClient($params[1]);
        break;

    case 'editarMoto':
        if (!empty($params[1])) { 
            $controller = new ControllerMoto();
            $controller->editarMoto($params[1]);
        } else {
            echo "Error: Falta el ID de la moto";
        }
        break;
    case 'actualizarMoto':
        if (!empty($params[1])) { 
            $controller = new ControllerMoto();
            $controller->actualizarMoto($params[1]);
        } else {
            echo "Error: Falta el ID de la moto";
        }
            break;
    default:
        $error = "404 page not found";
        $redir = "home";
        $controller = new ErrorController();
        $controller->showError($error, $redir);
        break;
}
