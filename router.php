<?php
require_once 'app/controllers/ControllerTurno.php';
require_once 'app/controllers/ControllerCliente.php';
require_once 'app/controllers/errorController.php';
require_once 'app/controllers/ControllerMoto.php';
require_once 'app/controllers/ControllerTurno.php';
require_once 'app/controllers/ControllerLogin.php';
require_once 'app/middlewares/session.auth.php';
require_once 'app/middlewares/verify.auth.php';
require_once 'app/middlewares/response.php';

define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

if (isset($_GET['action']) && !empty($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = 'home';
}

$res = new Response();
$params = explode('/', $action);

switch ($params[0]) {
    case 'home':
         sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerTurno(); 
        $controller->showHome();
        break;
    case 'clientes':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerCliente(); 
        $controller->getAllClientes();
        break;
    
    case 'historial':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerTurno(); 
        $controller->getAllTurnos();
        break;

    case 'motos':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerMoto(); 
        $controller->getAllMotos();
        break; 
    case 'agregarMoto':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
            $controller = new ControllerMoto(); 
            $controller->agregarMoto();
            break; 
    case 'motosPorModelo':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
            $controller = new ControllerMoto();
            if (isset($_GET['modelo'])) {
                $modelo = $_GET['modelo'];
                $controller->mostrarMotosModelo($modelo);
            } else {
                echo "No hay motos con ese modelo";
            }
            break;
    case 'borrarMoto':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
            if (!empty($params[1])) { // Si hay un segundo parámetro en la URL
                $controller = new ControllerMoto();
                $controller->borrarMoto([':ID' => $params[1]]);
            } else {
                echo "Error: Falta el ID de la moto";
            }
            break;

    case 'agregarCliente':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
            $controller = new ControllerCliente(); 
            $controller->addClient();
            break;

    case 'agregarTurno':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerTurno(); 
        $controller->addTurn();
        break;
    case 'login':
        $controller = new ControllerLogin();
        $controller->showLogin();
        break;

    case 'logOut':
        $controller = new ControllerLogin();
        $controller->logout();
        break;

    case 'verificarLogin':
        $controller = new ControllerLogin();
        $controller->login();
        break;
    
    case 'modificarCliente':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerCliente();
        $controller->updateClient($params[1]);
        break;

    case 'modificarTurno':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerTurno();
        $controller->updateTurn($params[1]);
        break;

    case 'eliminarCliente':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerCliente();
        $controller->deleteClient($params[1]);
        break;

    case 'eliminarTurno':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerTurno();
        $controller->deleteTurn($params[1]);
        break;

    case 'clienteNombre':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerCliente();
        if (!empty($_GET['nombre'])) {
            $client = trim($_GET['nombre']); // Eliminar espacios en blanco
            $controller->mostrarCliente($client);
        } else {
            echo "Error: Nombre no especificado.";
        }
        break;

    case 'turnoPatente':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        $controller = new ControllerTurno();
        if (!empty($_GET['patente'])) {
            $turn = trim($_GET['patente']); // Eliminar espacios en blanco
            $controller->mostrarTurno($turn);
        } else {
            echo "Error: Patente no especificado.";
        }
        break;

    case 'editarMoto':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        if (!empty($params[1])) { 
            $controller = new ControllerMoto();
            $controller->editarMoto($params[1]);
        } else {
            echo "Error: Falta el ID de la moto";
        }
        break;
    case 'verMoto':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
        if (!empty($params[1])) { 
            $controller = new ControllerMoto();
            $controller->verMoto($params[1]);
        } else {
            echo "Error: Falta el ID de la moto";
        }
        break;

    case 'verTurno':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
            if (!empty($params[1])) { 
                $controller = new ControllerTurno();
                $controller->verTurno($params[1]);
            } else {
                echo "Error: Falta el ID del turno";
            }
            break;

    case 'actualizarMoto':
        sessionAuthMiddleware($res);
        verifyAuthMiddleware($res);
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
