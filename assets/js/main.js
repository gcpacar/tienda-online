/**
 * Funcionalidades generales del sitio
 */

document.addEventListener('DOMContentLoaded', function() {
    // Menú móvil
    const mobileMenuButton = document.querySelector('.navbar-toggler');
    const mobileMenu = document.querySelector('.navbar-collapse');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('show');
        });
    }
    
    // Carrusel de productos
    initProductCarousel();
    
    // Validación de formularios
    initFormValidation();
    
    // Tooltips
    initTooltips();
});

// Inicializar carrusel de productos
function initProductCarousel() {
    const carousels = document.querySelectorAll('.product-carousel');
    
    carousels.forEach(carousel => {
        new bootstrap.Carousel(carousel, {
            interval: 5000,
            wrap: true
        });
    });
}

// Validación de formularios
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
}

// Inicializar tooltips
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Funciones para el carrito
function updateCartItemQuantity(itemId, quantity) {
    // Implementación de actualización de cantidad via AJAX
}

function removeCartItem(itemId) {
    // Implementación de eliminación de item via AJAX
}