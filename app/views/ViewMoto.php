<?php
class ViewMoto {
    public function showMotos($motos, $motoAEditar = null) {
        require_once './app/templates/listMotos.phtml';
    }
    public function addMoto() {
        require_once './app/templates/listMotos.phtml';
    }
    public function showMoto($moto) {
        require_once './app/templates/Moto.phtml';
    }

    public function showError($error = '') {
        $motos = []; // En caso de error, puede que no haya motos
        require_once './app/templates/listMotos.phtml';
    }
}
