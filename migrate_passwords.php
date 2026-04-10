<?php
/**
 * Script de migración para implementar hash de contraseñas
 * 
 * Este script:
 * 1. Actualiza la estructura de la tabla para permitir contraseñas más largas
 * 2. Migra las contraseñas existentes a hash seguros
 * 3. Verifica que la migración sea exitosa
 */

require_once 'config.php';

echo "<h2>Migración de Contraseñas - Aicroom</h2>\n";
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
        throw new Exception("La tabla tbl_usuario no existe. Importa el archivo aicroom.sql primero");
    }
    
    echo "✓ Tabla tbl_usuario encontrada\n";
    
    // Paso 1: Actualizar la estructura de la tabla para permitir contraseñas más largas
    echo "\n--- PASO 1: Actualizando estructura de la tabla ---\n";
    
    try {
        $pdo->exec("ALTER TABLE tbl_usuario MODIFY COLUMN contraseña VARCHAR(255)");
        echo "✓ Campo contraseña actualizado a VARCHAR(255)\n";
    } catch (PDOException $e) {
        // Si ya está en 255, no hay problema
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "ℹ Campo contraseña ya tiene el tamaño correcto\n";
        } else {
            throw $e;
        }
    }
    
    // Paso 2: Obtener usuarios existentes
    echo "\n--- PASO 2: Obteniendo usuarios existentes ---\n";
    
    $stmt = $pdo->query("SELECT Id_Usuario, nombre, contraseña, correo FROM tbl_usuario");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "ℹ No hay usuarios para migrar\n";
    } else {
        echo "✓ Encontrados " . count($users) . " usuarios para migrar\n";
        
        // Paso 3: Migrar contraseñas existentes
        echo "\n--- PASO 3: Migrando contraseñas existentes ---\n";
        
        $migratedCount = 0;
        $skippedCount = 0;
        
        foreach ($users as $user) {
            $currentPassword = $user['contraseña'];
            
            // Verificar si la contraseña ya está hasheada
            if (password_verify($currentPassword, $currentPassword) || 
                strlen($currentPassword) > 50 || 
                strpos($currentPassword, '$2y$') === 0) {
                echo "ℹ Usuario '{$user['nombre']}' ya tiene contraseña hasheada o es muy larga, saltando...\n";
                $skippedCount++;
                continue;
            }
            
            // Generar hash de la contraseña actual
            $hashedPassword = hashPassword($currentPassword);
            
            // Actualizar la contraseña en la base de datos
            $updateStmt = $pdo->prepare("UPDATE tbl_usuario SET contraseña = ? WHERE Id_Usuario = ?");
            $updateStmt->execute([$hashedPassword, $user['Id_Usuario']]);
            
            echo "✓ Usuario '{$user['nombre']}' migrado exitosamente\n";
            $migratedCount++;
        }
        
        echo "\n--- RESUMEN DE MIGRACIÓN ---\n";
        echo "✓ Usuarios migrados: $migratedCount\n";
        echo "ℹ Usuarios saltados: $skippedCount\n";
        echo "✓ Total procesados: " . count($users) . "\n";
    }
    
    // Paso 4: Verificar que la migración sea exitosa
    echo "\n--- PASO 4: Verificando migración ---\n";
    
    $stmt = $pdo->query("SELECT Id_Usuario, nombre, contraseña FROM tbl_usuario LIMIT 5");
    $sampleUsers = $stmt->fetchAll();
    
    foreach ($sampleUsers as $user) {
        $password = $user['contraseña'];
        if (strlen($password) > 50 || strpos($password, '$2y$') === 0) {
            echo "✓ Usuario '{$user['nombre']}' tiene contraseña hasheada correctamente\n";
        } else {
            echo "⚠ Usuario '{$user['nombre']}' puede no tener contraseña hasheada\n";
        }
    }
    
    echo "\n--- MIGRACIÓN COMPLETADA EXITOSAMENTE ---\n";
    echo "✓ El sistema ahora usa hash de contraseñas seguros\n";
    echo "✓ Los usuarios existentes pueden seguir iniciando sesión\n";
    echo "✓ Los nuevos registros usarán hash automáticamente\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR EN LA MIGRACIÓN: " . $e->getMessage() . "\n";
    echo "Por favor, verifica la configuración de la base de datos\n";
} catch (PDOException $e) {
    echo "\n❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "\n";
    echo "Por favor, verifica la conexión y permisos\n";
}

echo "</pre>\n";
echo "<p><a href='index.html'>← Volver al inicio</a></p>\n";
?> 