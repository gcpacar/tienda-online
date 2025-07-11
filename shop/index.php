<?php
require_once __DIR__ . '/funciones.php';
// Configuración base
define('ROOT_DIR', dirname(__DIR__));  // Apunta a C:\xampp\htdocs\curso
define('SHARED_DIR', ROOT_DIR . '/shared');
require_once __DIR__ . '/../config/paths.php';

// Control de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once SHARED_DIR . '/plantilla_cabecera.php';

$shop = new ShopFunctions();

// parámetros de filtrado
$filters = [
    'categoria' => $_GET['categoria'] ?? null,
    'busqueda' => $_GET['busqueda'] ?? null,
    'precio_min' => $_GET['precio_min'] ?? null,
    'precio_max' => $_GET['precio_max'] ?? null,
    'color' => $_GET['color'] ?? null,
    'talla' => $_GET['talla'] ?? null
];

// ordenamiento y paginación
$order = $_GET['orden'] ?? 'nombre';
$currentPage = max(1, $_GET['pagina'] ?? 1);
$productsPerPage = 12;

// productos
$products = $shop->getFilteredProducts($filters, $order, $currentPage, $productsPerPage);
$totalProducts = $shop->getTotalProductsCount($filters);
$totalPages = ceil($totalProducts / $productsPerPage);

// categorías y atributos para filtros
$categories = $shop->getAllCategories();
$attributes = $shop->getAvailableAttributes();
?>

<div class="container mt-4">
    <div class="row">
        <!-- sidebar de filtros -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Filtros</h5>
                </div>
                <div class="card-body">
                    <form id="filter-form" method="get" action="<?= BASE_URL ?>/shop">
                        <!-- filtro por categoría -->
                        <div class="mb-3">
                            <label class="form-label">Categorías</label>
                            <select name="categoria" class="form-select" onchange="this.form.submit()">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $filters['categoria'] == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- filtro por precio -->
                        <div class="mb-3">
                            <label class="form-label">Rango de precios</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">$</span>
                                <input type="number" name="precio_min" class="form-control" placeholder="Mínimo" 
                                       value="<?= htmlspecialchars($filters['precio_min'] ?? '') ?>">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="precio_max" class="form-control" placeholder="Máximo" 
                                       value="<?= htmlspecialchars($filters['precio_max'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- filtros por atributos -->
                        <?php 
                        $groupedAttributes = [];
                        foreach ($attributes as $attr) {
                            $groupedAttributes[$attr['atributo']][] = $attr['valor'];
                        }
                        
                        foreach ($groupedAttributes as $attrName => $values): 
                        ?>
                            <div class="mb-3">
                                <label class="form-label"><?= ucfirst($attrName) ?></label>
                                <?php foreach (array_unique($values) as $value): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="<?= $attrName ?>[]" 
                                               id="<?= $attrName ?>-<?= $value ?>" 
                                               value="<?= $value ?>"
                                               <?= in_array($value, (array)($filters[$attrName] ?? [])) ? 'checked' : '' ?>
                                               onchange="this.form.submit()">
                                        <label class="form-check-label" for="<?= $attrName ?>-<?= $value ?>">
                                            <?= htmlspecialchars($value) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>

                        <button type="submit" class="btn btn-primary w-100">Aplicar Filtros</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- listado de productos -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Nuestros Productos</h1>
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="ordenDropdown" data-bs-toggle="dropdown">
                        Ordenar por: 
                        <?= match($order) {
                            'nombre' => 'Nombre',
                            'precio_asc' => 'Precio: Menor a Mayor',
                            'precio_desc' => 'Precio: Mayor a Menor',
                            'nuevos' => 'Más Nuevos',
                            'populares' => 'Más Populares',
                            default => 'Nombre'
                        } ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['orden' => 'nombre'])) ?>">Nombre</a></li>
                        <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['orden' => 'precio_asc'])) ?>">Precio: Menor a Mayor</a></li>
                        <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['orden' => 'precio_desc'])) ?>">Precio: Mayor a Menor</a></li>
                        <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['orden' => 'nuevos'])) ?>">Más Nuevos</a></li>
                        <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['orden' => 'populares'])) ?>">Más Populares</a></li>
                    </ul>
                </div>
            </div>

            <!-- barra de busqueda -->
            <form class="mb-4" method="get" action="<?= BASE_URL ?>/shop">
                <div class="input-group">
                    <input type="text" name="busqueda" class="form-control" placeholder="Buscar productos..." 
                           value="<?= htmlspecialchars($filters['busqueda'] ?? '') ?>">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>

            <!-- resultados -->
            <?php if (empty($products)): ?>
                <div class="alert alert-info">No se encontraron productos con los filtros seleccionados.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="<?= htmlspecialchars($product['imagen_principal']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['nombre']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['nombre']) ?></h5>
                                    <p class="card-text">
                                        <?php if ($product['precio_oferta']): ?>
                                            <span class="text-danger"><del>$<?= number_format($product['precio'], 2) ?></del></span>
                                            <span class="text-success">$<?= number_format($product['precio_oferta'], 2) ?></span>
                                        <?php else: ?>
                                            <span>$<?= number_format($product['precio'], 2) ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-muted"><?= htmlspecialchars($product['categoria']) ?></p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="<?= BASE_URL ?>/detail/<?= $product['id'] ?>" class="btn btn-outline-primary">Ver Detalle</a>
                                    <button class="btn btn-primary add-to-cart" data-product-id="<?= $product['id'] ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- paginación -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once SHARED_DIR . '/plantilla_pie.php';?>

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
                // Actualizar contador del carrito
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }
                
                // Mostrar notificación con Toast o similar
                showToast('Producto agregado al carrito');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al agregar al carrito: ' + error.message);
        });
    });
});

// Función auxiliar para mostrar notificaciones
function showToast(message) {
    // Implementa tu propio sistema de notificaciones o usa alert simple
    alert(message);
}
</script>