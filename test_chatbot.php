<?php
// Test para verificar el sistema del chatbot
session_start();
require_once 'config.php';

echo "<h2>Test del Sistema del Chatbot</h2>";

// Simular login de usuario
$_SESSION['user_id'] = 2; // Usuario normal

echo "<h3>1. Probando get_user_status.php</h3>";
$userStatusUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/get_user_status.php';
$userStatusResponse = file_get_contents($userStatusUrl);
echo "<p>Respuesta: <pre>" . htmlspecialchars($userStatusResponse) . "</pre></p>";

echo "<h3>2. Probando check_test_completion.php</h3>";
$completionUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/check_test_completion.php';
$completionResponse = file_get_contents($completionUrl);
echo "<p>Respuesta: <pre>" . htmlspecialchars($completionResponse) . "</pre></p>";

echo "<h3>3. Verificando estructura de la base de datos para el chatbot</h3>";
try {
    $pdo = getConnection();
    
    // Verificar si el usuario existe
    $stmt = $pdo->prepare("SELECT Id_Usuario, nombre, correo, rol FROM tbl_usuario WHERE Id_Usuario = ?");
    $stmt->execute([2]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p style='color: green;'>✅ Usuario encontrado: " . $user['nombre'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Usuario no encontrado</p>";
    }
    
    // Verificar historial del usuario
    $stmt = $pdo->prepare("
        SELECT h.Id_historial, h.estado, h.fecha_registro
        FROM tbl_historial h 
        WHERE h.Id_UsuarioFK = ?
        ORDER BY h.fecha_registro DESC
    ");
    $stmt->execute([2]);
    $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Historiales del usuario: " . count($historial) . "</p>";
    
    if (count($historial) > 0) {
        echo "<h4>Último historial:</h4>";
        echo "<pre>" . print_r($historial[0], true) . "</pre>";
    }
    
    // Verificar si hay resultados
    $stmt = $pdo->prepare("
        SELECT r.Id_resultado, r.puntaje_total, r.resultado_final
        FROM tbl_resultados r
        JOIN tbl_historial h ON r.Id_historialFK = h.Id_historial
        WHERE h.Id_UsuarioFK = ?
    ");
    $stmt->execute([2]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Resultados del usuario: " . count($resultados) . "</p>";
    
    if (count($resultados) > 0) {
        echo "<h4>Último resultado:</h4>";
        echo "<pre>" . print_r($resultados[0], true) . "</pre>";
    }
    
    // Verificar respuestas
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM tbl_respuestas r
        JOIN tbl_historial h ON r.Id_historial = h.Id_historial
        WHERE h.Id_UsuarioFK = ?
    ");
    $stmt->execute([2]);
    $respuestasCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p>Respuestas del usuario: $respuestasCount</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Limpiar sesión
unset($_SESSION['user_id']);
?> 