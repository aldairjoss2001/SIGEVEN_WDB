<?php
/**
 * API: solicitudes.php
 * CRUD operations for requests management
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
                // Get single request
                $id = intval($_GET['id']);
                $stmt = $conn->prepare("
                    SELECT s.*, 
                           e.titulo as evento_titulo, e.descripcion as evento_descripcion,
                           e.tipo as evento_tipo, e.fecha_inicio, e.fecha_fin,
                           u.nombre as solicitante_nombre, u.correo as solicitante_correo,
                           a.nombre as admin_nombre
                    FROM solicitudes s
                    LEFT JOIN eventos e ON s.evento_id = e.id
                    LEFT JOIN usuarios u ON s.solicitante_id = u.id
                    LEFT JOIN usuarios a ON s.admin_id = a.id
                    WHERE s.id = ?
                ");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $solicitud = $result->fetch_assoc();
                
                if ($solicitud) {
                    $response['success'] = true;
                    $response['data'] = $solicitud;
                } else {
                    $response['message'] = 'Solicitud no encontrada';
                }
            } else {
                // List requests with filters
                $estado = isset($_GET['estado']) ? limpiar_dato($conn, $_GET['estado']) : null;
                $tipo = isset($_GET['tipo']) ? limpiar_dato($conn, $_GET['tipo']) : null;
                $solicitante_id = isset($_GET['solicitante_id']) ? intval($_GET['solicitante_id']) : null;
                
                $sql = "
                    SELECT s.*, 
                           e.titulo as evento_titulo, e.tipo as evento_tipo,
                           e.fecha_inicio, e.fecha_fin,
                           u.nombre as solicitante_nombre, u.correo as solicitante_correo
                    FROM solicitudes s
                    LEFT JOIN eventos e ON s.evento_id = e.id
                    LEFT JOIN usuarios u ON s.solicitante_id = u.id
                    WHERE 1=1
                ";
                $params = [];
                $types = "";
                
                if ($estado) {
                    $sql .= " AND s.estado = ?";
                    $params[] = $estado;
                    $types .= "s";
                }
                
                if ($tipo) {
                    $sql .= " AND s.tipo = ?";
                    $params[] = $tipo;
                    $types .= "s";
                }
                
                if ($solicitante_id) {
                    $sql .= " AND s.solicitante_id = ?";
                    $params[] = $solicitante_id;
                    $types .= "i";
                }
                
                $sql .= " ORDER BY s.fecha_solicitud DESC";
                
                $stmt = $conn->prepare($sql);
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                
                $solicitudes = [];
                while ($row = $result->fetch_assoc()) {
                    $solicitudes[] = $row;
                }
                
                // Get counts by status
                $countStmt = $conn->query("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                        SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas
                    FROM solicitudes
                ");
                $counts = $countStmt->fetch_assoc();
                
                $response['success'] = true;
                $response['data'] = $solicitudes;
                $response['total'] = count($solicitudes);
                $response['counts'] = $counts;
            }
            break;
            
        case 'POST':
            // Create new request
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) $data = $_POST;
            
            $evento_id = intval($data['evento_id'] ?? 0);
            $solicitante_id = intval($data['solicitante_id'] ?? 0);
            $tipo = limpiar_dato($conn, $data['tipo'] ?? 'creacion');
            $comentarios = limpiar_dato($conn, $data['comentarios'] ?? '');
            
            if ($evento_id <= 0 || $solicitante_id <= 0) {
                $response['message'] = 'ID de evento y solicitante son requeridos';
                break;
            }
            
            $stmt = $conn->prepare("INSERT INTO solicitudes (evento_id, solicitante_id, tipo, comentarios) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $evento_id, $solicitante_id, $tipo, $comentarios);
            
            if ($stmt->execute()) {
                // Update event status to pending
                $updateEvento = $conn->prepare("UPDATE eventos SET estado = 'pendiente' WHERE id = ?");
                $updateEvento->bind_param("i", $evento_id);
                $updateEvento->execute();
                
                $response['success'] = true;
                $response['message'] = 'Solicitud enviada exitosamente';
                $response['data'] = ['id' => $conn->insert_id];
            } else {
                $response['message'] = 'Error al crear solicitud: ' . $conn->error;
            }
            break;
            
        case 'PUT':
            // Update request (approve/reject)
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? $_GET['id'] ?? 0);
            
            if ($id <= 0) {
                $response['message'] = 'ID de solicitud requerido';
                break;
            }
            
            $estado = limpiar_dato($conn, $data['estado'] ?? '');
            $respuesta_admin = limpiar_dato($conn, $data['respuesta_admin'] ?? '');
            $admin_id = intval($data['admin_id'] ?? 0);
            
            if (empty($estado)) {
                $response['message'] = 'Estado es requerido';
                break;
            }
            
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Update request
                $stmt = $conn->prepare("UPDATE solicitudes SET estado = ?, respuesta_admin = ?, admin_id = ?, fecha_respuesta = NOW() WHERE id = ?");
                $stmt->bind_param("ssii", $estado, $respuesta_admin, $admin_id, $id);
                $stmt->execute();
                
                // Get event ID from request
                $getEvento = $conn->prepare("SELECT evento_id FROM solicitudes WHERE id = ?");
                $getEvento->bind_param("i", $id);
                $getEvento->execute();
                $eventoResult = $getEvento->get_result()->fetch_assoc();
                
                if ($eventoResult) {
                    // Update event status based on request status
                    $eventoEstado = ($estado === 'aprobada') ? 'aprobado' : (($estado === 'rechazada') ? 'rechazado' : 'pendiente');
                    $updateEvento = $conn->prepare("UPDATE eventos SET estado = ?, motivo_rechazo = ? WHERE id = ?");
                    $motivo = ($estado === 'rechazada') ? $respuesta_admin : null;
                    $updateEvento->bind_param("ssi", $eventoEstado, $motivo, $eventoResult['evento_id']);
                    $updateEvento->execute();
                }
                
                $conn->commit();
                
                $response['success'] = true;
                $response['message'] = 'Solicitud actualizada exitosamente';
            } catch (Exception $e) {
                $conn->rollback();
                $response['message'] = 'Error al actualizar: ' . $e->getMessage();
            }
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? $_GET['id'] ?? 0);
            
            if ($id <= 0) {
                $response['message'] = 'ID de solicitud requerido';
                break;
            }
            
            $stmt = $conn->prepare("DELETE FROM solicitudes WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Solicitud eliminada exitosamente';
            } else {
                $response['message'] = 'Error al eliminar solicitud: ' . $conn->error;
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
