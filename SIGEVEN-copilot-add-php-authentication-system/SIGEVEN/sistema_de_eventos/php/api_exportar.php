<?php
/**
 * API: exportar.php
 * Export data to Excel and PDF formats
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';
require_once 'config.php';

$tipo_reporte = isset($_GET['tipo']) ? limpiar_dato($conn, $_GET['tipo']) : 'eventos';
$formato = isset($_GET['formato']) ? limpiar_dato($conn, $_GET['formato']) : 'excel';
$fecha_inicio = isset($_GET['fecha_inicio']) ? limpiar_dato($conn, $_GET['fecha_inicio']) : null;
$fecha_fin = isset($_GET['fecha_fin']) ? limpiar_dato($conn, $_GET['fecha_fin']) : null;

try {
    $data = [];
    $headers = [];
    $titulo = '';
    
    switch ($tipo_reporte) {
        case 'usuarios':
            $titulo = 'Reporte de Usuarios';
            $headers = ['ID', 'Nombre', 'Correo', 'Rol', 'Código', 'Carrera', 'Estado', 'Fecha Registro'];
            
            $sql = "SELECT id, nombre, correo, rol, codigo, carrera, estado, DATE_FORMAT(fecha_registro, '%d/%m/%Y %H:%i') as fecha_registro FROM usuarios ORDER BY fecha_registro DESC";
            $result = $conn->query($sql);
            
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    $row['id'],
                    $row['nombre'],
                    $row['correo'],
                    ucfirst($row['rol']),
                    $row['codigo'] ?? '-',
                    $row['carrera'] ?? '-',
                    ucfirst($row['estado']),
                    $row['fecha_registro']
                ];
            }
            break;
            
        case 'eventos':
            $titulo = 'Reporte de Eventos';
            $headers = ['ID', 'Título', 'Tipo', 'Fecha Inicio', 'Fecha Fin', 'Espacio', 'Organizador', 'Capacidad', 'Inscritos', 'Estado'];
            
            $sql = "
                SELECT e.id, e.titulo, e.tipo, 
                       DATE_FORMAT(e.fecha_inicio, '%d/%m/%Y %H:%i') as fecha_inicio,
                       DATE_FORMAT(e.fecha_fin, '%d/%m/%Y %H:%i') as fecha_fin,
                       COALESCE(s.nombre, 'Por definir') as espacio,
                       u.nombre as organizador,
                       e.capacidad,
                       (SELECT COUNT(*) FROM inscripciones WHERE evento_id = e.id) as inscritos,
                       e.estado
                FROM eventos e
                LEFT JOIN espacios s ON e.espacio_id = s.id
                LEFT JOIN usuarios u ON e.organizador_id = u.id
            ";
            
            $params = [];
            $types = "";
            $where = [];
            
            if ($fecha_inicio) {
                $where[] = "DATE(e.fecha_inicio) >= ?";
                $params[] = $fecha_inicio;
                $types .= "s";
            }
            
            if ($fecha_fin) {
                $where[] = "DATE(e.fecha_fin) <= ?";
                $params[] = $fecha_fin;
                $types .= "s";
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            
            $sql .= " ORDER BY e.fecha_inicio DESC";
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    $row['id'],
                    $row['titulo'],
                    ucfirst($row['tipo']),
                    $row['fecha_inicio'],
                    $row['fecha_fin'],
                    $row['espacio'],
                    $row['organizador'],
                    $row['capacidad'],
                    $row['inscritos'],
                    ucfirst($row['estado'])
                ];
            }
            break;
            
        case 'solicitudes':
            $titulo = 'Reporte de Solicitudes';
            $headers = ['ID', 'Evento', 'Solicitante', 'Tipo', 'Estado', 'Fecha Solicitud', 'Fecha Respuesta'];
            
            $sql = "
                SELECT s.id, e.titulo as evento, u.nombre as solicitante, s.tipo, s.estado,
                       DATE_FORMAT(s.fecha_solicitud, '%d/%m/%Y %H:%i') as fecha_solicitud,
                       DATE_FORMAT(s.fecha_respuesta, '%d/%m/%Y %H:%i') as fecha_respuesta
                FROM solicitudes s
                LEFT JOIN eventos e ON s.evento_id = e.id
                LEFT JOIN usuarios u ON s.solicitante_id = u.id
                ORDER BY s.fecha_solicitud DESC
            ";
            $result = $conn->query($sql);
            
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    $row['id'],
                    $row['evento'],
                    $row['solicitante'],
                    ucfirst($row['tipo']),
                    ucfirst($row['estado']),
                    $row['fecha_solicitud'],
                    $row['fecha_respuesta'] ?? '-'
                ];
            }
            break;
            
        case 'espacios':
            $titulo = 'Reporte de Espacios';
            $headers = ['ID', 'Nombre', 'Tipo', 'Ubicación', 'Capacidad', 'Disponible', 'Equipamiento'];
            
            $sql = "SELECT * FROM espacios ORDER BY nombre";
            $result = $conn->query($sql);
            
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    $row['id'],
                    $row['nombre'],
                    ucfirst($row['tipo']),
                    $row['ubicacion'],
                    $row['capacidad'],
                    $row['disponible'] ? 'Sí' : 'No',
                    $row['equipamiento'] ?? '-'
                ];
            }
            break;
            
        case 'inscripciones':
            $titulo = 'Reporte de Inscripciones';
            $headers = ['ID', 'Evento', 'Usuario', 'Estado', 'Fecha Inscripción', 'Certificado'];
            
            $sql = "
                SELECT i.id, e.titulo as evento, u.nombre as usuario, i.estado,
                       DATE_FORMAT(i.fecha_inscripcion, '%d/%m/%Y %H:%i') as fecha_inscripcion,
                       i.certificado_generado
                FROM inscripciones i
                LEFT JOIN eventos e ON i.evento_id = e.id
                LEFT JOIN usuarios u ON i.usuario_id = u.id
                ORDER BY i.fecha_inscripcion DESC
            ";
            $result = $conn->query($sql);
            
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    $row['id'],
                    $row['evento'],
                    $row['usuario'],
                    ucfirst($row['estado']),
                    $row['fecha_inscripcion'],
                    $row['certificado_generado'] ? 'Sí' : 'No'
                ];
            }
            break;
            
        default:
            throw new Exception('Tipo de reporte no válido');
    }
    
    // Generate output based on format
    if ($formato === 'excel') {
        exportToExcel($titulo, $headers, $data);
    } elseif ($formato === 'pdf') {
        exportToPDF($titulo, $headers, $data);
    } else {
        // Return as JSON for preview
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'titulo' => $titulo,
            'headers' => $headers,
            'data' => $data,
            'total' => count($data)
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Export data to Excel format (CSV compatible with Excel)
 */
