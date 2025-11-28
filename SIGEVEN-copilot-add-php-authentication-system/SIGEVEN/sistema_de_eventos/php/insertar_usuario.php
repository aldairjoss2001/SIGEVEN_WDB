<?php
require_once("conexion.php");
require_once("sesion.php");

// Iniciar sesión para mensajes
iniciar_sesion_segura();

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../registro.php');
    exit();
}

// Obtener y limpiar datos del formulario
$nombre = isset($_POST['nombre']) ? limpiar_dato($conn, $_POST['nombre']) : '';
$correo = isset($_POST['correo']) ? limpiar_dato($conn, $_POST['correo']) : '';
$contraseña = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
$codigo = isset($_POST['codigo']) ? limpiar_dato($conn, $_POST['codigo']) : '';
$tipo_usuario = isset($_POST['tipo_usuario']) ? limpiar_dato($conn, $_POST['tipo_usuario']) : '';

// Validar campos obligatorios
if (empty($nombre) || empty($correo) || empty($contraseña) || empty($codigo) || empty($tipo_usuario)) {
    $_SESSION['error_registro'] = 'Todos los campos son obligatorios';
    header('Location: ../registro.php');
    exit();
}

// Validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_registro'] = 'El formato del correo electrónico no es válido';
    header('Location: ../registro.php');
    exit();
}

// Validar longitud de contraseña
if (strlen($contraseña) < 6) {
    $_SESSION['error_registro'] = 'La contraseña debe tener al menos 6 caracteres';
    header('Location: ../registro.php');
    exit();
}

// Encriptar contraseña
$hash = password_hash($contraseña, PASSWORD_BCRYPT);

// Insertar según el tipo de usuario
$registro_exitoso = false;

if ($tipo_usuario === 'estudiante') {
    // Validar formato de código de estudiante (E00000-0)
    if (!preg_match('/^E[0-9]{5}-[0-9]$/', $codigo)) {
        $_SESSION['error_registro'] = 'El formato del código de estudiante debe ser E00000-0';
        header('Location: ../registro.php');
        exit();
    }
    
    // Verificar si ya existe
    $stmt = $conn->prepare("SELECT id FROM estudiantes WHERE codigo_estudiante = ? OR correo = ?");
    $stmt->bind_param("ss", $codigo, $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_registro'] = 'El código de estudiante o correo ya está registrado';
        $stmt->close();
        $conn->close();
        header('Location: ../registro.php');
        exit();
    }
    $stmt->close();
    
    // Insertar estudiante
    $stmt = $conn->prepare("INSERT INTO estudiantes (codigo_estudiante, nombre, correo, contrasena) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $codigo, $nombre, $correo, $hash);
    $registro_exitoso = $stmt->execute();
    $stmt->close();
    
} elseif ($tipo_usuario === 'docente') {
    // Validar formato de código de docente (A00000-0)
    if (!preg_match('/^A[0-9]{5}-[0-9]$/', $codigo)) {
        $_SESSION['error_registro'] = 'El formato del código de docente debe ser A00000-0';
        header('Location: ../registro.php');
        exit();
    }
    
    // Verificar si ya existe
    $stmt = $conn->prepare("SELECT id FROM docentes WHERE codigo_docente = ? OR correo = ?");
    $stmt->bind_param("ss", $codigo, $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_registro'] = 'El código de docente o correo ya está registrado';
        $stmt->close();
        $conn->close();
        header('Location: ../registro.php');
        exit();
    }
    $stmt->close();
    
    // Insertar docente
    $stmt = $conn->prepare("INSERT INTO docentes (codigo_docente, nombre, correo, contrasena) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $codigo, $nombre, $correo, $hash);
    $registro_exitoso = $stmt->execute();
    $stmt->close();
} else {
    $_SESSION['error_registro'] = 'Tipo de usuario no válido';
    $conn->close();
    header('Location: ../registro.php');
    exit();
}

$conn->close();

// Redirigir con mensaje
if ($registro_exitoso) {
    $_SESSION['exito_registro'] = 'Usuario registrado correctamente. Ya puedes iniciar sesión.';
    // Redirigir al login correspondiente
    if ($tipo_usuario === 'estudiante') {
        header('Location: ../loginEstudiante.php');
    } else {
        header('Location: ../loginDocente.php');
    }
} else {
    $_SESSION['error_registro'] = 'Error al registrar el usuario. Por favor, intenta nuevamente.';
    header('Location: ../registro.php');
}
exit();
?>
