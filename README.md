# Proyecto curso de PHP - Documentación
# Alumno: Gianluca Curras
# Dni: 44.205.564

## Estructura del Proyecto

```
|   DDL.sql                # Script SQL para crear la base de datos
|   index.php              # Punto de entrada principal
|   README.md              # Este archivo de documentación
|
+---admin                  # Panel de administración
|   |   funciones.php      # Funciones específicas para admin
|   |   index.php          # Vista principal del admin
|   |
|   \---procesos           # Procesos del admin (CRUD)
|           crear_producto.php
|           editar_producto.php
|           eliminar_producto.php
|
+---assets                 # Assets estáticos
|   +---css                # Hojas de estilo
|   +---img                # Imágenes
|   +---js                 # JavaScript
|   +---lib                # Librerías externas
|   +---mail               # Configuración de email
|   \---scss               # Fuentes SCSS
|
+---cart                   # Carrito de compras
|       funciones.php
|       index.php
|
+---checkout               # Proceso de pago
|   |   funciones.php
|   |   index.php
|   |
|   \---procesos
|           enviar_pedido.php
|
+---config                 # Configuraciones
|       database.php       # Configuración de la base de datos
|
+---contact                # Contacto
|   |   index.php
|   |
|   \---procesos
|           procesar_formulario.php
|
+---detail                # Detalle de productos
|   |   funciones.php
|   |   index.php
|   |
|   \---procesos
|           agregar_al_carrito.php
|
+---home                  # Página principal
|       funciones.php
|       index.php
|
+---shared                # Plantillas compartidas
|       plantilla_cabecera.php
|       plantilla_pie.php
|       404.php
|
\---shop                  # Catálogo de productos
        funciones.php
        index.php
```

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Composer (recomendado)

## Instalación

1. Clonar el repositorio
2. Crear la base de datos ejecutando `DDL.sql`
3. Configurar las credenciales en `config/database.php`
4. Configurar el servidor web para que apunte a la carpeta pública

## Configuración

Editar `config/database.php` con los datos de conexión:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'usuario');
define('DB_PASS', 'contraseña');
define('DB_NAME', 'alumni');
```

## Características Principales

- Catálogo de productos con categorías
- Carrito de compras
- Proceso de checkout
- Panel de administración
- Sistema de autenticación
- Reseñas de productos
- Búsqueda y filtrado

## Credenciales de Administrador

- Email: admin@tienda.com
- Contraseña: admin123