<?php
// Configuración base
define('ROOT_DIR', dirname(__DIR__));  
define('SHARED_DIR', ROOT_DIR . '/shared');

// Control de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once SHARED_DIR . '/plantilla_cabecera.php';

$pageTitle = "Contacto";

?>
<head>
<link rel="stylesheet" href="/assets/css/style.min.css">
</head>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">Contáctanos</h1>
            
            <div class="row">
                <!-- información de contacto -->
                <div class="col-md-5 mb-4 mb-md-0">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="h5 card-title">Información de contacto</h3>
                            
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    <span>Av. Principal 123, Ciudad, País</span>
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-phone me-2 text-primary"></i>
                                    <span>+1 234 567 890</span>
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    <span>info@tiendaonline.com</span>
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    <span>Lunes a Viernes: 9am - 6pm</span>
                                </li>
                            </ul>
                            
                            <hr>
                            
                            <h4 class="h6 mt-4">Síguenos</h4>
                            <div class="social-icons">
                                <a href="#" class="text-dark me-2"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="text-dark me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-dark me-2"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="text-dark me-2"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- form de contacto -->
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 card-title">Envía un mensaje</h2>
                            <p class="text-muted mb-4">¿Tienes alguna pregunta? ¡Escríbenos!</p>
                            
                            <form id="contactForm" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <div class="invalid-feedback">Por favor ingresa tu nombre</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">Por favor ingresa un email válido</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Mensaje</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                    <div class="invalid-feedback">Por favor escribe tu mensaje</div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- mapa -->
            <div class="card mt-4">
                <div class="card-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345.678901234567!2d-0.12345678901234567!3d40.12345678901234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDA3JzI0LjQiTiAwwrAwNyczNi4xIlc!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus" 
                                allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/assets/mail/jqBootstrapValidation.min.js"></script>
<script src="/assets/mail/contact.js"></script>

<script>
// validación del formulario
document.getElementById('contactForm').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const asunto = document.getElementById('asunto').value.trim();
    const mensaje = document.getElementById('mensaje').value.trim();
    
    if (!nombre || !email || !asunto || !mensaje) {
        e.preventDefault();
        alert('Por favor complete todos los campos requeridos');
        return false;
    }
    
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        e.preventDefault();
        alert('Por favor ingrese un correo electrónico válido');
        return false;
    }
    
    return true;
});
</script>

<?php require_once SHARED_DIR . '/plantilla_pie.php';?>