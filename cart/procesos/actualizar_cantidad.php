<?php
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/funciones.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Datos JSON inválidos', 400);
    }

    if (empty($data['item_key']) || !isset($data['quantity'])) {
        throw new Exception('Datos incompletos', 400);
    }

    $cart = new CartFunctions();
    $success = $cart->updateCartItem($data['item_key'], (int)$data['quantity']);

    if (!$success) {
        throw new Exception('No se pudo actualizar el item', 500);
    }

    echo json_encode([
        'success' => true,
        'cart_count' => $_SESSION['cart_count'] ?? 0,
        'message' => 'Cantidad actualizada'
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode() ?: 500
    ]);
}
?>