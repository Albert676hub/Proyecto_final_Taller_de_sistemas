<?php
require_once '../../config/conexion.php';
include '../layout/header.php';

// Verificación obligatoria de sesión para el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: ../auth/login.php");
    exit;
}

$baseDatos = new Conexion();
$db = $baseDatos->obtenerConexion();

// Extraer categorías dinámicamente para el menú desplegable
$consultaCategorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
$sentenciaCategorias = $db->query($consultaCategorias);
$categorias = $sentenciaCategorias->fetchAll();

// Inicialización de variables para el mapeo de datos del formulario
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : null;
$nombre = "";
$id_categoria = "";
$descripcion = "";
$precio = "";
$stock = "";
$modo_edicion = false;

// Si existe un identificador, cargamos la información del artículo correspondiente
if ($id_producto) {
    $modo_edicion = true;
    $consultaProducto = "SELECT nombre, id_categoria, descripcion, precio, stock FROM productos WHERE id = :id AND estado = 1 LIMIT 1";
    $stmt = $db->prepare($consultaProducto);
    $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $producto = $stmt->fetch();
        $nombre = $producto['nombre'];
        $id_categoria = $producto['id_categoria'];
        $descripcion = $producto['descripcion'];
        $precio = $producto['precio'];
        $stock = $producto['stock'];
    } else {
        die("El producto solicitado no existe o no se encuentra activo.");
    }
}
?>

<div class="contenedor-principal-amplio">
    <div style="margin-bottom: 20px;">
        <button onclick="window.location.href='productos.php'" class="btn-secundario" style="width: auto; padding: 8px 20px; margin: 0; font-weight: 600;">
            ⬅ Cancelar y Regresar
        </button>
    </div>

    <h2><?php echo $modo_edicion ? 'Modificar Producto / Control de Inventario' : 'Agregar Nuevo Producto al Catálogo'; ?></h2>
    <p style="color: #666; margin-bottom: 20px;">Complete los campos del formulario para actualizar el catálogo comercial de la plataforma.</p>

    <div class="tarjeta-producto" style="max-width: 600px; margin: 20px 0; text-align: left; padding: 2.5rem;">
        <form action="../../controllers/producto_controller.php" method="POST">
            <?php if ($modo_edicion): ?>
                <input type="hidden" name="id" value="<?php echo $id_producto; ?>">
            <?php endif; ?>

            <div class="grupo-formulario">
                <label for="nombre">Nombre del Producto</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($nombre); ?>" required autocomplete="off">
            </div>

            <div class="grupo-formulario">
                <label for="id_categoria">Categoría Comercial</label>
                <select id="id_categoria" name="id_categoria" class="form-control" style="height: auto; background-color: #fcfcfc;" required>
                    <option value="">-- Seleccione una opción --</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $id_categoria) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo-formulario">
                <label for="descripcion">Descripción Técnica / Detalles</label>
                <textarea id="descripcion" name="descripcion" class="form-control" style="resize: vertical; min-height: 100px;" required><?php echo htmlspecialchars($descripcion); ?></textarea>
            </div>

            <div class="fila-formulario">
                <div class="grupo-formulario" style="flex: 1;">
                    <label for="precio">Precio Unitario ($)</label>
                    <input type="number" id="precio" name="precio" class="form-control" step="0.01" min="0.10" value="<?php echo htmlspecialchars($precio); ?>" required>
                </div>
                <div class="grupo-formulario" style="flex: 1;">
                    <label for="stock">Unidades en Inventario (Stock)</label>
                    <input type="number" id="stock" name="stock" class="form-control" step="1" min="0" value="<?php echo htmlspecialchars($stock); ?>" required>
                </div>
            </div>

            <button type="submit" class="btn-principal" style="padding: 14px; font-size: 1rem; margin-top: 15px; border-radius: 6px;">
                <i class="fas fa-save"></i> <?php echo $modo_edicion ? 'Guardar Cambios Operacionales' : 'Dar de Alta Producto'; ?>
            </button>
        </form>
    </div>
</div>

<?php 
include '../layout/footer.php'; 
?>