<?php
/**
 * Archivo: insertar_usuario_externo.php
 * Descripción: Registro de usuarios externos (público general)
 * Solo permite el registro de personas externas a la institución
 */

require_once("conexion.php");
require_once("sesion.php");

// Iniciar sesión para mensajes
iniciar_sesion_segura();

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../registro_externo.php');
    exit();
}

// Obtener y limpiar datos del formulario
$nombre = isset($_POST['nombre']) ? limpiar_dato($conn, $_POST['nombre']) : '';
$correo = isset($_POST['correo']) ? limpiar_dato($conn, $_POST['correo']) : '';
$telefono = isset($_POST['telefono']) ? limpiar_dato($conn, $_POST['telefono']) : '';
$contraseña = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

// Validar campos obligatorios
if (empty($nombre) || empty($correo) || empty($contraseña)) {
    $_SESSION['error_registro'] = 'El nombre, correo y contraseña son obligatorios';
    header('Location: ../registro_externo.php');
    exit();
}

// Validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_registro'] = 'El formato del correo electrónico no es válido';
    header('Location: ../registro_externo.php');
    exit();
}

// Validar que NO sea un correo institucional
$dominios_institucionales = ['@est.emi.edu.bo', '@doc.emi.edu.bo', '@adm.emi.edu.bo'];
foreach ($dominios_institucionales as $dominio) {
    if (strpos($correo, $dominio) !== false) {
        $_SESSION['error_registro'] = 'Los usuarios institucionales deben registrarse a través del administrador';
        header('Location: ../registro_externo.php');
        exit();
    }
}

// Validar longitud de contraseña
if (strlen($contraseña) < 6) {
    $_SESSION['error_registro'] = 'La contraseña debe tener al menos 6 caracteres';
    header('Location: ../registro_externo.php');
    exit();
}

// Encriptar contraseña
$hash = password_hash($contraseña, PASSWORD_BCRYPT);

// Crear tabla usuarios_externos si no existe
$crear_tabla = "CREATE TABLE IF NOT EXISTS usuarios_externos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    correo VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    contrasena VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($crear_tabla)) {
    $_SESSION['error_registro'] = 'Error al preparar la base de datos';
    $conn->close();
    header('Location: ../registro_externo.php');
    exit();
}

// Verificar si el correo ya existe en alguna tabla
// Verificar en estudiantes
$stmt = $conn->prepare("SELECT id FROM estudiantes WHERE correo = ?");
if ($stmt === false) {
    // La tabla estudiantes no existe, continuar
    $stmt = null;
} else {
        $stmt->bind_param("s", $correo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_registro'] = 'Este correo ya está registrado en el sistema';
        $stmt->close();
        $conn->close();
        header('Location: ../registro_externo.php');
        exit();
    }
    $stmt->close();
}

// Verificar en docentes
$stmt = $conn->prepare("SELECT id FROM docentes WHERE correo = ?");
if ($stmt !== false) {
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_registro'] = 'Este correo ya está registrado en el sistema';
        $stmt->close();
        $conn->close();
        header('Location: ../registro_externo.php');
        exit();
    }
    $stmt->close();
}

// Verificar en administradores
$stmt = $conn->prepare("SELECT id FROM administradores WHERE correo = ?");
if ($stmt !== false) {
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_registro'] = 'Este correo ya está registrado en el sistema';
        $stmt->close();
        $conn->close();
        header('Location: ../registro_externo.php');
        exit();
    }
    $stmt->close();
}

// Verificar si ya existe en usuarios_externos
$stmt = $conn->prepare("SELECT id FROM usuarios_externos WHERE correo = ?");
if ($stmt !== false) {
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_registro'] = 'Este correo ya está registrado';
        $stmt->close();
        $conn->close();
        header('Location: ../registro_externo.php');
        exit();
    }
    $stmt->close();
}

// Insertar usuario externo
$stmt = $conn->prepare("INSERT INTO usuarios_externos (nombre, correo, telefono, contrasena) VALUES (?, ?, ?, ?)");
if ($stmt === false) {
    $_SESSION['error_registro'] = 'Error al preparar el registro';
    $conn->close();
    header('Location: ../registro_externo.php');
    exit();
}
$stmt->bind_param("ssss", $nombre, $correo, $telefono, $hash);

if ($stmt->execute()) {
    $_SESSION['exito_registro'] = 'Registro exitoso. Ya puedes iniciar sesión.';
    $stmt->close();
    $conn->close();
    // Redirigir al login después de 2 segundos
    header('Location: ../registro_externo.php');
    exit();
} else {
    $_SESSION['error_registro'] = 'Error al registrar. Intente nuevamente.';
    $stmt->close();
    $conn->close();
    header('Location: ../registro_externo.php');
    exit();
}
?>
