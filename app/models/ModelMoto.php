<?php
require_once 'app/views/ViewMoto.php';

class ModelMoto {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=taller_moto;charset=utf8', 'root', '');
    }

    public function getMotos(){
        $query = $this->db->prepare("SELECT * FROM moto");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    public function insertMoto($modelo, $patente, $estado, $kilometros, $descripcion, $observaciones) {
        $query = $this->db->prepare("INSERT INTO moto (modelo, patente, estado, kilometros, descripcion, observaciones) VALUES (?, ?, ?, ?, ?, ?)");
        $query->execute([$modelo, $patente, $estado, $kilometros, $descripcion, $observaciones]);
    }
}
?>