<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/funciones.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// vaciar carrito en sesión
$_SESSION['cart'] = [];
$_SESSION['cart_count'] = 0;

// si está logueado, vaciar también en base de datos
if (isset($_SESSION['usuario_id'])) {
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("DELETE FROM carrito_items WHERE carrito_id = ?");
        $stmt->bind_param("i", $_SESSION['carrito_id']);
        $stmt->execute();
    } catch (Exception $e) {
        // registrar error
        error_log("Error al vaciar carrito: " . $e->getMessage());
    }
}

echo json_encode([
    'success' => true,
    'cart_count' => 0,
    'message' => 'Carrito vaciado correctamente'
]);
?>