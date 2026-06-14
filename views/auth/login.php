<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema</title>
    <link rel="stylesheet" href="/sistema_ventas/assets/css/estilo.css">
</head>
<body class="fondo-centrado">
    <div class="tarjeta-login">
        <h2>Iniciar Sesión</h2>

        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'registro_exitoso'): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 0.9rem; text-align: center; border: 1px solid #c3e6cb;">
                Cuenta creada correctamente. Ya puede ingresar.
            </div>
        <?php endif; ?>

        <form action="../../controllers/auth_controller.php" method="POST">
            <div class="grupo-formulario">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required autocomplete="off">
            </div>
            
            <div class="grupo-formulario">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            
            <button type="submit" class="btn-principal">Ingresar</button>
        </form>

        <div style="margin: 20px 0; border-top: 1px solid #eee; position: relative; text-align: center;">
            <span style="position: absolute; top: -10px; background: #fff; padding: 0 10px; color: #aaa; font-size: 0.85rem; left: 50%; transform: translateX(-50%);">O</span>
        </div>

        <div style="text-align: center;">
            <p style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">¿No tienes una cuenta aún?</p>
            <button onclick="window.location.href='registro.php'" class="btn-secundario" style="margin-top: 0;">Crear una Cuenta</button>
        </div>
    </div>
</body>
</html>