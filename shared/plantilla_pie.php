</main>
<?$base_url = '/curso';?>
<footer class="bg-dark text-white py-4 mt-4">
    <div class="container">
        <div class="row">
            <!-- info de la tienda -->
            <div class="col-md-4 mb-3">
                <h5>InsumoMG</h5>
                <p>Los mejores productos al mejor precio.</p>
                <div class="social-icons">
                    <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- enlaces -->
            <div class="col-md-2 mb-3">
                <h5>Enlaces</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= $base_url ?>/home/index.php" class="text-white">Inicio</a></li>
                    <li><a href="<?= $base_url ?>/shop/index.php" class="text-white">Tienda</a></li>
                    <li><a href="<?= $base_url ?>/about/index.php" class="text-white">Nosotros</a></li>
                    <li><a href="<?= $base_url ?>/contact/index.php" class="text-white">Contacto</a></li>
                </ul>
            </div>

            <!-- políticas -->
            <div class="col-md-2 mb-3">
                <h5>Políticas</h5>
                <ul class="list-unstyled">
                    <li><a href="/privacy" class="text-white">Privacidad</a></li>
                    <li><a href="/terms" class="text-white">Términos</a></li>
                    <li><a href="/shipping" class="text-white">Envíos</a></li>
                    <li><a href="/returns" class="text-white">Devoluciones</a></li>
                </ul>
            </div>

            <!-- contacto -->
            <div class="col-md-4 mb-3">
                <h5>Contacto</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-map-marker-alt me-2"></i> Dirección: Calle Falsa 123</li>
                    <li><i class="fas fa-phone me-2"></i> Teléfono: +123456789</li>
                    <li><i class="fas fa-envelope me-2"></i> Email: info@tiendaonline.com</li>
                </ul>
            </div>
        </div>

        <hr class="my-4 bg-light">

        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">&copy; <?= date('Y') ?> InsumoMG. Todos los derechos reservados.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <img src="<?= $base_url ?>/assets/img/payments.png" alt="Métodos de pago" class="img-fluid" style="max-height: 30px;">
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="/assets/lib/owlcarousel/owl.carousel.min.js"></script>

<script src="/assets/lib/easing/easing.min.js"></script>

<script src="/assets/js/main.js"></script>

<?php if (isset($pageScripts)): ?>
    <?php foreach ($pageScripts as $script): ?>
        <script src="<?= htmlspecialchars($script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>