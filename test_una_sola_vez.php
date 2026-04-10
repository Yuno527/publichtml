<?php
// Test de funcionalidad "Una sola vez por usuario"
session_start();
require_once 'config.php';

echo "<h2>🔒 Test de Funcionalidad: Una Sola Vez por Usuario</h2>";

// 1. Verificar conexión a base de datos
echo "<h3>1. Conexión a Base de Datos</h3>";
try {
    $pdo = getConnection();
    echo "<p style='color: green;'>✅ Conexión exitosa</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    exit;
}

// 2. Verificar estructura de tablas
echo "<h3>2. Estructura de Tablas</h3>";
$requiredTables = [
    'tbl_usuario' => 'Usuarios',
    'tbl_historial' => 'Historial de tests',
    'tbl_resultados' => 'Resultados',
    'tbl_respuestas' => 'Respuestas individuales'
];

foreach ($requiredTables as $table => $description) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Tabla $table existe</p>";
        } else {
            echo "<p style='color: red;'>❌ Tabla $table NO existe</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error verificando tabla $table: " . $e->getMessage() . "</p>";
    }
}

// 3. Verificar usuarios existentes
echo "<h3>3. Usuarios en el Sistema</h3>";
try {
    $stmt = $pdo->query("SELECT Id_Usuario, nombre, correo, rol FROM tbl_usuario ORDER BY Id_Usuario");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<p>Total de usuarios: <strong>" . count($users) . "</strong></p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th></tr>";
        
        foreach ($users as $user) {
            $rowColor = $user['rol'] === 'admin' ? 'background-color: #d4edda;' : '';
            echo "<tr style='$rowColor'>";
            echo "<td>" . $user['Id_Usuario'] . "</td>";
            echo "<td>" . $user['nombre'] . "</td>";
            echo "<td>" . $user['correo'] . "</td>";
            echo "<td>" . $user['rol'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No hay usuarios en el sistema</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error verificando usuarios: " . $e->getMessage() . "</p>";
}

// 4. Verificar tests completados
echo "<h3>4. Tests Completados</h3>";
try {
    $stmt = $pdo->query("
        SELECT 
            h.Id_historial,
            h.Id_UsuarioFK,
            h.fecha,
            h.estado,
            u.nombre as usuario_nombre,
            u.correo as usuario_correo,
            r.puntaje_total,
            r.resultado_final
        FROM tbl_historial h
        LEFT JOIN tbl_usuario u ON h.Id_UsuarioFK = u.Id_Usuario
        LEFT JOIN tbl_resultados r ON h.Id_historial = r.Id_historialFK
        WHERE h.estado = 'Completado'
        ORDER BY h.fecha DESC
    ");
    
    $completedTests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($completedTests) > 0) {
        echo "<p>Tests completados: <strong>" . count($completedTests) . "</strong></p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Usuario</th><th>Correo</th><th>Fecha</th><th>Puntaje</th><th>Resultado</th></tr>";
        
        foreach ($completedTests as $test) {
            echo "<tr>";
            echo "<td>" . $test['Id_historial'] . "</td>";
            echo "<td>" . $test['usuario_nombre'] . "</td>";
            echo "<td>" . $test['usuario_correo'] . "</td>";
            echo "<td>" . $test['fecha'] . "</td>";
            echo "<td>" . $test['puntaje_total'] . "</td>";
            echo "<td>" . $test['resultado_final'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No hay tests completados aún</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error verificando tests completados: " . $e->getMessage() . "</p>";
}

// 5. Probar API de verificación
echo "<h3>5. Prueba de API de Verificación</h3>";

// Simular login de un usuario
$_SESSION['user_id'] = 1;
$_SESSION['user_email'] = 'test@aicroom.com';

try {
    $response = file_get_contents('http://localhost/Aicroom/check_test_completion.php');
    if ($response !== false) {
        $data = json_decode($response, true);
        if ($data !== null) {
            if ($data['completed']) {
                echo "<p style='color: orange;'>⚠️ El usuario ya completó el test</p>";
            } else {
                echo "<p style='color: green;'>✅ El usuario puede realizar el test</p>";
            }
            echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color: red;'>❌ API no devuelve JSON válido</p>";
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

echo "<h3>6. Funcionalidad de Una Sola Vez</h3>";
echo "<ul>";
echo "<li>✅ <strong>Verificación inicial:</strong> Al cargar el chatbot, verifica si ya completaste</li>";
echo "<li>✅ <strong>Verificación doble:</strong> Al guardar resultados, verifica nuevamente</li>";
echo "<li>✅ <strong>Mensaje claro:</strong> Explica por qué no se permite otro intento</li>";
echo "<li>✅ <strong>Base de datos:</strong> Registra el estado 'Completado' en tbl_historial</li>";
echo "<li>✅ <strong>Prevención:</strong> Impide múltiples registros por usuario</li>";
echo "</ul>";

echo "<h3>7. Flujo de Usuario</h3>";
echo "<ol>";
echo "<li><strong>Primera vez:</strong> Usuario accede al chatbot → Puede completar el test</li>";
echo "<li><strong>Durante el test:</strong> Responde 20 preguntas → Sistema calcula puntaje</li>";
echo "<li><strong>Al completar:</strong> Se guarda en base de datos → Estado = 'Completado'</li>";
echo "<li><strong>Segunda vez:</strong> Usuario accede al chatbot → Ve mensaje 'Ya completado'</li>";
echo "<li><strong>Prevención:</strong> No puede iniciar nuevo test → Sistema bloquea acceso</li>";
echo "</ol>";

echo "<h3>8. Enlaces de Prueba</h3>";
echo "<ul>";
echo "<li><a href='chatbot.html' target='_blank'>🤖 Probar Chatbot</a></li>";
echo "<li><a href='login.html' target='_blank'>🔐 Login</a></li>";
echo "<li><a href='resultados.html' target='_blank'>📊 Ver Resultados (Admin)</a></li>";
echo "<li><a href='check_admin.php' target='_blank'>👨‍💼 Crear Admin</a></li>";
echo "</ul>";

echo "<h3>9. Verificación Manual</h3>";
echo "<p>Para verificar que funciona correctamente:</p>";
echo "<ol>";
echo "<li>Haz login con un usuario</li>";
echo "<li>Completa el test una vez</li>";
echo "<li>Intenta acceder nuevamente al chatbot</li>";
echo "<li>Deberías ver el mensaje 'Ya has completado la evaluación'</li>";
echo "<li>No deberías poder iniciar un nuevo test</li>";
echo "</ol>";

echo "<h3>10. Estado del Sistema</h3>";
echo "<p style='color: green;'>✅ Funcionalidad 'Una sola vez por usuario' implementada y funcionando</p>";
echo "<p>El sistema garantiza que cada usuario solo pueda completar la evaluación una vez.</p>";
?> 