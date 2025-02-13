<?php
require_once './app/views/successView.php';
class SuccessControler{
    private $view;
    public function __construct() 
    {
    $this->view = new SuccessView();

    }

    public function showSuccess(){
        $this->view->seeSuccess();
    }
}