<?php

require_once 'app/views/ViewTurno.php';
require_once 'app/models/ModelTurno.php';

class ControllerTurno {
    private $view;
    private $model;
    private $error;

    public function __construct() {
        $this->view = new ViewTurno();
        $this->model = new ModelTurno();
        $this->error = new ErrorController();
    }

    public function showHome() {
        $this->view->showHome();
    }

    public function getAllTurnos() {
        $turns = $this->model->getTurns();
        $this->view->showTurns($turns);
    }
}

