<?php
require_once 'config.php';

// Configurar headers para mostrar HTML
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Webhook Chatbot</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .container { max-width: 800px; margin: 0 auto; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #ddd; }
        .step.success { border-left-color: green; }
        .step.error { border-left-color: red; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 Test de Simulación Webhook Chatbot</h1>";

try {
    echo "<div class='step info'>
        <h3>Paso 1: Verificar conexión a base de datos</h3>";
    
    $pdo = getConnection();
    echo "<p class='success'> Conexión exitosa a la base de datos</p>";
    
    // Verificar usuario admin
    $stmt = $pdo->prepare("SELECT Id_Usuario, nombre, correo FROM tbl_usuario WHERE rol = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if (!$admin) {
        throw new Exception("No se encontró usuario administrador");
    }
    
    echo "<p class='success'> Usuario admin encontrado: {$admin['nombre']} ({$admin['correo']})</p>";
    echo "</div>";
    
    // Simular datos del webhook
    $webhookData = [
        'user_id' => $admin['Id_Usuario'],
        'user_email' => $admin['correo'],
        'user_name' => $admin['nombre'],
        'puntaje_total' => 52,
        'evaluacion_final' => 'Nivel alto',
        'respuestas' => [
            [ 'pregunta' => 'Pregunta 1', 'respuesta' => 'Respuesta 1', 'puntaje' => 3 ],
            [ 'pregunta' => 'Pregunta 2', 'respuesta' => 'Respuesta 2', 'puntaje' => 4 ],
            [ 'pregunta' => 'Pregunta 3', 'respuesta' => 'Respuesta 3', 'puntaje' => 2 ],
            [ 'pregunta' => 'Pregunta 4', 'respuesta' => 'Respuesta 4', 'puntaje' => 3 ],
            [ 'pregunta' => 'Pregunta 5', 'respuesta' => 'Respuesta 5', 'puntaje' => 4 ],
            [ 'pregunta' => 'Pregunta 6', 'respuesta' => 'Respuesta 6', 'puntaje' => 3 ],
            [ 'pregunta' => 'Pregunta 7', 'respuesta' => 'Respuesta 7', 'puntaje' => 2 ],
            [ 'pregunta' => 'Pregunta 8', 'respuesta' => 'Respuesta 8', 'puntaje' => 4 ],
            [ 'pregunta' => 'Pregunta 9', 'respuesta' => 'Respuesta 9', 'puntaje' => 3 ],
            [ 'pregunta' => 'Pregunta 10', 'respuesta' => 'Respuesta 10', 'puntaje' => 2 ],
            [ 'pregunta' => 'Pregunta 11', 'respuesta' => 'Respuesta 11', 'puntaje' => 3 ],
            [ 'pregunta' => 'Pregunta 12', 'respuesta' => 'Respuesta 12', 'puntaje' => 4 ],
            [ 'pregunta' => 'Pregunta 13', 'respuesta' => 'Respuesta 13', 'puntaje' => 2 ],
            [ 'pregunta' => 'Pregunta 14', 'respuesta' => 'Respuesta 14', 'puntaje' => 3 ],
            [ 'pregunta' => 'Pregunta 15', 'respuesta' => 'Respuesta 15', 'puntaje' => 4 ],
            [ 'pregunta' => 'Pregunta 16', 'respuesta' => 'Respuesta 16', 'puntaje' => 3 ],
            [ 'pregunta' => 'Pregunta 17', 'respuesta' => 'Respuesta 17', 'puntaje' => 2 ],
            [ 'pregunta' => 'Pregunta 18', 'respuesta' => 'Respuesta 18', 'puntaje' => 4 ],
            [ 'pregunta' => 'Pregunta 19', 'respuesta' => 'Respuesta 19', 'puntaje' => 3 ],
            [ 'pregunta' => 'Pregunta 20', 'respuesta' => 'Respuesta 20', 'puntaje' => 4 ]
        ]
    ];
    
    echo "<div class='step info'>
        <h3>Paso 2: Datos del webhook a procesar</h3>
        <pre>" . json_encode($webhookData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>
    </div>";
    
    // Procesar webhook (simular webhook_handler.php)
    echo "<div class='step info'>
        <h3>Paso 3: Procesando webhook</h3>";
    
    $pdo->beginTransaction();
    
    // 1. Crear historial
    $stmt = $pdo->prepare("
        INSERT INTO tbl_historial (Id_UsuarioFK, fecha, estado) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $admin['Id_Usuario'],
        date('Y-m-d'),
        'Completado'
    ]);
    
    $historialId = $pdo->lastInsertId();
    echo "<p class='success'> Historial creado con ID: {$historialId}</p>";
    
    // 2. Guardar respuestas
    $stmt = $pdo->prepare("
        INSERT INTO tbl_respuestas (Id_historial, pregunta, respuesta, puntaje) 
        VALUES (?, ?, ?, ?)
    ");
    
    $respuestasGuardadas = 0;
    foreach ($webhookData['respuestas'] as $respuesta) {
        $stmt->execute([
            $historialId,
            $respuesta['pregunta'],
            $respuesta['respuesta'],
            $respuesta['puntaje']
        ]);
        $respuestasGuardadas++;
    }
    
    echo "<p class='success'> {$respuestasGuardadas} respuestas guardadas</p>";
    
    // 3. Guardar resultado final
    $stmt = $pdo->prepare("
        INSERT INTO tbl_resultados (Id_historiaLFK, puntaje_total, resultado_final, fecha_registro) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $historialId,
        $webhookData['puntaje_total'],
        $webhookData['evaluacion_final'],
        date('Y-m-d H:i:s')
    ]);
    
    echo "<p class='success'>Resultado final guardado</p>";
    
    $pdo->commit();
    echo "</div>";
    
    // Verificar datos guardados
    echo "<div class='step success'>
        <h3>Paso 4: Verificación de datos guardados</h3>";
    
    $stmt = $pdo->prepare("
        SELECT 
            h.Id_historial,
            u.nombre,
            u.correo,
            r.puntaje_total,
            r.resultado_final,
            r.fecha_registro,
            COUNT(resp.Id_respuesta) as total_respuestas
        FROM tbl_resultados r
        JOIN tbl_historial h ON r.Id_historiaLFK = h.Id_historial
        JOIN tbl_usuario u ON h.Id_UsuarioFK = u.Id_Usuario
        LEFT JOIN tbl_respuestas resp ON h.Id_historial = resp.Id_historial
        WHERE h.Id_historial = ?
        GROUP BY h.Id_historial
    ");
    $stmt->execute([$historialId]);
    $resultado = $stmt->fetch();
    
    if ($resultado) {
        echo "<p class='success'> Datos verificados correctamente:</p>";
        echo "<ul>";
        echo "<li><strong>ID Historial:</strong> {$resultado['Id_historial']}</li>";
        echo "<li><strong>Usuario:</strong> {$resultado['nombre']}</li>";
        echo "<li><strong>Email:</strong> {$resultado['correo']}</li>";
        echo "<li><strong>Puntaje:</strong> {$resultado['puntaje_total']}/60</li>";
        echo "<li><strong>Resultado:</strong> {$resultado['resultado_final']}</li>";
        echo "<li><strong>Fecha:</strong> {$resultado['fecha_registro']}</li>";
        echo "<li><strong>Total respuestas:</strong> {$resultado['total_respuestas']}</li>";
        echo "</ul>";
    }
    
    echo "</div>";
    
    // Mostrar estadísticas actuales
    echo "<div class='step info'>
        <h3>Paso 5: Estadísticas actuales</h3>";
    
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_evaluaciones,
            AVG(r.puntaje_total) as promedio_puntaje,
            COUNT(CASE WHEN r.resultado_final = 'Nivel alto' THEN 1 END) as nivel_alto,
            COUNT(CASE WHEN r.resultado_final = 'Nivel medio' THEN 1 END) as nivel_medio,
            COUNT(CASE WHEN r.resultado_final = 'Nivel bajo' THEN 1 END) as nivel_bajo
        FROM tbl_resultados r
        JOIN tbl_historial h ON r.Id_historiaLFK = h.Id_historial
        WHERE h.estado = 'Completado'
    ");
    $stmt->execute();
    $stats = $stmt->fetch();
    
    echo "<p class='success'>📊 Estadísticas del sistema:</p>";
    echo "<ul>";
    echo "<li><strong>Total evaluaciones:</strong> {$stats['total_evaluaciones']}</li>";
    echo "<li><strong>Promedio puntaje:</strong> " . round($stats['promedio_puntaje'], 1) . "/60</li>";
    echo "<li><strong>Nivel alto:</strong> {$stats['nivel_alto']}</li>";
    echo "<li><strong>Nivel medio:</strong> {$stats['nivel_medio']}</li>";
    echo "<li><strong>Nivel bajo:</strong> {$stats['nivel_bajo']}</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div class='step success'>
        <h3> ¡Test completado exitosamente!</h3>
        <p>El sistema de guardado de resultados del chatbot está funcionando correctamente.</p>
        
        <h4>Próximos pasos para verificar:</h4>
        <ol>
            <li><a href='login.html' target='_blank'>Iniciar sesión como administrador</a></li>
            <li><a href='resultados.html' target='_blank'>Acceder al panel de resultados</a></li>
            <li>Verificar que aparezca el nuevo resultado en la tabla</li>
            <li>Hacer clic en 'Ver Detalles' para ver las respuestas individuales</li>
        </ol>
        
        <h4>Enlaces útiles:</h4>
        <ul>
            <li><a href='webhook_handler.php' target='_blank'>Webhook Handler</a> - Endpoint para recibir datos del chatbot</li>
            <li><a href='get_results.php' target='_blank'>Get Results API</a> - API para obtener resultados</li>
            <li><a href='get_result_details.php' target='_blank'>Get Result Details API</a> - API para detalles específicos</li>
        </ul>
    </div>";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "<div class='step error'>
        <h3> Error en el test</h3>
        <p class='error'>" . $e->getMessage() . "</p>
    </div>";
}
    
echo "</div></body></html>";
?> 