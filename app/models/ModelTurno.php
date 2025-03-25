<?php
require_once 'app/views/ViewTurno.php';

class ModelTurno {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=taller_moto;charset=utf8', 'root', '');
    }

    // ✅ Verificar si ya existe un turno para la misma moto en la misma fecha y hora
    public function existsTurno($ingreso, $hora, $patente) {
        $query = $this->db->prepare("SELECT COUNT(*) FROM turno WHERE ingreso = ? AND hora = ? AND patente = ?");
        $query->execute([$ingreso, $hora, $patente]);
        return $query->fetchColumn() > 0;
    }

    // ✅ Insertar un nuevo turno si no está duplicado
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

    // ✅ Obtener todos los turnos
    public function getTurnos() {
        $query = $this->db->prepare("SELECT * FROM turno");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    

    // ✅ Obtener turnos en formato JSON para FullCalendar
    public function getTurnosForCalendar() {
        $sql ="SELECT turno.id, turno.ingreso, turno.hora, turno.patente, cliente.nombre AS nombreCliente
        FROM turno
        JOIN moto ON turno.patente = moto.patente
        JOIN cliente ON moto.dni = cliente.dni";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function eraseTurn($id){
        $query = $this->db->prepare('DELETE FROM turno WHERE id = ?');
        $result = $query->execute([$id]);
        return $result;
    }
    public function checkIDExists($id){
        $query = $this->db->prepare("SELECT * FROM turno WHERE id = ?");
        $result = $query->execute([$id]);
        return $query->fetchColumn() > 0;
    }
    public function getTurn($id) {
        $query = $this->db->prepare("SELECT * FROM turno WHERE id = ?");
        $result = $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
    public function getTurns(){
        $query = $this->db->prepare("SELECT * FROM turno");
       $query->execute();
       return $query->fetchAll(PDO::FETCH_OBJ);
   }

   public function updateTurn($id, $ingreso, $entrega, $patente, $hora) {
    $query = "UPDATE turno SET ingreso=?, entrega=?, patente=?, hora=? WHERE id=?";
    $stmt = $this->db->prepare($query);
    return $stmt->execute([$ingreso, $entrega, $patente, $hora, $id]);
}
public function getTurnoByPatente($patente) {
    $query = $this->db->prepare("SELECT * FROM turno WHERE patente = ?");
    $query->execute([$patente]); // Pasar como array
    return $query->fetchAll(PDO::FETCH_OBJ);
}

}
?>
