<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../funciones.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    $_SESSION['flash_message'] = 'No se puede procesar el pedido';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /cart');
    exit;
}

$shipping = $_POST['shipping'] ?? [];
$billing = $_POST['billing'] ?? [];
$paymentMethod = $_POST['payment_method'] ?? 'tarjeta';
$orderNotes = $_POST['order_notes'] ?? '';
$userEmail = $_POST['email'] ?? ($_SESSION['user']['email'] ?? '');

if (empty($shipping['first_name']) || empty($shipping['address']) || empty($shipping['city']) || 
    empty($shipping['state']) || empty($shipping['zip']) || empty($userEmail)) {
    $_SESSION['flash_message'] = 'Por favor complete todos los campos requeridos';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /checkout');
    exit;
}

// si la dirección de facturación es diferente, validar los campos
if (empty($_POST['sameBillingAddress']) && 
    (empty($billing['first_name']) || empty($billing['address']) || 
     empty($billing['city']) || empty($billing['state']) || empty($billing['zip']))) {
    $_SESSION['flash_message'] = 'Por favor complete todos los campos de facturación';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /checkout');
    exit;
}

$checkout = new CheckoutFunctions();
$checkoutData = $checkout->getCheckoutData();

if (!$checkoutData) {
    $_SESSION['flash_message'] = 'Error al procesar tu pedido. Por favor intenta nuevamente.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /checkout');
    exit;
}

// preparar datos de la orden
$orderData = [
    'usuario_id' => $_SESSION['user']['id'] ?? null,
    'subtotal' => $checkoutData['subtotal'],
    'tax' => $checkoutData['tax'],
    'shipping' => $checkoutData['shipping'],
    'total' => $checkoutData['total'],
    'shipping_address' => implode(', ', [
        $shipping['first_name'] . ' ' . ($shipping['last_name'] ?? ''),
        $shipping['address'],
        $shipping['address2'] ?? '',
        $shipping['city'],
        $shipping['state'],
        $shipping['zip']
    ]),
    'billing_address' => !empty($_POST['sameBillingAddress']) ? 
        'Misma que dirección de envío' : 
        implode(', ', [
            $billing['first_name'] . ' ' . ($billing['last_name'] ?? ''),
            $billing['address'],
            $billing['address2'] ?? '',
            $billing['city'],
            $billing['state'],
            $billing['zip']
        ]),
    'payment_method' => $paymentMethod,
    'notes' => $orderNotes,
    'items' => $checkoutData['items']
];

// procesar orden
$orderId = $checkout->processOrder($orderData);

if ($orderId) {
    unset($_SESSION['cart']);
    unset($_SESSION['cart_count']);
    $_SESSION['last_order_id'] = $orderId;
    header('Location: /order-confirmation');
    exit;
} else {
    $_SESSION['flash_message'] = 'Hubo un error al procesar tu pedido. Por favor intenta nuevamente.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /checkout');
    exit;
}
?>