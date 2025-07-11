<?php
// Configuración de la base de datos
define('ENVIRONMENT', 'development');
define('DB_HOST', 'localhost'); 
define('DB_USER', 'root');       
define('DB_PASS', '');           
define('DB_NAME', 'alumni');
define('DB_CHARSET', 'utf8mb4');    

// reportar rerrores
define('DB_ERROR_MODE', PDO::ERRMODE_SILENT);

// conexión a la base de datos
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306); // Puerto explícito
        
        if ($conn->connect_error) {
            error_log("Error de conexión: ".$conn->connect_error);
            throw new Exception("Error de conexión. Verifica: 
               1. MySQL está corriendo
               2. Credenciales en config/database.php
               3. Usuario tiene privilegios");
        }
        
        if (!$conn->set_charset(DB_CHARSET)) {
            error_log("Error charset: ".$conn->error);
        }
    }
    return $conn;
}

// conexión a la base de datos usando PDO
function getPDOConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => DB_ERROR_MODE,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => true 
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Error PDO: " . $e->getMessage());
            throw new Exception("Error al conectar con la base de datos.");
        }
    }
    
    return $pdo;
}

// consulta preparada con MySQLi
function executeQuery($sql, $params = [], $types = '') {
    $conn = getDBConnection();
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Error al preparar consulta: " . $conn->error);
        throw new Exception("Error en la consulta a la base de datos.");
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Error al ejecutar consulta: " . $stmt->error);
        throw new Exception("Error al procesar la solicitud.");
    }
    
    return $stmt->get_result();
}

// prevencion de inyección SQL
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    $conn = getDBConnection();
    return $conn->real_escape_string(htmlspecialchars(trim($data)));
}
// manejo de excepciones global
set_exception_handler(function($e) {
    error_log("Excepción no capturada: " . $e->getMessage());
    
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        die("Error: " . $e->getMessage());
    } else {
        die("Ocurrió un error inesperado. Por favor intente más tarde.");
    }
});