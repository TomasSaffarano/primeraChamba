<?php
class ViewMoto {
    public function showMotos($motos){
        require_once './app/templates/listMotos.phtml';
    }
    public function showError($message) {
        echo "<h2>Error: $message</h2>";
    }
}