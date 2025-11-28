<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario Externo - SIGEVEN</title>
  <link rel="stylesheet" href="css/auth.css">
  <!-- Material Icons from Google Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      vertical-align: middle;
      margin-right: 8px;
    }
    .form-group-icon {
      position: relative;
    }
    .form-group-icon .material-symbols-outlined {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #163E8C;
      pointer-events: none;
    }
    .form-group-icon input {
      padding-left: 45px;
    }
    .info-box {
      background: #f0f7ff;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border-left: 4px solid #163E8C;
    }
    .info-box p {
      margin: 0;
      font-size: 0.9rem;
      color: #163E8C;
      line-height: 1.6;
    }
    .external-badge {
      display: inline-block;
      background: #FED600;
      color: #163E8C;
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 700;
      margin-left: 10px;
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
          <h1>
            <span class="material-symbols-outlined" style="font-size: 2rem; vertical-align: middle;">person_add</span>
            Registro Externo
            <span class="external-badge">Público</span>
          </h1>
          <p>Crea tu cuenta para acceder a eventos públicos</p>
        </div>

        <!-- Info Box -->
        <div class="info-box">
          <p>
            <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 1.3rem;">info</span>
            <strong>Usuarios Externos:</strong> Personas que no pertenecen a la institución pero desean participar en eventos abiertos al público.
          </p>
        </div>

        <div id="mensaje-error" style="display: none; padding: 10px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;"></div>
        <div id="mensaje-exito" style="display: none; padding: 10px; margin-bottom: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;"></div>

        <form action="php/insertar_usuario_externo.php" method="POST" id="form-registro">
          <!-- Nombre Completo -->
          <div class="form-group form-group-icon">
            <label for="nombre">
              <span class="material-symbols-outlined">person</span>
              Nombre Completo
            </label>
            <span class="material-symbols-outlined">person</span>
            <input type="text" id="nombre" name="nombre" required placeholder="Ingresa tu nombre completo">
          </div>

          <!-- Correo Electrónico -->
          <div class="form-group form-group-icon">
            <label for="correo">
              <span class="material-symbols-outlined">mail</span>
              Correo Electrónico
            </label>
            <span class="material-symbols-outlined">mail</span>
            <input type="email" id="correo" name="correo" required placeholder="ejemplo@correo.com">
            <small style="display: block; margin-top: 5px; color: #666;">Puedes usar cualquier correo electrónico</small>
          </div>

          <!-- Teléfono (opcional) -->
          <div class="form-group form-group-icon">
            <label for="telefono">
              <span class="material-symbols-outlined">phone</span>
              Teléfono (opcional)
            </label>
            <span class="material-symbols-outlined">phone</span>
            <input type="tel" id="telefono" name="telefono" placeholder="Ej: 70123456">
          </div>

          <!-- Contraseña -->
          <div class="form-group form-group-icon">
            <label for="contrasena">
              <span class="material-symbols-outlined">lock</span>
              Contraseña
            </label>
            <span class="material-symbols-outlined">lock</span>
            <input type="password" id="contrasena" name="contrasena" required minlength="6" placeholder="Mínimo 6 caracteres">
          </div>

          <button type="submit" class="btn-auth primary-btn">
            <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 8px;">how_to_reg</span>
            Registrarse
          </button>
        </form>

        <p class="form-footer">
          ¿Ya tienes cuenta? <a href="../login.php" class="link">Inicia sesión aquí</a>
        </p>

        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
          <p style="text-align: center; color: #666; font-size: 0.9rem;">
            <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 1rem;">school</span>
            ¿Eres estudiante, docente o administrativo?<br>
            <a href="../login.php" style="color: #163E8C; font-weight: 600;">Contacta al administrador para registrarte</a>
          </p>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 Sistema de Gestión de Eventos Universitarios. Todos los derechos reservados.</p>
  </footer>

  <script>
    // Mostrar mensajes de error/éxito desde sesión PHP
    <?php
    session_start();
    if (isset($_SESSION['error_registro'])) {
        echo "document.getElementById('mensaje-error').textContent = '" . addslashes($_SESSION['error_registro']) . "';";
        echo "document.getElementById('mensaje-error').style.display = 'block';";
        unset($_SESSION['error_registro']);
    }
    if (isset($_SESSION['exito_registro'])) {
        echo "document.getElementById('mensaje-exito').textContent = '" . addslashes($_SESSION['exito_registro']) . "';";
        echo "document.getElementById('mensaje-exito').style.display = 'block';";
        unset($_SESSION['exito_registro']);
    }
    ?>
  </script>
</body>
</html>
