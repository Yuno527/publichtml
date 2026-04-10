<?php
// Diagnóstico simple del chatbot
session_start();
require_once 'config.php';

echo "<h2>🔍 Diagnóstico del Chatbot - AICROOM</h2>";

// 1. Verificar si XAMPP está funcionando
echo "<h3>1. Estado del Servidor</h3>";
if (function_exists('phpinfo')) {
    echo "<p style='color: green;'>✅ PHP está funcionando</p>";
} else {
    echo "<p style='color: red;'>❌ PHP no está funcionando</p>";
}

// 2. Verificar base de datos
echo "<h3>2. Base de Datos</h3>";
try {
    $pdo = getConnection();
    echo "<p style='color: green;'>✅ Conexión a base de datos exitosa</p>";
    
    // Verificar si hay usuarios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_usuario");
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Usuarios en la base de datos: $userCount</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error de base de datos: " . $e->getMessage() . "</p>";
}

// 3. Verificar archivos críticos
echo "<h3>3. Archivos Críticos</h3>";
$files = [
    'chatbot.html' => 'Página del chatbot',
    'custom_chatbot.js' => 'JavaScript del chatbot',
    'config.php' => 'Configuración',
    'style.css' => 'Estilos CSS'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "<p style='color: green;'>✅ $description ($file) - $size bytes</p>";
    } else {
        echo "<p style='color: red;'>❌ $description ($file) - NO EXISTE</p>";
    }
}

// 4. Verificar APIs
echo "<h3>4. APIs del Chatbot</h3>";
$apis = [
    'get_user_status.php' => 'Estado del usuario',
    'check_test_completion.php' => 'Test completado',
    'save_test_results.php' => 'Guardar resultados'
];

foreach ($apis as $api => $description) {
    if (file_exists($api)) {
        echo "<p style='color: green;'>✅ $description ($api)</p>";
    } else {
        echo "<p style='color: red;'>❌ $description ($api) - NO EXISTE</p>";
    }
}

// 5. Verificar sesión actual
echo "<h3>5. Estado de la Sesión</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✅ Usuario logueado: ID " . $_SESSION['user_id'] . "</p>";
    if (isset($_SESSION['user_email'])) {
        echo "<p>Email: " . $_SESSION['user_email'] . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ No hay usuario logueado</p>";
}

// 6. Probar API de estado de usuario
echo "<h3>6. Prueba de API de Estado</h3>";
try {
    // Simular una sesión para la prueba
    $_SESSION['user_id'] = 1;
    $_SESSION['user_email'] = 'test@aicroom.com';
    
    $response = file_get_contents('http://localhost/Aicroom/get_user_status.php');
    if ($response !== false) {
        $data = json_decode($response, true);
        if ($data !== null) {
            echo "<p style='color: green;'>✅ API de estado funciona</p>";
            echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color: red;'>❌ API no devuelve JSON válido</p>";
            echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>" . htmlspecialchars($response) . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ No se puede acceder a la API</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error probando API: " . $e->getMessage() . "</p>";
}

// Limpiar sesión de prueba
unset($_SESSION['user_id']);
unset($_SESSION['user_email']);

echo "<h3>7. Instrucciones de Solución</h3>";
echo "<ol>";
echo "<li><strong>Si PHP no funciona:</strong> Verifica que XAMPP esté iniciado</li>";
echo "<li><strong>Si la base de datos falla:</strong> Verifica que MySQL esté corriendo en XAMPP</li>";
echo "<li><strong>Si faltan archivos:</strong> Descarga nuevamente los archivos del proyecto</li>";
echo "<li><strong>Si las APIs fallan:</strong> Verifica que Apache esté corriendo en XAMPP</li>";
echo "<li><strong>Si no hay usuarios:</strong> Ejecuta <code>check_admin.php</code> para crear un admin</li>";
echo "</ol>";

echo "<h3>8. Enlaces de Prueba</h3>";
echo "<ul>";
echo "<li><a href='chatbot.html' target='_blank'>Chatbot</a></li>";
echo "<li><a href='login.html' target='_blank'>Login</a></li>";
echo "<li><a href='check_admin.php' target='_blank'>Crear Admin</a></li>";
echo "<li><a href='http://localhost/Aicroom/' target='_blank'>Página Principal</a></li>";
echo "</ul>";

echo "<h3>9. ¿Qué problema específico tienes?</h3>";
echo "<p>Por favor, describe exactamente qué no funciona:</p>";
echo "<ul>";
echo "<li>¿El chatbot no se carga?</li>";
echo "<li>¿No aparece el diseño embebido?</li>";
echo "<li>¿No funciona el login?</li>";
echo "<li>¿No se guardan los resultados?</li>";
echo "<li>¿Aparecen errores en la consola del navegador?</li>";
echo "</ul>";
?> 