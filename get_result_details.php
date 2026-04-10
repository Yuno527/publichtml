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

// Obtener datos del POST
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['historialId'])) {
    echo json_encode(['success' => false, 'message' => 'ID de historial requerido']);
    exit;
}

$historialId = $input['historialId'];

try {
    $pdo = getConnection();
    
    // Obtener detalles del resultado (incluyendo análisis completo)
    $stmt = $pdo->prepare("
        SELECT 
            u.nombre,
            u.correo,
            r.puntaje_total,
            r.resultado_final as analisis_completo,
            r.fecha_registro
        FROM tbl_resultados r
        JOIN tbl_historial h ON r.Id_historialFK = h.Id_historial
        JOIN tbl_usuario u ON h.Id_UsuarioFK = u.Id_Usuario
        WHERE h.Id_historial = ? AND h.estado = 'Completado'
    ");
    
    $stmt->execute([$historialId]);
    $details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$details) {
        echo json_encode(['success' => false, 'message' => 'Resultado no encontrado']);
        exit;
    }
    
    // Obtener respuestas individuales
    $stmt = $pdo->prepare("
        SELECT pregunta, respuesta, puntaje
        FROM tbl_respuestas
        WHERE Id_historial = ?
        ORDER BY Id_respuesta
    ");
    
    $stmt->execute([$historialId]);
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'details' => $details,
        'responses' => $responses
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?> 