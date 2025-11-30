-- ===========================================
-- BASE DE DATOS COMPLETA PARA SIGEVEN
-- Sistema de Gestión de Eventos Universitarios
-- ===========================================

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS SIGEVEN DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE SIGEVEN;

-- ===========================================
-- TABLA: usuarios (unificada para todos los roles)
-- ===========================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('estudiante', 'docente', 'admin', 'externo') NOT NULL DEFAULT 'estudiante',
    codigo VARCHAR(20) NULL,
    carrera VARCHAR(100) NULL,
    telefono VARCHAR(20) NULL,
    foto_perfil VARCHAR(255) NULL,
    estado ENUM('activo', 'inactivo', 'pendiente') NOT NULL DEFAULT 'pendiente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    INDEX idx_correo (correo),
    INDEX idx_rol (rol),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- TABLA: espacios (aulas, auditorios, canchas, etc.)
-- ===========================================
CREATE TABLE IF NOT EXISTS espacios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('aula', 'auditorio', 'laboratorio', 'cancha', 'patio', 'salon', 'otro') NOT NULL,
    ubicacion VARCHAR(150) NOT NULL,
    capacidad INT NOT NULL DEFAULT 0,
    equipamiento TEXT NULL,
    disponible BOOLEAN NOT NULL DEFAULT TRUE,
    imagen VARCHAR(255) NULL,
    descripcion TEXT NULL,
    horario_apertura TIME DEFAULT '07:00:00',
    horario_cierre TIME DEFAULT '22:00:00',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_disponible (disponible)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- TABLA: eventos
