<?php
// Test del chatbot embebido
session_start();
require_once 'config.php';

echo "<h2>Test del Chatbot Embebido - AICROOM</h2>";

// 1. Verificar conexión a base de datos
echo "<h3>1. Conexión a Base de Datos</h3>";
try {
    $pdo = getConnection();
    echo "<p style='color: green;'>✅ Conexión exitosa</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    exit;
}

// 2. Verificar archivos del chatbot
echo "<h3>2. Archivos del Chatbot</h3>";
$requiredFiles = [
    'chatbot.html' => 'Página del chatbot embebido',
    'custom_chatbot.js' => 'Lógica del chatbot',
    'save_test_results.php' => 'Guardado de resultados',
    'check_test_completion.php' => 'Verificación de test completado',
    'get_user_status.php' => 'Estado del usuario'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $description ($file)</p>";
    } else {
        echo "<p style='color: red;'>❌ $description ($file) - NO EXISTE</p>";
    }
}

// 3. Verificar estructura de base de datos
echo "<h3>3. Estructura de Base de Datos</h3>";
$requiredTables = [
    'tbl_usuario' => ['Id_Usuario', 'nombre', 'correo', 'contraseña', 'rol'],
    'tbl_historial' => ['Id_historial', 'Id_UsuarioFK', 'fecha', 'estado'],
    'tbl_resultados' => ['Id_resultado', 'Id_historialFK', 'puntaje_total', 'resultado_final', 'fecha_registro'],
    'tbl_respuestas' => ['Id_respuesta', 'Id_historial', 'pregunta', 'respuesta', 'puntaje']
];

foreach ($requiredTables as $table => $requiredColumns) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Tabla $table existe</p>";
            
            // Verificar columnas
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $missingColumns = array_diff($requiredColumns, $columns);
            if (empty($missingColumns)) {
                echo "<p style='color: green;'>   ✅ Todas las columnas requeridas están presentes</p>";
            } else {
                echo "<p style='color: orange;'>   ⚠️ Columnas faltantes: " . implode(', ', $missingColumns) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Tabla $table NO existe</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error verificando tabla $table: " . $e->getMessage() . "</p>";
    }
}

// 4. Probar APIs del chatbot
echo "<h3>4. Prueba de APIs del Chatbot</h3>";

// Simular login de admin
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'admin@aicroom.com';
$_SESSION['user_name'] = 'Administrador';

$apis = [
    'get_user_status.php' => 'Estado del usuario',
    'check_test_completion.php' => 'Verificación de test completado'
];

foreach ($apis as $api => $description) {
    try {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $api;
        $response = file_get_contents($url);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if ($data !== null) {
                echo "<p style='color: green;'>✅ $description: API funciona correctamente</p>";
                echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
            } else {
                echo "<p style='color: red;'>❌ $description: Respuesta no es JSON válido</p>";
                echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . htmlspecialchars($response) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>❌ $description: No se pudo acceder a la API</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ $description: Error - " . $e->getMessage() . "</p>";
    }
}

// 5. Verificar funcionalidad de una sola vez
echo "<h3>5. Funcionalidad de Una Sola Vez</h3>";
try {
    // Verificar si el usuario actual ya completó el test
    $stmt = $pdo->prepare("
        SELECT h.Id_historial, h.fecha, r.puntaje_total, r.resultado_final
        FROM tbl_historial h 
        LEFT JOIN tbl_resultados r ON h.Id_historial = r.Id_historialFK
        WHERE h.Id_UsuarioFK = ? AND h.estado = 'Completado'
        ORDER BY h.fecha DESC
        LIMIT 1
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<p style='color: orange;'>⚠️ El usuario ya completó el test el " . $result['fecha'] . "</p>";
        echo "<p>Puntaje: " . $result['puntaje_total'] . " - Resultado: " . $result['resultado_final'] . "</p>";
    } else {
        echo "<p style='color: green;'>✅ El usuario puede realizar el test (no lo ha completado antes)</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error verificando test completado: " . $e->getMessage() . "</p>";
}

// Limpiar sesión
unset($_SESSION['user_id']);
unset($_SESSION['user_email']);
unset($_SESSION['user_name']);

echo "<h3>6. Instrucciones de Uso del Chatbot Embebido</h3>";
echo "<ol>";
echo "<li><strong>Acceso al chatbot:</strong> Ve a <a href='chatbot.html' target='_blank'>chatbot.html</a></li>";
echo "<li><strong>Login requerido:</strong> El chatbot verificará si estás logueado</li>";
echo "<li><strong>Una sola vez:</strong> Si ya completaste el test, verás un mensaje</li>";
echo "<li><strong>Proceso:</strong> Responde las 20 preguntas paso a paso</li>";
echo "<li><strong>Resultados:</strong> Se guardan automáticamente en la base de datos</li>";
echo "<li><strong>Admin:</strong> Los administradores pueden ver resultados en <a href='resultados.html' target='_blank'>resultados.html</a></li>";
echo "</ol>";

echo "<h3>7. Enlaces de Prueba</h3>";
echo "<ul>";
echo "<li><a href='chatbot.html' target='_blank'>Chatbot Embebido</a></li>";
echo "<li><a href='login.html' target='_blank'>Página de Login</a></li>";
echo "<li><a href='resultados.html' target='_blank'>Panel de Resultados (Admin)</a></li>";
echo "<li><a href='check_admin.php' target='_blank'>Verificar/Crear Admin</a></li>";
echo "</ul>";

echo "<h3>8. Características del Chatbot Embebido</h3>";
echo "<ul>";
echo "<li>✅ <strong>Diseño embebido:</strong> Se integra dentro del contenedor principal</li>";
echo "<li>✅ <strong>Una sola vez:</strong> Verifica si el usuario ya completó el test</li>";
echo "<li>✅ <strong>Login requerido:</strong> Solo usuarios autenticados pueden hacer el test</li>";
echo "<li>✅ <strong>20 preguntas:</strong> Banco completo de preguntas de habilidades blandas</li>";
echo "<li>✅ <strong>Guardado automático:</strong> Resultados se guardan en la base de datos</li>";
echo "<li>✅ <strong>Progreso visual:</strong> Barra de progreso durante el test</li>";
echo "<li>✅ <strong>Resultados para admin:</strong> Solo administradores pueden ver resultados</li>";
echo "</ul>";
?> 