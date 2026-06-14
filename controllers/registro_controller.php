<?php
session_start();
require_once '../models/Usuario.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpieza de datos (Sanitizacion)
    $nombre = htmlspecialchars(strip_tags(trim($_POST['nombre'])));
    $correo = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
    $contrasena = trim($_POST['contrasena']);

    // Validacion de servidor
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Error: Formato de correo invalido.");
    }

    if (strlen($contrasena) < 6) {
        die("Error: La contrasena debe tener al menos 6 caracteres.");
    }

    $modeloUsuario = new Usuario();

    // Verificamos inyecciones duplicadas
    if ($modeloUsuario->existeCorreo($correo)) {
        die("Error: El correo ingresado ya se encuentra registrado en el sistema.");
    }

    // Registramos al usuario
    if ($modeloUsuario->registrar($nombre, $correo, $contrasena)) {
        // Redirigimos al login con exito
        header("Location: ../views/auth/login.php?mensaje=registro_exitoso");
        exit;
    } else {
        echo "Error critico al registrar el usuario en la base de datos.";
    }
}
?>