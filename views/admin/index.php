<?php
// Utiliza el mismo encabezado global del sistema para mantener la consistencia visual
include '../layout/header.php';
?>

<div class="contenedor-principal-amplio">
    <h2>Panel de Control del Administrador</h2>
    <p style="margin-bottom: 30px; color: #666;">Seleccione el módulo operacional que desea gestionar en este momento.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 20px;">
        
        <div class="tarjeta-producto" style="text-align: center; cursor: pointer; padding: 2rem;" onclick="window.location.href='estadisticas.php'">
            <div style="font-size: 2.5rem; margin-bottom: 15px; color: #007bff;">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h3>Evaluación del Sistema</h3>
            <p style="color: #666; font-size: 0.9rem; margin-top: 10px; line-height: 1.4;">Análisis de tiempos de registro, intervalos de confianza y pruebas de hipótesis de rendimiento.</p>
            <button class="btn-principal" style="margin-top: 20px; pointer-events: none;">Acceder a Métricas</button>
        </div>

        <div class="tarjeta-producto" style="text-align: center; cursor: pointer; padding: 2rem;" onclick="window.location.href='ventas.php'">
            <div style="font-size: 2.5rem; margin-bottom: 15px; color: #28a745;">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3>Gestión de Ventas</h3>
            <p style="color: #666; font-size: 0.9rem; margin-top: 10px; line-height: 1.4;">Auditoría de pedidos completados, control de montos totales y validación de métodos de pago.</p>
            <button class="btn-principal" style="margin-top: 20px; background-color: #28a745; pointer-events: none;">Ver Transacciones</button>
        </div>

        <div class="tarjeta-producto" style="text-align: center; cursor: pointer; padding: 2rem;" onclick="window.location.href='productos.php'">
            <div style="font-size: 2.5rem; margin-bottom: 15px; color: #ffc107;">
                <i class="fas fa-boxes"></i>
            </div>
            <h3>Inventario de Productos</h3>
            <p style="color: #666; font-size: 0.9rem; margin-top: 10px; line-height: 1.4;">Monitoreo de niveles de existencias críticas, precios comerciales e información del catálogo.</p>
            <button class="btn-principal" style="margin-top: 20px; background-color: #ffc107; color: #333; pointer-events: none;">Administrar Stock</button>
        </div>

    </div>
</div>

<?php 
include '../layout/footer.php'; 
?>