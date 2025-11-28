<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar Sesión - Docentes</title>
    <link rel="stylesheet" href="css/auth.css" />
  </head>
  <body>
    <a class="skip-link" href="#main">Saltar al contenido</a>

    <header>
      <div class="header-inner">
        <img src="assets/icons/mobile.png" alt="logo" class="logo" />
        <nav class="main-nav" aria-label="Menú principal">
          <ul class="main-menu">
            <li><a href="index.html">INICIO</a></li>
            <li><a href="#">AYUDA</a></li>
            <li><a href="#">CONTACTO</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <main>
      <div id="main" class="auth-container">
        <div class="login-form">
          <div class="form-header">
            <h1>Acceso Docentes</h1>
            <p>Ingresa tus credenciales institucionales</p>
          </div>

          <div id="mensaje-error" style="display: none; padding: 10px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;"></div>
          <div id="mensaje-exito" style="display: none; padding: 10px; margin-bottom: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;"></div>

          <form action="php/login.php" method="POST">
            <input type="hidden" name="tipo_usuario" value="docente">
            
            <div class="form-group">
              <label for="username">Código de Docente</label>
              <input
                type="text"
                id="username"
                name="username"
                required
                pattern="A[0-9]{5}-[0-9]"
                placeholder="Ej: A20250-1"
                title="Formato requerido: AXXXXX-X (donde X son números)"
              />
            </div>

            <div class="form-group">
              <label for="password">Contraseña</label>
              <input
                type="password"
                id="password"
                name="password"
                required
                minlength="6"
                placeholder="Ingresa tu contraseña"
              />
              <div class="password-options">
                <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
              </div>
            </div>

            <button type="submit" class="btn-auth primary-btn">
              Iniciar Sesión
            </button>
          </form>

          <div class="auth-links">
            <p>
              ¿Eres Estudiante?
              <a href="loginEstudiante.php">Acceso para estudiantes</a>
            </p>
            <p>
              ¿No tienes cuenta?
              <a href="registro.php">Regístrate aquí</a>
            </p>
          </div>
        </div>
      </div>
    </main>

    <footer>
      <p style="color: #fed600">© 2025 Sistema de Gestión de Eventos Universitarios. Todos los derechos reservados.</p>
    </footer>

    <script>
      // Mostrar mensajes de error/éxito desde PHP
      window.addEventListener('DOMContentLoaded', function() {
        <?php
        session_start();
        if (isset($_SESSION['error_login'])) {
            echo "document.getElementById('mensaje-error').textContent = '" . addslashes($_SESSION['error_login']) . "';";
            echo "document.getElementById('mensaje-error').style.display = 'block';";
            unset($_SESSION['error_login']);
        }
        if (isset($_SESSION['exito_registro'])) {
            echo "document.getElementById('mensaje-exito').textContent = '" . addslashes($_SESSION['exito_registro']) . "';";
            echo "document.getElementById('mensaje-exito').style.display = 'block';";
            unset($_SESSION['exito_registro']);
        }
        ?>
      });
    </script>
  </body>
</html>
