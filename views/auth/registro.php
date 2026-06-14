<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Sistema de Ventas</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
</head>
<body class="fondo-centrado">
    <div class="tarjeta-login">
        <h2>Crear Cuenta</h2>
        <form action="../../controllers/registro_controller.php" method="POST">
            <div class="grupo-formulario">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" required autocomplete="name">
            </div>
            <div class="grupo-formulario">
                <label for="correo">Correo Electronico</label>
                <input type="email" id="correo" name="correo" required autocomplete="email">
            </div>
            <div class="grupo-formulario">
                <label for="contrasena">Contrasena</label>
                <input type="password" id="contrasena" name="contrasena" minlength="6" required>
            </div>
            <button type="submit" class="btn-principal">Registrarse</button>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="login.php" style="color: var(--color-primario); text-decoration: none; font-size: 0.9rem;">
                    ¿Ya tienes cuenta? Inicia sesion
                </a>
            </div>
        </form>
    </div>
</body>
</html>     