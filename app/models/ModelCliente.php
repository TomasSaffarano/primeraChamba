<?php
require_once 'app/views/ViewCliente.php';

class ModelCliente {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=taller_moto;charset=utf8', 'root', '');
    }

    public function getClients(){
        $query = $this->db->prepare("SELECT * FROM cliente");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function insertClient($name, $dni, $cellphone){
        $query = $this->db->prepare('INSERT INTO cliente(nombre,dni,telefono) VALUES (?, ?, ?)');
        $query->execute([$name,$dni, $cellphone]);
        $id = $this->db->lastInsertId();
        return $id;
    }

}
?>