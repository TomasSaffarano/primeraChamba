<?php
class ModelAdmin {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=taller_moto;charset=utf8', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function getUserByUserName($user_name) {
        $query = $this->db->prepare("SELECT * FROM admin WHERE usuario = ?");
        $query->execute([$user_name]);

        return $query->fetch(PDO::FETCH_OBJ);
    }
}
