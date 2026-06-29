<?php
require_once '../../models/Producto.php';
include '../layout/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$modeloProducto = new Producto();

$categorias = $modeloProducto->obtenerCategorias();

$categoria_actual =
isset($_GET['categoria'])
? intval($_GET['categoria'])
: null;

$productos =
$modeloProducto->obtenerTodos($categoria_actual);

?>

<div class="encabezado-tienda">

    <h2>Catálogo de Productos</h2>

    <div class="filtros-categoria">

        <a
        href="index.php"

        class="btn-filtro
        <?php echo $categoria_actual==null ? 'activo' : ''; ?>">

        Todos

        </a>


        <?php foreach($categorias as $cat): ?>

        <a

        href="index.php?categoria=<?php echo $cat['id']; ?>"

        class="btn-filtro
        <?php echo $categoria_actual==$cat['id'] ? 'activo':''; ?>">

        <?php echo htmlspecialchars($cat['nombre']); ?>

        </a>

        <?php endforeach; ?>

    </div>

</div>


<div class="cuadricula-productos">

<?php if(count($productos)>0): ?>

<?php foreach($productos as $producto): ?>

<div class="tarjeta-producto">


<div class="contenedor-imagen">

<?php if(!empty($producto['imagen_url'])): ?>

<img

src="/sistema_ventas/assets/img/<?php echo htmlspecialchars($producto['imagen_url']); ?>"

alt="<?php echo htmlspecialchars($producto['nombre']); ?>"

class="imagen-producto"

>

<?php else: ?>

<div class="imagen-placeholder">

Sin imagen

</div>

<?php endif; ?>

</div>


<h3>

<?php echo htmlspecialchars($producto['nombre']); ?>

</h3>


<p class="precio">

Bs.
<?php echo number_format($producto['precio'],2); ?>

</p>


<p class="stock">

Stock:
<?php echo htmlspecialchars($producto['stock']); ?>

</p>


<button

onclick="agregarAlCarrito(

<?php echo $producto['id']; ?>,

'<?php echo addslashes($producto['nombre']); ?>',

<?php echo $producto['precio']; ?>

)"

class="btn-principal btn-agregar"

>

Añadir al Carrito

</button>

</div>

<?php endforeach; ?>

<?php else: ?>

<p class="alerta-vacia">

No hay productos disponibles.

</p>

<?php endif; ?>

</div>


<?php include '../layout/footer.php'; ?>