<?php
require_once 'config.php';

// Permitir peticiones POST desde Landbot
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo permitir peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    // Obtener el contenido JSON del webhook
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Datos JSON inválidos');
    }
    
    // Log para debugging
    error_log("Webhook recibido: " . $input);
    
    // Extraer datos del webhook de Landbot
    $userId = $data['user_id'] ?? $data['userId'] ?? null;
    $userEmail = $data['user_email'] ?? $data['userEmail'] ?? null;
    $userName = $data['user_name'] ?? $data['userName'] ?? null;
    $puntajeTotal = $data['puntaje_total'] ?? $data['puntajeTotal'] ?? 0;
    $evaluacionFinal = $data['evaluacion_final'] ?? $data['evaluacionFinal'] ?? 'Sin evaluación';
    $respuestas = $data['respuestas'] ?? $data['answers'] ?? [];
    
    // Validar datos mínimos
    if (!$userId && !$userEmail) {
        throw new Exception('Se requiere ID de usuario o email');
    }
    
    $pdo = getConnection();
    
    // Buscar usuario por ID o email
    $stmt = $pdo->prepare("
        SELECT Id_Usuario, nombre, correo 
        FROM tbl_usuario 
        WHERE Id_Usuario = ? OR correo = ?
    ");
    $stmt->execute([$userId, $userEmail]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('Usuario no encontrado en la base de datos');
    }
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // 1. Crear registro en tbl_historial
    $stmt = $pdo->prepare("
        INSERT INTO tbl_historial (Id_UsuarioFK, fecha, estado) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $user['Id_Usuario'],
        date('Y-m-d'),
        'Completado'
    ]);
    
    $historialId = $pdo->lastInsertId();
    
    // 2. Guardar respuestas individuales si están disponibles
    if (!empty($respuestas) && is_array($respuestas)) {
        $stmt = $pdo->prepare("
            INSERT INTO tbl_respuestas (Id_historial, pregunta, respuesta, puntaje) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($respuestas as $respuesta) {
            $pregunta = $respuesta['pregunta'] ?? $respuesta['question'] ?? 'Pregunta sin especificar';
            $respuestaTexto = $respuesta['respuesta'] ?? $respuesta['answer'] ?? 'Sin respuesta';
            $puntaje = $respuesta['puntaje'] ?? $respuesta['score'] ?? 0;
            
            $stmt->execute([
                $historialId,
                $pregunta,
                $respuestaTexto,
                $puntaje
            ]);
        }
    }
    
    // 3. Guardar resultado final
    $stmt = $pdo->prepare("
        INSERT INTO tbl_resultados (Id_historiaLFK, puntaje_total, resultado_final, fecha_registro) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $historialId,
        $puntajeTotal,
        $evaluacionFinal,
        date('Y-m-d H:i:s')
    ]);
    
    // Confirmar transacción
    $pdo->commit();
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Resultados guardados exitosamente',
        'data' => [
            'historial_id' => $historialId,
            'user_id' => $user['Id_Usuario'],
            'user_name' => $user['nombre'],
            'puntaje_total' => $puntajeTotal,
            'evaluacion_final' => $evaluacionFinal
        ]
    ]);
    
} catch (Exception $e) {
    // Revertir transacción si hay error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Error en webhook: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 