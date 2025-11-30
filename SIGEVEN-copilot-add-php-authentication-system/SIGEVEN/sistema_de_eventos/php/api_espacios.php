<?php
/**
 * API: espacios.php
 * CRUD operations for spaces management
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get single space with reservations
                $id = intval($_GET['id']);
                $stmt = $conn->prepare("SELECT * FROM espacios WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $espacio = $result->fetch_assoc();
                
                if ($espacio) {
                    // Get upcoming reservations for this space
                    $resStmt = $conn->prepare("
                        SELECT r.*, e.titulo as evento_titulo, u.nombre as usuario_nombre
                        FROM reservas_espacios r
                        LEFT JOIN eventos e ON r.evento_id = e.id
                        LEFT JOIN usuarios u ON r.usuario_id = u.id
                        WHERE r.espacio_id = ? AND r.fecha_reserva >= CURDATE()
                        ORDER BY r.fecha_reserva, r.hora_inicio
                        LIMIT 10
                    ");
                    $resStmt->bind_param("i", $id);
                    $resStmt->execute();
                    $resResult = $resStmt->get_result();
                    
                    $reservas = [];
                    while ($row = $resResult->fetch_assoc()) {
                        $reservas[] = $row;
                    }
                    $espacio['proximas_reservas'] = $reservas;
                    
                    $response['success'] = true;
                    $response['data'] = $espacio;
                } else {
                    $response['message'] = 'Espacio no encontrado';
                }
            } elseif (isset($_GET['disponibilidad'])) {
                // Check availability for a specific date/time
                $fecha = limpiar_dato($conn, $_GET['fecha']);
                $hora_inicio = limpiar_dato($conn, $_GET['hora_inicio'] ?? '08:00');
                $hora_fin = limpiar_dato($conn, $_GET['hora_fin'] ?? '18:00');
                
                $sql = "
                    SELECT e.* 
                    FROM espacios e
                    WHERE e.disponible = 1
                    AND e.id NOT IN (
                        SELECT r.espacio_id FROM reservas_espacios r
                        WHERE r.fecha_reserva = ?
                        AND r.estado IN ('pendiente', 'aprobada')
                        AND ((r.hora_inicio < ? AND r.hora_fin > ?) OR (r.hora_inicio < ? AND r.hora_fin > ?) OR (r.hora_inicio >= ? AND r.hora_fin <= ?))
                    )
                ";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssss", $fecha, $hora_fin, $hora_inicio, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $espacios = [];
                while ($row = $result->fetch_assoc()) {
                    $espacios[] = $row;
                }
                
                $response['success'] = true;
                $response['data'] = $espacios;
            } else {
                // List all spaces
                $tipo = isset($_GET['tipo']) ? limpiar_dato($conn, $_GET['tipo']) : null;
                $disponible = isset($_GET['disponible']) ? (bool)$_GET['disponible'] : null;
                
                $sql = "SELECT * FROM espacios WHERE 1=1";
                $params = [];
                $types = "";
                
                if ($tipo) {
                    $sql .= " AND tipo = ?";
                    $params[] = $tipo;
                    $types .= "s";
                }
                
                if ($disponible !== null) {
                    $sql .= " AND disponible = ?";
                    $params[] = $disponible ? 1 : 0;
                    $types .= "i";
                }
                
                $sql .= " ORDER BY nombre";
                
                $stmt = $conn->prepare($sql);
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                
                $espacios = [];
                while ($row = $result->fetch_assoc()) {
                    $espacios[] = $row;
                }
                
                $response['success'] = true;
                $response['data'] = $espacios;
                $response['total'] = count($espacios);
            }
            break;
            
        case 'POST':
            // Create new space
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) $data = $_POST;
            
            $nombre = limpiar_dato($conn, $data['nombre'] ?? '');
            $tipo = limpiar_dato($conn, $data['tipo'] ?? 'otro');
            $ubicacion = limpiar_dato($conn, $data['ubicacion'] ?? '');
            $capacidad = intval($data['capacidad'] ?? 0);
            $equipamiento = limpiar_dato($conn, $data['equipamiento'] ?? '');
            $descripcion = limpiar_dato($conn, $data['descripcion'] ?? '');
            $disponible = isset($data['disponible']) ? (bool)$data['disponible'] : true;
            
            if (empty($nombre) || empty($ubicacion)) {
                $response['message'] = 'Nombre y ubicación son requeridos';
                break;
            }
            
            $stmt = $conn->prepare("INSERT INTO espacios (nombre, tipo, ubicacion, capacidad, equipamiento, descripcion, disponible) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $disponibleInt = $disponible ? 1 : 0;
            $stmt->bind_param("sssisii", $nombre, $tipo, $ubicacion, $capacidad, $equipamiento, $descripcion, $disponibleInt);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Espacio creado exitosamente';
                $response['data'] = ['id' => $conn->insert_id];
            } else {
                $response['message'] = 'Error al crear espacio: ' . $conn->error;
            }
            break;
            
        case 'PUT':
            // Update space
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? $_GET['id'] ?? 0);
            
            if ($id <= 0) {
                $response['message'] = 'ID de espacio requerido';
                break;
            }
            
            $fields = [];
            $params = [];
            $types = "";
            
            $stringFields = ['nombre', 'tipo', 'ubicacion', 'equipamiento', 'descripcion', 'horario_apertura', 'horario_cierre'];
            $intFields = ['capacidad'];
            
            foreach ($stringFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $params[] = limpiar_dato($conn, $data[$field]);
                    $types .= "s";
                }
            }
            
            foreach ($intFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $params[] = intval($data[$field]);
                    $types .= "i";
                }
            }
            
            if (isset($data['disponible'])) {
                $fields[] = "disponible = ?";
                $params[] = (bool)$data['disponible'] ? 1 : 0;
                $types .= "i";
            }
            
            if (empty($fields)) {
                $response['message'] = 'No hay campos para actualizar';
                break;
            }
            
            $sql = "UPDATE espacios SET " . implode(", ", $fields) . " WHERE id = ?";
            $params[] = $id;
            $types .= "i";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Espacio actualizado exitosamente';
            } else {
                $response['message'] = 'Error al actualizar espacio: ' . $conn->error;
            }
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? $_GET['id'] ?? 0);
            
            if ($id <= 0) {
                $response['message'] = 'ID de espacio requerido';
                break;
            }
            
            // Check if space has active reservations
            $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM reservas_espacios WHERE espacio_id = ? AND estado IN ('pendiente', 'aprobada') AND fecha_reserva >= CURDATE()");
            $checkStmt->bind_param("i", $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result()->fetch_assoc();
            
            if ($checkResult['count'] > 0) {
                $response['message'] = 'No se puede eliminar el espacio porque tiene reservas activas';
                break;
            }
            
            $stmt = $conn->prepare("DELETE FROM espacios WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Espacio eliminado exitosamente';
            } else {
                $response['message'] = 'Error al eliminar espacio: ' . $conn->error;
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
