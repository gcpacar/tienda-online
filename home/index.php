<?php
require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/../config/paths.php';
require_once ROOT_PATH . '/config/database.php';
require_once SHARED_PATH . '/plantilla_cabecera.php';


$home = new HomeFunctions();

$pageTitle = "Inicio";

$carouselItems = $home->getCarouselItems();
$featuredCategories = $home->getFeaturedCategories();
$bestSellingProducts = $home->getBestSellingProducts();
$newestProducts = $home->getNewestProducts();
$specialOffers = $home->getSpecialOffers();

?>

<!-- carrusel principal -->
<div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php foreach ($carouselItems as $key => $item): ?>
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="<?= $key ?>" 
                    class="<?= $key === 0 ? 'active' : '' ?>" aria-current="true"></button>
        <?php endforeach; ?>
    </div>
    
    <div class="carousel-inner">
        <?php foreach ($carouselItems as $key => $item): ?>
            <div class="carousel-item <?= $key === 0 ? 'active' : '' ?>">
                <img src="<?= htmlspecialchars($item['imagen']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($item['titulo']) ?>">
                <div class="carousel-caption d-none d-md-block">
                    <h5><?= htmlspecialchars($item['titulo']) ?></h5>
                    <p><?= htmlspecialchars($item['texto']) ?></p>
                    <a href="<?= BASE_URL ?>/shop?oferta=1" class="btn btn-primary">
                    <?= htmlspecialchars($item['boton']) ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- categorías destacadas -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Categorías Destacadas</h2>
            <p class="section-subtitle">Explora nuestras principales categorías</p>
        </div>
        
        <div class="row">
            <?php foreach ($featuredCategories as $category): ?>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="category-card">
                        <a href="<?= BASE_URL ?>/shop?categoria=<?= $category['id'] ?>" class="text-decoration-none">
                            <div class="card">
                                <img src="<?= htmlspecialchars($category['imagen'] ?? '/assets/img/default-category.png') ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($category['nombre']) ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?= htmlspecialchars($category['nombre']) ?></h5>
                                    <small class="text-muted"><?= $category['product_count'] ?> productos</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ofertas especiales -->
<?php if (!empty($specialOffers)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Ofertas Especiales</h2>
            <p class="section-subtitle">Aprovecha nuestros mejores descuentos</p>
        </div>
        
        <div class="row">
            <?php foreach ($specialOffers as $product): ?>
                <div class="col-md-6 mb-4">
                    <div class="offer-card">
                        <div class="card h-100">
                            <div class="row g-0">
                                <div class="col-md-5">
                                    <img src="<?= htmlspecialchars($product['imagen_principal']) ?>" 
                                         class="img-fluid rounded-start h-100" 
                                         alt="<?= htmlspecialchars($product['nombre']) ?>"
                                         style="object-fit: cover;">
                                </div>
                                <div class="col-md-7">
                                    <div class="card-body">
                                        <div class="badge bg-danger mb-2">OFERTA</div>
                                        <h3 class="card-title"><?= htmlspecialchars($product['nombre']) ?></h3>
                                        <p class="card-text">
                                            <span class="text-decoration-line-through text-muted me-2">
                                                $<?= number_format($product['precio'], 2) ?>
                                            </span>
                                            <span class="text-danger fw-bold">
                                                $<?= number_format($product['precio_oferta'], 2) ?>
                                            </span>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Ahorras: $<?= number_format($product['precio'] - $product['precio_oferta'], 2) ?>
                                            </small>
                                        </p>
                                        <a href="<?= BASE_URL ?>/detail/<?= $product['id'] ?>" class="btn btn-primary mt-3">
                                            Ver Producto
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- productos más vendidos -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Los Más Vendidos</h2>
            <p class="section-subtitle">Productos favoritos de nuestros clientes</p>
        </div>
        
        <div class="row">
            <?php foreach ($bestSellingProducts as $product): ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                <?php include SHARED_PATH . '/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/shop?orden=populares" class="btn btn-outline-primary">Ver Todos</a>
        </div>
    </div>
</section>

<!-- nuevos productos -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Nuevos Productos</h2>
            <p class="section-subtitle">Descubre nuestras últimas incorporaciones</p>
        </div>
        
        <div class="row">
            <?php foreach ($newestProducts as $product): ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <?php include SHARED_PATH . '/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/shop?orden=nuevos" class="btn btn-outline-primary">Ver Todos</a>
        </div>
    </div>
</section>

<!-- banner de suscripción -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h3>¡Suscríbete a nuestro boletín!</h3>
        <p class="mb-4">Recibe ofertas exclusivas y novedades directamente en tu email.</p>
        
        <form class="row justify-content-center">
            <div class="col-md-6 col-lg-4 mb-2 mb-md-0">
                <input type="email" class="form-control" placeholder="Tu email">
            </div>
            <div class="col-md-4 col-lg-2">
                <button type="submit" class="btn btn-light w-100">Suscribirse</button>
            </div>
        </form>
    </div>
</section>

<?require_once SHARED_PATH . '/plantilla_cabecera.php';?>