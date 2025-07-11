<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = '/curso'; 
$pageTitle = $pageTitle ?? 'InsumoMG';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.min.css">
    <link rel="icon" href="<?= $base_url ?>/assets/img/favicon.ico" type="image/x-icon">
</head>
<body>
    <!-- barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= $base_url ?>/index.php"><img src="<?= $base_url ?>/assets/img/logo.png" alt="InsumoMG"></a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>/index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>/shop/index.php">Tienda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>/contact/index.php">Contacto</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <!-- barra de búsqueda -->
                    <li class="nav-item me-2">
                        <form class="d-flex" action="<?= $base_url ?>/shop" method="get">
                            <div class="input-group">
                                <input type="search" name="busqueda" class="form-control form-control-sm" 
                                       placeholder="Buscar..." aria-label="Buscar">
                                <button class="btn btn-sm btn-outline-light" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </li>
                    
                    <!-- usuario/login -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <li><span class="dropdown-item-text">Hola, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="<?= $base_url ?>/admin"><i class="fas fa-cog"></i> Administración</a></li>
                                <?php endif; ?>
                                
                                <li><a class="dropdown-item" href="<?= $base_url ?>/user/profile.php"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="<?= $base_url ?>/user/orders"><i class="fas fa-box-open"></i> Mis Pedidos</a></li>
                                <li><a class="dropdown-item" href="<?= $base_url ?>/login/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                            <?php else: ?> 
                                <li><a class="dropdown-item" href="<?= $base_url ?>/login/index.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
                                <li><a class="dropdown-item" href="<?= $base_url ?>/login/register.php"><i class="fas fa-user-plus"></i> Registrarse</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    <!-- carrito -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= $base_url ?>/cart">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if (!empty($_SESSION['cart_count'])): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $_SESSION['cart_count'] ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- contenedor principal -->
    <main class="container my-4">
        <!-- mensaje flash -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['flash_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>