-- ===========================================
CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    tipo ENUM('academico', 'cultural', 'deportivo', 'social', 'otro') NOT NULL,
    categoria VARCHAR(100) NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    espacio_id INT NULL,
    capacidad INT NOT NULL DEFAULT 0,
    organizador_id INT NOT NULL,
    estado ENUM('borrador', 'pendiente', 'aprobado', 'rechazado', 'cancelado', 'finalizado') NOT NULL DEFAULT 'borrador',
    imagen VARCHAR(255) NULL,
    requisitos TEXT NULL,
    documentos TEXT NULL COMMENT 'JSON con lista de documentos adjuntos',
    motivo_rechazo TEXT NULL,
    publico BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_estado (estado),
    INDEX idx_fecha_inicio (fecha_inicio),
    INDEX idx_organizador (organizador_id),
    FOREIGN KEY (espacio_id) REFERENCES espacios(id) ON DELETE SET NULL,
    FOREIGN KEY (organizador_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- TABLA: solicitudes (solicitudes de eventos)
-- ===========================================
CREATE TABLE IF NOT EXISTS solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    solicitante_id INT NOT NULL,
    tipo ENUM('creacion', 'modificacion', 'cancelacion', 'inscripcion') NOT NULL DEFAULT 'creacion',
    estado ENUM('pendiente', 'aprobada', 'rechazada', 'en_revision') NOT NULL DEFAULT 'pendiente',
    comentarios TEXT NULL,
    respuesta_admin TEXT NULL,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta TIMESTAMP NULL,
    admin_id INT NULL,
    INDEX idx_estado (estado),
    INDEX idx_tipo (tipo),
    INDEX idx_solicitante (solicitante_id),
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (solicitante_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- TABLA: inscripciones (asistencia a eventos)
-- ===========================================
CREATE TABLE IF NOT EXISTS inscripciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'asistio', 'no_asistio', 'cancelada') NOT NULL DEFAULT 'pendiente',
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_asistencia TIMESTAMP NULL,
    certificado_generado BOOLEAN DEFAULT FALSE,
    INDEX idx_evento (evento_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_estado (estado),
    UNIQUE KEY unique_inscripcion (evento_id, usuario_id),
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- TABLA: reservas_espacios
-- ===========================================
CREATE TABLE IF NOT EXISTS reservas_espacios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    espacio_id INT NOT NULL,
    evento_id INT NULL,
    usuario_id INT NOT NULL,
    fecha_reserva DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    estado ENUM('pendiente', 'aprobada', 'rechazada', 'cancelada') NOT NULL DEFAULT 'pendiente',
    motivo VARCHAR(255) NULL,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_espacio (espacio_id),
    INDEX idx_fecha (fecha_reserva),
    INDEX idx_estado (estado),
    FOREIGN KEY (espacio_id) REFERENCES espacios(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- TABLA: notificaciones
-- ===========================================
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo ENUM('info', 'exito', 'advertencia', 'error', 'evento', 'solicitud') NOT NULL DEFAULT 'info',
    leida BOOLEAN NOT NULL DEFAULT FALSE,
    enlace VARCHAR(255) NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario_id),
    INDEX idx_leida (leida),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- DATOS DE PRUEBA
-- ===========================================

-- Usuarios de prueba (contraseña: password123)
INSERT INTO usuarios (nombre, correo, contrasena, rol, codigo, carrera, estado) VALUES 
('Administrador Sistema', 'admin@adm.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ADM001', NULL, 'activo'),
('Dr. Carlos Mendoza', 'carlos.mendoza@doc.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente', 'DOC001', 'Ingeniería de Sistemas', 'activo'),
('Lic. Ana García', 'ana.garcia@doc.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente', 'DOC002', 'Ingeniería Civil', 'activo'),
('Juan Pérez López', 'juan.perez@est.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 'E213456-7', 'Ingeniería de Sistemas', 'activo'),
('María Fernández', 'maria.fernandez@est.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 'E213457-8', 'Ingeniería Industrial', 'activo'),
('Carlos Mamani Quispe', 'carlos.mamani@est.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 'E213458-9', 'Ingeniería Civil', 'pendiente'),
('Usuario Externo', 'externo@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'externo', NULL, NULL, 'activo')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Espacios de prueba
INSERT INTO espacios (nombre, tipo, ubicacion, capacidad, equipamiento, disponible, descripcion) VALUES 
('Auditorio Principal', 'auditorio', 'Edificio Central - Piso 1', 300, 'Proyector HD, Sistema de sonido, Aire acondicionado, Wifi', TRUE, 'Auditorio principal para eventos masivos y conferencias'),
('Laboratorio de Computación A', 'laboratorio', 'Bloque A - Piso 2', 40, '25 computadoras, Proyector, Pizarra digital', TRUE, 'Laboratorio para clases de sistemas y eventos tecnológicos'),
('Laboratorio de Robótica', 'laboratorio', 'Bloque B - Piso 3', 20, '15 kits de robótica, Proyector, Herramientas', TRUE, 'Laboratorio especializado en robótica y automatización'),
('Cancha Principal', 'cancha', 'Área deportiva', 500, 'Iluminación nocturna, Graderías, Vestuarios', TRUE, 'Cancha multiuso para eventos deportivos'),
('Salón de Honor', 'salon', 'Edificio Central - Piso 2', 100, 'Proyector, Sistema de audio, Mobiliario formal', TRUE, 'Salón para eventos formales y ceremonias'),
('Patio Central', 'patio', 'Área central del campus', 200, 'Iluminación, Conexión eléctrica', TRUE, 'Espacio abierto para ferias y actividades al aire libre'),
('Aula B-101', 'aula', 'Bloque B - Piso 1', 45, 'Proyector, Pizarra, Aire acondicionado', TRUE, 'Aula estándar para clases y reuniones')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Eventos de prueba
INSERT INTO eventos (titulo, descripcion, tipo, fecha_inicio, fecha_fin, espacio_id, capacidad, organizador_id, estado, publico) VALUES 
('VII Versión CTF EMI 2025', 'Séptima versión de desafío captura la bandera para estudiantes de sistemas. Incluye talleres de seguridad informática y competencias prácticas.', 'academico', '2025-10-10 18:00:00', '2025-10-11 21:00:00', 2, 80, 2, 'pendiente', TRUE),
('Campaña de Arborización 2025', 'Actividad de responsabilidad social universitaria para concientizar sobre el cuidado del medio ambiente y reforestación.', 'social', '2025-11-15 08:00:00', '2025-11-15 13:00:00', 6, 50, 3, 'pendiente', TRUE),
('Taller de Robótica Avanzada', 'Taller práctico de robótica para estudiantes de ingeniería. Uso de kits Arduino y programación de robots autónomos.', 'academico', '2025-11-05 14:00:00', '2025-11-05 18:00:00', 3, 20, 2, 'aprobado', TRUE),
('Festival Cultural EMI', 'Festival anual con presentaciones artísticas, música, danza y gastronomía de diferentes regiones de Bolivia.', 'cultural', '2025-11-20 10:00:00', '2025-11-20 20:00:00', 4, 400, 1, 'aprobado', TRUE),
('Seminario de Inteligencia Artificial', 'Seminario sobre aplicaciones de IA en la ingeniería moderna.', 'academico', '2025-11-25 09:00:00', '2025-11-25 17:00:00', 1, 250, 2, 'pendiente', TRUE),
('Torneo de Fútbol Interfacultades', 'Campeonato deportivo entre las diferentes facultades de la EMI.', 'deportivo', '2025-11-30 08:00:00', '2025-11-30 18:00:00', 4, 300, 1, 'aprobado', TRUE)
ON DUPLICATE KEY UPDATE titulo=VALUES(titulo);

-- Solicitudes de prueba
INSERT INTO solicitudes (evento_id, solicitante_id, tipo, estado, comentarios) VALUES 
(1, 2, 'creacion', 'pendiente', 'Solicitud de creación del evento CTF EMI 2025'),
(2, 3, 'creacion', 'pendiente', 'Solicitud para campaña de arborización'),
(5, 2, 'creacion', 'pendiente', 'Solicitud de seminario de IA')
ON DUPLICATE KEY UPDATE estado=VALUES(estado);

-- Inscripciones de prueba
INSERT INTO inscripciones (evento_id, usuario_id, estado) VALUES 
(3, 4, 'confirmada'),
(3, 5, 'confirmada'),
(4, 4, 'pendiente'),
(4, 5, 'pendiente'),
(6, 4, 'confirmada')
ON DUPLICATE KEY UPDATE estado=VALUES(estado);

-- Notificaciones de prueba
INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, leida) VALUES 
(2, 'Solicitud enviada', 'Tu solicitud para el evento CTF EMI 2025 ha sido enviada correctamente.', 'info', FALSE),
(2, 'Evento aprobado', 'Tu evento "Taller de Robótica Avanzada" ha sido aprobado.', 'exito', FALSE),
(4, 'Inscripción confirmada', 'Tu inscripción al Taller de Robótica ha sido confirmada.', 'exito', TRUE),
(1, 'Nueva solicitud', 'Hay 3 solicitudes pendientes de revisión.', 'solicitud', FALSE)
ON DUPLICATE KEY UPDATE titulo=VALUES(titulo);

-- Verificar datos insertados
SELECT 'Usuarios:' AS tabla, COUNT(*) AS total FROM usuarios
UNION ALL
SELECT 'Espacios:', COUNT(*) FROM espacios
UNION ALL
SELECT 'Eventos:', COUNT(*) FROM eventos
UNION ALL
SELECT 'Solicitudes:', COUNT(*) FROM solicitudes
UNION ALL
SELECT 'Inscripciones:', COUNT(*) FROM inscripciones
UNION ALL
SELECT 'Notificaciones:', COUNT(*) FROM notificaciones;
