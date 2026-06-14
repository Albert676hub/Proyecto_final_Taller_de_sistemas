<?php
session_start();

// Si el usuario no ha iniciado sesion, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: views/auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Sistema de Ventas</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body class="fondo-centrado">
    <div class="tarjeta-login" style="text-align: center;">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h2>
        <p>Sistema central de ventas.</p>
        <br>
        <button onclick="window.location.href='views/tienda/checkout.php'" class="btn-principal">Ir a Pasarela de Pago</button>
        <br>
        <a href="controllers/logout.php" style="display:block; margin-top:15px; color:#dc3545; text-decoration:none;">Cerrar Sesion</a>
    </div>
</body>
</html>