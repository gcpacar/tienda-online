<?php
require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/../config/paths.php';

// Control de sesión 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once SHARED_DIR . '/plantilla_cabecera.php';

// Verifica si hay un ID de producto
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /shop');
    exit;
}

$productId = (int)$_GET['id'];
$productDetail = new ProductDetailFunctions();
$product = $productDetail->getProductDetails($productId);

// Si el producto no existe, redirige a la tienda
if (!$product) {
    header('Location: /shop');
    exit;
}

$pageTitle = $product['nombre'] . " - InsumoMG";

?>

<div class="container py-5">
    <!-- ruta de navegación -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/shop">Tienda</a></li>
            <li class="breadcrumb-item"><a href="/shop?categoria=<?= $product['categoria_id'] ?>"><?= htmlspecialchars($product['categoria']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['nombre']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- galería de imágenes -->
        <div class="col-md-6">
            <div class="row">
                <!-- imagen principal -->
                <div class="col-12 mb-3">
                    <img id="mainProductImage" src="<?= htmlspecialchars($product['imagen_principal']) ?>" 
                         class="img-fluid rounded border" 
                         alt="<?= htmlspecialchars($product['nombre']) ?>">
                </div>
                
                <!-- miniaturas -->
                <?php if (!empty($product['imagenes'])): ?>
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="#" class="thumbnail active" data-image="<?= htmlspecialchars($product['imagen_principal']) ?>">
                                <img src="<?= htmlspecialchars($product['imagen_principal']) ?>" 
                                     class="img-thumbnail" 
                                     width="80" 
                                     alt="Miniatura">
                            </a>
                            
                            <?php foreach ($product['imagenes'] as $image): ?>
                                <a href="#" class="thumbnail" data-image="<?= htmlspecialchars($image['imagen_url']) ?>">
                                    <img src="<?= htmlspecialchars($image['imagen_url']) ?>" 
                                         class="img-thumbnail" 
                                         width="80" 
                                         alt="Miniatura">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- detalles del producto -->
        <div class="col-md-6">
            <h1 class="mb-3"><?= htmlspecialchars($product['nombre']) ?></h1>
            
            <!-- rating -->
            <div class="d-flex align-items-center mb-3">
                <div class="rating-stars me-2">
                    <?php
                    $avgRating = 4.5;
                    $fullStars = floor($avgRating);
                    $hasHalfStar = ($avgRating - $fullStars) >= 0.5;
                    
                    for ($i = 1; $i <= 5; $i++):
                        if ($i <= $fullStars):
                    ?>
                            <i class="fas fa-star text-warning"></i>
                        <?php elseif ($hasHalfStar && $i == $fullStars + 1): ?>
                            <i class="fas fa-star-half-alt text-warning"></i>
                        <?php else: ?>
                            <i class="far fa-star text-warning"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <a href="#reseñas" class="text-muted small">(<?= count($product['reseñas']) ?> reseñas)</a>
            </div>
            
            <!-- precio -->
            <div class="mb-4">
                <?php if ($product['precio_oferta'] > 0): ?>
                    <h3 class="text-danger">
                        <span class="text-decoration-line-through me-2">$<?= number_format($product['precio'], 2) ?></span>
                        $<?= number_format($product['precio_oferta'], 2) ?>
                    </h3>
                    <span class="badge bg-danger">Ahorras $<?= number_format($product['precio'] - $product['precio_oferta'], 2) ?></span>
                <?php else: ?>
                    <h3>$<?= number_format($product['precio'], 2) ?></h3>
                <?php endif; ?>
            </div>
            
            <!-- descripción corta -->
            <p class="lead mb-4"><?= htmlspecialchars($product['descripcion_corta'] ?? '') ?></p>
            
            <!-- formulario para carrito -->
            <form id="addToCartForm" action="/detail/procesos/agregar_al_carrito.php" method="post">
                <input type="hidden" name="producto_id" value="<?= $product['id'] ?>">
                
                <!-- seleccionar atributos -->
                <?php foreach ($product['atributos'] as $attrName => $values): ?>
                    <div class="mb-3">
                        <label class="form-label"><?= ucfirst($attrName) ?></label>
                        <select name="atributos[<?= $attrName ?>]" class="form-select" required>
                            <option value="">Seleccione <?= $attrName ?></option>
                            <?php foreach ($values as $value): ?>
                                <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($value) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
                
                <!-- cantidad -->
                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <div class="input-group" style="max-width: 120px;">
                        <button class="btn btn-outline-secondary" type="button" id="decrementQty">-</button>
                        <input type="number" name="cantidad" class="form-control text-center" 
                               value="1" min="1" max="<?= $product['stock'] ?>" id="productQty">
                        <button class="btn btn-outline-secondary" type="button" id="incrementQty">+</button>
                    </div>
                    <small class="text-muted"><?= $product['stock'] ?> disponibles</small>
                </div>
                
                <!-- botones -->
                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-primary flex-grow-1 py-3">
                        <i class="fas fa-shopping-cart me-2"></i> Agregar al Carrito
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="addToWishlist">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
            </form>
            
            <!-- detalles -->
            <div class="accordion mb-4" id="productAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#descripcion">
                            Descripción Completa
                        </button>
                    </h2>
                    <div id="descripcion" class="accordion-collapse collapse show" data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <?= nl2br(htmlspecialchars($product['descripcion'])) ?>
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#especificaciones">
                            Especificaciones
                        </button>
                    </h2>
                    <div id="especificaciones" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <table class="table">
                                <tbody>
                                    <?php foreach ($product['atributos'] as $attrName => $values): ?>
                                        <tr>
                                            <th><?= ucfirst($attrName) ?></th>
                                            <td><?= implode(', ', array_map('htmlspecialchars', $values)) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <th>Categoría</th>
                                        <td><?= htmlspecialchars($product['categoria']) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- compartir en redes -->
            <div class="d-flex align-items-center gap-2 mb-5">
                <span class="me-2">Compartir:</span>
                <a href="#" class="text-muted" title="Compartir en Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-muted" title="Compartir en Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-muted" title="Compartir en Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-muted" title="Compartir en WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>

    <!-- productos relacionados -->
    <?php if (!empty($product['relacionados'])): ?>
        <section class="mt-5 pt-5 border-top">
            <h3 class="mb-4">Productos Relacionados</h3>
            <div class="row">
                <?php foreach ($product['relacionados'] as $related): ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <?php include SHARED_DIR . '/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- reseñas -->
    <section id="reseñas" class="mt-5 pt-5 border-top">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Reseñas de Clientes</h3>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
                Escribir Reseña
            </button>
        </div>
        
        <?php if (!empty($product['reseñas'])): ?>
            <div class="row">
                <?php foreach ($product['reseñas'] as $review): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($review['usuario']) ?></h5>
                                    <div class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $review['calificacion']): ?>
                                                <i class="fas fa-star"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-2">
                                    <?= date('d/m/Y', strtotime($review['fecha'])) ?>
                                </small>
                                <p class="card-text"><?= htmlspecialchars($review['comentario']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No hay reseñas todavía. Sé el primero en opinar.</div>
        <?php endif; ?>
    </section>
</div>

<!-- escribir reseña -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Escribe tu reseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/producto/procesos/agregar_reseña.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="producto_id" value="<?= $product['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Calificación</label>
                        <div class="rating-input">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?= $i ?>" name="calificacion" value="<?= $i ?>" 
                                       <?= $i == 5 ? 'checked' : '' ?>>
                                <label for="star<?= $i ?>"><i class="fas fa-star"></i></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Comentario</label>
                        <textarea name="comentario" class="form-control" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Reseña</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// cambiar imagen principal
document.querySelectorAll('.thumbnail').forEach(thumb => {
    thumb.addEventListener('click', function(e) {
        e.preventDefault();
        const mainImage = document.getElementById('mainProductImage');
        mainImage.src = this.dataset.image;
        
        // actualizar clase
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
    });
});

// manejo de incremento/decremento para cantidad
document.getElementById('incrementQty').addEventListener('click', function() {
    const qtyInput = document.getElementById('productQty');
    const max = parseInt(qtyInput.max);
    if (parseInt(qtyInput.value) < max) {
        qtyInput.value = parseInt(qtyInput.value) + 1;
    }
});

document.getElementById('decrementQty').addEventListener('click', function() {
    const qtyInput = document.getElementById('productQty');
    if (parseInt(qtyInput.value) > 1) {
        qtyInput.value = parseInt(qtyInput.value) - 1;
    }
});
</script>

<?php require_once SHARED_DIR . '/plantilla_pie.php';?>