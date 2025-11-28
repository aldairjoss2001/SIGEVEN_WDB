<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Autenticación - SIGEVEN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #fed600;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
        .test-section {
            background: #f9f9f9;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #fed600;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border-left-color: #28a745;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border-left-color: #dc3545;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .info {
            color: #004085;
            background-color: #cce5ff;
            border-left-color: #0066cc;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #fed600;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background-color: #fed600;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #e5c100;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Sistema de Autenticación SIGEVEN</h1>
        <p>Página de prueba del sistema de autenticación PHP con MySQL</p>

        <h2>📊 Estado del Sistema</h2>
        <div class="test-section">
            <?php
            require_once 'php/config.php';
            require_once 'php/conexion.php';
            require_once 'php/sesion.php';

            // Prueba 1: Conexión a la base de datos
            echo "<h3>✓ Conexión a Base de Datos</h3>";
            if ($conn->ping()) {
                echo "<p class='success'>Conexión exitosa a MySQL</p>";
                echo "<p>Base de datos: <code>" . DB_NAME . "</code></p>";
            } else {
                echo "<p class='error'>Error de conexión</p>";
            }

            // Prueba 2: Verificar tablas
            echo "<h3>✓ Verificación de Tablas</h3>";
            $tables = ['estudiantes', 'docentes', 'administradores'];
            foreach ($tables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result->num_rows > 0) {
                    $count = $conn->query("SELECT COUNT(*) as total FROM $table")->fetch_assoc();
                    echo "<p class='success'>Tabla <code>$table</code>: {$count['total']} registros</p>";
                } else {
                    echo "<p class='error'>Tabla <code>$table</code> no existe</p>";
                }
            }

            // Prueba 3: Estado de la sesión
            echo "<h3>✓ Estado de Sesión</h3>";
            iniciar_sesion_segura();
            if (esta_autenticado()) {
                $datos = obtener_datos_sesion();
                echo "<p class='success'>Usuario autenticado</p>";
                echo "<table>";
                echo "<tr><th>Campo</th><th>Valor</th></tr>";
                echo "<tr><td>ID</td><td>{$datos['id']}</td></tr>";
                echo "<tr><td>Tipo</td><td>{$datos['tipo']}</td></tr>";
                echo "<tr><td>Nombre</td><td>{$datos['nombre']}</td></tr>";
                echo "<tr><td>Código</td><td>{$datos['codigo']}</td></tr>";
                echo "<tr><td>Correo</td><td>{$datos['correo']}</td></tr>";
                echo "</table>";
                echo "<a href='php/logout.php' class='btn'>Cerrar Sesión</a>";
            } else {
                echo "<p class='info'>No hay sesión activa</p>";
            }

            $conn->close();
            ?>
        </div>

        <h2>👥 Usuarios de Prueba</h2>
        <div class="test-section">
            <p class="info">Puedes usar estos usuarios para probar el sistema:</p>
            
            <h3>Estudiantes</h3>
            <table>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Contraseña</th>
                </tr>
                <tr>
                    <td>E20250-1</td>
                    <td>Juan Pérez González</td>
                    <td>test123</td>
                </tr>
                <tr>
                    <td>E20250-2</td>
                    <td>María López Silva</td>
                    <td>test123</td>
                </tr>
                <tr>
                    <td>E20250-3</td>
                    <td>Carlos Mamani Quispe</td>
                    <td>test123</td>
                </tr>
            </table>

            <h3>Docentes</h3>
            <table>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Contraseña</th>
                </tr>
                <tr>
                    <td>A20250-1</td>
                    <td>Dr. Roberto Gutiérrez</td>
                    <td>test123</td>
                </tr>
                <tr>
                    <td>A20250-2</td>
                    <td>Ing. Ana Fernández</td>
                    <td>test123</td>
                </tr>
            </table>

            <h3>Administrador</h3>
            <table>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Contraseña</th>
                </tr>
                <tr>
                    <td>admin</td>
                    <td>Administrador del Sistema</td>
                    <td>admin123</td>
                </tr>
            </table>
        </div>

        <h2>🔗 Enlaces de Prueba</h2>
        <div class="test-section">
            <a href="loginEstudiante.php" class="btn">Login Estudiantes</a>
            <a href="loginDocente.php" class="btn">Login Docentes</a>
            <a href="../sistema_de_eventos_admin/login.php" class="btn">Login Admin</a>
            <a href="registro.php" class="btn">Registro</a>
        </div>

        <h2>📖 Documentación</h2>
        <div class="test-section">
            <p>Para más información sobre el sistema de autenticación, consulta:</p>
            <ul>
                <li><a href="README_AUTH.md" target="_blank">README_AUTH.md</a> - Documentación completa</li>
                <li><a href="database/schema.sql" target="_blank">schema.sql</a> - Estructura de base de datos</li>
                <li><a href="database/test_data.sql" target="_blank">test_data.sql</a> - Datos de prueba</li>
            </ul>
        </div>

        <h2>⚙️ Configuración</h2>
        <div class="test-section">
            <?php
            echo "<p>Host: <code>" . DB_HOST . "</code></p>";
            echo "<p>Base de datos: <code>" . DB_NAME . "</code></p>";
            echo "<p>Tiempo de sesión: <code>" . (SESSION_LIFETIME / 60) . " minutos</code></p>";
            echo "<p>Nombre de sesión: <code>" . SESSION_NAME . "</code></p>";
            ?>
        </div>
    </div>
</body>
</html>
