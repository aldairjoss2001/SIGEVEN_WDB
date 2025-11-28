# Sistema de Inicio de Sesión Unificado - SIGEVEN

## 📋 Descripción

Sistema de autenticación unificado para SIGEVEN que determina el rol del usuario según el dominio de su correo electrónico.

---

## 🚀 Instalación y Configuración

### 1. Requisitos Previos

- XAMPP instalado en Windows
- Apache y MySQL activos

### 2. Ubicación de los Archivos

Coloca todos los archivos en la carpeta del proyecto:

```
C:\xampp\htdocs\SIGEVEN\
```

Estructura de archivos:

```
SIGEVEN/
├── conexion.php                    # Conexión a la base de datos
├── login.php                       # Formulario de inicio de sesión
├── logout.php                      # Cerrar sesión
├── dashboard_estudiante.php        # Panel de estudiantes
├── dashboard_docente.php           # Panel de docentes
├── dashboard_admin.php             # Panel administrativo
└── crear_usuario_demo.php          # (Opcional) Crear usuarios de prueba
```

### 3. Crear la Base de Datos

**Opción A: Usando phpMyAdmin**

1. Abre tu navegador y ve a: `http://localhost/phpmyadmin`
2. Haz clic en la pestaña "SQL"
3. Copia y pega todo el contenido del archivo `schema_unificado.sql`
4. Haz clic en "Continuar" o "Go"

**Opción B: Desde línea de comandos**

```bash
mysql -u root -p < schema_unificado.sql
```

Esto creará:
- Base de datos: `SIGEVEN`
- Tabla: `usuarios` con campos id, nombre, correo, contrasena
- 3 usuarios de prueba

---

## 👥 Usuarios de Prueba

Después de ejecutar el script SQL, tendrás estos usuarios:

| Tipo | Correo | Contraseña | Rol |
|------|--------|------------|-----|
| Estudiante | juan.perez@est.emi.edu.bo | password123 | estudiante |
| Docente | maria.lopez@doc.emi.edu.bo | password123 | docente |
| Administrativo | carlos.mamani@adm.emi.edu.bo | password123 | administrativo |

---

## 🔐 Lógica de Roles

El sistema detecta automáticamente el rol según el dominio del correo:

| Dominio | Rol |
|---------|-----|
| @est.emi.edu.bo | estudiante |
| @doc.emi.edu.bo | docente |
| @adm.emi.edu.bo | administrativo |

**NO hay campo "rol" en la base de datos.** Todo se determina por el correo.

---

## 🧪 Cómo Probar el Sistema

### Paso 1: Asegúrate de que Apache y MySQL estén activos

1. Abre el Panel de Control de XAMPP
2. Inicia "Apache"
3. Inicia "MySQL"

### Paso 2: Importa la base de datos

Ejecuta el archivo `schema_unificado.sql` en phpMyAdmin o MySQL

### Paso 3: Accede al login

Abre tu navegador y ve a:

```
http://localhost/SIGEVEN/login.php
```

### Paso 4: Prueba con diferentes usuarios

**Probar como Estudiante:**
- Correo: `juan.perez@est.emi.edu.bo`
- Contraseña: `password123`
- Te redirigirá a: `dashboard_estudiante.php`

**Probar como Docente:**
- Correo: `maria.lopez@doc.emi.edu.bo`
- Contraseña: `password123`
- Te redirigirá a: `dashboard_docente.php`

**Probar como Administrativo:**
- Correo: `carlos.mamani@adm.emi.edu.bo`
- Contraseña: `password123`
- Te redirigirá a: `dashboard_admin.php`

### Paso 5: Crear usuarios adicionales (opcional)

Accede a:

```
http://localhost/SIGEVEN/crear_usuario_demo.php
```

Completa el formulario para crear nuevos usuarios. Recuerda usar un correo con dominio válido.

---

## 📁 Descripción de Archivos

### conexion.php

- Establece conexión a MySQL usando PDO
- Contiene la función `obtener_rol_por_correo($correo)` que determina el rol
- Configuración de la base de datos

### login.php

- Formulario único de inicio de sesión
- Valida correo y contraseña
- Determina el rol automáticamente
- Redirige al dashboard correspondiente
- Muestra mensajes de error si las credenciales son incorrectas

### logout.php

- Cierra la sesión del usuario
- Destruye todas las variables de sesión
- Redirige al login

### dashboard_estudiante.php

