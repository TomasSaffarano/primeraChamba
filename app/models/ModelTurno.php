<?php
require_once 'app/views/ViewTurno.php';
class ModelTurno {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=taller_moto;charset=utf8', 'root', '');
    }


    public function createTurno($fechaIngreso, $fechaEntrega, $idCliente) {
        $query = $this->db->prepare("INSERT INTO turnos (fecha_ingreso, fecha_entrega, id_cliente) VALUES (?, ?, ?)");
        $query->execute([$fechaIngreso, $fechaEntrega, $idCliente]);
        return $this->db->lastInsertId(); 
    }

    public function getTurnoById($id) {
        $query = $this->db->prepare("SELECT * FROM turnos WHERE id = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function getTurnosByCliente($idCliente) {
        $query = $this->db->prepare("SELECT * FROM turnos WHERE id_cliente = ?");
        $query->execute([$idCliente]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}
?>