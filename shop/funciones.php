<?php
require_once __DIR__ . '/../config/database.php';


class ShopFunctions {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    // funcion para filtrar y ordenar productos
    public function getFilteredProducts($filters = [], $order = 'nombre', $page = 1, $perPage = 12) {
        $query = "SELECT p.*, c.nombre as categoria FROM productos p 
                 JOIN categorias c ON p.categoria_id = c.id 
                 WHERE 1=1";
        $params = [];
        $types = '';

        // filtros
        if (!empty($filters['categoria'])) {
            $query .= " AND p.categoria_id = ?";
            $params[] = $filters['categoria'];
            $types .= 'i';
        }

        if (!empty($filters['busqueda'])) {
            $query .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ?)";
            $searchTerm = "%{$filters['busqueda']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }

        if (!empty($filters['precio_min'])) {
            $query .= " AND p.precio >= ?";
            $params[] = $filters['precio_min'];
            $types .= 'd';
        }

        if (!empty($filters['precio_max'])) {
            $query .= " AND p.precio <= ?";
            $params[] = $filters['precio_max'];
            $types .= 'd';
        }

        // ordenar
        $validOrders = [
            'nombre' => 'p.nombre ASC',
            'precio_asc' => 'p.precio ASC',
            'precio_desc' => 'p.precio DESC',
            'nuevos' => 'p.fecha_creacion DESC',
            'populares' => '(SELECT COUNT(*) FROM orden_items oi WHERE oi.producto_id = p.id) DESC'
        ];
        $orderBy = $validOrders[$order] ?? $validOrders['nombre'];
        $query .= " ORDER BY $orderBy";

        // paginacion
        $offset = ($page - 1) * $perPage;
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';

        // ejecutar consulta
        $stmt = $this->db->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // conteo total de productos para paginación
    public function getTotalProductsCount($filters = []) {
        $query = "SELECT COUNT(*) as total FROM productos p WHERE 1=1";
        $params = [];
        $types = '';
        // filtros
        if (!empty($filters['categoria'])) {
            $query .= " AND p.categoria_id = ?";
            $params[] = $filters['categoria'];
            $types .= 'i';
        }

        if (!empty($filters['busqueda'])) {
            $query .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ?)";
            $searchTerm = "%{$filters['busqueda']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }

        if (!empty($filters['precio_min'])) {
            $query .= " AND p.precio >= ?";
            $params[] = $filters['precio_min'];
            $types .= 'd';
        }

        if (!empty($filters['precio_max'])) {
            $query .= " AND p.precio <= ?";
            $params[] = $filters['precio_max'];
            $types .= 'd';
        }

        $stmt = $this->db->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc()['total'];
    }

    // categorías para el filtro
    public function getAllCategories() {
        $query = "SELECT id, nombre FROM categorias ORDER BY nombre";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // atributos disponibles para filtros
    public function getAvailableAttributes() {
        $query = "SELECT DISTINCT atributo, valor FROM producto_atributos ORDER BY atributo, valor";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>