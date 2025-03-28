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
    public function getMotoByIdInfo($id) {
        $query = $this->db->prepare("
            SELECT m.*, c.nombre AS nombre_cliente, c.telefono AS telefono_cliente
            FROM moto m
            JOIN cliente c ON m.dni = c.dni
            WHERE m.id = ?
        ");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ); // Devuelve un objeto en lugar de un array
    }

    public function insertMoto($modelo, $patente, $estado, $kilometros, $descripcion, $observaciones, $dni) {
        try {
            $query = $this->db->prepare("INSERT INTO moto (modelo, patente, estado, kilometros, descripcion, observaciones, dni) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?)");
            $result = $query->execute([$modelo, $patente, $estado, $kilometros, $descripcion, $observaciones, $dni]);
    
            if (!$result) {
                throw new Exception("Error al insertar la moto");
            }
    
            return $result;
        } catch (PDOException $e) {
            die("Error en la base de datos: " . $e->getMessage()); // Muestra el error específico de SQL
        }
    }
    

    public function getMotosByModelo($modelo) {
        $query = $this->db->prepare("SELECT *  FROM moto WHERE modelo = ?");
        $query->execute([$modelo]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    public function eliminarMoto($id) {
        $query = $this->db->prepare("DELETE FROM moto WHERE id = ?");
        return $query->execute([$id]); // Devuelve true si la eliminación fue exitosa, false si no
    }
    
    public function existePatente($patente) {
        $query = $this->db->prepare("SELECT COUNT(*) FROM moto WHERE patente = ?");
        $query->execute([$patente]);
        return $query->fetchColumn() > 0; 
    }
    


    public function getMotoByID($id) {
        $query = $this->db->prepare("SELECT * FROM moto WHERE id = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
    
    public function updateMoto($id, $modelo, $patente, $estado, $dni, $kilometros, $descripcion, $observaciones) {
        $query = $this->db->prepare("UPDATE moto SET modelo = ?, patente = ?, estado = ?, dni = ?, kilometros = ?, descripcion = ?, observaciones = ? WHERE id = ?");
        return $query->execute([$modelo, $patente, $estado, $dni, $kilometros, $descripcion, $observaciones, $id]);
    }

    public function getMotoByPatente($patente) {
        $query = $this->db->prepare("SELECT * FROM moto WHERE patente = ?");
        $query->execute([$patente]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
    public function getMotosByDNI($dni) {
        $query = $this->db->prepare("SELECT * FROM moto WHERE dni= ?");
        $query->execute([$dni]);
        return $query->fetchAll(PDO::FETCH_OBJ); // Esto devuelve un array de objetos
    }
    
}
?>
