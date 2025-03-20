<?php
require_once 'app/views/ViewCliente.php';

class ModelCliente {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;' . 'dbname=taller_moto;charset=utf8', 'root', '');
    }

    public function getClients(){
         $query = $this->db->prepare("SELECT * FROM cliente ORDER BY nombre ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function insertClient($name, $dni, $cellphone){
        $query = $this->db->prepare('INSERT INTO cliente(nombre,dni,telefono) VALUES (?, ?, ?)');
        $query->execute([$name,$dni, $cellphone]);
        $id = $this->db->lastInsertId();
        return $id;
    }

    public function obtenerClientePorDNI($dni) {
        $stmt = $this->db->prepare("SELECT * FROM cliente WHERE dni = ?");
        $stmt->execute([$dni]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function checkIDExists($id_client){
        $query = $this->db->prepare("SELECT * FROM cliente WHERE id = ?");
        $result = $query->execute([$id_client]);
        return $query->fetchColumn() > 0;
    }

    public function eraseClient($id){
        $query = $this->db->prepare('DELETE FROM cliente WHERE id = ?');
        $result = $query->execute([$id]);
        return $result;
    }

    public function getClient($id) {
        $query = $this->db->prepare("SELECT * FROM cliente WHERE id = ?");
        $result = $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function updateClient($id, $name, $dni, $cellphone) {
        $query = $this->db->prepare('UPDATE cliente SET nombre = ?, dni = ?, telefono = ? WHERE id = ?');
        $query->execute([$name, $dni, $cellphone,$id]);
                return true; 
     } 
     

     public function getClienteNombre($nombre) {
        $query = $this->db->prepare("SELECT * FROM cliente WHERE nombre LIKE ?");
        $query->execute(["%$nombre%"]);  // Se pasa el valor correctamente con los % dentro del array
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}   
?>
