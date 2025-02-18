<?php
class ViewCliente {
    public function showClients($clients){
        require_once './app/templates/listClients.phtml';
    }

    public function addClient()
    {
        require_once 'app/templates/formClient.phtml';
    }

    public function showClientForm($client = null, $isEdit = false)
    {
        require_once 'app/templates/formClient.phtml';
    }
}