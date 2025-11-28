<?php
require_once 'config.php';

// Crear conexión usando las constantes de configuración
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Configurar charset
$conn->set_charset(DB_CHARSET);

// Verificar conexión
if ($conn->connect_error) {
    error_log("Error de conexión a la base de datos: " . $conn->connect_error);
    die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
}

// Función auxiliar para prevenir SQL injection
function limpiar_dato($conn, $dato) {
    return $conn->real_escape_string(trim($dato));
}
?>
