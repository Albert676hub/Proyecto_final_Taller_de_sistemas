<?php
require_once '../../config/conexion.php';
include '../layout/header.php';

$baseDatos = new Conexion();
$db = $baseDatos->obtenerConexion();

// Consulta estructurada para asociar el pedido con la información de la cuenta de usuario
$consulta = "SELECT p.id, p.total, p.metodo_pago, p.fecha_pedido, u.nombre AS cliente 
             FROM pedidos p 
             INNER JOIN usuarios u ON p.id_usuario = u.id 
             ORDER BY p.fecha_pedido DESC";
$sentencia = $db->query($consulta);
$pedidos = $sentencia->fetchAll();
?>

<div class="contenedor-principal-amplio">
    <div style="margin-bottom: 20px;">
        <button onclick="window.location.href='index.php'" class="btn-secundario" style="width: auto; padding: 8px 20px; margin: 0; font-weight: 600;">
            Volver al Menú de Elección
        </button>
    </div>

    <h2>Control de Ventas y Transacciones</h2>
    <p style="color: #666; margin-bottom: 20px;">Historial detallado de los pedidos procesados de manera segura en la pasarela.</p>
    
    <div class="tarjeta-producto" style="text-align: left; padding: 2rem; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; background-color: #f8f9fa;">
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Código de Pedido</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Nombre del Cliente</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Fecha y Hora</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Método Utilizado</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Importe Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($pedidos) > 0): ?>
                    <?php foreach($pedidos as $pedido): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;"><strong>#<?php echo str_pad($pedido['id'], 5, "0", STR_PAD_LEFT); ?></strong></td>
                            <td style="padding: 12px; color: #555;"><?php echo htmlspecialchars($pedido['cliente']); ?></td>
                            <td style="padding: 12px; color: #666;"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></td>
                            <td style="padding: 12px; text-transform: uppercase; font-size: 0.9rem;"><?php echo htmlspecialchars($pedido['metodo_pago']); ?></td>
                            <td style="padding: 12px; color: #28a745; font-weight: bold;">Bs. <?php echo number_format($pedido['total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: #888;">No se registran transacciones en el sistema comercial.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
include '../layout/footer.php'; 
?>