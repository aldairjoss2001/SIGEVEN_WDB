<?php
/**
 * API: calendario.php
 * Calendar events for all roles
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';
require_once 'config.php';

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    // Get date range from parameters
    $mes = isset($_GET['mes']) ? intval($_GET['mes']) : intval(date('m'));
    $anio = isset($_GET['anio']) ? intval($_GET['anio']) : intval(date('Y'));
    $usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : null;
    $rol = isset($_GET['rol']) ? limpiar_dato($conn, $_GET['rol']) : null;
    $tipo = isset($_GET['tipo']) ? limpiar_dato($conn, $_GET['tipo']) : null;
    
    // Calculate first and last day of month
    $primerDia = "$anio-$mes-01";
    $ultimoDia = date('Y-m-t', strtotime($primerDia));
    
    // Base query for events
    $sql = "
        SELECT 
            e.id,
            e.titulo,
            e.descripcion,
            e.tipo,
            e.categoria,
            e.fecha_inicio,
            e.fecha_fin,
            e.capacidad,
            e.estado,
            e.publico,
            s.nombre as espacio_nombre,
            s.ubicacion as espacio_ubicacion,
            u.nombre as organizador_nombre,
            (SELECT COUNT(*) FROM inscripciones WHERE evento_id = e.id AND estado IN ('confirmada', 'asistio')) as inscritos
        FROM eventos e
        LEFT JOIN espacios s ON e.espacio_id = s.id
        LEFT JOIN usuarios u ON e.organizador_id = u.id
        WHERE e.estado IN ('aprobado', 'finalizado')
        AND (
            (DATE(e.fecha_inicio) BETWEEN ? AND ?)
            OR (DATE(e.fecha_fin) BETWEEN ? AND ?)
            OR (DATE(e.fecha_inicio) <= ? AND DATE(e.fecha_fin) >= ?)
        )
    ";
    $params = [$primerDia, $ultimoDia, $primerDia, $ultimoDia, $primerDia, $ultimoDia];
    $types = "ssssss";
    
    // Filter by type if specified
    if ($tipo) {
        $sql .= " AND e.tipo = ?";
        $params[] = $tipo;
        $types .= "s";
    }
    
    // Filter by user role - show user's own events if specified
    if ($usuario_id && $rol === 'docente') {
        // For docentes, also show their own pending events
        $sql = "
            SELECT 
                e.id,
                e.titulo,
                e.descripcion,
                e.tipo,
                e.categoria,
                e.fecha_inicio,
                e.fecha_fin,
                e.capacidad,
                e.estado,
                e.publico,
                s.nombre as espacio_nombre,
                s.ubicacion as espacio_ubicacion,
                u.nombre as organizador_nombre,
                (SELECT COUNT(*) FROM inscripciones WHERE evento_id = e.id AND estado IN ('confirmada', 'asistio')) as inscritos
            FROM eventos e
            LEFT JOIN espacios s ON e.espacio_id = s.id
            LEFT JOIN usuarios u ON e.organizador_id = u.id
            WHERE (
                e.estado IN ('aprobado', 'finalizado')
                OR (e.organizador_id = ? AND e.estado IN ('pendiente', 'borrador'))
            )
            AND (
                (DATE(e.fecha_inicio) BETWEEN ? AND ?)
                OR (DATE(e.fecha_fin) BETWEEN ? AND ?)
                OR (DATE(e.fecha_inicio) <= ? AND DATE(e.fecha_fin) >= ?)
            )
        ";
        $params = [$usuario_id, $primerDia, $ultimoDia, $primerDia, $ultimoDia, $primerDia, $ultimoDia];
        $types = "issssss";
        
        if ($tipo) {
            $sql .= " AND e.tipo = ?";
            $params[] = $tipo;
            $types .= "s";
        }
    }
    
    // For students, include events they're inscribed to
    if ($usuario_id && $rol === 'estudiante') {
        $sql = "
            SELECT 
                e.id,
                e.titulo,
                e.descripcion,
                e.tipo,
                e.categoria,
                e.fecha_inicio,
                e.fecha_fin,
                e.capacidad,
                e.estado,
                e.publico,
                s.nombre as espacio_nombre,
                s.ubicacion as espacio_ubicacion,
                u.nombre as organizador_nombre,
                (SELECT COUNT(*) FROM inscripciones WHERE evento_id = e.id AND estado IN ('confirmada', 'asistio')) as inscritos,
                (SELECT estado FROM inscripciones WHERE evento_id = e.id AND usuario_id = ?) as mi_inscripcion
            FROM eventos e
            LEFT JOIN espacios s ON e.espacio_id = s.id
            LEFT JOIN usuarios u ON e.organizador_id = u.id
            WHERE e.estado IN ('aprobado', 'finalizado')
            AND (
                (DATE(e.fecha_inicio) BETWEEN ? AND ?)
                OR (DATE(e.fecha_fin) BETWEEN ? AND ?)
                OR (DATE(e.fecha_inicio) <= ? AND DATE(e.fecha_fin) >= ?)
            )
        ";
        $params = [$usuario_id, $primerDia, $ultimoDia, $primerDia, $ultimoDia, $primerDia, $ultimoDia];
        $types = "issssss";
        
        if ($tipo) {
            $sql .= " AND e.tipo = ?";
            $params[] = $tipo;
            $types .= "s";
        }
    }
    
    $sql .= " ORDER BY e.fecha_inicio ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $eventos = [];
    while ($row = $result->fetch_assoc()) {
        // Format for calendar display
        $eventos[] = [
            'id' => $row['id'],
            'title' => $row['titulo'],
            'description' => $row['descripcion'],
            'type' => $row['tipo'],
            'category' => $row['categoria'],
            'start' => $row['fecha_inicio'],
            'end' => $row['fecha_fin'],
            'location' => $row['espacio_nombre'] ? $row['espacio_nombre'] . ' - ' . $row['espacio_ubicacion'] : 'Por definir',
            'organizer' => $row['organizador_nombre'],
            'capacity' => $row['capacidad'],
            'registered' => $row['inscritos'],
            'status' => $row['estado'],
            'public' => (bool)$row['publico'],
            'myInscription' => $row['mi_inscripcion'] ?? null
        ];
    }
    
    // Group events by day
    $eventosPorDia = [];
    foreach ($eventos as $evento) {
        $fechaInicio = new DateTime($evento['start']);
        $fechaFin = new DateTime($evento['end']);
        
        // Add event to each day it spans
        $fecha = clone $fechaInicio;
        while ($fecha <= $fechaFin) {
            $dia = $fecha->format('Y-m-d');
            if (!isset($eventosPorDia[$dia])) {
                $eventosPorDia[$dia] = [];
            }
            $eventosPorDia[$dia][] = $evento;
            $fecha->modify('+1 day');
        }
    }
    
    $response['success'] = true;
    $response['data'] = [
        'eventos' => $eventos,
        'eventosPorDia' => $eventosPorDia,
        'mes' => $mes,
        'anio' => $anio,
        'primerDia' => $primerDia,
        'ultimoDia' => $ultimoDia
    ];
    $response['total'] = count($eventos);

} catch (Exception $e) {
    $response['message'] = 'Error del servidor: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
