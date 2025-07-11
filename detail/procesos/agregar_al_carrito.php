<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../../shared/plantilla_cabecera.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /shop');
    exit;
}

$productId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$attributes = $_POST['atributos'] ?? [];

if (!$productId || !$quantity) {
    $_SESSION['flash_message'] = 'Datos del producto inválidos';
    $_SESSION['flash_type'] = 'danger';
    header("Location: /detail/$productId");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Busco si el producto ya está en el carrito con los mismos atributos
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['producto_id'] == $productId && json_encode($item['atributos']) === json_encode($attributes)) {
        $item['cantidad'] += $quantity;
        $found = true;
        break;
    }
}

// Si no esta agrego nuevo item
if (!$found) {
    $_SESSION['cart'][] = [
        'producto_id' => $productId,
        'cantidad' => $quantity,
        'atributos' => $attributes,
        'agregado_en' => date('Y-m-d H:i:s')
    ];
}

// Actualizo contador
$_SESSION['cart_count'] = array_reduce($_SESSION['cart'], function($carry, $item) {
    return $carry + $item['cantidad'];
}, 0);

// Redirigo al carrito
$_SESSION['flash_message'] = 'Producto agregado al carrito';
$_SESSION['flash_type'] = 'success';
header('Location: /cart');
exit;
?>