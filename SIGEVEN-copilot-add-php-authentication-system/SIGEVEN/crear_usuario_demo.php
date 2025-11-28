<?php
/**
 * Archivo: crear_usuario_demo.php
 * Descripción: Script de ejemplo para crear usuarios usando password_hash()
 * Ubicación: C:\xampp\htdocs\SIGEVEN\crear_usuario_demo.php
 * 
 * NOTA: Este archivo es solo para demostración y pruebas.
 * Accede a: http://localhost/SIGEVEN/crear_usuario_demo.php
 * 
 * IMPORTANTE: Elimina o protege este archivo en producción
 */

// Incluir el archivo de conexión
require_once 'conexion.php';

// Variable para mensajes
$mensaje = '';
$error = '';

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    
    if (empty($nombre) || empty($correo) || empty($contrasena)) {
        $error = 'Todos los campos son obligatorios';
    } else {
        try {
            // Verificar si el correo ya existe
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo");
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            
            if ($stmt->fetch()) {
                $error = 'Este correo ya está registrado';
            } else {
                // Hash de la contraseña
                $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
                
                // Insertar el nuevo usuario
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena) VALUES (:nombre, :correo, :contrasena)");
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':contrasena', $contrasena_hash);
                
                if ($stmt->execute()) {
                    $mensaje = 'Usuario creado exitosamente. Puedes iniciar sesión en login.php';
                    // Limpiar campos
                    $nombre = '';
                    $correo = '';
                    $contrasena = '';
                } else {
                    $error = 'Error al crear el usuario';
                }
            }
        } catch(PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario Demo - SIGEVEN</title>
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
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #155724;
        }
        
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #721c24;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #5568d3;
        }
        
        .links {
            margin-top: 20px;
            text-align: center;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        
        .domains {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .domains li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Crear Usuario Demo</h1>
        
        <div class="warning">
            <strong>⚠️ ADVERTENCIA:</strong> Este archivo es solo para pruebas y desarrollo.
            <strong>Elimínalo o protégelo en producción.</strong>
        </div>
        
        <div class="info">
            <strong>ℹ️ Información sobre roles:</strong>
            <p>El rol se detecta automáticamente según el dominio del correo:</p>
            <ul class="domains">
                <li><code>@est.emi.edu.bo</code> → Estudiante</li>
                <li><code>@doc.emi.edu.bo</code> → Docente</li>
                <li><code>@adm.emi.edu.bo</code> → Administrativo</li>
            </ul>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="success">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="crear_usuario_demo.php">
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input 
                    type="text" 
                    id="nombre" 
                    name="nombre" 
                    placeholder="Ej: Juan Pérez González"
                    required
                    value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input 
                    type="email" 
                    id="correo" 
                    name="correo" 
                    placeholder="Ej: juan.perez@est.emi.edu.bo"
                    required
                    value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>"
                >
                <small style="color: #666; display: block; margin-top: 5px;">
                    Debe terminar en @est.emi.edu.bo, @doc.emi.edu.bo o @adm.emi.edu.bo
                </small>
            </div>
            
            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input 
                    type="password" 
                    id="contrasena" 
                    name="contrasena" 
                    placeholder="Ingrese una contraseña"
                    required
                >
                <small style="color: #666; display: block; margin-top: 5px;">
                    La contraseña será encriptada automáticamente con password_hash()
                </small>
            </div>
            
            <button type="submit" class="btn">Crear Usuario</button>
        </form>
        
        <div class="links">
            <a href="login.php">← Ir al Login</a>
        </div>
    </div>
</body>
</html>
