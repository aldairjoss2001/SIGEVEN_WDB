<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGEVEN - Sistema de Gestión de Eventos Universitarios</title>
    <link rel="stylesheet" href="sistema_de_eventos/css/auth.css">
</head>
<body>
    <a class="skip-link" href="#main">Saltar al contenido</a>

    <header>
        <div class="header-inner">
            <img src="sistema_de_eventos/assets/icons/mobile.png" alt="logo" class="logo" />
            <nav class="main-nav" aria-label="Menú principal">
                <ul class="main-menu">
                    <li><a href="index.php">INICIO</a></li>
                    <li><a href="#">AYUDA</a></li>
                    <li><a href="#">CONTACTO</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div id="main" class="auth-container">
            <div class="login-form" style="max-width: 600px;">
                <div class="form-header">
                    <h1>SIGEVEN</h1>
                    <p>Sistema de Gestión de Eventos Universitarios</p>
                </div>

                <div style="text-align: left; margin-bottom: 2rem;">
                    <h2 style="color: var(--azul-institucional); font-size: 1.3rem; margin-bottom: 1rem;">¿Qué es SIGEVEN?</h2>
                    <p style="color: #555; line-height: 1.6; margin-bottom: 1rem;">
                        SIGEVEN es una plataforma digital diseñada para facilitar la gestión y organización de eventos 
                        académicos, culturales y deportivos en la Escuela Militar de Ingeniería (EMI).
                    </p>
                    
                    <h3 style="color: var(--azul-institucional); font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 0.8rem;">Funcionalidades Principales</h3>
                    <ul style="color: #555; line-height: 1.8; padding-left: 1.5rem;">
                        <li><strong>Para Estudiantes:</strong> Consulta y registro a eventos universitarios, visualización de calendario académico y notificaciones.</li>
                        <li><strong>Para Docentes:</strong> Creación y gestión de eventos académicos, seguimiento de participantes y reportes.</li>
                        <li><strong>Para Administrativos:</strong> Panel de control completo, gestión de usuarios, aprobación de eventos y estadísticas.</li>
                    </ul>

                    <h3 style="color: var(--azul-institucional); font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 0.8rem;">Beneficios</h3>
                    <ul style="color: #555; line-height: 1.8; padding-left: 1.5rem;">
                        <li>✓ Centralización de información de eventos</li>
                        <li>✓ Acceso rápido y fácil desde cualquier dispositivo</li>
                        <li>✓ Comunicación efectiva entre estudiantes, docentes y administración</li>
                        <li>✓ Reducción de procesos manuales y papelería</li>
                    </ul>
                </div>

                <a href="login.php" class="btn-auth primary-btn" style="text-decoration: none; display: block; text-align: center;">
                    Iniciar Sesión
                </a>
                
                <div class="auth-links">
                    <p>
                        ¿Necesitas crear una cuenta?
                        <a href="crear_usuario_demo.php">Regístrate aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p style="color: #fed600">© 2025 Sistema de Gestión de Eventos Universitarios. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
