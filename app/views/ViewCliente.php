<?php
class ViewCliente {
    public function showClients($clients){
        require_once './app/templates/listClients.phtml';
    }
}