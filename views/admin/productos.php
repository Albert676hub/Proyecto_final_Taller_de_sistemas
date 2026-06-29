<?php
require_once '../../config/conexion.php';
include '../layout/header.php';

$baseDatos = new Conexion();
$db = $baseDatos->obtenerConexion();

// Consulta relacional para extraer los detalles comerciales de los productos activos
$consulta = "SELECT p.id, p.nombre, p.precio, p.stock, c.nombre AS categoria 
             FROM productos p 
             INNER JOIN categorias c ON p.id_categoria = c.id 
             WHERE p.estado = 1
             ORDER BY p.id DESC";
$sentencia = $db->query($consulta);
$productos = $sentencia->fetchAll();
?>

<div class="contenedor-principal-amplio">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <button onclick="window.location.href='index.php'" class="btn-secundario" style="width: auto; padding: 8px 20px; margin: 0; font-weight: 600;">
                ⬅ Volver al Menú de Elección
            </button>
        </div>
        <button onclick="window.location.href='producto_formulario.php'" class="btn-principal" style="width: auto; padding: 10px 20px; background-color: #28a745; margin: 0;">
            + Nuevo Producto
        </button>
    </div>

    <h2>Inventario General de Productos</h2>
    <p style="color: #666; margin-bottom: 20px;">Supervisión técnica de niveles de stock operativo distribuidos por categorías comerciales.</p>

    <?php if (isset($_GET['mensaje'])): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95rem;">
            <?php 
                if ($_GET['mensaje'] === 'creado') echo 'Producto registrado exitosamente en el catálogo.';
                if ($_GET['mensaje'] === 'actualizado') echo 'Los datos y existencias del producto fueron actualizados correctamente.';
            ?>
        </div>
    <?php endif; ?>

    <div class="tarjeta-producto" style="text-align: left; padding: 2rem; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; background-color: #f8f9fa;">
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Identificador</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Descripción del Artículo</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Categoría Asociada</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Precio Base</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Unidades Disponibles</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($productos) > 0): ?>
                    <?php foreach($productos as $producto): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px; color: #888;"><?php echo $producto['id']; ?></td>
                            <td style="padding: 12px;"><strong><?php echo htmlspecialchars($producto['nombre']); ?></strong></td>
                            <td style="padding: 12px; color: #555;"><?php echo htmlspecialchars($producto['categoria']); ?></td>
                            <td style="padding: 12px; font-weight: 600;">Bs. <?php echo number_format($producto['precio'], 2); ?></td>
                            <td style="padding: 12px;">
                                <?php if($producto['stock'] > 10): ?>
                                    <span style="background-color: #e2e3e5; color: #383d41; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">
                                        <?php echo $producto['stock']; ?> Unidades
                                    </span>
                                <?php else: ?>
                                    <span style="background-color: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: bold;">
                                        Mínimo Crítico: <?php echo $producto['stock']; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px;">
                                <button onclick="window.location.href='producto_formulario.php?id=<?php echo $producto['id']; ?>'" style="background: none; border: none; color: #007bff; cursor: pointer; font-size: 1rem;" title="Editar producto u optimizar inventario">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #888;">No existen artículos dados de alta en el catálogo base.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
include '../layout/footer.php'; 
?>