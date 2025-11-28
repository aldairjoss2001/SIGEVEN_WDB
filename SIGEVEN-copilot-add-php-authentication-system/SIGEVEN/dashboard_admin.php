<?php
/**
 * Archivo: dashboard_admin.php
 * Descripción: Panel para administrativos
 * Ubicación: C:\xampp\htdocs\SIGEVEN\dashboard_admin.php
 */

// Iniciar sesión
session_start();

// Verificar que hay sesión activa
if (!isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
    // No hay sesión, redirigir a login
    header('Location: login.php');
    exit();
}

// Verificar que el rol sea administrativo
if ($_SESSION['rol'] !== 'administrativo') {
    // Rol incorrecto, cerrar sesión y redirigir
    session_destroy();
    header('Location: login.php');
    exit();
}

// Obtener datos de sesión
$nombre = $_SESSION['nombre'];
$correo = $_SESSION['correo'];
$rol = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - SIGEVEN</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
            font-size: 24px;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 40px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-card h2 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .welcome-card p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .info-card h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .info-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            display: inline-block;
            width: 120px;
        }
        
        .info-value {
            color: #333;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 15px;
            background: #e74c3c;
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>SIGEVEN - Panel Administrativo</h1>
            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
        </div>
        
        <div class="welcome-card">
            <h2>¡Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</h2>
            <p>Tu rol es: <strong><?php echo htmlspecialchars($rol); ?></strong></p>
        </div>
        
        <div class="info-card">
            <h3>Información de tu Cuenta</h3>
            <div class="info-item">
                <span class="info-label">ID Usuario:</span>
                <span class="info-value"><?php echo htmlspecialchars($_SESSION['id']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Nombre:</span>
                <span class="info-value"><?php echo htmlspecialchars($nombre); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Correo:</span>
                <span class="info-value"><?php echo htmlspecialchars($correo); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Rol:</span>
                <span class="info-value"><span class="badge"><?php echo strtoupper($rol); ?></span></span>
            </div>
        </div>
    </div>
</body>
</html>
