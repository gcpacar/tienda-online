<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/funciones.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$itemKey = $data['item_key'] ?? null;

if ($itemKey === null) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// eliminar de la sesión
if (isset($_SESSION['cart'][$itemKey])) {
    unset($_SESSION['cart'][$itemKey]);
    $_SESSION['cart_count'] = array_reduce($_SESSION['cart'], function($total, $item) {
        return $total + $item['cantidad'];
    }, 0);
    
    // si está logueado, eliminar también de la base de datos
    if (isset($_SESSION['usuario_id'])) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("DELETE FROM carrito_items WHERE id = ? AND carrito_id = ?");
            $stmt->bind_param("ii", $itemKey, $_SESSION['carrito_id']);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al eliminar item: " . $e->getMessage());
        }
    }
    
    echo json_encode([
        'success' => true,
        'cart_count' => $_SESSION['cart_count'],
        'message' => 'Producto eliminado del carrito'
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Item no encontrado']);
}
?>