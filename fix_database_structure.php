<?php
/**
 * Script para corregir la estructura de la base de datos
 * 
 * Este script:
 * 1. Verifica la estructura actual de la tabla tbl_usuario
 * 2. Agrega la columna empresa_donde_labora si no existe
 * 3. Actualiza el campo contraseña a VARCHAR(255) para hash
 */

require_once 'config.php';

echo "<h2>Corrección de Estructura de Base de Datos - Aicroom</h2>\n";
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
    
    // Paso 1: Verificar estructura actual
    echo "\n--- PASO 1: Verificando estructura actual ---\n";
    
    $stmt = $pdo->query("DESCRIBE tbl_usuario");
    $columns = $stmt->fetchAll();
    
    $existingColumns = [];
    foreach ($columns as $column) {
        $existingColumns[$column['Field']] = $column['Type'];
        echo "Campo: {$column['Field']} - Tipo: {$column['Type']}\n";
    }
    
    // Paso 2: Agregar columna empresa_donde_labora si no existe
    echo "\n--- PASO 2: Verificando columna empresa_donde_labora ---\n";
    
    if (!array_key_exists('empresa_donde_labora', $existingColumns)) {
        echo "⚠ Columna empresa_donde_labora no existe, agregándola...\n";
        
        try {
            $pdo->exec("ALTER TABLE tbl_usuario ADD COLUMN empresa_donde_labora VARCHAR(100) DEFAULT NULL AFTER correo");
            echo "✓ Columna empresa_donde_labora agregada exitosamente\n";
        } catch (PDOException $e) {
            echo "❌ Error al agregar columna: " . $e->getMessage() . "\n";
            throw $e;
        }
    } else {
        echo "✓ Columna empresa_donde_labora ya existe\n";
    }
    
    // Paso 3: Verificar y actualizar campo contraseña
    echo "\n--- PASO 3: Verificando campo contraseña ---\n";
    
    if (array_key_exists('contraseña', $existingColumns)) {
        $passwordType = $existingColumns['contraseña'];
        echo "Campo contraseña actual: $passwordType\n";
        
        if (strpos($passwordType, '255') === false) {
            echo "⚠ Campo contraseña es muy corto, actualizándolo...\n";
            
            try {
                $pdo->exec("ALTER TABLE tbl_usuario MODIFY COLUMN contraseña VARCHAR(255)");
                echo "✓ Campo contraseña actualizado a VARCHAR(255)\n";
            } catch (PDOException $e) {
                echo "❌ Error al actualizar campo contraseña: " . $e->getMessage() . "\n";
                throw $e;
            }
        } else {
            echo "✓ Campo contraseña ya tiene el tamaño correcto\n";
        }
    } else {
        echo "❌ Campo contraseña no existe\n";
        throw new Exception("Campo contraseña no encontrado en la tabla");
    }
    
    // Paso 4: Verificar estructura final
    echo "\n--- PASO 4: Verificando estructura final ---\n";
    
    $stmt = $pdo->query("DESCRIBE tbl_usuario");
    $finalColumns = $stmt->fetchAll();
    
    foreach ($finalColumns as $column) {
        echo "Campo: {$column['Field']} - Tipo: {$column['Type']}\n";
    }
    
    // Paso 5: Probar inserción
    echo "\n--- PASO 5: Probando inserción de datos ---\n";
    
    try {
        // Crear un usuario de prueba temporal
        $testData = [
            'nombre' => 'Usuario_Test_' . time(),
            'correo' => 'test' . time() . '@example.com',
            'empresa_donde_labora' => 'Empresa Test',
            'puesto' => 'Tester',
            'contraseña' => 'test123',
            'fecha_registro' => date('Y-m-d'),
            'rol' => 'usuario'
        ];
        
        $stmt = $pdo->prepare("INSERT INTO tbl_usuario (nombre, contraseña, correo, empresa_donde_labora, puesto, fecha_registro, rol) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $testData['nombre'],
            $testData['contraseña'],
            $testData['correo'],
            $testData['empresa_donde_labora'],
            $testData['puesto'],
            $testData['fecha_registro'],
            $testData['rol']
        ]);
        
        if ($result) {
            $userId = $pdo->lastInsertId();
            echo "✓ Inserción de prueba exitosa (ID: $userId)\n";
            
            // Eliminar usuario de prueba
            $pdo->exec("DELETE FROM tbl_usuario WHERE Id_Usuario = $userId");
            echo "✓ Usuario de prueba eliminado\n";
        } else {
            echo "❌ Error en inserción de prueba\n";
        }
        
    } catch (PDOException $e) {
        echo "❌ Error en prueba de inserción: " . $e->getMessage() . "\n";
        throw $e;
    }
    
    echo "\n--- ESTRUCTURA CORREGIDA EXITOSAMENTE ---\n";
    echo "✓ La tabla tbl_usuario ahora tiene la estructura correcta\n";
    echo "✓ La columna empresa_donde_labora está disponible\n";
    echo "✓ El campo contraseña soporta hash seguros\n";
    echo "✓ El sistema de registro funcionará correctamente\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Por favor, verifica la configuración de la base de datos\n";
} catch (PDOException $e) {
    echo "\n❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "\n";
    echo "Por favor, verifica la conexión y permisos\n";
}

echo "</pre>\n";
echo "<p><a href='index.html'>← Volver al inicio</a></p>\n";
echo "<p><a href='register.html'>🔄 Probar Registro</a></p>\n";
?> 