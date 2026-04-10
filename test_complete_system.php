<?php
// Test completo del sistema
session_start();
require_once 'config.php';

echo "<h2>Test Completo del Sistema AICROOM</h2>";

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

// 3. Verificar usuarios
echo "<h3>3. Usuarios en el Sistema</h3>";
try {
    $stmt = $pdo->query("SELECT Id_Usuario, nombre, correo, rol FROM tbl_usuario ORDER BY Id_Usuario");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
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
        
        // Verificar si hay admin
        $adminUsers = array_filter($users, function($user) { return $user['rol'] === 'admin'; });
        if (count($adminUsers) > 0) {
            echo "<p style='color: green;'>✅ Hay " . count($adminUsers) . " usuario(s) admin</p>";
        } else {
            echo "<p style='color: red;'>❌ No hay usuarios admin</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ No hay usuarios en el sistema</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error verificando usuarios: " . $e->getMessage() . "</p>";
}

// 4. Verificar datos de prueba
echo "<h3>4. Datos de Prueba</h3>";
try {
    // Historiales
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_historial");
    $historialCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Historiales: $historialCount</p>";
    
    // Resultados
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_resultados");
    $resultadosCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Resultados: $resultadosCount</p>";
    
    // Respuestas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_respuestas");
    $respuestasCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Respuestas: $respuestasCount</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error verificando datos: " . $e->getMessage() . "</p>";
}

// 5. Probar APIs
echo "<h3>5. Prueba de APIs</h3>";

// Simular login de admin
$_SESSION['user_id'] = 1;

$apis = [
    'get_user_status.php' => 'Estado del usuario',
    'get_results.php' => 'Resultados',
    'check_test_completion.php' => 'Verificación de test'
];

foreach ($apis as $api => $description) {
    try {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $api;
        $response = file_get_contents($url);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if ($data !== null) {
                echo "<p style='color: green;'>✅ $description: API funciona correctamente</p>";
            } else {
                echo "<p style='color: red;'>❌ $description: Respuesta no es JSON válido</p>";
                echo "<pre>" . htmlspecialchars($response) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>❌ $description: No se pudo acceder a la API</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ $description: Error - " . $e->getMessage() . "</p>";
    }
}

// Limpiar sesión
unset($_SESSION['user_id']);

echo "<h3>6. Instrucciones de Uso</h3>";
echo "<ol>";
echo "<li><strong>Crear usuario admin:</strong> Ejecuta <code>check_admin.php</code> si no hay usuarios admin</li>";
echo "<li><strong>Login como admin:</strong> Usa las credenciales del usuario admin</li>";
echo "<li><strong>Acceder a resultados:</strong> La pestaña 'Resultados' aparecerá automáticamente</li>";
echo "<li><strong>Probar chatbot:</strong> Ve a <code>chatbot.html</code> y completa una evaluación</li>";
echo "</ol>";

echo "<h3>7. Enlaces de Prueba</h3>";
echo "<ul>";
echo "<li><a href='check_admin.php' target='_blank'>Verificar/Crear Admin</a></li>";
echo "<li><a href='login.html' target='_blank'>Página de Login</a></li>";
echo "<li><a href='resultados.html' target='_blank'>Panel de Resultados</a></li>";
echo "<li><a href='chatbot.html' target='_blank'>Evaluación de Habilidades</a></li>";
echo "</ul>";
?> 