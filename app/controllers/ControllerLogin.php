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
                return $this->view->showLogin('Contraseña incorrecta');
            }
    
            // Iniciar sesión si la contraseña es correcta
            session_start();
            $_SESSION['ID_USER'] = $userFromDB->id;
            $_SESSION['NAME_USER'] = $userFromDB->usuario;
            header('Location: ' . BASE_URL);
            exit();
        } else {
            return $this->view->showLogin('El usuario no existe');
        }
    }
        

    public function logout() {
        session_start(); 
        session_destroy(); 
        header("Location: " . BASE_URL . "home");
        exit();
    }
}
