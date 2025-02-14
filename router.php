<?php

require_once 'app/controllers/ControllerTurno.php';
require_once 'app/controllers/ControllerCliente.php';
require_once 'app/controllers/errorController.php';
require_once 'app/controllers/succesController.php';

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

    default:
        echo "Página no encontrada";
        break;

    case 'agregarCliente':
        $controller = new ControllerCliente(); 
        $controller->addClient();
        break;
    
        default:
        $error = "404 page not found";
        $redir = "home";
        $controler = new ErrorControler();
        $controler->showError($error, $redir);
        break;

        case 'realizado': 
            $controler= new SuccessControler();
            $controler->showSuccess();
            break; 
}
