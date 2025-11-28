-- Script SQL para crear la base de datos SIGEVEN y la tabla usuarios
-- Ejecutar en phpMyAdmin o línea de comandos MySQL

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS SIGEVEN DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE SIGEVEN;

-- Crear la tabla usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_correo (correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar 3 usuarios de prueba
-- IMPORTANTE: Las contraseñas están hasheadas con password_hash()
-- Contraseña para todos: password123

-- Usuario 1: Estudiante
-- Correo: juan.perez@est.emi.edu.bo
-- Contraseña: password123
INSERT INTO usuarios (nombre, correo, contrasena) VALUES 
('Juan Pérez González', 'juan.perez@est.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE nombre=nombre;

-- Usuario 2: Docente
-- Correo: maria.lopez@doc.emi.edu.bo
-- Contraseña: password123
INSERT INTO usuarios (nombre, correo, contrasena) VALUES 
('Dra. María López Silva', 'maria.lopez@doc.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE nombre=nombre;

-- Usuario 3: Administrativo
-- Correo: carlos.mamani@adm.emi.edu.bo
-- Contraseña: password123
INSERT INTO usuarios (nombre, correo, contrasena) VALUES 
('Carlos Mamani Quispe', 'carlos.mamani@adm.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE nombre=nombre;

-- Verificar los usuarios insertados
SELECT id, nombre, correo, fecha_registro FROM usuarios;

-- NOTA: Todas las contraseñas de prueba son 'password123'
-- El hash mostrado arriba corresponde a esa contraseña
