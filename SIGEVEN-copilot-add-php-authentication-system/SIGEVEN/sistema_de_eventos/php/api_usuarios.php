<?php
/**
 * API: usuarios.php
 * CRUD operations for users management (Admin)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';
require_once 'config.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($method) {
        case 'GET':
            // Get users list or single user
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt = $conn->prepare("SELECT id, nombre, correo, rol, codigo, carrera, telefono, estado, fecha_registro, ultimo_acceso FROM usuarios WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                if ($user) {
                    $response['success'] = true;
                    $response['data'] = $user;
                } else {
                    $response['message'] = 'Usuario no encontrado';
                }
            } else {
                // List users with optional filters
                $rol = isset($_GET['rol']) ? limpiar_dato($conn, $_GET['rol']) : null;
                $estado = isset($_GET['estado']) ? limpiar_dato($conn, $_GET['estado']) : null;
                $buscar = isset($_GET['buscar']) ? limpiar_dato($conn, $_GET['buscar']) : null;
                
                $sql = "SELECT id, nombre, correo, rol, codigo, carrera, telefono, estado, fecha_registro FROM usuarios WHERE 1=1";
                $params = [];
                $types = "";
                
                if ($rol) {
                    $sql .= " AND rol = ?";
                    $params[] = $rol;
                    $types .= "s";
                }
                
                if ($estado) {
                    $sql .= " AND estado = ?";
                    $params[] = $estado;
                    $types .= "s";
                }
                
                if ($buscar) {
                    $sql .= " AND (nombre LIKE ? OR correo LIKE ? OR codigo LIKE ?)";
                    $buscarLike = "%$buscar%";
                    $params[] = $buscarLike;
                    $params[] = $buscarLike;
                    $params[] = $buscarLike;
                    $types .= "sss";
                }
                
                $sql .= " ORDER BY fecha_registro DESC";
                
                $stmt = $conn->prepare($sql);
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                
                $usuarios = [];
                while ($row = $result->fetch_assoc()) {
                    $usuarios[] = $row;
                }
                
                $response['success'] = true;
                $response['data'] = $usuarios;
                $response['total'] = count($usuarios);
            }
            break;
            
        case 'POST':
            // Create new user
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                $data = $_POST;
            }
            
            $nombre = limpiar_dato($conn, $data['nombre'] ?? '');
            $correo = limpiar_dato($conn, $data['correo'] ?? '');
            $contrasena = $data['contrasena'] ?? '';
            $rol = limpiar_dato($conn, $data['rol'] ?? 'estudiante');
            $codigo = limpiar_dato($conn, $data['codigo'] ?? '');
            $carrera = limpiar_dato($conn, $data['carrera'] ?? '');
            $estado = limpiar_dato($conn, $data['estado'] ?? 'pendiente');
            
            if (empty($nombre) || empty($correo) || empty($contrasena)) {
                $response['message'] = 'Nombre, correo y contraseña son requeridos';
                break;
            }
            
            // Check if email exists
            $checkStmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $checkStmt->bind_param("s", $correo);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                $response['message'] = 'El correo ya está registrado';
                break;
            }
            
            $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol, codigo, carrera, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $nombre, $correo, $contrasenaHash, $rol, $codigo, $carrera, $estado);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Usuario creado exitosamente';
                $response['data'] = ['id' => $conn->insert_id];
            } else {
                $response['message'] = 'Error al crear usuario: ' . $conn->error;
            }
            break;
            
        case 'PUT':
            // Update user
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? $_GET['id'] ?? 0);
            
            if ($id <= 0) {
                $response['message'] = 'ID de usuario requerido';
                break;
            }
            
            $fields = [];
            $params = [];
            $types = "";
            
            if (isset($data['nombre'])) {
                $fields[] = "nombre = ?";
                $params[] = limpiar_dato($conn, $data['nombre']);
                $types .= "s";
            }
            
            if (isset($data['correo'])) {
                $fields[] = "correo = ?";
                $params[] = limpiar_dato($conn, $data['correo']);
                $types .= "s";
            }
            
            if (isset($data['rol'])) {
                $fields[] = "rol = ?";
                $params[] = limpiar_dato($conn, $data['rol']);
                $types .= "s";
            }
            
            if (isset($data['codigo'])) {
                $fields[] = "codigo = ?";
                $params[] = limpiar_dato($conn, $data['codigo']);
                $types .= "s";
            }
            
            if (isset($data['carrera'])) {
                $fields[] = "carrera = ?";
                $params[] = limpiar_dato($conn, $data['carrera']);
                $types .= "s";
            }
            
            if (isset($data['estado'])) {
                $fields[] = "estado = ?";
                $params[] = limpiar_dato($conn, $data['estado']);
                $types .= "s";
            }
            
            if (isset($data['contrasena']) && !empty($data['contrasena'])) {
                $fields[] = "contrasena = ?";
                $params[] = password_hash($data['contrasena'], PASSWORD_DEFAULT);
                $types .= "s";
            }
            
            if (empty($fields)) {
                $response['message'] = 'No hay campos para actualizar';
                break;
            }
            
            $sql = "UPDATE usuarios SET " . implode(", ", $fields) . " WHERE id = ?";
            $params[] = $id;
            $types .= "i";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Usuario actualizado exitosamente';
            } else {
                $response['message'] = 'Error al actualizar usuario: ' . $conn->error;
            }
            break;
            
        case 'DELETE':
            // Delete user
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? $_GET['id'] ?? 0);
            
            if ($id <= 0) {
                $response['message'] = 'ID de usuario requerido';
                break;
            }
            
            // Don't allow deleting admin users
            $checkStmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = ?");
            $checkStmt->bind_param("i", $id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user && $user['rol'] === 'admin') {
                $response['message'] = 'No se puede eliminar un usuario administrador';
                break;
            }
            
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Usuario eliminado exitosamente';
            } else {
                $response['message'] = 'Error al eliminar usuario: ' . $conn->error;
            }
            break;
            
        default:
            $response['message'] = 'Método no permitido';
            http_response_code(405);
    }
} catch (Exception $e) {
    $response['message'] = 'Error del servidor: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
