<?php
/**
 * API: eventos.php
 * CRUD operations for events management
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
                // Get single event with details
                $id = intval($_GET['id']);
                $stmt = $conn->prepare("
                    SELECT e.*, u.nombre as organizador_nombre, u.correo as organizador_correo,
                           s.nombre as espacio_nombre, s.ubicacion as espacio_ubicacion
                    FROM eventos e
                    LEFT JOIN usuarios u ON e.organizador_id = u.id
                    LEFT JOIN espacios s ON e.espacio_id = s.id
                    WHERE e.id = ?
                ");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $evento = $result->fetch_assoc();
                
                if ($evento) {
                    // Get inscriptions count
                    $inscStmt = $conn->prepare("SELECT COUNT(*) as total FROM inscripciones WHERE evento_id = ?");
                    $inscStmt->bind_param("i", $id);
                    $inscStmt->execute();
                    $inscResult = $inscStmt->get_result()->fetch_assoc();
                    $evento['inscritos'] = $inscResult['total'];
                    
                    $response['success'] = true;
                    $response['data'] = $evento;
                } else {
                    $response['message'] = 'Evento no encontrado';
                }
            } else {
                // List events with filters
                $estado = isset($_GET['estado']) ? limpiar_dato($conn, $_GET['estado']) : null;
                $tipo = isset($_GET['tipo']) ? limpiar_dato($conn, $_GET['tipo']) : null;
                $organizador_id = isset($_GET['organizador_id']) ? intval($_GET['organizador_id']) : null;
                $fecha_desde = isset($_GET['fecha_desde']) ? limpiar_dato($conn, $_GET['fecha_desde']) : null;
                $fecha_hasta = isset($_GET['fecha_hasta']) ? limpiar_dato($conn, $_GET['fecha_hasta']) : null;
                $buscar = isset($_GET['buscar']) ? limpiar_dato($conn, $_GET['buscar']) : null;
                
                $sql = "
                    SELECT e.*, u.nombre as organizador_nombre,
                           s.nombre as espacio_nombre, s.ubicacion as espacio_ubicacion,
                           (SELECT COUNT(*) FROM inscripciones WHERE evento_id = e.id) as inscritos
                    FROM eventos e
                    LEFT JOIN usuarios u ON e.organizador_id = u.id
                    LEFT JOIN espacios s ON e.espacio_id = s.id
                    WHERE 1=1
                ";
                $params = [];
                $types = "";
                
                if ($estado) {
                    $sql .= " AND e.estado = ?";
                    $params[] = $estado;
                    $types .= "s";
                }
                
                if ($tipo) {
                    $sql .= " AND e.tipo = ?";
                    $params[] = $tipo;
                    $types .= "s";
                }
                
                if ($organizador_id) {
                    $sql .= " AND e.organizador_id = ?";
                    $params[] = $organizador_id;
                    $types .= "i";
                }
                
                if ($fecha_desde) {
                    $sql .= " AND e.fecha_inicio >= ?";
                    $params[] = $fecha_desde;
                    $types .= "s";
                }
                
                if ($fecha_hasta) {
                    $sql .= " AND e.fecha_fin <= ?";
                    $params[] = $fecha_hasta;
                    $types .= "s";
                }
                
                if ($buscar) {
                    $sql .= " AND (e.titulo LIKE ? OR e.descripcion LIKE ?)";
                    $buscarLike = "%$buscar%";
                    $params[] = $buscarLike;
                    $params[] = $buscarLike;
                    $types .= "ss";
                }
                
                $sql .= " ORDER BY e.fecha_inicio DESC";
                
                $stmt = $conn->prepare($sql);
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                
                $eventos = [];
                while ($row = $result->fetch_assoc()) {
                    $eventos[] = $row;
                }
                
                $response['success'] = true;
                $response['data'] = $eventos;
                $response['total'] = count($eventos);
            }
            break;
            
        case 'POST':
            // Create new event
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) $data = $_POST;
            
            $titulo = limpiar_dato($conn, $data['titulo'] ?? '');
            $descripcion = limpiar_dato($conn, $data['descripcion'] ?? '');
            $tipo = limpiar_dato($conn, $data['tipo'] ?? 'academico');
            $categoria = limpiar_dato($conn, $data['categoria'] ?? '');
            $fecha_inicio = limpiar_dato($conn, $data['fecha_inicio'] ?? '');
            $fecha_fin = limpiar_dato($conn, $data['fecha_fin'] ?? '');
            $espacio_id = !empty($data['espacio_id']) ? intval($data['espacio_id']) : null;
            $capacidad = intval($data['capacidad'] ?? 0);
            $organizador_id = intval($data['organizador_id'] ?? 0);
            $requisitos = limpiar_dato($conn, $data['requisitos'] ?? '');
            $publico = isset($data['publico']) ? (bool)$data['publico'] : true;
            
            if (empty($titulo) || empty($descripcion) || empty($fecha_inicio) || empty($fecha_fin) || $organizador_id <= 0) {
                $response['message'] = 'Título, descripción, fechas y organizador son requeridos';
                break;
            }
            
            $stmt = $conn->prepare("
                INSERT INTO eventos (titulo, descripcion, tipo, categoria, fecha_inicio, fecha_fin, espacio_id, capacidad, organizador_id, requisitos, publico, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'borrador')
            ");
            $stmt->bind_param("ssssssiiisi", $titulo, $descripcion, $tipo, $categoria, $fecha_inicio, $fecha_fin, $espacio_id, $capacidad, $organizador_id, $requisitos, $publico);
            
            if ($stmt->execute()) {
                $evento_id = $conn->insert_id;
                $response['success'] = true;
                $response['message'] = 'Evento creado exitosamente';
                $response['data'] = ['id' => $evento_id];
            } else {
                $response['message'] = 'Error al crear evento: ' . $conn->error;
            }
            break;
            
        case 'PUT':
            // Update event (including status changes)
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? $_GET['id'] ?? 0);
            
            if ($id <= 0) {
                $response['message'] = 'ID de evento requerido';
                break;
            }
            
            $fields = [];
            $params = [];
            $types = "";
            
            $allowedFields = ['titulo', 'descripcion', 'tipo', 'categoria', 'fecha_inicio', 'fecha_fin', 'capacidad', 'requisitos', 'publico', 'estado', 'motivo_rechazo'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    if ($field === 'publico') {
                        $params[] = (int)$data[$field];
                        $types .= "i";
                    } else {
                        $params[] = limpiar_dato($conn, $data[$field]);
                        $types .= "s";
                    }
                }
            }
            
            if (isset($data['espacio_id'])) {
                $fields[] = "espacio_id = ?";
                $params[] = !empty($data['espacio_id']) ? intval($data['espacio_id']) : null;
                $types .= "i";
            }
            
            if (empty($fields)) {
                $response['message'] = 'No hay campos para actualizar';
                break;
            }
            
            $sql = "UPDATE eventos SET " . implode(", ", $fields) . " WHERE id = ?";
            $params[] = $id;
            $types .= "i";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Evento actualizado exitosamente';
            } else {
                $response['message'] = 'Error al actualizar evento: ' . $conn->error;
            }
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? $_GET['id'] ?? 0);
            
            if ($id <= 0) {
                $response['message'] = 'ID de evento requerido';
                break;
            }
            
            $stmt = $conn->prepare("DELETE FROM eventos WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Evento eliminado exitosamente';
            } else {
                $response['message'] = 'Error al eliminar evento: ' . $conn->error;
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
