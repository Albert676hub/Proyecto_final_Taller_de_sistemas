<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['usuario_id'])) {
    
    $datos_json = json_decode(file_get_contents("php://input"), true);
    
    $id_usuario = $_SESSION['usuario_id'];
    $metodo_pago = isset($datos_json['metodo']) ? $datos_json['metodo'] : 'desconocido';
    $tiempo_segundos = isset($datos_json['tiempo_segundos']) ? floatval($datos_json['tiempo_segundos']) : 0;
    
    // Calculamos el total real basándonos en la sesión del carrito de forma segura,
    // evitando que el usuario altere el precio desde el navegador.
    $total_real = 0;
    if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $item) {
            $total_real += ($item['precio'] * $item['cantidad']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'El carrito está vacío.']);
        exit;
    }

    $baseDatos = new Conexion();
    $db = $baseDatos->obtenerConexion();

    try {
        $db->beginTransaction();

        // 1. Insertar pedido principal
        $consultaPedido = "INSERT INTO pedidos (id_usuario, total, metodo_pago, estado_pago) 
                           VALUES (:id_usuario, :total, :metodo_pago, 'pagado')";
        $stmt = $db->prepare($consultaPedido);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':total' => $total_real,
            ':metodo_pago' => $metodo_pago
        ]);

        $id_pedido = $db->lastInsertId();

        // 2. Insertar detalles del pedido y restar stock
        $consultaDetalle = "INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad, precio_unitario) 
                            VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)";
        $stmtDetalle = $db->prepare($consultaDetalle);

        $consultaStock = "UPDATE productos SET stock = stock - :cantidad WHERE id = :id_producto";
        $stmtStock = $db->prepare($consultaStock);

        foreach ($_SESSION['carrito'] as $id_producto => $item) {
            $stmtDetalle->execute([
                ':id_pedido' => $id_pedido,
                ':id_producto' => $id_producto,
                ':cantidad' => $item['cantidad'],
                ':precio_unitario' => $item['precio']
            ]);

            $stmtStock->execute([
                ':cantidad' => $item['cantidad'],
                ':id_producto' => $id_producto
            ]);
        }

        // 3. Registrar estadísticas de tiempo para tu Dashboard
        $consultaStats = "INSERT INTO estadisticas_desempeno (id_pedido, tiempo_segundos) VALUES (:id_pedido, :tiempo_segundos)";
        $stmtStats = $db->prepare($consultaStats);
        $stmtStats->execute([
            ':id_pedido' => $id_pedido,
            ':tiempo_segundos' => $tiempo_segundos
        ]);

        $db->commit();
        
        // ==========================================
        // VACIADO AUTOMÁTICO DEL CARRITO
        // ==========================================
        unset($_SESSION['carrito']); 
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Pago validado correctamente.'
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error al procesar el pedido.'
        ]);
    }
} else {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
}
?>