<?php
// Clase para gestionar la conexion a la base de datos de forma segura
class Conexion {
    private $host = "localhost";
    private $usuario = "root";
    private $contrasena = "";
    private $nombre_bd = "sistema_ventas";
    private $conexion;

    public function obtenerConexion() {
        $this->conexion = null;

        try {
            // Configuracion del DSN y opciones de seguridad PDO
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->nombre_bd . ";charset=utf8mb4";
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Desactiva la emulacion para mayor seguridad contra inyeccion SQL
            ];
            
            $this->conexion = new PDO($dsn, $this->usuario, $this->contrasena, $opciones);
            
        } catch(PDOException $excepcion) {
            // En un entorno de produccion, esto debe registrarse en un log de errores, no mostrarse al usuario.
            die("Error critico de conexion: " . $excepcion->getMessage());
        }

        return $this->conexion;
    }
}
?>