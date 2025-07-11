<?php
require_once __DIR__ . '/funciones.php';
// Configuración base 
define('ROOT_DIR', dirname(__DIR__)); 
define('SHARED_DIR', ROOT_DIR . '/shared');
require_once __DIR__ . '/../config/paths.php';

// Control de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once SHARED_DIR . '/plantilla_cabecera.php';

$checkout = new CheckoutFunctions();
$data = $checkout->getCheckoutData();

// Si no hay items en el carrito redirigir
if (!$data || empty($data['items'])) {
    $_SESSION['flash_message'] = 'Tu carrito está vacío';
    $_SESSION['flash_type'] = 'warning';
    header('Location: /cart');
    exit;
}

$pageTitle = "Finalizar Compra";

?>

<div class="container py-5">
    <div class="row">
        <!-- resumen del pedido -->
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="h5 mb-0">Resumen de tu pedido</h3>
                </div>
                
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($data['items'] as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($item['producto']['nombre']) ?></h6>
                                    <small class="text-muted">
                                        <?= $item['cantidad'] ?> x $<?= number_format($item['precio_unitario'], 2) ?>
                                        <?php if (!empty($item['atributos'])): ?>
                                            <br>
                                            <?php foreach ($item['atributos'] as $attr => $value): ?>
                                                <small><?= ucfirst($attr) ?>: <?= htmlspecialchars($value) ?></small>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <span>$<?= number_format($item['total'], 2) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <table class="table mt-3">
                        <tbody>
                            <tr>
                                <th>Subtotal</th>
                                <td class="text-end">$<?= number_format($data['subtotal'], 2) ?></td>
                            </tr>
                            <tr>
                                <th>Envío</th>
                                <td class="text-end">$<?= number_format($data['shipping'], 2) ?></td>
                            </tr>
                            <tr>
                                <th>Impuestos</th>
                                <td class="text-end">$<?= number_format($data['tax'], 2) ?></td>
                            </tr>
                            <tr class="fw-bold">
                                <th>Total</th>
                                <td class="text-end">$<?= number_format($data['total'], 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- form de checkout -->
        <div class="col-lg-7">
            <form id="checkoutForm" action="/checkout/procesos/enviar_pedido.php" method="post">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Información de contacto</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($data['user'])): ?>
                            <div class="mb-3">
                                <p>Estás comprando como: <strong><?= htmlspecialchars($data['user']['nombre']) ?></strong></p>
                                <p>Email: <strong><?= htmlspecialchars($data['user']['email']) ?></strong></p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- dirección de envío -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Dirección de envío</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($data['user'])): ?>
                            <?php $addresses = $checkout->getUserAddresses($data['user']['id']); ?>
                            <?php if (!empty($addresses)): ?>
                                <div class="mb-3">
                                    <label class="form-label">Seleccionar dirección guardada</label>
                                    <select class="form-select mb-3" id="savedShippingAddress">
                                        <option value="">Nueva dirección</option>
                                        <?php foreach ($addresses as $address): ?>
                                            <option value="<?= $address['id'] ?>">
                                                <?= htmlspecialchars($address['nombre']) ?> - 
                                                <?= htmlspecialchars($address['direccion']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="shipping_first_name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="shipping_first_name" name="shipping[first_name]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_last_name" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="shipping_last_name" name="shipping[last_name]" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="shipping_address" name="shipping[address]" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_address2" class="form-label">Dirección 2 (opcional)</label>
                            <input type="text" class="form-control" id="shipping_address2" name="shipping[address2]">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="shipping_city" class="form-label">Ciudad</label>
                                <input type="text" class="form-control" id="shipping_city" name="shipping[city]" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="shipping_state" class="form-label">Provincia/Estado</label>
                                <input type="text" class="form-control" id="shipping_state" name="shipping[state]" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="shipping_zip" class="form-label">Código postal</label>
                                <input type="text" class="form-control" id="shipping_zip" name="shipping[zip]" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_phone" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="shipping_phone" name="shipping[phone]" required>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sameBillingAddress" checked>
                            <label class="form-check-label" for="sameBillingAddress">
                                Usar la misma dirección para facturación
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- dirección de facturación -->
                <div class="card mb-4" id="billingAddressCard" style="display: none;">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Dirección de facturación</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="billing_first_name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="billing_first_name" name="billing[first_name]">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_last_name" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="billing_last_name" name="billing[last_name]">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="billing_address" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="billing_address" name="billing[address]">
                        </div>
                        
                        <div class="mb-3">
                            <label for="billing_address2" class="form-label">Dirección 2 (opcional)</label>
                            <input type="text" class="form-control" id="billing_address2" name="billing[address2]">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="billing_city" class="form-label">Ciudad</label>
                                <input type="text" class="form-control" id="billing_city" name="billing[city]">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="billing_state" class="form-label">Provincia/Estado</label>
                                <input type="text" class="form-control" id="billing_state" name="billing[state]">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="billing_zip" class="form-label">Código postal</label>
                                <input type="text" class="form-control" id="billing_zip" name="billing[zip]">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- metodo de pago -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Método de pago</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="tarjeta" checked>
                                <label class="form-check-label" for="creditCard">
                                    Tarjeta de crédito/débito
                                </label>
                            </div>
                            
                            <div id="creditCardFields" class="mt-3 ps-4">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="card_number" class="form-label">Número de tarjeta</label>
                                        <input type="text" class="form-control" id="card_number" name="card[number]" placeholder="1234 5678 9012 3456">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="card_name" class="form-label">Nombre en la tarjeta</label>
                                        <input type="text" class="form-control" id="card_name" name="card[name]">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="card_expiry" class="form-label">Expiración (MM/AA)</label>
                                        <input type="text" class="form-control" id="card_expiry" name="card[expiry]" placeholder="MM/AA">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="card_cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="card_cvv" name="card[cvv]" placeholder="123">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                <label class="form-check-label" for="paypal">
                                    PayPal
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer" value="transferencia">
                                <label class="form-check-label" for="transfer">
                                    Transferencia bancaria
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- notas del pedido -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Notas del pedido (opcional)</h3>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" id="order_notes" name="order_notes" rows="3" placeholder="Notas especiales para tu pedido..."></textarea>
                    </div>
                </div>
                
                <!-- terminos y condiciones -->
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="terms" required>
                    <label class="form-check-label" for="terms">
                        He leído y acepto los <a href="/terms" target="_blank">términos y condiciones</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                    Confirmar Pedido
                </button>

            </form>
        </div>
    </div>
</div>

<script>
// mostrar/ocultar dirección de facturación
document.getElementById('sameBillingAddress').addEventListener('change', function() {
    document.getElementById('billingAddressCard').style.display = this.checked ? 'none' : 'block';
});

// cargar dirección guardada
document.getElementById('savedShippingAddress')?.addEventListener('change', function() {
    if (this.value) {
        // Aca iría una llamada AJAX pero muestro un mensaje de ejemplo
        alert('Cargando dirección seleccionada...');
    }
});

// validación del formulario
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    if (!document.getElementById('terms').checked) {
        e.preventDefault();
        alert('Debes aceptar los términos y condiciones');
        return false;
    }
    return true;
});
</script>

<?php require_once __DIR__ . '/../shared/plantilla_pie.php'; ?>