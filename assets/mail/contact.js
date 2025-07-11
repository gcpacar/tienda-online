/**
 * Validación del formulario de contacto con Bootstrap
 */

document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        // Inicializar validación
        initContactValidation(contactForm);
        
        // Manejar envío del formulario
        contactForm.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                e.preventDefault();
                submitContactForm(this);
            }
            
            this.classList.add('was-validated');
        }, false);
    }
});

/**
 * Inicializar validación del formulario
 */
function initContactValidation(form) {
    // Validación en tiempo real
    form.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }
        });
        
        input.addEventListener('blur', function() {
            this.classList.add('validated');
            if (!this.checkValidity()) {
                this.classList.add('is-invalid');
            }
        });
    });
}

/**
 * Enviar formulario via AJAX
 */
function submitContactForm(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Mostrar estado de carga
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';
    
    // Enviar datos
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('¡Mensaje enviado con éxito! Nos pondremos en contacto pronto.', 'success');
            form.reset();
            form.classList.remove('was-validated');
        } else {
            showAlert(data.error || 'Error al enviar el mensaje. Por favor intenta nuevamente.', 'danger');
        }
    })
    .catch(error => {
        showAlert('Error de conexión. Por favor intenta nuevamente.', 'danger');
        console.error('Error:', error);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

/**
 * Mostrar alerta de estado
 */
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('.container') || document.body;
    container.prepend(alertDiv);
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        const alert = bootstrap.Alert.getInstance(alertDiv);
        if (alert) alert.close();
    }, 5000);
}