<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar Sesión - Docentes</title>
    <link rel="stylesheet" href="css/auth.css" />
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
      .material-symbols-outlined {
        font-family: 'Material Symbols Outlined';
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      }
      
      /* Password Toggle Styles */
      .password-wrapper {
        position: relative;
      }
      .password-wrapper input {
        padding-right: 50px;
      }
      .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #163E8C;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .toggle-password:hover {
        color: #FED600;
      }
      .toggle-password .material-symbols-outlined {
        font-size: 22px;
      }
      
      /* Toast Notifications */
      .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 10000;
        transform: translateX(400px);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
      }
      .toast-notification.show {
        transform: translateX(0);
        opacity: 1;
      }
      .toast-notification.success { background: linear-gradient(135deg, #22c55e, #16a34a); }
      .toast-notification.error { background: linear-gradient(135deg, #ef4444, #dc2626); }
      .toast-notification.info { background: linear-gradient(135deg, #163E8C, #1e4d8c); }
      .toast-notification.warning { background: linear-gradient(135deg, #eab308, #ca8a04); }
      
      /* Footer Styles */
      .emi-footer {
        background: linear-gradient(135deg, #163E8C 0%, #0d2b5e 100%);
        color: white;
        padding: 0;
        margin-top: auto;
      }
      .footer-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
      }
      .footer-section h4 {
        color: #FED600;
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .footer-section h4 .material-symbols-outlined {
        font-size: 18px;
      }
      .footer-section p {
        color: rgba(255,255,255,0.85);
        font-size: 0.85rem;
        margin: 0.3rem 0;
        line-height: 1.4;
      }
      .footer-section p .material-symbols-outlined {
        font-size: 14px;
        vertical-align: middle;
        margin-right: 4px;
      }
      .footer-bottom {
        background: rgba(0,0,0,0.2);
        padding: 0.75rem 1.5rem;
        text-align: center;
      }
      .footer-bottom p {
        color: #FED600;
        font-size: 0.8rem;
        margin: 0;
      }
      
      body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }
      main {
        flex: 1;
      }
    </style>
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
            <h1><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 2rem; margin-right: 8px;">school</span>Acceso Docentes</h1>
            <p>Ingresa tus credenciales institucionales</p>
          </div>

          <div id="mensaje-error" style="display: none; padding: 10px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;"></div>
          <div id="mensaje-exito" style="display: none; padding: 10px; margin-bottom: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;"></div>

          <form action="php/login.php" method="POST" id="loginForm">
            <input type="hidden" name="tipo_usuario" value="docente">
            
            <div class="form-group">
              <label for="username"><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px;">badge</span> Código de Docente</label>
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
              <label for="password"><span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px;">lock</span> Contraseña</label>
              <div class="password-wrapper">
                <input
                  type="password"
                  id="password"
                  name="password"
                  required
                  minlength="6"
                  placeholder="Ingresa tu contraseña"
                />
                <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                  <span class="material-symbols-outlined">visibility</span>
                </button>
              </div>
              <div class="password-options">
                <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
              </div>
            </div>

            <button type="submit" class="btn-auth primary-btn">
              <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 8px;">login</span>
              Iniciar Sesión
            </button>
          </form>

          <div class="auth-links">
            <p>
              ¿Eres Estudiante?
              <a href="loginEstudiante.php"><span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">person</span> Acceso para estudiantes</a>
            </p>
            <p>
              ¿No tienes cuenta?
              <a href="registro.php"><span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">person_add</span> Regístrate aquí</a>
            </p>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer EMI -->
    <footer class="emi-footer">
      <div class="footer-container">
        <div class="footer-section">
          <h4><span class="material-symbols-outlined">location_on</span> EMI Central</h4>
          <p>Av. Arce No. 2642, Zona San Jorge</p>
          <p>La Paz, Bolivia</p>
        </div>
        <div class="footer-section">
          <h4><span class="material-symbols-outlined">school</span> Unidades Académicas</h4>
          <p>La Paz • Santa Cruz • Cochabamba</p>
          <p>Riberalta • Trópico</p>
        </div>
        <div class="footer-section">
          <h4><span class="material-symbols-outlined">link</span> Páginas Relacionadas</h4>
          <p>Ministerio de Defensa</p>
          <p>Ejército de Bolivia • CEUB</p>
        </div>
        <div class="footer-section">
          <h4><span class="material-symbols-outlined">contact_phone</span> Contacto</h4>
          <p><span class="material-symbols-outlined">phone</span> +591 2432266</p>
          <p><span class="material-symbols-outlined">mail</span> dnis@adm.emi.edu.bo</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>2025 © Escuela Militar de Ingeniería | Última actualización: 15/09/2025</p>
      </div>
    </footer>

    <script>
      // Toast Notification System
      function showToast(message, type = 'info') {
        const icons = {
          success: 'check_circle',
          error: 'error',
          warning: 'warning',
          info: 'info'
        };
        
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.innerHTML = `
          <span class="material-symbols-outlined">${icons[type]}</span>
          <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
          toast.classList.remove('show');
          setTimeout(() => toast.remove(), 400);
        }, 4000);
      }
      
      // Toggle Password Visibility
      function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('.material-symbols-outlined');
        
        if (input.type === 'password') {
          input.type = 'text';
          icon.textContent = 'visibility_off';
          showToast('Contraseña visible', 'info');
        } else {
          input.type = 'password';
          icon.textContent = 'visibility';
          showToast('Contraseña oculta', 'info');
        }
      }
      
      // Form submission notification
      document.getElementById('loginForm').addEventListener('submit', function(e) {
        showToast('Iniciando sesión...', 'info');
      });

      // Mostrar mensajes de error/éxito desde PHP
      window.addEventListener('DOMContentLoaded', function() {
        <?php
        session_start();
        if (isset($_SESSION['error_login'])) {
            $error_msg = htmlspecialchars($_SESSION['error_login'], ENT_QUOTES, 'UTF-8');
            echo "showToast('" . $error_msg . "', 'error');";
            echo "document.getElementById('mensaje-error').textContent = '" . $error_msg . "';";
            echo "document.getElementById('mensaje-error').style.display = 'block';";
            unset($_SESSION['error_login']);
        }
        if (isset($_SESSION['exito_registro'])) {
            $exito_msg = htmlspecialchars($_SESSION['exito_registro'], ENT_QUOTES, 'UTF-8');
            echo "showToast('" . $exito_msg . "', 'success');";
            echo "document.getElementById('mensaje-exito').textContent = '" . $exito_msg . "';";
            echo "document.getElementById('mensaje-exito').style.display = 'block';";
            unset($_SESSION['exito_registro']);
        }
        ?>
      });
    </script>
  </body>
</html>
