# Guía de Instalación Rápida - SIGEVEN Authentication

## Pasos de Instalación

### 1. Preparar el entorno

Asegúrate de tener instalado:
- XAMPP/WAMP/LAMP (Apache + MySQL + PHP 7.0+)
- phpMyAdmin (opcional pero recomendado)

### 2. Copiar archivos

Coloca los archivos del proyecto en el directorio del servidor web:
```
C:/xampp/htdocs/SIGEVEN/         (Windows)
/var/www/html/SIGEVEN/           (Linux)
```

### 3. Crear la base de datos

**Opción A: Usando phpMyAdmin**
1. Abre phpMyAdmin en tu navegador: `http://localhost/phpmyadmin`
2. Haz clic en "Import" (Importar)
3. Selecciona el archivo: `SIGEVEN/sistema_de_eventos/database/schema.sql`
4. Haz clic en "Go" (Ejecutar)

**Opción B: Usando línea de comandos**
```bash
mysql -u root -p < SIGEVEN/sistema_de_eventos/database/schema.sql
```

### 4. (Opcional) Cargar datos de prueba

Para cargar usuarios de ejemplo:

**phpMyAdmin:**
1. Selecciona la base de datos `sigeven`
2. Importa: `SIGEVEN/sistema_de_eventos/database/test_data.sql`

**Línea de comandos:**
```bash
mysql -u root -p sigeven < SIGEVEN/sistema_de_eventos/database/test_data.sql
```

### 5. Configurar credenciales de MySQL

Si tus credenciales de MySQL son diferentes, edita:
`SIGEVEN/sistema_de_eventos/php/config.php`

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('DB_NAME', 'sigeven');
```

### 6. Iniciar el servidor

**XAMPP:**
1. Abre el Panel de Control de XAMPP
2. Inicia Apache
3. Inicia MySQL

**WAMP:**
1. Inicia todos los servicios de WAMP

### 7. Probar el sistema

Abre tu navegador y accede a:

**Página de prueba:**
```
http://localhost/SIGEVEN/SIGEVEN/sistema_de_eventos/test_auth.php
```

Esta página te mostrará:
- Estado de la conexión a la base de datos
- Tablas creadas y número de registros
- Estado de sesión actual
- Lista de usuarios de prueba

**Páginas de login:**
- Estudiantes: `http://localhost/SIGEVEN/SIGEVEN/sistema_de_eventos/loginEstudiante.php`
- Docentes: `http://localhost/SIGEVEN/SIGEVEN/sistema_de_eventos/loginDocente.php`
- Admin: `http://localhost/SIGEVEN/SIGEVEN/sistema_de_eventos_admin/login.php`

**Página de registro:**
```
http://localhost/SIGEVEN/SIGEVEN/sistema_de_eventos/registro.php
```

### 8. Credenciales de prueba

Si cargaste los datos de prueba, puedes usar:

**Estudiantes:**
- Usuario: E20250-1 / Contraseña: test123
- Usuario: E20250-2 / Contraseña: test123
- Usuario: E20250-3 / Contraseña: test123

**Docentes:**
- Usuario: A20250-1 / Contraseña: test123
- Usuario: A20250-2 / Contraseña: test123

**Administrador:**
- Usuario: admin / Contraseña: admin123

## Solución de Problemas

### Error: "Connection refused"
- Asegúrate de que MySQL está ejecutándose
- Verifica que el puerto 3306 no esté bloqueado

### Error: "Table doesn't exist"
- Ejecuta nuevamente el archivo `schema.sql`
- Verifica que la base de datos `sigeven` existe

### Error: "Access denied"
- Verifica las credenciales en `php/config.php`
- El usuario MySQL debe tener permisos sobre la base de datos `sigeven`

### Las sesiones no funcionan
- Verifica que PHP tiene permisos de escritura en el directorio de sesiones
- En Windows: `C:\xampp\tmp`
- En Linux: `/var/lib/php/sessions` o `/tmp`

### Error 404 - Página no encontrada
- Verifica la ruta completa del proyecto
- Asegúrate de que Apache está apuntando al directorio correcto

## Próximos Pasos

Una vez que el sistema esté funcionando:

1. Cambia la contraseña del administrador por defecto
2. Lee la documentación completa en `README_AUTH.md`
3. Protege las páginas que requieren autenticación
4. Personaliza los mensajes y estilos según necesites

## Estructura de Archivos Importantes

```
SIGEVEN/sistema_de_eventos/
├── php/
│   ├── config.php              # ← Editar credenciales aquí
│   ├── conexion.php
│   ├── sesion.php
│   ├── login.php
│   ├── logout.php
│   ├── insertar_usuario.php
│   └── verificar_sesion.php
├── database/
│   ├── schema.sql              # ← Importar primero
│   └── test_data.sql           # ← Importar después (opcional)
├── test_auth.php               # ← Página de prueba
├── loginEstudiante.php
├── loginDocente.php
└── registro.php

sistema_de_eventos_admin/
└── login.php
```

## Soporte

Para más ayuda, consulta:
- `README_AUTH.md` - Documentación completa del sistema
- Logs de error de Apache: `xampp/apache/logs/error.log`
- Logs de error de PHP: Verifica `php.ini` para ubicación
