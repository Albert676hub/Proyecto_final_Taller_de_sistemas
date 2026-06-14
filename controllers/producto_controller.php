<?php
session_start();
require_once '../config/conexion.php';

// Validación de seguridad de la petición en el servidor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] == 1) {
    
    // Limpieza y sanitización de datos de entrada para mitigar vulnerabilidades XSS
    $id_producto = isset($_POST['id']) ? intval($_POST['id']) : null;
    $nombre = htmlspecialchars(strip_tags(trim($_POST['nombre'])));
    $id_categoria = intval($_POST['id_categoria']);
    $descripcion = htmlspecialchars(strip_tags(trim($_POST['descripcion'])));
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    // Validaciones básicas de integridad de negocio
    if (empty($nombre) || $id_categoria <= 0 || $precio <= 0 || $stock < 0) {
        die("Error: Los datos provistos no cumplen con los criterios mínimos del sistema.");
    }

    $baseDatos = new Conexion();
    $db = $baseDatos->obtenerConexion();

    try {
        if ($id_producto) {
            // MODO EDICIÓN: Consulta preparada para actualizar registros existentes de forma segura
            $sql = "UPDATE productos 
                    SET nombre = :nombre, id_categoria = :id_categoria, descripcion = :descripcion, precio = :precio, stock = :stock 
                    WHERE id = :id AND estado = 1";
            $sentencia = $db->prepare($sql);
            $sentencia->bindParam(':id', $id_producto, PDO::PARAM_INT);
            $mensaje_retorno = "actualizado";
        } else {
            // MODO CREACIÓN: Consulta preparada para insertar nuevos elementos en el inventario
            $sql = "INSERT INTO productos (id_categoria, nombre, descripcion, precio, stock, estado) 
                    VALUES (:id_categoria, :nombre, :descripcion, :precio, :stock, 1)";
            $sentencia = $db->prepare($sql);
            $mensaje_retorno = "creado";
        }

        // Mapeo seguro de parámetros PDO para anular intentos de inyección SQL
        $sentencia->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $sentencia->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $sentencia->bindParam(':precio', $precio);
        $sentencia->bindParam(':stock', $stock, PDO::PARAM_INT);

        if ($sentencia->execute()) {
            // Redirección con parámetro de confirmación exitosa
            header("Location: ../views/admin/productos.php?mensaje=" . $mensaje_retorno);
            exit;
        } else {
            echo "Error interno: No se pudo completar la operación en la base de datos.";
        }

    } catch (PDOException $e) {
        die("Error crítico del sistema: " . $e->getMessage());
    }
} else {
    // Respuesta HTTP en caso de intentos de accesos no autorizados al backend
    http_response_code(403);
    echo "Acceso denegado de forma explícita.";
}
?>