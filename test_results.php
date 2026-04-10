<?php
// Test para verificar el sistema de resultados
session_start();
require_once 'config.php';

echo "<h2>Test del Sistema de Resultados</h2>";

// Simular login de admin
$_SESSION['user_id'] = 1; // Asumiendo que el admin tiene ID 1

echo "<h3>1. Probando get_user_status.php</h3>";
$userStatusUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/get_user_status.php';
$userStatusResponse = file_get_contents($userStatusUrl);
echo "<p>Respuesta: <pre>" . htmlspecialchars($userStatusResponse) . "</pre></p>";

echo "<h3>2. Probando get_results.php</h3>";
$resultsUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/get_results.php';
$resultsResponse = file_get_contents($resultsUrl);
echo "<p>Respuesta: <pre>" . htmlspecialchars($resultsResponse) . "</pre></p>";

echo "<h3>3. Verificando estructura de la base de datos</h3>";
try {
    $pdo = getConnection();
    
    // Verificar tablas
    $tables = ['tbl_usuario', 'tbl_historial', 'tbl_resultados', 'tbl_respuestas'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Tabla $table existe</p>";
            
            // Mostrar estructura
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<ul>";
            foreach ($columns as $column) {
                echo "<li>" . $column['Field'] . " - " . $column['Type'] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>❌ Tabla $table NO existe</p>";
        }
    }
    
    // Verificar datos
    echo "<h3>4. Verificando datos en las tablas</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_usuario");
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Usuarios: $userCount</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_historial");
    $historialCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Historiales: $historialCount</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_resultados");
    $resultadosCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Resultados: $resultadosCount</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_respuestas");
    $respuestasCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Respuestas: $respuestasCount</p>";
    
    // Probar consulta de resultados
    echo "<h3>5. Probando consulta de resultados</h3>";
    $stmt = $pdo->prepare("
        SELECT 
            h.Id_historial,
            u.nombre,
            u.correo,
            r.puntaje_total,
            r.resultado_final,
            r.fecha_registro
        FROM tbl_resultados r
        JOIN tbl_historial h ON r.Id_historialFK = h.Id_historial
        JOIN tbl_usuario u ON h.Id_UsuarioFK = u.Id_Usuario
        WHERE h.estado = 'Completado'
        ORDER BY r.fecha_registro DESC
    ");
    
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Resultados encontrados: " . count($results) . "</p>";
    
    if (count($results) > 0) {
        echo "<h4>Primer resultado:</h4>";
        echo "<pre>" . print_r($results[0], true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Limpiar sesión
unset($_SESSION['user_id']);
?> 