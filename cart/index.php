<?php
require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/../config/paths.php';
// Configuración base 
define('ROOT_DIR', dirname(__DIR__)); 
define('SHARED_DIR', ROOT_DIR . '/shared');

// Control de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once SHARED_DIR . '/plantilla_cabecera.php';

$cart = new CartFunctions();
$cartData = $cart->getCartProducts();

$pageTitle = "Carrito de Compras";

?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">Tu Carrito</h1>
            
            <?php if (empty($cartData['items'])): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                        <h3>Tu carrito está vacío</h3>
                        <p class="text-muted mb-4">No has agregado ningún producto a tu carrito todavía.</p>
                        <a href="../shop/index.php" class="btn btn-primary">Ir a la Tienda</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartData['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($item['producto']['imagen_principal']) ?>" 
                                                 alt="<?= htmlspecialchars($item['producto']['nombre']) ?>" 
                                                 class="img-thumbnail me-3" 
                                                 width="80">
                                            <div>
                                                <h5 class="mb-1"><?= htmlspecialchars($item['producto']['nombre']) ?></h5>
                                                <?php if (!empty($item['atributos'])): ?>
                                                    <small class="text-muted">
                                                        <?php foreach ($item['atributos'] as $attr => $value): ?>
                                                            <span><?= ucfirst($attr) ?>: <?= htmlspecialchars($value) ?></span><br>
                                                        <?php endforeach; ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                                    <td>
                                        <div class="input-group" style="max-width: 120px;">
                                            <button class="btn btn-outline-secondary update-qty" 
                                                    type="button" 
                                                    data-item-key="<?= $item['key'] ?>" 
                                                    data-action="decrease">-</button>
                                            <input type="number" 
                                                   class="form-control text-center qty-input" 
                                                   value="<?= $item['cantidad'] ?>" 
                                                   min="1" 
                                                   data-item-key="<?= $item['key'] ?>">
                                            <button class="btn btn-outline-secondary update-qty" 
                                                    type="button" 
                                                    data-item-key="<?= $item['key'] ?>" 
                                                    data-action="increase">+</button>
                                        </div>
                                    </td>
                                    <td>$<?= number_format($item['total'], 2) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger remove-item" 
                                                data-item-key="<?= $item['key'] ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between mb-4">
                    <a href="<?= BASE_URL ?>/shop" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Seguir Comprando
                    </a>
                    <button class="btn btn-outline-danger" id="clearCart">
                        <i class="fas fa-trash-alt me-2"></i> Vaciar Carrito
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($cartData['items'])): ?>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Resumen del Pedido</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Subtotal</th>
                                    <td class="text-end">$<?= number_format($cartData['subtotal'], 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Envío</th>
                                    <td class="text-end">
                                        <?php if ($cartData['subtotal'] > 50): ?>
                                            <span class="text-success">Gratis</span>
                                        <?php else: ?>
                                            $5.99
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr class="fw-bold">
                                    <th>Total</th>
                                    <td class="text-end">$<?= number_format($cartData['subtotal'] + ($cartData['subtotal'] > 50 ? 0 : 5.99), 2) ?></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="d-grid">
                            <a href="<?= BASE_URL ?>/checkout/index.php" class="btn btn-primary py-2">
                                Proceder al Pago
                            </a>
                        </div>
                        
                        <?php if (!isset($_SESSION['user'])): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                ¿Ya tienes una cuenta? <a href="<?= BASE_URL ?>/login?redirect=/checkout">Inicia sesión</a> para un proceso más rápido.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
<!-- cupón de descuento -->
<div class="card mt-3">
    <div class="card-header bg-light">
        <h3 class="h5 mb-0">Cupón de Descuento</h3>
    </div>
    <div class="card-body">
        <form id="couponForm">
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="codigo" placeholder="Código de cupón" required>
                <button class="btn btn-outline-secondary" type="submit">Aplicar</button>
            </div>
            <?php if (isset($_SESSION['cupon'])): ?>
                <div class="alert alert-success mt-2">
                    Cupón aplicado: <?= $_SESSION['cupon']['codigo'] ?> 
                    (Descuento: <?= $_SESSION['cupon']['tipo'] === 'porcentaje' ? $_SESSION['cupon']['descuento'].'%' : '$'.$_SESSION['cupon']['descuento'] ?>)
                    <button type="button" class="btn-close float-end" id="removeCoupon"></button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once SHARED_DIR . '/plantilla_pie.php';?>

<script>
// actualizar cantidad
document.querySelectorAll('.update-qty').forEach(button => {
    button.addEventListener('click', function() {
        const itemKey = this.dataset.itemKey;
        const input = this.closest('.input-group').querySelector('.qty-input');
        let newQty = parseInt(input.value);
        
        if (this.dataset.action === 'increase') {
            newQty++;
        } else {
            newQty = Math.max(1, newQty - 1);
        }
        
        input.value = newQty;
        updateCartItem(itemKey, newQty);
    });
});

// función para actualizar items
async function updateCartItem(itemKey, quantity) {
    try {
        const response = await fetch('<?= BASE_URL ?>/cart/procesos/actualizar_cantidad.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                item_key: itemKey,
                quantity: quantity
            })
        });
        
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.error || 'Error al actualizar');
        }
        
        showNotification(data.message || 'Cantidad actualizada');
        
        // actualizo el contador
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = data.cart_count;
        }
        
        // recargo página
        location.reload();
        
    } catch (error) {
        showNotification(error.message || 'Error de conexión', 'error');
        console.error('Error:', error);
    }
}

