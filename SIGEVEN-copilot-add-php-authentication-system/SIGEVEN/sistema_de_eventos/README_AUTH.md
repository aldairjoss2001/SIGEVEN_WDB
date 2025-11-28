# Sistema de Autenticación PHP con MySQL para SIGEVEN

Este documento describe el sistema de autenticación implementado en el Sistema de Gestión de Eventos Universitarios (SIGEVEN).

## Características del Sistema

- ✅ Autenticación segura con hash de contraseñas (bcrypt)
- ✅ Gestión de sesiones PHP
- ✅ Protección contra SQL injection mediante prepared statements
- ✅ Soporte para tres tipos de usuarios:
  - **Estudiantes**: Código formato E00000-0
  - **Docentes**: Código formato A00000-0
  - **Administradores**: Username alfanumérico
- ✅ Validación de formularios en cliente y servidor
- ✅ Mensajes de error y éxito para el usuario
- ✅ Sistema de registro para estudiantes y docentes
- ✅ Control de tiempo de vida de sesiones (1 hora por defecto)

## Instalación y Configuración

### 1. Requisitos Previos

- Servidor web con PHP 7.0 o superior (Apache/XAMPP/WAMP)
- MySQL 5.7 o superior
- Extensiones PHP requeridas:
  - mysqli
  - session

### 2. Configuración de la Base de Datos

1. Accede a tu servidor MySQL (phpMyAdmin o línea de comandos)

2. Ejecuta el script de creación de base de datos:
   ```bash
   mysql -u root -p < SIGEVEN/sistema_de_eventos/database/schema.sql
   ```
   
   O importa manualmente el archivo `database/schema.sql` desde phpMyAdmin.

3. El script creará:
   - Base de datos `sigeven`
   - Tabla `estudiantes`
   - Tabla `docentes`
   - Tabla `administradores`
   - Un usuario administrador por defecto

### 3. Configuración del Servidor

1. Edita el archivo `php/config.php` si tus credenciales de MySQL son diferentes:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'sigeven');
   ```

2. Asegúrate de que el módulo `mod_rewrite` está habilitado en Apache.

3. Coloca los archivos en el directorio raíz de tu servidor web (por ejemplo: `htdocs` o `www`).

### 4. Usuario Administrador por Defecto

El sistema incluye un usuario administrador predefinido:

- **Usuario**: admin
- **Contraseña**: admin123

**⚠️ IMPORTANTE**: Cambia esta contraseña inmediatamente después de la instalación.

## Estructura del Sistema

### Archivos PHP de Autenticación

```
php/
├── config.php              # Configuración general del sistema
├── conexion.php            # Conexión a la base de datos
├── sesion.php              # Gestión de sesiones
├── login.php               # Procesa el inicio de sesión
├── logout.php              # Cierra la sesión
├── insertar_usuario.php    # Registro de nuevos usuarios
└── verificar_sesion.php    # Verifica estado de sesión (API)
```

### Páginas de Login

- `loginEstudiante.php` - Login para estudiantes
- `loginDocente.php` - Login para docentes  
- `sistema_de_eventos_admin/login.php` - Login para administradores
- `registro.php` - Registro de nuevos usuarios

### Base de Datos

El archivo `database/schema.sql` contiene la estructura completa de las tablas.

## Uso del Sistema

### Para Estudiantes y Docentes

1. **Registro**:
   - Accede a `registro.php`
   - Selecciona el tipo de usuario (Estudiante/Docente)
   - Completa el formulario con tus datos
   - El código debe seguir el formato:
     - Estudiantes: `E00000-0` (ejemplo: E20250-1)
     - Docentes: `A00000-0` (ejemplo: A20250-1)

2. **Inicio de Sesión**:
   - Accede a `loginEstudiante.php` o `loginDocente.php`
   - Ingresa tu código y contraseña
   - Serás redirigido a tu perfil

3. **Cerrar Sesión**:
   - Utiliza el botón de logout o accede a `php/logout.php`

### Para Administradores

1. **Inicio de Sesión**:
   - Accede a `sistema_de_eventos_admin/login.php`
   - Usa las credenciales de administrador
   - Serás redirigido al panel administrativo

## Protección de Páginas

Para proteger páginas que requieren autenticación, añade al inicio del archivo PHP:

```php
<?php
require_once 'php/sesion.php';
requerir_autenticacion('estudiante'); // o 'docente' o 'admin'
?>
```

Ejemplo completo:

```php
<?php
require_once 'php/sesion.php';
requerir_autenticacion('estudiante');
$usuario = obtener_datos_sesion();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Perfil de <?php echo htmlspecialchars($usuario['nombre']); ?></title>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?></h1>
    <p>Código: <?php echo htmlspecialchars($usuario['codigo']); ?></p>
    <a href="php/logout.php">Cerrar Sesión</a>
</body>
</html>
```

## Verificación de Sesión con JavaScript

Para verificar la sesión desde JavaScript (útil para SPAs):

```javascript
fetch('php/verificar_sesion.php')
  .then(response => response.json())
  .then(data => {
    if (data.autenticado) {
      console.log('Usuario autenticado:', data.usuario);
    } else {
      window.location.href = 'loginEstudiante.php';
    }
  });
```

## Seguridad

El sistema implementa las siguientes medidas de seguridad:

1. **Contraseñas Hasheadas**: Uso de `password_hash()` con BCRYPT
2. **Prepared Statements**: Prevención de SQL injection
3. **Validación de Datos**: En cliente y servidor
4. **Sesiones Seguras**: 
   - Cookie HttpOnly
   - Regeneración de ID de sesión
   - Tiempo de vida limitado
5. **Sanitización de Entrada**: Limpieza de datos de formularios

## Personalización

### Cambiar Tiempo de Sesión

Edita `php/config.php`:
```php
define('SESSION_LIFETIME', 7200); // 2 horas en segundos
```

### Cambiar Rutas de Redirección

Edita `php/config.php`:
```php
define('LOGIN_ESTUDIANTE_URL', '../loginEstudiante.php');
define('LOGIN_DOCENTE_URL', '../loginDocente.php');
define('LOGIN_ADMIN_URL', '../../sistema_de_eventos_admin/login.php');
```

## Solución de Problemas

### Error de Conexión a la Base de Datos

- Verifica que MySQL esté ejecutándose
- Comprueba las credenciales en `php/config.php`
- Asegúrate de que la base de datos `sigeven` existe

### Las Sesiones no Persisten

- Verifica que PHP tenga permisos de escritura en el directorio de sesiones
- Comprueba la configuración de `session.save_path` en `php.ini`

### Errores de Login

- Verifica que el usuario existe en la base de datos
- Comprueba que el campo `activo` esté en 1
- Revisa los logs de error de PHP

## Próximos Pasos

Funcionalidades recomendadas para implementar:

- [ ] Recuperación de contraseña por correo
- [ ] Validación de correo electrónico
- [ ] Sistema de permisos por roles
- [ ] Registro de actividad (logs)
- [ ] Autenticación de dos factores (2FA)
- [ ] Cambio de contraseña desde el perfil
- [ ] Límite de intentos de login

## Soporte

Para más información sobre el proyecto SIGEVEN, contacta al equipo de desarrollo.
