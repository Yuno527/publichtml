<?php
require_once 'config.php';
require_once 'check_auth.php';

// Verificar autenticación
if (!isLoggedIn()) {
    sendResponse(false, 'No autenticado');
}

// Verificar que sea administrador
$currentUser = getCurrentUser();
if ($currentUser['role'] !== 'admin') {
    sendResponse(false, 'Acceso denegado. Solo administradores pueden ver estadísticas.');
}

try {
    $pdo = getConnection();
    
    // Total de pruebas
    $stmt = $pdo->query("
        SELECT COUNT(*) as total 
        FROM tbl_historial 
        WHERE estado = 'Completado'
    ");
    $totalTests = $stmt->fetch()['total'];
    
    // Puntaje promedio
    $stmt = $pdo->query("
        SELECT AVG(r.puntaje_total) as promedio 
        FROM tbl_resultados r
        INNER JOIN tbl_historial h ON r.Id_historiaLFK = h.Id_historial
        WHERE h.estado = 'Completado'
    ");
    $avgScore = round($stmt->fetch()['promedio'] ?? 0, 1);
    
    // Puntajes altos (80+)
    $stmt = $pdo->query("
        SELECT COUNT(*) as altos 
        FROM tbl_resultados r
        INNER JOIN tbl_historial h ON r.Id_historiaLFK = h.Id_historial
        WHERE h.estado = 'Completado' AND r.puntaje_total >= 80
    ");
    $highScores = $stmt->fetch()['altos'];
    
    // Pruebas de hoy
    $stmt = $pdo->query("
        SELECT COUNT(*) as hoy 
        FROM tbl_historial 
        WHERE estado = 'Completado' AND fecha = CURDATE()
    ");
    $todayTests = $stmt->fetch()['hoy'];
    
    $stats = [
        'totalTests' => $totalTests,
        'avgScore' => $avgScore,
        'highScores' => $highScores,
        'todayTests' => $todayTests
    ];
    
    sendResponse(true, 'Estadísticas obtenidas exitosamente', $stats);
    
} catch (Exception $e) {
    error_log("Error obteniendo estadísticas: " . $e->getMessage());
    sendResponse(false, 'Error al obtener estadísticas: ' . $e->getMessage());
}
?> 