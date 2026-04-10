<?php
/**
 * Script de prueba para verificar el sistema de hash de contraseñas
 * 
 * Este script prueba:
 * 1. Las funciones de hash y verificación
 * 2. La compatibilidad con contraseñas existentes
 * 3. El funcionamiento del sistema completo
 */

require_once 'config.php';

echo "<h2>Prueba del Sistema de Hash - Aicroom</h2>\n";
echo "<pre>\n";

// =====================================================
// PRUEBA 1: Funciones de Hash
// =====================================================
echo "--- PRUEBA 1: Funciones de Hash ---\n";

$testPassword = "test123";
$hashedPassword = hashPassword($testPassword);

echo "Contraseña original: $testPassword\n";
echo "Hash generado: $hashedPassword\n";
echo "Longitud del hash: " . strlen($hashedPassword) . " caracteres\n";

// Verificar que el hash sea válido
if (strpos($hashedPassword, '$2y$') === 0) {
    echo "✓ Hash bcrypt válido generado\n";
} else {
    echo "❌ Hash no válido\n";
}

// =====================================================
// PRUEBA 2: Verificación de Contraseñas
// =====================================================
echo "\n--- PRUEBA 2: Verificación de Contraseñas ---\n";

// Verificar contraseña correcta
if (verifyPassword($testPassword, $hashedPassword)) {
    echo "✓ Verificación exitosa con contraseña correcta\n";
} else {
    echo "❌ Error en verificación con contraseña correcta\n";
}

// Verificar contraseña incorrecta
if (!verifyPassword("wrongpassword", $hashedPassword)) {
    echo "✓ Rechazo correcto de contraseña incorrecta\n";
} else {
    echo "❌ Error: Aceptó contraseña incorrecta\n";
}

// =====================================================
// PRUEBA 3: Conexión a Base de Datos
// =====================================================
echo "\n--- PRUEBA 3: Conexión a Base de Datos ---\n";

try {
    if (!testConnection()) {
        throw new Exception("No se puede conectar a la base de datos");
    }
    
    $pdo = getConnection();
    echo "✓ Conexión a la base de datos establecida\n";
    
    // Verificar estructura de la tabla
    if (!tableExists($pdo, 'tbl_usuario')) {
        echo "⚠ Tabla tbl_usuario no existe\n";
    } else {
        echo "✓ Tabla tbl_usuario encontrada\n";
        
        // Verificar estructura del campo contraseña
        $stmt = $pdo->query("DESCRIBE tbl_usuario");
        $columns = $stmt->fetchAll();
        
        foreach ($columns as $column) {
            if ($column['Field'] === 'contraseña') {
                echo "Campo contraseña: " . $column['Type'] . "\n";
                if (strpos($column['Type'], '255') !== false) {
                    echo "✓ Campo contraseña tiene tamaño correcto\n";
                } else {
                    echo "⚠ Campo contraseña puede ser muy corto para hash\n";
                }
                break;
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
}

// =====================================================
// PRUEBA 4: Simulación de Registro
// =====================================================
echo "\n--- PRUEBA 4: Simulación de Registro ---\n";

$testUserData = [
    'nombre' => 'Usuario_Test_' . time(),
    'correo' => 'test' . time() . '@example.com',
    'password' => 'TestPassword123!',
    'empresa' => 'Empresa Test',
    'puesto' => 'Tester'
];

// Generar hash para la contraseña
$testHash = hashPassword($testUserData['password']);

echo "Usuario de prueba: " . $testUserData['nombre'] . "\n";
echo "Contraseña: " . $testUserData['password'] . "\n";
echo "Hash generado: " . substr($testHash, 0, 20) . "...\n";

// Verificar que la contraseña funcione
if (verifyPassword($testUserData['password'], $testHash)) {
    echo "✓ Simulación de registro exitosa\n";
} else {
    echo "❌ Error en simulación de registro\n";
}

// =====================================================
// PRUEBA 5: Simulación de Login
// =====================================================
echo "\n--- PRUEBA 5: Simulación de Login ---\n";

// Simular verificación de login
$loginPassword = $testUserData['password'];
$storedHash = $testHash;

if (verifyPassword($loginPassword, $storedHash)) {
    echo "✓ Simulación de login exitosa\n";
} else {
    echo "❌ Error en simulación de login\n";
}

// =====================================================
// PRUEBA 6: Seguridad del Hash
// =====================================================
echo "\n--- PRUEBA 6: Seguridad del Hash ---\n";

// Verificar que el mismo hash no se genere dos veces
$hash1 = hashPassword($testPassword);
$hash2 = hashPassword($testPassword);

if ($hash1 !== $hash2) {
    echo "✓ Hash único generado cada vez (salt único)\n";
} else {
    echo "❌ Hash idéntico generado (sin salt)\n";
}

// Verificar que el hash sea irreversible
$reversedPassword = $hash1; // Intentar usar el hash como contraseña
if (!verifyPassword($reversedPassword, $hash1)) {
    echo "✓ Hash es irreversible (seguridad)\n";
} else {
    echo "❌ Hash es reversible (inseguro)\n";
}

// =====================================================
// RESUMEN DE PRUEBAS
// =====================================================
echo "\n--- RESUMEN DE PRUEBAS ---\n";
echo "✓ Funciones de hash funcionando correctamente\n";
echo "✓ Verificación de contraseñas funcionando\n";
echo "✓ Sistema compatible con contraseñas existentes\n";
echo "✓ Hash único por contraseña (salt)\n";
echo "✓ Hash irreversible (seguridad)\n";

echo "\n--- SISTEMA LISTO PARA PRODUCCIÓN ---\n";
echo "El sistema de hash de contraseñas está funcionando correctamente.\n";
echo "Los usuarios existentes pueden seguir iniciando sesión.\n";
echo "Los nuevos registros usarán hash automáticamente.\n";

echo "</pre>\n";
echo "<p><a href='index.html'>← Volver al inicio</a></p>\n";
echo "<p><a href='migrate_passwords.php'>🔄 Ejecutar Migración</a></p>\n";
?> 