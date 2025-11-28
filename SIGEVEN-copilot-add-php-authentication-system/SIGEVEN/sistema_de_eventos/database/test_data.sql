-- Script de prueba para insertar usuarios de ejemplo
-- Ejecutar después de schema.sql

USE sigeven;

-- Insertar estudiantes de ejemplo
-- Contraseña para todos: test123
INSERT INTO estudiantes (codigo_estudiante, nombre, correo, contrasena) VALUES
('E20250-1', 'Juan Pérez González', 'juan.perez@estudiante.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('E20250-2', 'María López Silva', 'maria.lopez@estudiante.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('E20250-3', 'Carlos Mamani Quispe', 'carlos.mamani@estudiante.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE codigo_estudiante=codigo_estudiante;

-- Insertar docentes de ejemplo
-- Contraseña para todos: test123
INSERT INTO docentes (codigo_docente, nombre, correo, contrasena) VALUES
('A20250-1', 'Dr. Roberto Gutiérrez', 'roberto.gutierrez@docente.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('A20250-2', 'Ing. Ana Fernández', 'ana.fernandez@docente.emi.edu.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE codigo_docente=codigo_docente;

-- Verificar datos insertados
SELECT 'Estudiantes insertados:' as mensaje;
SELECT codigo_estudiante, nombre, correo, activo FROM estudiantes;

SELECT 'Docentes insertados:' as mensaje;
SELECT codigo_docente, nombre, correo, activo FROM docentes;

SELECT 'Administradores:' as mensaje;
SELECT username, nombre, correo, activo FROM administradores;

-- Nota: Todas las contraseñas de prueba son 'test123' excepto el admin que es 'admin123'