- Panel para estudiantes
- Verifica que el usuario tenga rol "estudiante"
- Muestra información de la cuenta
- Botón para cerrar sesión

### dashboard_docente.php

- Panel para docentes
- Verifica que el usuario tenga rol "docente"
- Muestra información de la cuenta
- Botón para cerrar sesión

### dashboard_admin.php

- Panel para administrativos
- Verifica que el usuario tenga rol "administrativo"
- Muestra información de la cuenta
- Botón para cerrar sesión

### crear_usuario_demo.php

- Script de ejemplo para crear usuarios
- Muestra cómo usar `password_hash()`
- Solo para desarrollo (eliminar en producción)

---

## 🔒 Características de Seguridad

1. **Contraseñas encriptadas:** Todas las contraseñas se almacenan usando `password_hash()` con `PASSWORD_DEFAULT` (bcrypt)

2. **Validación de contraseñas:** Se usa `password_verify()` para comparar las contraseñas de forma segura

3. **PDO con Prepared Statements:** Previene inyección SQL

4. **Validación de sesión:** Cada dashboard verifica que:
   - Exista una sesión activa
   - El rol coincida con el dashboard

5. **Protección contra acceso no autorizado:** Si un usuario intenta acceder a un dashboard que no le corresponde, se cierra su sesión y se redirige al login

---

## 🎨 Personalización del CSS

Los archivos actuales incluyen CSS inline básico. Para aplicar tus propios estilos:

1. Crea un archivo `css/login.css` con tus estilos
2. En `login.php`, reemplaza el `<style>` interno por:

```html
<link rel="stylesheet" href="css/login.css">
```

3. Haz lo mismo con los dashboards

---

## ❓ Solución de Problemas

### Error: "Access denied for user 'root'@'localhost'"

**Solución:** Verifica que MySQL esté iniciado en XAMPP. Si tienes una contraseña en MySQL, edita `conexion.php` y cambia:

```php
$password = ""; // Por defecto está vacío
```

a:

```php
$password = "tu_contraseña_mysql";
```

### Error: "Could not find driver"

**Solución:** Activa la extensión PDO de MySQL en PHP:

1. Abre `C:\xampp\php\php.ini`
2. Busca `;extension=pdo_mysql`
3. Quita el punto y coma: `extension=pdo_mysql`
4. Guarda y reinicia Apache

### Error: "Table 'sigeven.usuarios' doesn't exist"

**Solución:** Asegúrate de haber ejecutado el script `schema_unificado.sql` en phpMyAdmin o MySQL.

### El login no funciona con los usuarios de prueba

**Solución:** 
1. Verifica que ejecutaste el script SQL correctamente
2. En phpMyAdmin, ve a la base de datos SIGEVEN → tabla usuarios
3. Verifica que los 3 usuarios estén insertados
4. La contraseña es: `password123` (todo en minúsculas)

---

## 📞 Soporte

Si tienes problemas:

1. Verifica que Apache y MySQL estén activos en XAMPP
2. Revisa los logs de error de PHP en: `C:\xampp\apache\logs\error.log`
3. Asegúrate de que la base de datos `SIGEVEN` existe y tiene la tabla `usuarios`
4. Verifica que los archivos estén en `C:\xampp\htdocs\SIGEVEN\`

---

## 🔄 Flujo del Sistema

```
Usuario → login.php
    ↓
Ingresa correo y contraseña
    ↓
Sistema busca usuario en BD
    ↓
Verifica contraseña con password_verify()
    ↓
Detecta rol por dominio del correo
    ↓
Crea sesión con datos del usuario
    ↓
Redirige según rol:
    • @est.emi.edu.bo → dashboard_estudiante.php
    • @doc.emi.edu.bo → dashboard_docente.php
    • @adm.emi.edu.bo → dashboard_admin.php
```

---

## ✅ Checklist de Verificación

- [ ] XAMPP instalado
- [ ] Apache iniciado
- [ ] MySQL iniciado
- [ ] Base de datos SIGEVEN creada
- [ ] Tabla usuarios creada
- [ ] 3 usuarios de prueba insertados
- [ ] Archivos PHP en C:\xampp\htdocs\SIGEVEN\
- [ ] Probado login con cada tipo de usuario
- [ ] Verificado que el logout funciona
- [ ] Verificado que no se puede acceder a dashboards sin sesión

---

**¡Listo! Tu sistema de inicio de sesión unificado está funcionando.**
