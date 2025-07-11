<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../shared/plantilla_cabecera.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

// procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // iniciar sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['rol']; // usamos el valor exacto de la DB
                
                // redirigir a la pagina solicitada o al inicio
                $redirect = $_GET['redirect'] ?? '/';
                header("Location: $redirect");
                exit;
            }
        }
        
        // credenciales son incorrectas
        $error = "Email o contraseña incorrectos";
    } catch (Exception $e) {
        error_log("Error en login: " . $e->getMessage());
        $error = "Error al procesar la solicitud. Inténtalo de nuevo.";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Iniciar Sesión</h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Ingresar</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="/login/register.php">¿No tienes cuenta? Regístrate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/plantilla_pie.php'; ?>