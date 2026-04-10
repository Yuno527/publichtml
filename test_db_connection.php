<?php
session_start();
require_once 'config.php';

echo "<h2>Test de Conexión a Base de Datos</h2>";

try {
    $pdo = getConnection();
    echo "<p style='color: green;'>✅ Conexión a la base de datos exitosa</p>";
    
    // Verificar si hay usuarios en la tabla
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_usuario");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total de usuarios en la base de datos: " . $result['total'] . "</p>";
    
    // Mostrar estructura de la tabla
    $stmt = $pdo->query("DESCRIBE tbl_usuario");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Estructura de la tabla tbl_usuario:</h3>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>" . $column['Field'] . " - " . $column['Type'] . "</li>";
    }
    echo "</ul>";
    
    // Verificar sesión
    echo "<h3>Estado de la sesión:</h3>";
    if (isset($_SESSION['user_id'])) {
        echo "<p>Usuario logueado: ID " . $_SESSION['user_id'] . "</p>";
        
        // Probar la consulta del usuario
        $stmt = $pdo->prepare("SELECT Id_Usuario, nombre, correo, rol FROM tbl_usuario WHERE Id_Usuario = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>Datos del usuario:</p>";
            echo "<ul>";
            echo "<li>ID: " . $user['Id_Usuario'] . "</li>";
            echo "<li>Nombre: " . $user['nombre'] . "</li>";
            echo "<li>Correo: " . $user['correo'] . "</li>";
            echo "<li>Rol: " . $user['rol'] . "</li>";
            echo "</ul>";
            
            // Probar JSON response
            $jsonResponse = [
                'logged_in' => true,
                'user_id' => $user['Id_Usuario'],
                'user_name' => $user['nombre'],
                'user_email' => $user['correo'],
                'user_role' => $user['rol']
            ];
            echo "<h3>Respuesta JSON:</h3>";
            echo "<pre>" . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p style='color: red;'>❌ Usuario no encontrado en la base de datos</p>";
        }
    } else {
        echo "<p>No hay usuario logueado</p>";
        
        // Probar JSON response para usuario no logueado
        $jsonResponse = [
            'logged_in' => false,
            'message' => 'No autenticado'
        ];
        echo "<h3>Respuesta JSON (no logueado):</h3>";
        echo "<pre>" . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "</pre>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    echo "<h3>Configuración actual:</h3>";
    echo "<ul>";
    echo "<li>Host: " . DB_HOST . "</li>";
    echo "<li>Database: " . DB_NAME . "</li>";
    echo "<li>Username: " . DB_USER . "</li>";
    echo "<li>Password: " . (empty(DB_PASS) ? 'vacío' : 'configurado') . "</li>";
    echo "</ul>";
}
?> 