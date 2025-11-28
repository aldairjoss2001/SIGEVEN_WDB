<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo - Sistema de Eventos</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-card">
            <!-- Header -->
            <div class="admin-login-header">
                <div class="admin-logo">
                    <span class="logo-icon">⚙️</span>
                    <h1>Panel Administrativo</h1>
                </div>
                <p class="admin-subtitle">Sistema de Gestión de Eventos Universitarios</p>
            </div>

            <!-- Form -->
            <div id="mensaje-error" style="display: none; padding: 10px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;"></div>
            
            <form class="admin-login-form" action="../sistema_de_eventos/php/login.php" method="POST">
                <input type="hidden" name="tipo_usuario" value="admin">
                
                <div class="form-group">
                    <label for="admin-username" class="required">Usuario Administrativo</label>
                    <input type="text" id="admin-username" name="username" 
                           placeholder="usuario.admin" required>
                </div>
                
                <div class="form-group">
                    <label for="admin-password" class="required">Contraseña</label>
                    <input type="password" id="admin-password" name="password" 
                           placeholder="••••••••" required>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span class="checkmark"></span>
                        Mantener sesión iniciada
                    </label>
                </div>
                
                <button type="submit" class="admin-login-btn">
                    <span class="btn-icon">🔐</span>
                    Acceder al Panel
                </button>
            </form>

            <!-- Footer -->
            <div class="admin-login-footer">
                <div class="security-notice">
                    <span class="security-icon">⚠️</span>
                    <p>Acceso restringido al personal autorizado de la EMI</p>
                </div>
            </div>
        </div>
    </div>

    <script>
      // Mostrar mensajes de error desde PHP
      window.addEventListener('DOMContentLoaded', function() {
        <?php
        session_start();
        if (isset($_SESSION['error_login'])) {
            echo "document.getElementById('mensaje-error').textContent = '" . addslashes($_SESSION['error_login']) . "';";
            echo "document.getElementById('mensaje-error').style.display = 'block';";
            unset($_SESSION['error_login']);
        }
        ?>
      });
    </script>
</body>
</html>