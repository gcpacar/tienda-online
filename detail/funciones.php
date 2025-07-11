<?php
require_once __DIR__ . '/../config/database.php';

// Control de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ProductDetailFunctions {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    // detalles del producto
    public function getProductDetails($productId) {
        $query = "SELECT p.*, c.nombre as categoria 
                  FROM productos p 
                  JOIN categorias c ON p.categoria_id = c.id 
                  WHERE p.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if (!$product) {
            return null;
        }

        $product['imagenes'] = $this->getProductImages($productId);

        $product['atributos'] = $this->getProductAttributes($productId);

        $product['relacionados'] = $this->getRelatedProducts($productId, $product['categoria_id']);

        $product['reseñas'] = $this->getProductReviews($productId);

        return $product;
    }

    private function getProductImages($productId) {
        $query = "SELECT imagen_url FROM producto_imagenes 
                  WHERE producto_id = ? 
                  ORDER BY orden";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function getProductAttributes($productId) {
        $query = "SELECT atributo, valor FROM producto_atributos 
                  WHERE producto_id = ? 
                  ORDER BY atributo, valor";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        
        $attributes = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $attributes[$row['atributo']][] = $row['valor'];
        }
        
        return $attributes;
    }

    private function getRelatedProducts($productId, $categoryId, $limit = 4) {
        $query = "SELECT p.* FROM productos p 
                  WHERE p.categoria_id = ? AND p.id != ? 
                  ORDER BY RAND() 
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iii", $categoryId, $productId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function getProductReviews($productId) {
        $query = "SELECT r.*, u.nombre as usuario 
                  FROM reseñas r
                  JOIN usuarios u ON r.usuario_id = u.id
                  WHERE r.producto_id = ?
                  ORDER BY r.fecha DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>