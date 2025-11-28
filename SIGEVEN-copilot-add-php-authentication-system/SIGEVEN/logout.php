<?php
/**
 * Archivo: logout.php
 * Descripción: Cerrar sesión del usuario
 * Ubicación: C:\xampp\htdocs\SIGEVEN\logout.php
 */

// Iniciar sesión
session_start();

// Eliminar todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, borrar también la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_unset();
session_destroy();

// Redirigir al login
header('Location: login.php');
exit();
?>
