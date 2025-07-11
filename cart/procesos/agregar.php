<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/funciones.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['product_id'] ?? null;
$quantity = $data['quantity'] ?? 1;

if (!$productId) {
    echo json_encode(['success' => false, 'error' => 'ID de producto no proporcionado']);
    exit;
}

// iniciar carrito en sesión si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    $_SESSION['cart_count'] = 0;
}

// verificar si el producto ya está en el carrito
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['producto_id'] == $productId) {
        $item['cantidad'] += $quantity;
        $found = true;
        break;
    }
}

// si no existe agregarlo
if (!$found) {
    $_SESSION['cart'][] = [
        'producto_id' => $productId,
        'cantidad' => $quantity,
        'atributos' => $data['atributos'] ?? null
    ];
}

// actualizar el contador
$_SESSION['cart_count'] = array_reduce($_SESSION['cart'], function($total, $item) {
    return $total + $item['cantidad'];
}, 0);

echo json_encode([
    'success' => true,
    'cart_count' => $_SESSION['cart_count'],
    'message' => 'Producto añadido al carrito'
]);
?>