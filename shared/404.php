<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página no encontrada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
            padding-top: 100px;
        }
        h1 {
            font-size: 50px;
            margin-bottom: 10px;
        }
        p {
            font-size: 20px;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>404</h1>
    <p>Ups... la página que buscás no existe.</p>
    <p><a href="/curso/index.php">Volver al inicio</a></p>
</body>
</html>
