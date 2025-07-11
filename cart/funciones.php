<?php
require_once __DIR__ . '/../config/database.php';

// Control de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CartFunctions {
    private $db;    

    public function __construct() {
        $this->db = getDBConnection();
    }

    
    // productos del carrito
    
    public function getCartProducts() {
        if (empty($_SESSION['cart'])) {
            return ['items' => [], 'total' => 0];
        }
    
        $cartItems = [];
        $total = 0;
        $db = getDBConnection();
    
        foreach ($_SESSION['cart'] as $key => $item) {
            $query = "SELECT p.*, c.nombre as categoria 
                     FROM productos p 
                     JOIN categorias c ON p.categoria_id = c.id 
                     WHERE p.id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $item['producto_id']);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
    
            if ($product) {
                $price = $product['precio_oferta'] > 0 ? $product['precio_oferta'] : $product['precio'];
                $itemTotal = $price * $item['cantidad'];
                
                $cartItems[] = [
                    'key' => $key,
                    'producto' => $product,
                    'cantidad' => $item['cantidad'],
                    'atributos' => $item['atributos'] ?? null,
                    'precio_unitario' => $price,
                    'total' => $itemTotal
                ];
                
                $total += $itemTotal;
            }
        }
    
        return [
            'items' => $cartItems,
            'subtotal' => $total,
            'total' => $total
        ];
    }

    
    // actualizo la cantidad en el carrito
    public function updateCartItem($itemKey, $quantity) {
    
        
        if (isset($_SESSION['cart'][$itemKey])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$itemKey]);
            } else {
                $_SESSION['cart'][$itemKey]['cantidad'] = $quantity;
            }
            
            $this->updateCartCount();
            return true;
        }
        
        return false;
    }

    
    // eliminar producto del carrito
    public function removeCartItem($itemKey) {
        
        
        if (isset($_SESSION['cart'][$itemKey])) {
            unset($_SESSION['cart'][$itemKey]);
            $this->updateCartCount();
            return true;
        }
        
        return false;
    }
   
    private function updateCartCount() {
        $_SESSION['cart_count'] = array_reduce($_SESSION['cart'], function($carry, $item) {
            return $carry + $item['cantidad'];
        }, 0);
    }
}

// clase auxiliar de productos
class Product {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function getProductById($id) {
        $query = "SELECT * FROM productos WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>