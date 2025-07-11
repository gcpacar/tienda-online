<?php
require_once __DIR__ . '/../config/database.php';
class HomeFunctions {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    // categorías destacadas para mostrar en la página de inicio
    public function getFeaturedCategories($limit = 6) {
        $query = "SELECT c.*, COUNT(p.id) as product_count 
                  FROM categorias c
                  LEFT JOIN productos p ON p.categoria_id = c.id
                  GROUP BY c.id
                  ORDER BY product_count DESC
                  LIMIT ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

// productos más vendidos 

public function getBestSellingProducts($limit = 8) {
    $query = "SELECT p.*, c.nombre as categoria, SUM(oi.cantidad) as total_sold
              FROM productos p
              LEFT JOIN orden_items oi ON oi.producto_id = p.id
              LEFT JOIN categorias c ON p.categoria_id = c.id
              GROUP BY p.id
              ORDER BY total_sold DESC
              LIMIT ?";
    
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// últimos productos agregados
public function getNewestProducts($limit = 8) {
    $query = "SELECT p.*, c.nombre as categoria 
              FROM productos p
              LEFT JOIN categorias c ON p.categoria_id = c.id
              ORDER BY p.fecha_creacion DESC 
              LIMIT ?";
    
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    // precio con descuentos
    public function getSpecialOffers($limit = 2) {
        $query = "SELECT * FROM productos 
                  WHERE precio_oferta > 0 
                  ORDER BY (precio - precio_oferta) DESC
                  LIMIT ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

   // imágenes del carrusel principal
    public function getCarouselItems() {
        return [
            [
                'imagen' => 'https://i.imgur.com/GH7HbFA.png',
                'titulo' => 'Hot Sale!',
                'texto' => 'Descuentos especiales en productos seleccionados',
                'enlace' => '/shop?oferta=1',
                'boton' => 'Ver Ofertas'
            ],
            [
                'imagen' => 'https://i.imgur.com/qGhUYWN.png',
                'titulo' => 'InsumoMG',
                'texto' => 'Construye con Confiaza, Construye con Calidad.',
                'enlace' => '/shop?orden=nuevos',
                'boton' => 'Ver Productos'
            ]
        ];
    }
}
?>