<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario - SIGEVEN</title>
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
    .form-group-icon input,
    .form-group-icon select {
      padding-left: 45px;
    }
    .user-type-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 15px;
      margin-bottom: 1.5rem;
    }
    .user-type-card {
      padding: 1.2rem;
      border: 2px solid #ddd;
      border-radius: 12px;
      cursor: pointer;
      text-align: center;
      transition: all 0.3s ease;
      background: white;
    }
    .user-type-card:hover {
      border-color: #163E8C;
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(22, 62, 140, 0.2);
    }
    .user-type-card.selected {
      border-color: #163E8C;
      background: rgba(22, 62, 140, 0.05);
    }
    .user-type-card .material-symbols-outlined {
      font-size: 2.5rem;
      color: #163E8C;
      margin: 0 auto 8px;
      display: block;
    }
    .user-type-card label {
      font-weight: 600;
      color: #333;
      cursor: pointer;
      font-size: 0.9rem;
    }
    .user-type-card input[type="radio"] {
      display: none;
    }
    .info-text {
      font-size: 0.85rem;
      color: #666;
      margin-top: 8px;
      line-height: 1.4;
    }
    .info-badge {
      display: inline-block;
      background: #FED600;
      color: #163E8C;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      margin-top: 8px;
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
          <h1><span class="material-symbols-outlined" style="font-size: 2rem; vertical-align: middle;">person_add</span>Registro de Usuario</h1>
          <p>Crea tu cuenta en SIGEVEN</p>
        </div>

        <div id="mensaje-error" style="display: none; padding: 10px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;"></div>
        <div id="mensaje-exito" style="display: none; padding: 10px; margin-bottom: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;"></div>

        <form action="php/insertar_usuario.php" method="POST" id="form-registro">
          <!-- Tipo de Usuario con Cards -->
          <div class="form-group">
            <label style="display: block; margin-bottom: 12px; font-weight: 600;">
              <span class="material-symbols-outlined" style="vertical-align: middle;">badge</span>
              Tipo de Usuario
            </label>
            <div class="user-type-cards">
              <div class="user-type-card" onclick="selectUserType('estudiante')">
                <input type="radio" name="tipo_usuario" id="tipo_estudiante" value="estudiante" required>
                <label for="tipo_estudiante">
                  <span class="material-symbols-outlined">school</span>
                  Estudiante
                </label>
              </div>
              <div class="user-type-card" onclick="selectUserType('docente')">
                <input type="radio" name="tipo_usuario" id="tipo_docente" value="docente" required>
                <label for="tipo_docente">
                  <span class="material-symbols-outlined">psychology</span>
                  Docente
                </label>
              </div>
              <div class="user-type-card" onclick="selectUserType('externo')">
                <input type="radio" name="tipo_usuario" id="tipo_externo" value="externo" required>
                <label for="tipo_externo">
                  <span class="material-symbols-outlined">public</span>
                  Externo
                </label>
                <span class="info-badge">¡Nuevo!</span>
              </div>
            </div>
            <p class="info-text" id="tipo-info-text" style="display: none;"></p>
          </div>

          <!-- Código (solo para estudiantes/docentes) -->
          <div class="form-group form-group-icon" id="codigo-group" style="display: none;">
            <label for="codigo">
              <span class="material-symbols-outlined">tag</span>
              Código
            </label>
            <span class="material-symbols-outlined">fingerprint</span>
            <input type="text" id="codigo" name="codigo" placeholder="Ej: E20250-1 o A20250-1">
          </div>

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

          <!-- Información adicional para externos -->
          <div id="externo-info" style="display: none; background: #f0f7ff; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
            <p style="margin: 0; font-size: 0.9rem; color: #163E8C; line-height: 1.6;">
              <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 1.2rem;">info</span>
              <strong>Usuarios Externos:</strong> Personas que no pertenecen a la institución pero desean participar en eventos abiertos al público.
            </p>
          </div>

          <button type="submit" class="btn-auth primary-btn">
            <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 8px;">how_to_reg</span>
            Registrarse
          </button>
        </form>

        <div class="auth-links">
          <p>
            ¿Ya tienes cuenta? 
            <a href="../login.php"><span class="material-symbols-outlined" style="font-size: 1rem;">login</span>Iniciar Sesión</a>
          </p>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <p style="color: #fed600">© 2025 Sistema de Gestión de Eventos Universitarios. Todos los derechos reservados.</p>
  </footer>

  <script>
    // Función para seleccionar tipo de usuario con las cards
    function selectUserType(tipo) {
      // Actualizar radio button
      document.getElementById('tipo_' + tipo).checked = true;
      
      // Actualizar estilos visuales de las cards
      document.querySelectorAll('.user-type-card').forEach(card => {
        card.classList.remove('selected');
      });
      event.currentTarget.classList.add('selected');
      
      // Mostrar/ocultar campo de código según el tipo
      const codigoGroup = document.getElementById('codigo-group');
      const codigoInput = document.getElementById('codigo');
      const externoInfo = document.getElementById('externo-info');
      const tipoInfoText = document.getElementById('tipo-info-text');
      
      if (tipo === 'externo') {
        codigoGroup.style.display = 'none';
        codigoInput.required = false;
        externoInfo.style.display = 'block';
        tipoInfoText.style.display = 'block';
        tipoInfoText.innerHTML = '<span class="material-symbols-outlined" style="font-size: 1rem;">info</span> Los usuarios externos no requieren código institucional.';
      } else {
        codigoGroup.style.display = 'block';
        codigoInput.required = true;
        externoInfo.style.display = 'none';
        tipoInfoText.style.display = 'block';
        
        if (tipo === 'estudiante') {
          codigoInput.placeholder = 'Ej: E20250-1';
          codigoInput.pattern = 'E[0-9]{5}-[0-9]';
          codigoInput.title = 'Formato requerido: EXXXXX-X (donde X son números)';
          tipoInfoText.innerHTML = '<span class="material-symbols-outlined" style="font-size: 1rem;">info</span> Código de estudiante: formato EXXXXX-X';
        } else if (tipo === 'docente') {
          codigoInput.placeholder = 'Ej: A20250-1';
          codigoInput.pattern = 'A[0-9]{5}-[0-9]';
          codigoInput.title = 'Formato requerido: AXXXXX-X (donde X son números)';
          tipoInfoText.innerHTML = '<span class="material-symbols-outlined" style="font-size: 1rem;">info</span> Código de docente: formato AXXXXX-X';
        }
      }
    }

    // Mostrar mensajes de error/éxito desde PHP
    window.addEventListener('DOMContentLoaded', function() {
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
    });
  </script>
</body>
</html>
