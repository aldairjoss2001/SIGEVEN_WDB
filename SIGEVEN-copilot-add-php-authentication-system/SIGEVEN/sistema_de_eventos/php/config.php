<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sigeven');
define('DB_CHARSET', 'utf8mb4');

// Configuración de sesión
define('SESSION_LIFETIME', 3600); // 1 hora en segundos
define('SESSION_NAME', 'SIGEVEN_SESSION');

// Rutas de redirección
define('LOGIN_ESTUDIANTE_URL', '../loginEstudiante.php');
define('LOGIN_DOCENTE_URL', '../loginDocente.php');
define('LOGIN_ADMIN_URL', '../../sistema_de_eventos_admin/login.php');

// Timezone
date_default_timezone_set('America/La_Paz');
?>
