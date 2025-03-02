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

    public function getTurns() {
        $query = $this->db->prepare("SELECT id, 
                                            DATE_FORMAT(ingreso, '%d/%m/%Y') AS ingreso, 
                                            DATE_FORMAT(entrega, '%d/%m/%Y') AS entrega, 
                                            patente 
                                     FROM turno");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function insertTurn($ingreso, $entrega, $patente){
        $query = $this->db->prepare('INSERT INTO turno(ingreso,entrega,patente) VALUES (?, ?, ?)');
        $query->execute([$ingreso,$entrega, $patente]);
        $id = $this->db->lastInsertId();
        return $id;
    }

    public function checkIDExists($id_turn){
        $query = $this->db->prepare("SELECT * FROM turno WHERE id = ?");
        $result = $query->execute([$id_turn]);
        return $query->fetchColumn() > 0;
    }

    
    public function eraseTurn($id){
        $query = $this->db->prepare('DELETE FROM turno WHERE id = ?');
        $result = $query->execute([$id]);
        return $result;
    }

    public function getTurn($id) {
        $query = $this->db->prepare("SELECT * FROM turno WHERE id = ?");
        $result = $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function updateTurn($id, $ingreso, $entrega, $patente) {
        $query = $this->db->prepare('UPDATE turno SET ingreso = ?, entrega = ?, patente = ? WHERE id = ?');
        $query->execute([$ingreso, $entrega, $patente,$id]);
            return true; 
     } 

    public function getTurnPatent($patent) {
        $query = $this->db->prepare("SELECT * FROM turno WHERE patente = ?");
        $result = $query->execute([$patent]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}
?>