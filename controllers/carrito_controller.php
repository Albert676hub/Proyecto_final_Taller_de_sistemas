<?php
session_start();

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents('php://input'), true);
    $accion = $datos['accion'] ?? '';

    switch ($accion) {
        case 'agregar':
            $id = $datos['id'];
            if (isset($_SESSION['carrito'][$id])) {
                $_SESSION['carrito'][$id]['cantidad'] += 1;
            } else {
                $_SESSION['carrito'][$id] = [
                    'nombre' => $datos['nombre'],
                    'precio' => $datos['precio'],
                    'cantidad' => 1
                ];
            }
            break;

        case 'eliminar':
            $id = $datos['id'];
            if (isset($_SESSION['carrito'][$id])) {
                unset($_SESSION['carrito'][$id]);
            }
            break;

        case 'actualizar':
            $id = $datos['id'];
            $cantidad = intval($datos['cantidad']);
            if ($cantidad > 0 && isset($_SESSION['carrito'][$id])) {
                $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
            } else if ($cantidad <= 0) {
                unset($_SESSION['carrito'][$id]);
            }
            break;
    }

    // Calcular totales para enviarlos de vuelta al frontend
    $total_articulos = 0;
    $total_precio = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total_articulos += $item['cantidad'];
        $total_precio += ($item['precio'] * $item['cantidad']);
    }

    echo json_encode([
        'status' => 'success',
        'carrito' => $_SESSION['carrito'],
        'total_articulos' => $total_articulos,
        'total_precio' => number_format($total_precio, 2)
    ]);
    exit;
}
?>