<?php
require_once __DIR__ . '/../config/database.php';

class AdminFunctions {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    
    // verifico si el usuario es administrador
    public function isAdmin() {
        session_start();
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    
    // productos con información de categoría  
    public function getAllProducts() {
        $query = "SELECT p.*, c.nombre as categoria 
                 FROM productos p 
                 JOIN categorias c ON p.categoria_id = c.id
                 ORDER BY p.id DESC";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // categorías disponibles
    public function getAllCategories() {
        $query = "SELECT id, nombre FROM categorias ORDER BY nombre";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    
    // crear un nuevo producto
    
    public function createProduct($productData) {
        $query = "INSERT INTO productos (
                    nombre, descripcion, precio, precio_oferta, categoria_id, 
                    imagen_principal, stock, destacado
                 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "ssddissi",
            $productData['nombre'],
            $productData['descripcion'],
            $productData['precio'],
            $productData['precio_oferta'],
            $productData['categoria_id'],
            $productData['imagen_principal'],
            $productData['stock'],
            $productData['destacado']
        );

        if ($stmt->execute()) {
            return $this->db->insert_id;
        } else {
            error_log("Error al crear producto: " . $stmt->error);
            return false;
        }
    }

    
    // subir una imagen
    public function uploadImage($file) {
        $uploadDir = __DIR__ . '/../../assets/img/products/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // validar tipo de archivo
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Tipo de archivo no permitido. Solo se aceptan JPG, PNG y WEBP.");
        }

        // validar tamaño
        if ($file['size'] > $maxSize) {
            throw new Exception("El archivo es demasiado grande. Tamaño máximo: 5MB.");
        }

        // generar nombre
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('product_') . '.' . $extension;
        $destination = $uploadDir . $filename;

        // mover el archivo
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Error al subir el archivo.");
        }

        return '/assets/img/products/' . $filename;
    }
}
?>