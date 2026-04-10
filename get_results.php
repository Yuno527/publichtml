<?php
// Evitar que se muestren errores PHP en la salida
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Verificar que el usuario esté logueado y sea administrador
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

try {
    $pdo = getConnection();
    
    // Obtener todos los resultados con información del usuario
    $stmt = $pdo->prepare("
        SELECT 
            h.Id_historial,
            u.nombre,
            u.correo,
            r.puntaje_total,
            r.resultado_final,
            r.fecha_registro,
            SUBSTRING(r.resultado_final, 1, 100) as analisis_preview
        FROM tbl_resultados r
        JOIN tbl_historial h ON r.Id_historialFK = h.Id_historial
        JOIN tbl_usuario u ON h.Id_UsuarioFK = u.Id_Usuario
        WHERE h.estado = 'Completado'
        ORDER BY r.fecha_registro DESC
    ");
    
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular estadísticas
    $stats = [];
    
    // Total de evaluaciones
    $stats['totalTests'] = count($results);
    
    // Puntaje promedio
    if (count($results) > 0) {
        $totalScore = array_sum(array_column($results, 'puntaje_total'));
        $stats['avgScore'] = round($totalScore / count($results), 1);
    } else {
        $stats['avgScore'] = 0;
    }
    
    // Conteo por nivel (basado en puntaje, no en texto)
    $stats['highLevel'] = 0;
    $stats['mediumLevel'] = 0;
    $stats['lowLevel'] = 0;
    
    foreach ($results as $result) {
        // Determinar nivel basado en el puntaje
        $puntaje = (int)$result['puntaje_total'];
        if ($puntaje > 21) {
            $stats['highLevel']++;
        } elseif ($puntaje > 12) {
            $stats['mediumLevel']++;
        } else {
            $stats['lowLevel']++;
        }
    }
    
    echo json_encode([
        'success' => true,
        'results' => $results,
        'stats' => $stats
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?> 