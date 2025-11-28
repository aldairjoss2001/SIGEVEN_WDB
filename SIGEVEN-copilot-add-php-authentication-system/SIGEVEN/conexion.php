<?php
/**
 * Archivo: conexion.php
 * Descripción: Conexión a la base de datos MySQL usando PDO
 * Ubicación: C:\xampp\htdocs\SIGEVEN\conexion.php
 */

// Configuración de la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "SIGEVEN";

try {
    // Crear conexión PDO
    // PDO es preferible porque:
    // - Soporta prepared statements nativamente
    // - Previene SQL injection automáticamente
    // - Es más moderno y flexible
    // - Maneja errores con excepciones
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Conexión exitosa (opcional: comentar en producción)
    // echo "Conexión exitosa a la base de datos";
    
} catch(PDOException $e) {
    // Manejo de errores de conexión
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

/**
 * Función para determinar el rol del usuario según su correo
 * @param string $correo - Correo electrónico del usuario
 * @return string - Rol del usuario: "estudiante", "docente", "administrativo" o "desconocido"
 */
function obtener_rol_por_correo($correo) {
    // Convertir a minúsculas para comparación
    $correo = strtolower($correo);
    
    // Detectar rol según el dominio del correo
    if (str_ends_with($correo, '@est.emi.edu.bo')) {
        return 'estudiante';
    } elseif (str_ends_with($correo, '@doc.emi.edu.bo')) {
        return 'docente';
    } elseif (str_ends_with($correo, '@adm.emi.edu.bo')) {
        return 'administrativo';
    } else {
        return 'desconocido';
    }
}
?>
