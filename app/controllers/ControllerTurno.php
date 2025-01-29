<?php

require_once 'app/views/ViewTurno.php';
require_once 'app/models/ModelTurno.php';

class ControllerTurno {
    private $view;
    private $model;

    public function __construct() {
        $this->view = new ViewTurno();
        $this->model = new ModelTurno();
    }

    public function showHome() {
        $this->view->showHome();
    }
}

