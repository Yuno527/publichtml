<?php
require_once 'config.php';
require_once 'check_auth.php';

// Verificar autenticación
if (!isLoggedIn()) {
    header('Location: login.html');
    exit;
}

// Verificar que sea administrador
$currentUser = getCurrentUser();
if ($currentUser['role'] !== 'admin') {
    header('Location: index.html');
    exit;
}

try {
    $pdo = getConnection();
    
    // Obtener filtros
    $dateFrom = $_GET['dateFrom'] ?? '';
    $dateTo = $_GET['dateTo'] ?? '';
    $userFilter = $_GET['userFilter'] ?? '';
    $scoreFilter = $_GET['scoreFilter'] ?? '';
    
    // Construir consulta base
    $query = "
        SELECT 
            u.nombre,
            u.correo,
            h.fecha,
            r.puntaje_total,
            r.resultado_final,
            r.fecha_registro
        FROM tbl_historial h
        INNER JOIN tbl_usuario u ON h.Id_UsuarioFK = u.Id_Usuario
        INNER JOIN tbl_resultados r ON h.Id_historial = r.Id_historiaLFK
        WHERE h.estado = 'Completado'
    ";
    
    $params = [];
    
    // Aplicar filtros
    if ($dateFrom) {
        $query .= " AND h.fecha >= ?";
        $params[] = $dateFrom;
    }
    
    if ($dateTo) {
        $query .= " AND h.fecha <= ?";
        $params[] = $dateTo;
    }
    
    if ($userFilter) {
        $query .= " AND (u.nombre LIKE ? OR u.correo LIKE ?)";
        $params[] = "%$userFilter%";
        $params[] = "%$userFilter%";
    }
    
    if ($scoreFilter) {
        switch ($scoreFilter) {
            case 'high':
                $query .= " AND r.puntaje_total >= 80";
                break;
            case 'medium':
                $query .= " AND r.puntaje_total >= 60 AND r.puntaje_total < 80";
                break;
            case 'low':
                $query .= " AND r.puntaje_total < 60";
                break;
        }
    }
    
    // Ordenar por fecha más reciente
    $query .= " ORDER BY h.fecha DESC, r.fecha_registro DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    // Configurar headers para descarga CSV
    $filename = 'resultados_evaluaciones_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Crear archivo CSV
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Encabezados
    fputcsv($output, [
        'Nombre',
        'Email',
        'Fecha de Evaluación',
        'Puntaje Total',
        'Evaluación Final',
        'Fecha de Registro'
    ]);
    
    // Datos
    foreach ($results as $row) {
        fputcsv($output, [
            $row['nombre'],
            $row['correo'],
            $row['fecha'],
            $row['puntaje_total'],
            $row['resultado_final'],
            $row['fecha_registro']
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    error_log("Error exportando resultados: " . $e->getMessage());
    echo "Error al exportar resultados: " . $e->getMessage();
}
?> 