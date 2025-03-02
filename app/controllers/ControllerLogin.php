<?php
require_once './app/models/ModelAdmin.php';
require_once './app/views/ViewLogin.php';

class ControllerLogin {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new ModelAdmin();
        $this->view = new ViewLogin();
    }

    public function showLogin() {
        return $this->view->showLogin();
    }

     public function login() {
        session_start();
        
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = time();
        }
    
        // Verificar si el usuario está bloqueado
        $max_attempts = 3;  // Número máximo de intentos
        $block_time = 60;   // Tiempo de bloqueo en segundos (1 minuto)
        
        if ($_SESSION['login_attempts'] >= $max_attempts) {
            $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
            
            if ($time_since_last_attempt < $block_time) {
                return $this->view->showLogin("Demasiados intentos. Intenta nuevamente en " . ($block_time - $time_since_last_attempt) . " segundos.");
            } else {
                // Reiniciar intentos después del período de bloqueo
                $_SESSION['login_attempts'] = 0;
            }
        }
    
        if (!isset($_POST['usuario']) || empty($_POST['usuario'])) {
            return $this->view->showLogin('Falta completar el nombre de usuario');
        }
        if (!isset($_POST['contraseña']) || empty($_POST['contraseña'])) {
            return $this->view->showLogin('Falta completar la contraseña');
        }
    
        $user_name = $_POST['usuario'];
        $password = $_POST['contraseña'];
    
        $userFromDB = $this->model->getUserByUserName($user_name);
    
        if ($userFromDB) {
            if (!password_verify($password, $userFromDB->contraseña)) {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                return $this->view->showLogin("Contraseña incorrecta. Intento " . $_SESSION['login_attempts'] . " de $max_attempts");
            }
    
            // Restablecer intentos fallidos si el login es exitoso
            $_SESSION['login_attempts'] = 0;
    
            // Iniciar sesión si la contraseña es correcta
            $_SESSION['ID_USER'] = $userFromDB->id;
            $_SESSION['NAME_USER'] = $userFromDB->usuario;
            header('Location: ' . BASE_URL);
            exit();
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            return $this->view->showLogin("El usuario no existe. Intento " . $_SESSION['login_attempts'] . " de $max_attempts");
        }
    }


    public function logout() {
        session_start(); 
        session_destroy(); 
        header("Location: " . BASE_URL . "home");
        exit();
    }
}
