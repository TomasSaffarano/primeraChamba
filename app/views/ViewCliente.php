<?php
class ViewCliente {
    // Mostrar todos los clientes
    public function showClients($clients) {
        require_once './app/templates/listClients.phtml';
    }

    // Mostrar formulario para agregar un cliente
    public function addClient() {
        require_once './app/templates/listClients.phtml';
    }

    // Mostrar formulario para editar un cliente (con los datos del cliente)
    public function showClientForm($client = null, $isEdit = false, $clients = []) {
        require_once './app/templates/listClients.phtml';
    }
    
}