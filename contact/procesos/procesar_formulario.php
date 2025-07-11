<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /contact');
    exit;
}

$nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$asunto = filter_input(INPUT_POST, 'asunto', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$mensaje = filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$newsletter = isset($_POST['newsletter']) ? 1 : 0;

// validar campos requeridos
if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
    $_SESSION['flash_message'] = 'Por favor complete todos los campos requeridos';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /contact');
    exit;
}

// validar formato del correo electrónico
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_message'] = 'Por favor ingrese un correo electrónico válido';
    $_SESSION['flash_type'] = 'danger';
    header('Location: /contact');
    exit;
}

// guardar mensaje en la db
try {
    $db = getDBConnection();
    $query = "INSERT INTO mensajes (nombre, email, asunto, mensaje, newsletter, fecha) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("ssssi", $nombre, $email, $asunto, $mensaje, $newsletter);
    $stmt->execute();
    
    $_SESSION['flash_message'] = 'Gracias por contactarnos. Te responderemos pronto.';
    $_SESSION['flash_type'] = 'success';
    
} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Ocurrió un error al enviar tu mensaje. Por favor intenta nuevamente.';
    $_SESSION['flash_type'] = 'danger';
    error_log("Error al procesar formulario de contacto: " . $e->getMessage());
}

header('Location: /contact');
exit;

// enviar email de confirmación
function enviarEmailConfirmacion($email, $nombre) {
    $to = $email;
    $subject = 'Gracias por contactarnos';
    $message = "Hola $nombre,\n\nHemos recibido tu mensaje y te responderemos pronto.\n\nSaludos,\nEl equipo de InsumoMG";
    $headers = 'From: no-reply@tiendaonline.com' . "\r\n" .
               'Reply-To: info@tiendaonline.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();
    
    mail($to, $subject, $message, $headers);
}
?>