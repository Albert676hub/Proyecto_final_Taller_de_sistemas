<?php
require_once __DIR__ . '/../config/conexion.php';

class Usuario {
    private $conexion;

    public function __construct() {
        $instancia = new Conexion();
        $this->conexion = $instancia->obtenerConexion();
    }

    // Metodo para registrar un nuevo usuario con contrasena encriptada
    public function registrar($nombre, $correo, $contrasena) {
        try {
            // El id_rol 2 corresponde a 'cliente' segun nuestra base de datos
            $consulta = "INSERT INTO usuarios (id_rol, nombre, correo, contrasena) VALUES (2, :nombre, :correo, :contrasena)";
            $sentencia = $this->conexion->prepare($consulta);
            
            // Encriptacion robusta usando BCRYPT
            $hash = password_hash($contrasena, PASSWORD_BCRYPT);
            
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $sentencia->bindParam(':correo', $correo, PDO::PARAM_STR);
            $sentencia->bindParam(':contrasena', $hash, PDO::PARAM_STR);
            
            return $sentencia->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Metodo para verificar si un correo ya existe antes de registrar
    public function existeCorreo($correo) {
        $consulta = "SELECT id FROM usuarios WHERE correo = :correo";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':correo', $correo, PDO::PARAM_STR);
        $sentencia->execute();
        
        return $sentencia->rowCount() > 0;
    }
}
?>