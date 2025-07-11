<?php
// Configuración básica para emails
$to = 'contacto@tienda.com';
$subject = 'Nuevo mensaje de contacto';
$headers = 'From: webmaster@tienda.com' . "\r\n" .
           'Reply-To: webmaster@tienda.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

// Procesar datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    $body = "Nombre: $name\nEmail: $email\n\nMensaje:\n$message";
    
    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al enviar el mensaje']);
    }
}
?>