<?php
// Asegurarnos de que la sesión esté iniciada solo si no lo está ya
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de Ventas</title>
    <link rel="stylesheet" href="/sistema_ventas/assets/css/estilo.css">
</head>
<body>
    <header class="cabecera-principal">
        <div class="contenedor-cabecera">
            <h1>Multi Shop</h1>
            <nav>
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <span class="bienvenida">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                    <a href="/sistema_ventas/views/tienda/index.php" class="enlace-nav">Catálogo</a>
                    
                    <div class="contenedor-carrito-nav">
                        <button id="btn-toggle-carrito" class="btn-carrito">
                            🛒 Carrito <span id="contador-carrito" class="badge">0</span>
                        </button>
                        
                        <div id="dropdown-carrito" class="dropdown-carrito oculto">
                            <div class="cabecera-carrito">
                                <h4>Tu Carrito</h4>
                            </div>
                            <div id="lista-carrito" class="lista-carrito">
                                </div>
                            <div class="pie-carrito">
                                <div class="total-carrito">Total: Bs. <span id="total-precio-carrito">0.00</span></div>
                                <a href="/sistema_ventas/views/tienda/checkout.php" id="btn-ir-pagar" class="btn-principal btn-bloque" onclick="verificarCarritoVacio(event)">Ir a Pagar</a>
                            </div>
                        </div>
                    </div>

                    <a href="/sistema_ventas/controllers/logout.php" class="enlace-nav btn-salir">Salir</a>
                <?php else: ?>
                    <a href="/sistema_ventas/views/auth/login.php" class="enlace-nav">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="contenedor-principal-amplio">