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

$data = json_decode(file_get_contents('php://input'), true);
$codigo = trim($data['codigo'] ?? '');

if (empty($codigo)) {
    echo json_encode(['success' => false, 'error' => 'Código no proporcionado']);
    exit;
}

// verificar cupón en db
try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM cupones WHERE codigo = ? AND fecha_inicio <= NOW() AND fecha_fin >= NOW()");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $cupon = $stmt->get_result()->fetch_assoc();
    
    if ($cupon) {
        $_SESSION['cupon'] = [
            'id' => $cupon['id'],
            'codigo' => $cupon['codigo'],
            'descuento' => $cupon['descuento'],
            'tipo' => $cupon['tipo'] 
        ];
        
        echo json_encode([
            'success' => true,
            'descuento' => $cupon['tipo'] === 'porcentaje' ? $cupon['descuento'].'%' : '$'.$cupon['descuento'],
            'message' => 'Cupón aplicado correctamente'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Cupón no válido o expirado']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar cupón']);
}
?>