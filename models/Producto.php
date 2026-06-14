<?php
require_once __DIR__ . '/../config/conexion.php';

class Producto {
    private $conexion;

    public function __construct() {
        $instancia = new Conexion();
        $this->conexion = $instancia->obtenerConexion();
    }

    public function obtenerCategorias() {
        $consulta = "SELECT id, nombre FROM categorias";
        $sentencia = $this->conexion->query($consulta);
        return $sentencia->fetchAll();
    }

    public function obtenerTodos($id_categoria = null) {
        $sql = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, p.imagen_url, c.nombre as categoria 
                FROM productos p 
                INNER JOIN categorias c ON p.id_categoria = c.id 
                WHERE p.estado = 1";
        
        if ($id_categoria != null) {
            $sql .= " AND p.id_categoria = :id_categoria";
        }

        $sentencia = $this->conexion->prepare($sql);

        if ($id_categoria != null) {
            $sentencia->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        }

        $sentencia->execute();
        return $sentencia->fetchAll();
    }
}
?>