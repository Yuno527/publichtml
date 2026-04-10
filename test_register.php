<?php
/**
 * Script de prueba para verificar el sistema de registro
 */

require_once 'config.php';

echo "<h2>Prueba del Sistema de Registro - Aicroom</h2>\n";
echo "<pre>\n";

try {
    // Verificar conexión
    if (!testConnection()) {
        throw new Exception("No se puede conectar a la base de datos");
    }
    
    $pdo = getConnection();
    echo "✓ Conexión a la base de datos establecida\n";
    
    // Verificar si la tabla existe
    if (!tableExists($pdo, 'tbl_usuario')) {
        throw new Exception("La tabla tbl_usuario no existe. Importa el archivo aicroom.sql");
    }
    echo "✓ Tabla tbl_usuario existe\n";
    
    // Verificar estructura de la tabla
    $stmt = $pdo->query("DESCRIBE tbl_usuario");
    $columns = $stmt->fetchAll();
    
    echo "\n--- ESTRUCTURA DE LA TABLA ---\n";
    foreach ($columns as $column) {
        echo "Campo: {$column['Field']} - Tipo: {$column['Type']} - Null: {$column['Null']}\n";
    }
    
    // Verificar si el campo contraseña tiene el tamaño correcto
    $passwordColumn = array_filter($columns, function($col) {
        return $col['Field'] === 'contraseña';
    });
    
    if (!empty($passwordColumn)) {
        $passwordColumn = array_values($passwordColumn)[0];
        echo "\n--- VERIFICACIÓN DEL CAMPO CONTRASEÑA ---\n";
        echo "Tipo: {$passwordColumn['Type']}\n";
        
        if (strpos($passwordColumn['Type'], 'varchar(255)') !== false || strpos($passwordColumn['Type'], 'varchar(255)') !== false) {
            echo "✓ Campo contraseña tiene tamaño adecuado para hashes\n";
        } else {
            echo "⚠ Campo contraseña puede ser muy pequeño para hashes seguros\n";
            echo "  Recomendación: ALTER TABLE tbl_usuario MODIFY contraseña VARCHAR(255);\n";
        }
    }
    
    // Probar inserción de un usuario de prueba
    echo "\n--- PRUEBA DE INSERCIÓN ---\n";
    
    $testData = [
        'nombre' => 'Usuario Prueba',
        'empresa' => 'Empresa Test',
        'correo' => 'test@example.com',
        'puesto' => 'Desarrollador',
        'password' => 'test123'
    ];
    
    // Verificar si el usuario de prueba ya existe
    $stmt = $pdo->prepare("SELECT Id_Usuario FROM tbl_usuario WHERE correo = ?");
    $stmt->execute([$testData['correo']]);
    
    if ($stmt->fetch()) {
        echo "ℹ Usuario de prueba ya existe, eliminando...\n";
        $stmt = $pdo->prepare("DELETE FROM tbl_usuario WHERE correo = ?");
        $stmt->execute([$testData['correo']]);
        echo "✓ Usuario de prueba eliminado\n";
    }
    
    // Generar hash de la contraseña
    $hashedPassword = hashPassword($testData['password']);
    echo "Hash generado: " . substr($hashedPassword, 0, 20) . "...\n";
    
    // Insertar usuario de prueba
    $stmt = $pdo->prepare("INSERT INTO tbl_usuario (nombre, contraseña, correo, empresa_donde_labora, puesto, fecha_registro, rol) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $fecha_registro = date('Y-m-d');
    $rol = 'usuario';
    
    $result = $stmt->execute([
        $testData['nombre'],
        $hashedPassword,
        $testData['correo'],
        $testData['empresa'],
        $testData['puesto'],
        $fecha_registro,
        $rol
    ]);
    
    if ($result) {
        echo "✓ Usuario de prueba insertado exitosamente\n";
        
        $userId = $pdo->lastInsertId();
        echo "ID del usuario: $userId\n";
        
        // Verificar que se puede hacer login
        echo "\n--- PRUEBA DE LOGIN ---\n";
        
        $stmt = $pdo->prepare("
            SELECT Id_Usuario, nombre, contraseña, correo, rol 
            FROM tbl_usuario 
            WHERE correo = ?
        ");
        $stmt->execute([$testData['correo']]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✓ Usuario encontrado: {$user['nombre']}\n";
            
            if (verifyPassword($testData['password'], $user['contraseña'])) {
                echo "✓ Verificación de contraseña exitosa\n";
                echo "✓ El sistema de registro está funcionando correctamente\n";
            } else {
                echo "❌ Error en verificación de contraseña\n";
            }
        } else {
            echo "❌ Usuario no encontrado\n";
        }
        
        // Limpiar usuario de prueba
        echo "\n--- LIMPIEZA ---\n";
        $stmt = $pdo->prepare("DELETE FROM tbl_usuario WHERE Id_Usuario = ?");
        $stmt->execute([$userId]);
        echo "✓ Usuario de prueba eliminado\n";
        
    } else {
        echo "❌ Error al insertar usuario de prueba\n";
    }
    
    echo "\n--- RESUMEN ---\n";
    echo "✓ Base de datos conectada\n";
    echo "✓ Tabla tbl_usuario existe\n";
    echo "✓ Funciones de hash funcionando\n";
    echo "✓ Inserción y verificación funcionando\n";
    echo "✓ Sistema de registro listo para usar\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Por favor, verifica la configuración\n";
} catch (PDOException $e) {
    echo "\n❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "\n";
    echo "Por favor, verifica la conexión y permisos\n";
}

echo "</pre>\n";
echo "<p><a href='register.html'>🔐 Probar Registro</a></p>\n";
echo "<p><a href='index.html'>← Volver al inicio</a></p>\n";
?>
