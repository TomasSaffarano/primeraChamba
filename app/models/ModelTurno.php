<?php
require_once 'app/views/ViewTurno.php';

class ModelTurno {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=taller_moto;charset=utf8', 'root', '');
    }

    // ✅ Método para verificar si ya existe un turno para la misma moto en la misma fecha y hora
    public function existsTurno($ingreso, $hora, $patente) {
        $query = $this->db->prepare("SELECT COUNT(*) FROM turno WHERE ingreso = ? AND hora = ? AND patente = ?");
        $query->execute([$ingreso, $hora, $patente]);
        return $query->fetchColumn() > 0;
    }

    // ✅ Método para insertar un nuevo turno si no está duplicado
    public function createTurno($ingreso, $hora, $entrega, $patente) {
        try {
            $stmt = $this->db->prepare("INSERT INTO turno (ingreso, hora, entrega, patente) VALUES (?, ?, ?, ?)");
            $success = $stmt->execute([$ingreso, $hora, $entrega, $patente]);
    
            if (!$success) {
                error_log("Error al insertar turno: " . implode(" - ", $stmt->errorInfo()));
            }
    
            return $success;
        } catch (PDOException $e) {
            error_log("Excepción al insertar turno: " . $e->getMessage());
            return false;
        }
    }

    // ✅ Obtener turno por ID
    public function getTurnoById($id) {
        $query = $this->db->prepare("SELECT * FROM turno WHERE id = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    // ✅ Obtener todos los turnos de un cliente
    public function getTurnosByCliente($idCliente) {
        $query = $this->db->prepare("SELECT * FROM turno WHERE id_cliente = ?");
        $query->execute([$idCliente]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}
?>
