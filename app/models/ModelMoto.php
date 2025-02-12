<?php

class ModelMoto {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=taller_moto;charset=utf8', 'root', '');
    }

    public function getMotos() {
        $query = $this->db->prepare("SELECT * FROM moto");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function insertMoto($modelo, $patente, $estado, $kilometros, $descripcion, $observaciones,$dni) {
        $query = $this->db->prepare("INSERT INTO moto (modelo, patente, estado, kilometros, descripcion, observaciones, dni) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?)");
        $query->execute([$modelo, $patente, $estado, $kilometros, $descripcion, $observaciones, $dni]);
    }

    public function getMotosByDNI($dni) {
        $query = $this->db->prepare("SELECT *  FROM moto WHERE dni = ?");
        $query->execute([$dni]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}

?>
