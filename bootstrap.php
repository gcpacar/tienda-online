<?php
// carpeta raíz del proyecto
define('BASE_PATH', __DIR__);
// configuración de la base de datos
require_once BASE_PATH . '/config/database.php';
// inicio de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
