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
    public function obtenerClientePorDNI($dni) {
        $stmt = $this->db->prepare("SELECT * FROM cliente WHERE dni = ?");
        $stmt->execute([$dni]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
?>