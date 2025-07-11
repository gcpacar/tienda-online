<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/funciones.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

unset($_SESSION['cupon']);

echo json_encode([
    'success' => true,
    'message' => 'Cupón removido correctamente'
]);
?>