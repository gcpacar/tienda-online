<?php
require_once __DIR__ . '/funciones.php';

$admin = new AdminFunctions();

// verifico permisos
if (!$admin->isAdmin()) {
    header('Location: /login?redirect=/admin');
    exit;
}

$products = $admin->getAllProducts();
$categories = $admin->getAllCategories();

$pageTitle = "Panel de Administración - Productos";

require_once __DIR__ . '/../../shared/plantilla_cabecera.php';

?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin">
                            <i class="fas fa-boxes me-2"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/categorias">
                            <i class="fas fa-tags me-2"></i> Categorías
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/pedidos">
                            <i class="fas fa-clipboard-list me-2"></i> Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/usuarios">
                            <i class="fas fa-users me-2"></i> Usuarios
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- contenido principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Productos</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/admin/procesos/crear_producto.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Nuevo Producto
                    </a>
                </div>
            </div>

            <!-- mensajes -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($_GET['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- tabla de productos -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($product['imagen_principal']) ?>" 
                                         alt="<?= htmlspecialchars($product['nombre']) ?>" 
                                         width="50" 
                                         class="img-thumbnail">
                                </td>
                                <td><?= htmlspecialchars($product['nombre']) ?></td>
                                <td>
                                    <?php if ($product['precio_oferta'] > 0): ?>
                                        <span class="text-danger"><del>$<?= number_format($product['precio'], 2) ?></del></span>
                                        <span class="text-success">$<?= number_format($product['precio_oferta'], 2) ?></span>
                                    <?php else: ?>
                                        $<?= number_format($product['precio'], 2) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($product['categoria']) ?></td>
                                <td><?= $product['stock'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/admin/procesos/editar_producto.php?id=<?= $product['id'] ?>" 
                                           class="btn btn-outline-primary" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger delete-product" 
                                                data-id="<?= $product['id'] ?>" 
                                                title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- confirmar para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" class="btn btn-danger" id="confirmDelete">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
// control para eliminación de producto
document.querySelectorAll('.delete-product').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.id;
        const deleteButton = document.getElementById('confirmDelete');
        
        deleteButton.href = `/admin/procesos/eliminar_producto.php?id=${productId}`;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });
});
</script>

<?php require_once __DIR__ . '/../../shared/plantilla_pie.php'; ?>