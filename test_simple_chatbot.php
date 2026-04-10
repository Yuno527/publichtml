<?php
// Test simple del chatbot
session_start();
require_once 'config.php';

echo "<h2>🔧 Test Simple del Chatbot - AICROOM</h2>";

// 1. Verificar que XAMPP esté funcionando
echo "<h3>1. Estado del Servidor</h3>";
if (function_exists('phpinfo')) {
    echo "<p style='color: green;'>✅ PHP está funcionando</p>";
} else {
    echo "<p style='color: red;'>❌ PHP no está funcionando</p>";
    exit;
}

// 2. Verificar base de datos
echo "<h3>2. Base de Datos</h3>";
try {
    $pdo = getConnection();
    echo "<p style='color: green;'>✅ Conexión a base de datos exitosa</p>";
    
    // Verificar si hay usuarios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_usuario");
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Usuarios en la base de datos: <strong>$userCount</strong></p>";
    
    if ($userCount == 0) {
        echo "<p style='color: orange;'>⚠️ No hay usuarios. Ejecuta <a href='check_admin.php'>check_admin.php</a> para crear un admin</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error de base de datos: " . $e->getMessage() . "</p>";
    exit;
}

// 3. Verificar archivos críticos
echo "<h3>3. Archivos del Chatbot</h3>";
$files = [
    'chatbot.html' => 'Página del chatbot',
    'config.php' => 'Configuración',
    'style.css' => 'Estilos CSS',
    'get_user_status.php' => 'API de estado',
    'check_test_completion.php' => 'API de test completado',
    'save_test_results.php' => 'API de guardado'
];

$allFilesExist = true;
foreach ($files as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "<p style='color: green;'>✅ $description ($file) - $size bytes</p>";
    } else {
        echo "<p style='color: red;'>❌ $description ($file) - NO EXISTE</p>";
        $allFilesExist = false;
    }
}

if (!$allFilesExist) {
    echo "<p style='color: red;'>❌ Faltan archivos críticos. El chatbot no funcionará.</p>";
    exit;
}

// 4. Probar API de estado de usuario
echo "<h3>4. Prueba de API de Estado</h3>";
try {
    // Simular una sesión para la prueba
    $_SESSION['user_id'] = 1;
    $_SESSION['user_email'] = 'test@aicroom.com';
    $_SESSION['user_name'] = 'Usuario Test';
    
    $response = file_get_contents('http://localhost/Aicroom/get_user_status.php');
    if ($response !== false) {
        $data = json_decode($response, true);
        if ($data !== null) {
            echo "<p style='color: green;'>✅ API de estado funciona correctamente</p>";
            echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color: red;'>❌ API no devuelve JSON válido</p>";
            echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . htmlspecialchars($response) . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ No se puede acceder a la API</p>";
        echo "<p>Verifica que Apache esté corriendo en XAMPP</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error probando API: " . $e->getMessage() . "</p>";
}

// Limpiar sesión de prueba
unset($_SESSION['user_id']);
unset($_SESSION['user_email']);
unset($_SESSION['user_name']);

echo "<h3>5. Estado Actual</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✅ Usuario logueado: ID " . $_SESSION['user_id'] . "</p>";
} else {
    echo "<p style='color: orange;'>⚠️ No hay usuario logueado</p>";
}

echo "<h3>6. Próximos Pasos</h3>";
echo "<ol>";
echo "<li><strong>Si todo está verde:</strong> El chatbot debería funcionar correctamente</li>";
echo "<li><strong>Si hay errores rojos:</strong> Sigue las instrucciones de solución</li>";
echo "<li><strong>Si no hay usuarios:</strong> Ejecuta <a href='check_admin.php'>check_admin.php</a></li>";
echo "<li><strong>Para probar el chatbot:</strong> Ve a <a href='chatbot.html'>chatbot.html</a></li>";
echo "</ol>";

echo "<h3>7. Enlaces de Prueba</h3>";
echo "<ul>";
echo "<li><a href='chatbot.html' target='_blank'>🚀 Chatbot Embebido</a></li>";
echo "<li><a href='login.html' target='_blank'>🔐 Página de Login</a></li>";
echo "<li><a href='check_admin.php' target='_blank'>👨‍💼 Crear Admin</a></li>";
echo "<li><a href='resultados.html' target='_blank'>📊 Panel de Resultados</a></li>";
echo "</ul>";

echo "<h3>8. Solución de Problemas</h3>";
echo "<ul>";
echo "<li><strong>XAMPP no funciona:</strong> Inicia Apache y MySQL desde el panel de control de XAMPP</li>";
echo "<li><strong>Base de datos falla:</strong> Verifica que MySQL esté corriendo</li>";
echo "<li><strong>APIs no responden:</strong> Verifica que Apache esté corriendo</li>";
echo "<li><strong>Chatbot no se carga:</strong> Limpia caché del navegador (Ctrl+F5)</li>";
echo "<li><strong>Errores JavaScript:</strong> Abre la consola del navegador (F12) para ver errores</li>";
echo "</ul>";

echo "<h3>9. Verificación Final</h3>";
echo "<p>Si todo está funcionando, deberías poder:</p>";
echo "<ul>";
echo "<li>✅ Acceder a <a href='chatbot.html'>chatbot.html</a></li>";
echo "<li>✅ Ver el chatbot embebido (no como pop-up)</li>";
echo "<li>✅ Hacer login si no estás autenticado</li>";
echo "<li>✅ Completar el test una sola vez</li>";
echo "<li>✅ Ver el mensaje de 'ya completado' si ya lo hiciste</li>";
echo "</ul>";
?> 