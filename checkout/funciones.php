<?php
require_once __DIR__ . '/../config/database.php';

// Control de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CheckoutFunctions {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    
    // datos del carrito para el checkout
    public function getCheckoutData() {
        
        
        if (empty($_SESSION['cart'])) {
            return null;
        }

        $cartItems = [];
        $subtotal = 0;
        $productModel = new Product();

        foreach ($_SESSION['cart'] as $item) {
            $product = $productModel->getProductById($item['producto_id']);
            if ($product) {
                $price = $product['precio_oferta'] > 0 ? $product['precio_oferta'] : $product['precio'];
                $itemTotal = $price * $item['cantidad'];
                
                $cartItems[] = [
                    'producto' => $product,
                    'cantidad' => $item['cantidad'],
                    'atributos' => $item['atributos'],
                    'precio_unitario' => $price,
                    'total' => $itemTotal
                ];
                
                $subtotal += $itemTotal;
            }
        }

        // calcular total con impuestos y envío
        $taxRate = 0.21; 
        $shipping = $subtotal > 100000 ? 0 : 100000; // Envío gratis para compras > $100.000
        $tax = $subtotal * $taxRate;
        $total = $subtotal + $tax + $shipping;

        return [
            'items' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'user' => $_SESSION['user'] ?? null
        ];
    }

    
    // Procesar una nueva orden
    public function processOrder($orderData) {
        $this->db->begin_transaction();

        try {
            // 1. inserto la orden principal
            $query = "INSERT INTO ordenes (
                usuario_id, fecha, estado, subtotal, impuestos, envio, total,
                direccion_envio, direccion_facturacion, metodo_pago, notas
            ) VALUES (?, NOW(), 'pendiente', ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "iddddsssss",
                $orderData['usuario_id'],
                $orderData['subtotal'],
                $orderData['tax'],
                $orderData['shipping'],
                $orderData['total'],
                $orderData['shipping_address'],
                $orderData['billing_address'],
                $orderData['payment_method'],
                $orderData['notes']
            );
            $stmt->execute();
            $orderId = $this->db->insert_id;

            // 2. inserto los items de la orden
            foreach ($orderData['items'] as $item) {
                $query = "INSERT INTO orden_items (
                    orden_id, producto_id, cantidad, precio_unitario, atributos
                ) VALUES (?, ?, ?, ?, ?)";
                
                $stmt = $this->db->prepare($query);
                $atributosJson = json_encode($item['atributos']);
                $stmt->bind_param(
                    "iiids",
                    $orderId,
                    $item['producto']['id'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $atributosJson
                );
                $stmt->execute();

                // 3. actualizo el stock del producto
                $query = "UPDATE productos SET stock = stock - ? WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("ii", $item['cantidad'], $item['producto']['id']);
                $stmt->execute();
            }

            $this->db->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al procesar orden: " . $e->getMessage());
            return false;
        }
    }

    
    // direcciones guardadas del usuario
    
    public function getUserAddresses($userId) {
        $query = "SELECT * FROM direcciones WHERE usuario_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// clase auxiliar para productos
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