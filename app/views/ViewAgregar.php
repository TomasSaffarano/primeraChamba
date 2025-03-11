<?php
class ViewAgregar {
    // Mostrar todos los clientes
    public function mostrarCalendario() {
        require_once './app/templates/calendario.phtml';
    }
    public function mostrarformulario($fechaIngreso){
        require_once './app/templates/agregarForm.phtml';    
    }
}