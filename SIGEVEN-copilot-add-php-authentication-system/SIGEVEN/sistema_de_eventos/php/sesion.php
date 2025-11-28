<?php
require_once 'config.php';

// Iniciar sesión de forma segura
function iniciar_sesion_segura() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configuración segura de sesión
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Cambiar a 1 si se usa HTTPS
        
        session_name(SESSION_NAME);
        session_start();
        
        // Regenerar ID de sesión periódicamente para prevenir session fixation
        if (!isset($_SESSION['iniciada'])) {
            session_regenerate_id(true);
            $_SESSION['iniciada'] = true;
            $_SESSION['tiempo_inicio'] = time();
        }
        
        // Verificar tiempo de vida de la sesión
        if (isset($_SESSION['tiempo_inicio']) && (time() - $_SESSION['tiempo_inicio'] > SESSION_LIFETIME)) {
            cerrar_sesion();
            return false;
        }
        
        // Actualizar tiempo de última actividad
        $_SESSION['ultima_actividad'] = time();
    }
    return true;
}

// Verificar si el usuario está autenticado
function esta_autenticado() {
    iniciar_sesion_segura();
    return isset($_SESSION['usuario_id']) && isset($_SESSION['tipo_usuario']);
}

// Verificar tipo de usuario
function verificar_tipo_usuario($tipo_requerido) {
    if (!esta_autenticado()) {
        return false;
    }
    return $_SESSION['tipo_usuario'] === $tipo_requerido;
}

// Obtener datos de sesión
function obtener_datos_sesion() {
    if (!esta_autenticado()) {
        return null;
    }
    return [
        'id' => $_SESSION['usuario_id'],
        'tipo' => $_SESSION['tipo_usuario'],
        'nombre' => $_SESSION['nombre'] ?? '',
        'codigo' => $_SESSION['codigo'] ?? '',
        'correo' => $_SESSION['correo'] ?? ''
    ];
}

// Crear sesión de usuario
function crear_sesion_usuario($id, $tipo, $nombre, $codigo, $correo) {
    iniciar_sesion_segura();
    session_regenerate_id(true);
    
    $_SESSION['usuario_id'] = $id;
    $_SESSION['tipo_usuario'] = $tipo; // 'estudiante', 'docente', 'admin'
    $_SESSION['nombre'] = $nombre;
    $_SESSION['codigo'] = $codigo;
    $_SESSION['correo'] = $correo;
    $_SESSION['tiempo_inicio'] = time();
    $_SESSION['ultima_actividad'] = time();
}

// Cerrar sesión
function cerrar_sesion() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
}

// Redirigir si no está autenticado
function requerir_autenticacion($tipo_usuario = null) {
    if (!esta_autenticado()) {
        // Redirigir según el tipo de usuario esperado
        if ($tipo_usuario === 'estudiante') {
            header('Location: ' . LOGIN_ESTUDIANTE_URL);
        } elseif ($tipo_usuario === 'docente') {
            header('Location: ' . LOGIN_DOCENTE_URL);
        } elseif ($tipo_usuario === 'admin') {
            header('Location: ' . LOGIN_ADMIN_URL);
        } else {
            header('Location: ' . LOGIN_ESTUDIANTE_URL);
        }
        exit();
    }
    
    // Si se especifica un tipo, verificar que coincida
    if ($tipo_usuario !== null && $_SESSION['tipo_usuario'] !== $tipo_usuario) {
        cerrar_sesion();
        header('Location: ' . LOGIN_ESTUDIANTE_URL);
        exit();
    }
}
?>
