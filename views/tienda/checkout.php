<?php
session_start();
// Validar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// NUEVO: Bloquear acceso si el carrito está vacío
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../layout/header.php'; // Reutilizamos el header para mantener el carrito visible
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="contenedor-principal-amplio">
    <div class="tarjeta-checkout">
        <h2 style="text-align:center; margin-bottom: 10px;">Finalizar Compra</h2>
        <p style="text-align:center; color:#666; margin-bottom: 30px;">Seleccione un método de pago seguro</p>
        
        <div class="opciones-pago">
            <button id="btn-tarjeta" onclick="mostrarMetodo('tarjeta')" class="btn-pago activo">
                <i class="fas fa-credit-card fa-2x"></i>
                Tarjeta de Débito/Crédito
            </button>
            <button id="btn-qr" onclick="mostrarMetodo('qr')" class="btn-pago">
                <i class="fas fa-qrcode fa-2x"></i>
                Pago QR Simple
            </button>
        </div>

        <div id="pago-tarjeta" class="metodo-pago">
            <form id="formulario-tarjeta" onsubmit="procesarTarjeta(event)">
                <div class="grupo-formulario">
                    <label for="titular">Nombre en la tarjeta</label>
                    <input type="text" id="titular" class="form-control" placeholder="Ej. Juan Pérez" required>
                </div>
                <div class="grupo-formulario">
                    <label for="numero_tarjeta">Número de Tarjeta</label>
                    <input type="text" id="numero_tarjeta" class="form-control" maxlength="16" placeholder="0000 0000 0000 0000" required>
                </div>
                <div class="fila-formulario">
                    <div class="grupo-formulario" style="flex: 1;">
                        <label for="fecha_vencimiento">Vencimiento</label>
                        <input type="text" id="fecha_vencimiento" class="form-control" maxlength="5" placeholder="MM/AA" required>
                    </div>
                    <div class="grupo-formulario" style="flex: 1;">
                        <label for="cvv">Código de Seguridad (CVV)</label>
                        <input type="password" id="cvv" class="form-control" maxlength="3" placeholder="***" required>
                    </div>
                </div>
                <button type="submit" class="btn-principal" style="padding: 15px; font-size:1.1rem; border-radius: 8px;">
                    <i class="fas fa-lock"></i> Pagar y Finalizar
                </button>
            </form>
        </div>

        <div id="pago-qr" class="metodo-pago oculto" style="text-align: center;">
            <p>Escanee este código desde su aplicación financiera.</p>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; display:inline-block; margin: 20px 0;">
                <i class="fas fa-qrcode" style="font-size: 150px; color: #333;"></i>
            </div>
            <br>
            <button onclick="procesarTarjeta(new Event('submit'))" class="btn-principal" style="max-width: 300px;">
                Ya realicé el pago
            </button>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>