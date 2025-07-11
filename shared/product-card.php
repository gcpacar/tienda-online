<?php
require_once __DIR__ . '/../config/paths.php';

if (!isset($product)) return;
?>
<div class="product-card">
    <div class="card h-100">
        <!-- oferta -->
        <?php if ($product['precio_oferta'] > 0): ?>
            <div class="badge bg-danger position-absolute" style="top: 0.5rem; right: 0.5rem">
                Oferta
            </div>
        <?php endif; ?>
        
        <!-- imagen de producto -->
        <img src="<?= htmlspecialchars($product['imagen_principal'] ?? '/assets/img/default-product.png') ?>" 
             class="card-img-top" 
             alt="<?= htmlspecialchars($product['nombre']) ?>">
        
        <div class="card-body">
            <!-- categoría -->
            <div class="text-muted small mb-1">
                <?= htmlspecialchars($product['categoria'] ?? 'General') ?>
            </div>
            
            <!-- titulo de producto -->
            <h3 class="card-title h6">
                <a href="/detail/<?= $product['id'] ?>" class="text-decoration-none text-dark">
                    <?= htmlspecialchars($product['nombre']) ?>
                </a>
            </h3>
            
            <!-- precio -->
            <div class="mb-2">
                <?php if ($product['precio_oferta'] > 0): ?>
                    <span class="text-decoration-line-through text-muted me-2">
                        $<?= number_format($product['precio'], 2) ?>
                    </span>
                    <span class="text-danger fw-bold">
                        $<?= number_format($product['precio_oferta'], 2) ?>
                    </span>
                <?php else: ?>
                    <span class="fw-bold">
                        $<?= number_format($product['precio'], 2) ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <!-- añadir al carrito -->
            <div class="d-grid">
                <button class="btn btn-outline-primary add-to-cart" data-product-id="<?= $product['id'] ?>">
                    <i class="fas fa-shopping-cart me-1"></i> Añadir
                </button>
            </div>
        </div>
    </div>
</div>
<script>
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
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Actualizo contador del carrito
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }
                showToast('Producto agregado al carrito');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al agregar al carrito: ' + error.message);
        });
    });
});

function showToast(message) {
    // Implementa tu propio sistema de notificaciones o usa alert simple
    alert(message);
}
</script>