// notificaciones
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// agregar al carrito
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        
        fetch('<?= BASE_URL ?>/cart/procesos/agregar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // actualizo contador del carrito
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }
                
                // mostrar notificación
                showNotification(data.message || 'Producto agregado al carrito');
            } else {
                showNotification(data.error || 'Error al agregar al carrito', 'error');
            }
        })
        .catch(error => {
            showNotification('Error de conexión', 'error');
            console.error('Error:', error);
        });
    });
});

// vaciar carrito
document.getElementById('clearCart')?.addEventListener('click', function() {
    if (confirm('¿Estás seguro de que quieres vaciar tu carrito?')) {
        fetch('<?= BASE_URL ?>/cart/procesos/vaciar_carrito.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message);
                // actualizo contador
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = 0;
                }
                // recargo página
                location.reload();
            }
        })
        .catch(error => {
            showNotification('Error al vaciar carrito', 'error');
            console.error('Error:', error);
        });
    }
});

// eliminar item
document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', function() {
        if (confirm('¿Estás seguro de que quieres eliminar este producto de tu carrito?')) {
            const itemKey = this.dataset.itemKey;
            removeCartItem(itemKey);
        }
    });
});

function removeCartItem(itemKey) {
    fetch('<?= BASE_URL ?>/cart/procesos/eliminar_item.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            item_key: itemKey
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message);
            // actualizo contador
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
            // recargo página
            location.reload();
        } else {
            showNotification(data.error || 'Error al eliminar', 'error');
        }
    })
    .catch(error => {
        showNotification('Error de conexión', 'error');
        console.error('Error:', error);
    });
}

// formulario de cupón
document.getElementById('couponForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const codigo = this.codigo.value.trim();
    
    try {
        const response = await fetch(`<?= BASE_URL ?>/cart/procesos/aplicar_descuento.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ codigo })
        });
        
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.error || 'Error al aplicar cupón');
        }
        
        showNotification(data.message);
        setTimeout(() => location.reload(), 1000);
        
    } catch (error) {
        showNotification(error.message, 'error');
    }
});


</script>

<style>
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    background: #28a745;
    color: white;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transform: translateX(100%);
    transition: transform 0.3s ease;
    z-index: 9999;
    display: flex;
    align-items: center;
}

.notification.show {
    transform: translateX(0);
}

.notification.error {
    background: #dc3545;
}

.notification i {
    margin-right: 10px;
    font-size: 1.2rem;
}

.notification-content {
    display: flex;
    align-items: center;
}
</style>
