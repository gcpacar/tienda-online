<?php
// Configuración base
define('ROOT_DIR', __DIR__);
define('SHARED_DIR', ROOT_DIR . '/shared');

// Control de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once SHARED_DIR . '../plantilla_cabecera.php';

$request = $_SERVER['REQUEST_URI'];
$base_path = '/curso';
$request_path = ltrim(str_replace([$base_path, 'index.php'], '', $request), '/');
$request_path = explode('?', $request_path)[0]; 

switch ($request_path) {
    case '/':
        case '/home':
            require __DIR__ . '/home/index.php';
            break;
        case '/shop':
            require __DIR__ . '/shop/index.php';
            break;
    case 'index.php':
        case '':
        case 'home':
            require __DIR__ . '/home/index.php';
            break;        
    case 'shop':
        require __DIR__ . '/shop/index.php';
        break;
    case 'cart':
        require __DIR__ . '/cart/index.php';
        break;
    case 'checkout':
        require __DIR__ . '/checkout/index.php';
        break;
    case 'admin':
        require __DIR__ . '/admin/index.php';
        break;
    case 'detail':
        require __DIR__ . '/detail/index.php';
        break;
    case 'contact':
        require __DIR__ . '/contact/index.php';
        break;
    default:
        // carga la página dinámica
        if (preg_match('/^detail\/(\d+)$/', $request_path, $matches)) {
            $_GET['id'] = $matches[1];
            require __DIR__ . '/detail/index.php';
        } else {
            http_response_code(404);
            require __DIR__ . '/shared/404.php';
        }
        break;
}
require_once SHARED_DIR . '/plantilla_pie.php';

?>