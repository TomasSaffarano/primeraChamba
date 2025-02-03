<?php

require_once 'app/views/ViewCliente.php';
require_once 'app/models/ModelCliente.php';

class ControllerCliente {
    private $view;
    private $model;

    public function __construct() {
        $this->view = new ViewCliente();
        $this->model = new ModelCliente();
    }

    public function getAllClientes() {
        $clients = $this->model->getClients();
        $this->view->showClients($clients);
    }
}
