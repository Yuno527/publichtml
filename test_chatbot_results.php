<?php
require_once 'config.php';

echo "<h2>Test de Guardado de Resultados del Chatbot</h2>";

try {
    $pdo = getConnection();
    
    // Simular datos del webhook del chatbot
    $testData = [
        'user_id' => 1, // ID del usuario admin
        'user_email' => 'breiner@aicroom.com',
        'user_name' => 'Breiner',
        'puntaje_total' => 60,
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
    
    // Verificar que el usuario existe
    $stmt = $pdo->prepare("SELECT Id_Usuario, nombre, correo FROM tbl_usuario WHERE Id_Usuario = ?");
    $stmt->execute([$testData['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "<p style='color: red;'>❌ Usuario no encontrado</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Usuario encontrado: {$user['nombre']} ({$user['correo']})</p>";
    
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
    echo "<p style='color: green;'>✅ Historial creado con ID: {$historialId}</p>";
    
    // 2. Guardar respuestas individuales
    $stmt = $pdo->prepare("
        INSERT INTO tbl_respuestas (Id_historial, pregunta, respuesta, puntaje) 
        VALUES (?, ?, ?, ?)
    ");
    
    $respuestasGuardadas = 0;
    foreach ($testData['respuestas'] as $respuesta) {
        $stmt->execute([
            $historialId,
            $respuesta['pregunta'],
            $respuesta['respuesta'],
            $respuesta['puntaje']
        ]);
        $respuestasGuardadas++;
    }
    
    echo "<p style='color: green;'>✅ {$respuestasGuardadas} respuestas guardadas</p>";
    
    // 3. Guardar resultado final
    $stmt = $pdo->prepare("
        INSERT INTO tbl_resultados (Id_historiaLFK, puntaje_total, resultado_final, fecha_registro) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $historialId,
        $testData['puntaje_total'],
        $testData['evaluacion_final'],
        date('Y-m-d H:i:s')
    ]);
    
    echo "<p style='color: green;'>✅ Resultado final guardado</p>";
    
    // Confirmar transacción
    $pdo->commit();
    
    echo "<h3>✅ Test completado exitosamente</h3>";
    echo "<p><strong>Datos guardados:</strong></p>";
    echo "<ul>";
    echo "<li>Usuario: {$user['nombre']}</li>";
    echo "<li>Puntaje total: {$testData['puntaje_total']}/60</li>";
    echo "<li>Resultado: {$testData['evaluacion_final']}</li>";
    echo "<li>Respuestas: {$respuestasGuardadas}</li>";
    echo "</ul>";
    
    // Verificar que se guardó correctamente
    $stmt = $pdo->prepare("
        SELECT 
            h.Id_historial,
            u.nombre,
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
        echo "<h3>✅ Verificación en base de datos:</h3>";
        echo "<ul>";
        echo "<li>ID Historial: {$resultado['Id_historial']}</li>";
        echo "<li>Usuario: {$resultado['nombre']}</li>";
        echo "<li>Puntaje: {$resultado['puntaje_total']}</li>";
        echo "<li>Resultado: {$resultado['resultado_final']}</li>";
        echo "<li>Fecha: {$resultado['fecha_registro']}</li>";
        echo "<li>Total respuestas: {$resultado['total_respuestas']}</li>";
        echo "</ul>";
    }
    
    echo "<h3>🎯 Próximos pasos:</h3>";
    echo "<ol>";
    echo "<li>Inicia sesión como administrador en <a href='login.html'>login.html</a></li>";
    echo "<li>Accede al panel de resultados en <a href='resultados.html'>resultados.html</a></li>";
    echo "<li>Verifica que aparezca el nuevo resultado en la tabla</li>";
    echo "<li>Haz clic en 'Ver Detalles' para ver las respuestas individuales</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 