<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// si el usuario ya está logueado, redirigir
if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

// procesar form de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "El nombre es requerido";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden";
    }
    
    if (empty($errors)) {
        try {
            $db = getDBConnection();
            
            // verifico si el email ya existe
            $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "El email ya está registrado";
            } else {
                // crear usuario
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password, rol, fecha_creacion) VALUES (?, ?, ?, 'cliente', NOW())");
                $stmt->bind_param("sss", $name, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    $user_id = $db->insert_id;
                    
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_role'] = 'cliente'; // Asigno rol por defecto
                    
                    header('Location: /');
                    exit;
                } else {
                    $errors[] = "Error al registrar el usuario. Inténtalo de nuevo.";
                }
            }
        } catch (Exception $e) {
            error_log("Error en registro: " . $e->getMessage());
            $errors[] = "Error al procesar el registro. Inténtalo de nuevo.";
        }
    }
}

require_once __DIR__ . '/../shared/plantilla_cabecera.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Registro de Usuario</h2>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Registrarse</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="/login">¿Ya tienes cuenta? Inicia sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/plantilla_pie.php'; ?> 