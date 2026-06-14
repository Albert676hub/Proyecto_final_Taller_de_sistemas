<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizacion basica de entrada
    $correo = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
    $contrasena = trim($_POST['contrasena']);

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Formato de correo invalido.");
    }

    $baseDatos = new Conexion();
    $db = $baseDatos->obtenerConexion();

    // Consulta preparada para prevenir inyeccion SQL
    $consulta = "SELECT id, nombre, contrasena, id_rol FROM usuarios WHERE correo = :correo LIMIT 1";
    $sentencia = $db->prepare($consulta);
    $sentencia->bindParam(':correo', $correo, PDO::PARAM_STR);
    $sentencia->execute();

    if ($sentencia->rowCount() > 0) {
        $fila = $sentencia->fetch();
        
        // Verificacion segura de la contrasena hasheada
        if (password_verify($contrasena, $fila['contrasena'])) {
            session_regenerate_id(true);
            
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['usuario_nombre'] = $fila['nombre'];
            $_SESSION['usuario_rol'] = $fila['id_rol']; // 1 = Admin, 2 = Cliente
            
            // REDIRECCIÓN DINÁMICA SEGÚN EL ROL
            if ($_SESSION['usuario_rol'] == 1) {
                header("Location: ../views/admin/index.php");
            } else {
                header("Location: ../views/tienda/index.php");
            }
            exit;
        } else {
            echo "Credenciales incorrectas."; // Falla la contraseña
        }
    } else {
        echo "Credenciales incorrectas."; // No existe el correo
    }
}
?>