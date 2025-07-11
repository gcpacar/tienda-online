<?php
// Configuración base
define('ROOT_DIR', dirname(__DIR__)); 
define('SHARED_DIR', ROOT_DIR . '/shared');
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../config/database.php';

// Control de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once SHARED_DIR . '/plantilla_cabecera.php';

$db = getDBConnection();

// datos del usuario
$user_id = $_SESSION['user_id'];
$query = "SELECT id, nombre, email, fecha_creacion FROM usuarios WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// últimos pedidos
$ordersQuery = "SELECT id, fecha, estado, total FROM ordenes WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 5";
$ordersStmt = $db->prepare($ordersQuery);
$ordersStmt->bind_param("i", $user_id);
$ordersStmt->execute();
$orders = $ordersStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = "Mi Perfil - " . htmlspecialchars($user['nombre']);
$updateSuccess = false; // Procesar actualización de perfil
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name)) {
        $errors[] = "El nombre es requerido";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El email no es válido";
    }

    // Verificar si el email ya existe
    $emailCheck = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $emailCheck->bind_param("si", $email, $user_id);
    $emailCheck->execute();
    if ($emailCheck->get_result()->num_rows > 0) {
        $errors[] = "El email ya está registrado por otro usuario";
    }

    // Si se cambia la contraseña
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $errors[] = "La nueva contraseña debe tener al menos 6 caracteres";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "Las contraseñas no coinciden";
        }

        // Verificar contraseña actual
        $passwordCheck = $db->prepare("SELECT password FROM usuarios WHERE id = ?");
        $passwordCheck->bind_param("i", $user_id);
        $passwordCheck->execute();
        $dbPassword = $passwordCheck->get_result()->fetch_assoc()['password'];

        if (!password_verify($current_password, $dbPassword)) {
            $errors[] = "La contraseña actual es incorrecta";
        }
    }
    // Si no hay errores, actualizar el perfil
    if (empty($errors)) {
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE usuarios SET nombre = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $db->prepare($updateQuery);
            $stmt->bind_param("sssi", $name, $email, $hashed_password, $user_id);
        } else {
            $updateQuery = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
            $stmt = $db->prepare($updateQuery);
            $stmt->bind_param("ssi", $name, $email, $user_id);
        }

        if ($stmt->execute()) {
            $updateSuccess = true;
            // Actualizar datos de sesión
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            // Refrescar datos del usuario
            $user['nombre'] = $name;
            $user['email'] = $email;
        } else {
            $errors[] = "Error al actualizar el perfil. Inténtalo de nuevo.";
        }
    }
}
?>

<div class="container py-5">
    <div class="row">
        <!-- Menú de usuario -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Mi Cuenta</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= BASE_URL ?>/user/profile" class="list-group-item list-group-item-action active">
                        <i class="fas fa-user-circle me-2"></i> Mi Perfil
                    </a>
                    <a href="<?= BASE_URL ?>/user/orders" class="list-group-item list-group-item-action">
                        <i class="fas fa-box-open me-2"></i> Mis Pedidos
                    </a>
                    <a href="<?= BASE_URL ?>/user/addresses" class="list-group-item list-group-item-action">
                        <i class="fas fa-map-marker-alt me-2"></i> Direcciones
                    </a>
                    <a href="<?= BASE_URL ?>/user/wishlist" class="list-group-item list-group-item-action">
                        <i class="fas fa-heart me-2"></i> Lista de deseos
                    </a>
                    <a href="<?= BASE_URL ?>/logout" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Mi Perfil</h4>
                </div>
                <div class="card-body">
                    <?php if ($updateSuccess): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            Perfil actualizado correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($user['nombre']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                                <small class="text-muted">Solo si deseas cambiar la contraseña</small>
                            </div>
                            <div class="col-md-4">
                                <label for="new_password" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            <div class="col-md-4">
                                <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información de la cuenta</h5>
                            <ul class="list-unstyled">
                                <li><strong>Miembro desde:</strong> <?= date('d/m/Y', strtotime($user['fecha_creacion'])) ?></li>
                                <li><strong>ID de usuario:</strong> <?= $user['id'] ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Últimos pedidos</h5>
                            <?php if (!empty($orders)): ?>
                                <ul class="list-unstyled">
                                    <?php foreach ($orders as $order): ?>
                                        <li>
                                            <a href="<?= BASE_URL ?>/user/order/<?= $order['id'] ?>">
                                                Pedido #<?= $order['id'] ?> - 
                                                <?= number_format($order['total'], 2) ?> - 
                                                <span class="badge bg-<?= 
                                                    $order['estado'] === 'completada' ? 'success' : 
                                                    ($order['estado'] === 'pendiente' ? 'warning' : 'secondary') 
                                                ?>">
                                                    <?= ucfirst($order['estado']) ?>
                                                </span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <a href="<?= BASE_URL ?>/user/orders" class="btn btn-sm btn-outline-primary">
                                    Ver todos los pedidos
                                </a>
                            <?php else: ?>
                                <p class="text-muted">No hay pedidos recientes</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once SHARED_DIR . '/plantilla_pie.php';?>