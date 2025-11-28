<?php
require_once 'conexion.php';
require_once 'sesion.php';

// Iniciar sesión
iniciar_sesion_segura();

// Si ya está autenticado, redirigir
if (esta_autenticado()) {
    $tipo = $_SESSION['tipo_usuario'];
    if ($tipo === 'estudiante') {
        header('Location: ../PerfilEstudiante.html');
    } elseif ($tipo === 'docente') {
        header('Location: ../PerfilDocente.html');
    } elseif ($tipo === 'admin') {
        header('Location: ../../sistema_de_eventos_admin/index.html');
    }
    exit();
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.html');
    exit();
}

// Obtener datos del formulario
$username = isset($_POST['username']) ? limpiar_dato($conn, $_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$tipo_usuario = isset($_POST['tipo_usuario']) ? limpiar_dato($conn, $_POST['tipo_usuario']) : '';

// Validar que los campos no estén vacíos
if (empty($username) || empty($password) || empty($tipo_usuario)) {
    $_SESSION['error_login'] = 'Todos los campos son obligatorios';
    header('Location: ' . obtener_url_login($tipo_usuario));
    exit();
}

// Función para obtener URL de login según tipo
function obtener_url_login($tipo) {
    switch($tipo) {
        case 'estudiante':
            return '../loginEstudiante.php';
        case 'docente':
            return '../loginDocente.php';
        case 'admin':
            return '../../sistema_de_eventos_admin/login.php';
        default:
            return '../index.html';
    }
}

// Función para obtener URL de perfil según tipo
function obtener_url_perfil($tipo) {
    switch($tipo) {
        case 'estudiante':
            return '../PerfilEstudiante.html';
        case 'docente':
            return '../PerfilDocente.html';
        case 'admin':
            return '../../sistema_de_eventos_admin/index.html';
        default:
            return '../index.html';
    }
}

// Autenticar según el tipo de usuario
$autenticado = false;
$usuario_data = null;

if ($tipo_usuario === 'estudiante') {
    // Buscar estudiante por código
    $stmt = $conn->prepare("SELECT id, codigo_estudiante, nombre, correo, contrasena, activo FROM estudiantes WHERE codigo_estudiante = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $usuario_data = $result->fetch_assoc();
        if ($usuario_data['activo'] == 1 && password_verify($password, $usuario_data['contrasena'])) {
            $autenticado = true;
        }
    }
    $stmt->close();
    
} elseif ($tipo_usuario === 'docente') {
    // Buscar docente por código
    $stmt = $conn->prepare("SELECT id, codigo_docente, nombre, correo, contrasena, activo FROM docentes WHERE codigo_docente = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $usuario_data = $result->fetch_assoc();
        if ($usuario_data['activo'] == 1 && password_verify($password, $usuario_data['contrasena'])) {
            $autenticado = true;
        }
    }
    $stmt->close();
    
} elseif ($tipo_usuario === 'admin') {
    // Buscar administrador por username
    $stmt = $conn->prepare("SELECT id, username, nombre, correo, contrasena, activo FROM administradores WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $usuario_data = $result->fetch_assoc();
        if ($usuario_data['activo'] == 1 && password_verify($password, $usuario_data['contrasena'])) {
            $autenticado = true;
        }
    }
    $stmt->close();
}

$conn->close();

// Procesar resultado de autenticación
if ($autenticado && $usuario_data) {
    // Determinar el código según el tipo de usuario
    $codigo = '';
    if ($tipo_usuario === 'estudiante') {
        $codigo = $usuario_data['codigo_estudiante'];
    } elseif ($tipo_usuario === 'docente') {
        $codigo = $usuario_data['codigo_docente'];
    } elseif ($tipo_usuario === 'admin') {
        $codigo = $usuario_data['username'];
    }
    
    // Crear sesión
    crear_sesion_usuario(
        $usuario_data['id'],
        $tipo_usuario,
        $usuario_data['nombre'],
        $codigo,
        $usuario_data['correo']
    );
    
    // Redirigir al perfil correspondiente
    header('Location: ' . obtener_url_perfil($tipo_usuario));
    exit();
} else {
    // Login fallido
    $_SESSION['error_login'] = 'Credenciales incorrectas o usuario inactivo';
    header('Location: ' . obtener_url_login($tipo_usuario));
    exit();
}
?>
