<?php
class ErrorView{
    
    public function seeError($error, $redir){
        require_once 'app/templates/error.phtml';
    
    }
    public function showError($mensaje, $redir) {
        $_SESSION['error_message'] = $mensaje;
        header('Location: ' . BASE_URL . $redir);
        exit();
    }
}