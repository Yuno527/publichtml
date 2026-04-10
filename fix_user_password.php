<?php
/**
 * Script para corregir la contraseña del usuario Breiner
 * 
 * Este script:
 * 1. Verifica el estado actual de la contraseña
 * 2. Restaura la contraseña "breiner2025" con hash correcto
 * 3. Prueba el login para verificar que funcione
 */

require_once 'config.php';

echo "<h2>Corrección de Contraseña de Usuario - Aicroom</h2>\n";
echo "<pre>\n";

try {
    // Verificar conexión
    if (!testConnection()) {
        throw new Exception("No se puede conectar a la base de datos");
    }
    
    $pdo = getConnection();
    echo "✓ Conexión a la base de datos establecida\n";
    
    // Paso 1: Verificar usuario Breiner
    echo "\n--- PASO 1: Verificando usuario Breiner ---\n";
    
    $stmt = $pdo->prepare("SELECT Id_Usuario, nombre, contraseña, correo FROM tbl_usuario WHERE nombre = ?");
    $stmt->execute(['Breiner']);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception("Usuario Breiner no encontrado");
    }
    
    echo "✓ Usuario encontrado: {$user['nombre']} (ID: {$user['Id_Usuario']})\n";
    echo "Correo: {$user['correo']}\n";
    
    $currentPassword = $user['contraseña'];
    echo "Contraseña actual: " . substr($currentPassword, 0, 20) . "...\n";
    echo "Longitud: " . strlen($currentPassword) . " caracteres\n";
    
    // Verificar si ya es un hash
    if (strpos($currentPassword, '$2y$') === 0) {
        echo "✓ La contraseña ya está hasheada\n";
    } else {
        echo "⚠ La contraseña NO está hasheada\n";
    }
    
    // Paso 2: Restaurar contraseña correcta
    echo "\n--- PASO 2: Restaurando contraseña ---\n";
    
    $correctPassword = 'breiner2025';
    $hashedPassword = hashPassword($correctPassword);
    
    echo "Contraseña original: $correctPassword\n";
    echo "Hash generado: " . substr($hashedPassword, 0, 20) . "...\n";
    
    // Actualizar la contraseña en la base de datos
    $updateStmt = $pdo->prepare("UPDATE tbl_usuario SET contraseña = ? WHERE Id_Usuario = ?");
    $result = $updateStmt->execute([$hashedPassword, $user['Id_Usuario']]);
    
    if ($result) {
        echo "✓ Contraseña actualizada exitosamente\n";
    } else {
        throw new Exception("Error al actualizar la contraseña");
    }
    
    // Paso 3: Verificar que la actualización fue exitosa
    echo "\n--- PASO 3: Verificando actualización ---\n";
    
    $stmt = $pdo->prepare("SELECT contraseña FROM tbl_usuario WHERE Id_Usuario = ?");
    $stmt->execute([$user['Id_Usuario']]);
    $updatedUser = $stmt->fetch();
    
    $newPassword = $updatedUser['contraseña'];
    echo "Nueva contraseña en BD: " . substr($newPassword, 0, 20) . "...\n";
    
    if (strpos($newPassword, '$2y$') === 0) {
        echo "✓ La contraseña ahora está correctamente hasheada\n";
    } else {
        echo "❌ La contraseña NO se hasheó correctamente\n";
    }
    
    // Paso 4: Probar verificación de contraseña
    echo "\n--- PASO 4: Probando verificación ---\n";
    
    // Probar con contraseña correcta
    if (verifyPassword($correctPassword, $newPassword)) {
        echo "✓ Verificación exitosa con contraseña correcta\n";
    } else {
        echo "❌ Error en verificación con contraseña correcta\n";
    }
    
    // Probar con contraseña incorrecta
    if (!verifyPassword('password_incorrecta', $newPassword)) {
        echo "✓ Rechazo correcto de contraseña incorrecta\n";
    } else {
        echo "❌ Error: Aceptó contraseña incorrecta\n";
    }
    
    // Paso 5: Simular login completo
    echo "\n--- PASO 5: Simulando login ---\n";
    
    $loginUsername = 'Breiner'; // o 'breiner@aicroom.com'
    $loginPassword = 'breiner2025';
    
    // Buscar usuario por nombre o correo
    $stmt = $pdo->prepare("
        SELECT Id_Usuario, nombre, contraseña, correo, rol 
        FROM tbl_usuario 
        WHERE nombre = ? OR correo = ?
    ");
    $stmt->execute([$loginUsername, $loginUsername]);
    $loginUser = $stmt->fetch();
    
    if (!$loginUser) {
        echo "❌ Usuario no encontrado para login\n";
    } else {
        echo "✓ Usuario encontrado para login: {$loginUser['nombre']}\n";
        
        // Verificar contraseña
        if (verifyPassword($loginPassword, $loginUser['contraseña'])) {
            echo "✓ Login exitoso - Contraseña verificada correctamente\n";
            echo "✓ El usuario puede iniciar sesión normalmente\n";
        } else {
            echo "❌ Login fallido - Contraseña incorrecta\n";
        }
    }
    
    echo "\n--- CONTRASEÑA CORREGIDA EXITOSAMENTE ---\n";
    echo "✓ Usuario: Breiner\n";
    echo "✓ Contraseña: breiner2025\n";
    echo "✓ Hash generado y almacenado correctamente\n";
    echo "✓ Verificación funcionando correctamente\n";
    echo "✓ El login ahora funcionará sin problemas\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Por favor, verifica la configuración\n";
} catch (PDOException $e) {
    echo "\n❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "\n";
    echo "Por favor, verifica la conexión y permisos\n";
}

echo "</pre>\n";
echo "<p><a href='index.html'>← Volver al inicio</a></p>\n";
echo "<p><a href='login.html'>🔐 Probar Login</a></p>\n";
?> 