function exportToExcel($titulo, $headers, $data) {
    $filename = strtolower(str_replace(' ', '_', $titulo)) . '_' . date('Y-m-d_His') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // UTF-8 BOM for Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Title row
    fputcsv($output, [$titulo]);
    fputcsv($output, ['Generado el: ' . date('d/m/Y H:i:s')]);
    fputcsv($output, []); // Empty row
    
    // Headers
    fputcsv($output, $headers);
    
    // Data rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    // Summary
    fputcsv($output, []);
    fputcsv($output, ['Total de registros: ' . count($data)]);
    
    fclose($output);
    exit();
}

/**
 * Export data to PDF format (HTML-based PDF)
 */
function exportToPDF($titulo, $headers, $data) {
    $filename = strtolower(str_replace(' ', '_', $titulo)) . '_' . date('Y-m-d_His') . '.html';
    
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($titulo) . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #163E8C;
        }
        .logo { font-size: 24px; color: #163E8C; font-weight: bold; }
        .logo span { color: #FED600; }
        h1 { color: #163E8C; margin: 15px 0; font-size: 22px; }
        .date { color: #666; font-size: 12px; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
            font-size: 11px;
        }
        th { 
            background: #163E8C; 
            color: white; 
            padding: 10px 8px; 
            text-align: left;
            font-weight: 600;
        }
        td { 
            padding: 8px; 
            border-bottom: 1px solid #ddd; 
        }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #f0f0f0; }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
            color: #163E8C;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">SIGEVEN - <span>EMI</span></div>
        <h1>' . htmlspecialchars($titulo) . '</h1>
        <div class="date">Generado el: ' . date('d/m/Y H:i:s') . '</div>
    </div>
    
    <button onclick="window.print()" class="no-print" style="margin-bottom: 20px; padding: 10px 20px; background: #163E8C; color: white; border: none; cursor: pointer; border-radius: 4px;">Imprimir / Guardar como PDF</button>
    
    <table>
        <thead>
            <tr>';
    
    foreach ($headers as $header) {
        $html .= '<th>' . htmlspecialchars($header) . '</th>';
    }
    
    $html .= '</tr>
        </thead>
        <tbody>';
    
    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($row as $cell) {
            $html .= '<td>' . htmlspecialchars($cell) . '</td>';
        }
        $html .= '</tr>';
    }
    
    $html .= '</tbody>
    </table>
    
    <div class="total">Total de registros: ' . count($data) . '</div>
    
    <div class="footer">
        <p>Sistema de Gestión de Eventos Universitarios - Escuela Militar de Ingeniería</p>
        <p>Av. Arce No. 2642, Zona San Jorge, La Paz Bolivia</p>
        <p>© 2025 EMI - Todos los derechos reservados</p>
    </div>
</body>
</html>';
    
    echo $html;
    exit();
}
?>
