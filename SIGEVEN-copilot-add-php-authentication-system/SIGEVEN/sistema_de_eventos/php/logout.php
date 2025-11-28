<?php
require_once 'sesion.php';

// Obtener tipo de usuario antes de cerrar sesión
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'estudiante';

// Cerrar sesión
cerrar_sesion();

// Redirigir al login correspondiente
switch($tipo_usuario) {
    case 'estudiante':
        header('Location: ../loginEstudiante.php');
        break;
    case 'docente':
        header('Location: ../loginDocente.php');
        break;
    case 'admin':
        header('Location: ../../sistema_de_eventos_admin/login.php');
        break;
    default:
        header('Location: ../index.html');
}
exit();
?>
