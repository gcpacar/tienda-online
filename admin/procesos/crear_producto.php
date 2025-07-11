<?php
require_once __DIR__ . '/../funciones.php';

$admin = new AdminFunctions();

if (!$admin->isAdmin()) {
    header('Location: /login?redirect=/admin');
    exit;
}

// procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $requiredFields = ['nombre', 'descripcion', 'precio', 'categoria_id', 'stock'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("El campo $field es requerido.");
            }
        }
        
        $productData = [
            'nombre' => trim($_POST['nombre']),
            'descripcion' => trim($_POST['descripcion']),
            'precio' => (float)$_POST['precio'],
            'precio_oferta' => !empty($_POST['precio_oferta']) ? (float)$_POST['precio_oferta'] : 0,
            'categoria_id' => (int)$_POST['categoria_id'],
            'stock' => (int)$_POST['stock'],
            'destacado' => isset($_POST['destacado']) ? 1 : 0,
            'imagen_principal' => '/assets/img/default-product.jpg'
        ];

        // procesar imagen si se subió
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $productData['imagen_principal'] = $admin->uploadImage($_FILES['imagen']);
        }

        // crear el producto
        $productId = $admin->createProduct($productData);

        if ($productId) {
            header('Location: /admin?success=Producto creado exitosamente');
            exit;
        } else {
            throw new Exception("Error al crear el producto en la base de datos.");
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
$categories = $admin->getAllCategories();
$pageTitle = "Crear Nuevo Producto";
require_once __DIR__ . '/../../shared/plantilla_cabecera.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h2 class="h4 mb-0">Crear Nuevo Producto</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <!-- nombre -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <!-- descripción -->
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="5" required></textarea>
                        </div>

                        <div class="row">
                            <!-- precio -->
                            <div class="col-md-6 mb-3">
                                <label for="precio" class="form-label">Precio</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio" name="precio" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>

                            <!-- precio de oferta -->
                            <div class="col-md-6 mb-3">
                                <label for="precio_oferta" class="form-label">Precio de Oferta (opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio_oferta" name="precio_oferta" 
                                           step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- categoría -->
                            <div class="col-md-6 mb-3">
                                <label for="categoria_id" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria_id" name="categoria_id" required>
                                    <option value="">Seleccionar categoría</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>">
                                            <?= htmlspecialchars($category['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- stock -->
                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label">Stock Disponible</label>
                                <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                            </div>
                        </div>

                        <!-- imagen -->
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen Principal</label>
                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                            <small class="text-muted">Formatos aceptados: JPG, PNG, WEBP (Max. 5MB)</small>
                        </div>

                        <!-- destacado -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="destacado" name="destacado">
                            <label class="form-check-label" for="destacado">Producto Destacado</label>
                        </div>

                        <!-- botones -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/admin" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../shared/plantilla_pie.php'; ?>