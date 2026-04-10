<?php
/**
 * Script para analizar la base de datos y diagnosticar problemas
 * Este script:
 * 1. Verifica la estructura de la base de datos
 * 2. Analiza los usuarios existentes
 * 3. Verifica el estado de las contraseñas
 * 4. Identifica problemas específicos
 */

// Incluir archivo de configuración que contiene las credenciales de la base de datos
require_once 'config.php';

// Iniciar la salida HTML para mostrar los resultados del análisis
echo "<h2>Análisis de Base de Datos - Aicroom</h2>\n";
echo "<pre>\n";

// Bloque try-catch para manejar excepciones durante la ejecución
try {
    // Verificar conexión a la base de datos
    if (!testConnection()) {
        throw new Exception("No se puede conectar a la base de datos");
    }
    
    // Obtener conexión PDO a la base de datos
    $pdo = getConnection();
    echo "✓ Conexión a la base de datos establecida\n";
    
    // Paso 1: Verificar estructura de la tabla
    echo "\n--- PASO 1: Estructura de la Tabla ---\n";
    
    // Verificar si la tabla tbl_usuario existe en la base de datos
    if (!tableExists($pdo, 'tbl_usuario')) {
        throw new Exception("La tabla tbl_usuario no existe");
    }
    
    // Consultar la estructura de la tabla tbl_usuario
    $stmt = $pdo->query("DESCRIBE tbl_usuario");
    $columns = $stmt->fetchAll();
    
    // Mostrar la estructura de la tabla
    echo "Estructura de tbl_usuario:\n";
    foreach ($columns as $column) {
        echo "  - {$column['Field']}: {$column['Type']}\n";
    }
    
    // Paso 2: Analizar usuarios existentes
    echo "\n--- PASO 2: Usuarios Existentes ---\n";
    
    // Consultar todos los usuarios de la base de datos
    $stmt = $pdo->query("SELECT Id_Usuario, nombre, contraseña, correo, empresa_donde_labora, puesto, rol, fecha_registro FROM tbl_usuario");
    $users = $stmt->fetchAll();
    
    // Verificar si hay usuarios en la base de datos
    if (empty($users)) {
        echo "⚠ No hay usuarios en la base de datos\n";
    } else {
        echo "✓ Encontrados " . count($users) . " usuarios:\n\n";
        
        // Iterar sobre cada usuario para analizar sus datos
        foreach ($users as $user) {
            echo "Usuario ID: {$user['Id_Usuario']}\n";
            echo "  Nombre: {$user['nombre']}\n";
            echo "  Correo: {$user['correo']}\n";
            echo "  Empresa: " . ($user['empresa_donde_labora'] ?: 'NULL') . "\n";
            echo "  Puesto: " . ($user['puesto'] ?: 'NULL') . "\n";
            echo "  Rol: {$user['rol']}\n";
            echo "  Fecha: {$user['fecha_registro']}\n";
            
            // Analizar contraseña del usuario
            $password = $user['contraseña'];
            echo "  Contraseña:\n";
            echo "    - Valor: " . substr($password, 0, 30) . (strlen($password) > 30 ? '...' : '') . "\n";
            echo "    - Longitud: " . strlen($password) . " caracteres\n";
            
            // Verificar el formato de la contraseña (bcrypt, texto plano o desconocido)
            if (strpos($password, '$2y$') === 0) {
                echo "    - Estado: ✓ Hash bcrypt válido\n";
                
                // Probar contraseñas comunes para verificar si el hash funciona
                $testPasswords = ['breiner2025', 'password', '123456', 'admin'];
                $passwordWorks = false;
                
                foreach ($testPasswords as $testPass) {
                    if (verifyPassword($testPass, $password)) {
                        echo "    - Contraseña original: '$testPass' ✓\n";
                        $passwordWorks = true;
                        break;
                    }
                }
                
                // Si ninguna contraseña común funciona, indicar problema
                if (!$passwordWorks) {
                    echo "    - ⚠ Hash válido pero no se puede verificar con contraseñas comunes\n";
                }
                
            } elseif (strlen($password) <= 50) {
                echo "    - Estado: ⚠ Contraseña en texto plano (insegura)\n";
                
                // Intentar verificar contraseña en texto plano
                if (verifyPassword($password, $password)) {
                    echo "    - ⚠ Verificación fallida (esperado para texto plano)\n";
                }
                
            } else {
                echo "    - Estado: ❓ Formato desconocido\n";
            }
            
            echo "\n";
        }
    }
    
    // Paso 3: Probar funciones de hash
    echo "\n--- PASO 3: Prueba de Funciones de Hash ---\n";
    
    // Probar el sistema de hash con una contraseña de prueba
    $testPassword = "test123";
    $hashedPassword = hashPassword($testPassword);
    
    echo "Contraseña de prueba: $testPassword\n";
    echo "Hash generado: " . substr($hashedPassword, 0, 30) . "...\n";
    echo "Longitud del hash: " . strlen($hashedPassword) . " caracteres\n";
    
    // Verificar que el hash generado sea un formato bcrypt válido
    if (strpos($hashedPassword, '$2y$') === 0) {
        echo "✓ Hash bcrypt válido generado\n";
    } else {
        echo "❌ Hash no válido\n";
    }
    
    // Verificar que la función de verificación funcione correctamente
    if (verifyPassword($testPassword, $hashedPassword)) {
        echo "✓ Verificación exitosa\n";
    } else {
        echo "❌ Error en verificación\n";
    }
    
    // Paso 4: Crear usuario de prueba
    echo "\n--- PASO 4: Creando Usuario de Prueba ---\n";
    
    // Datos para el usuario de prueba
    $testUserData = [
        'nombre' => 'Usuario_Test_' . time(),
        'correo' => 'test' . time() . '@example.com',
        'empresa_donde_labora' => 'Empresa Test',
        'puesto' => 'Tester',
        'contraseña' => 'TestPassword123!',
        'fecha_registro' => date('Y-m-d'),
        'rol' => 'usuario'
    ];
    
    // Generar hash para la contraseña del usuario de prueba
    $testHash = hashPassword($testUserData['contraseña']);
    
    echo "Usuario de prueba: " . $testUserData['nombre'] . "\n";
    echo "Contraseña: " . $testUserData['password'] . "\n";
    echo "Hash generado: " . substr($testHash, 0, 30) . "...\n";
    
    // Insertar usuario de prueba en la base de datos
    $stmt = $pdo->prepare("INSERT INTO tbl_usuario (nombre, contraseña, correo, empresa_donde_labora, puesto, fecha_registro, rol) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        $testUserData['nombre'],
        $testHash,
        $testUserData['correo'],
        $testUserData['empresa_donde_labora'],
        $testUserData['puesto'],
        $testUserData['fecha_registro'],
        $testUserData['rol']
    ]);
    
    // Verificar si la inserción fue exitosa
    if ($result) {
        $userId = $pdo->lastInsertId();
        echo "✓ Usuario de prueba creado exitosamente (ID: $userId)\n";
        
        // Verificar que el login funcione con el usuario de prueba
        $stmt = $pdo->prepare("SELECT contraseña FROM tbl_usuario WHERE Id_Usuario = ?");
        $stmt->execute([$userId]);
        $storedUser = $stmt->fetch();
        
        if (verifyPassword($testUserData['contraseña'], $storedUser['contraseña'])) {
            echo "✓ Login del usuario de prueba funciona correctamente\n";
        } else {
            echo "❌ Login del usuario de prueba falla\n";
        }
        
        // Eliminar usuario de prueba para limpiar la base de datos
        $pdo->exec("DELETE FROM tbl_usuario WHERE Id_Usuario = $userId");
        echo "✓ Usuario de prueba eliminado\n";
        
    } else {
        echo "❌ Error al crear usuario de prueba\n";
    }
    
    // Paso 5: Diagnóstico del problema
    echo "\n--- PASO 5: Diagnóstico del Problema ---\n";
    
    // Identificar usuarios con problemas de contraseña
    if (!empty($users)) {
        $problemUsers = [];
        
        foreach ($users as $user) {
            $password = $user['contraseña'];
            
            // Verificar solo usuarios con hash bcrypt
            if (strpos($password, '$2y$') === 0) {
                // Es un hash, verificar si funciona
                $testPasswords = ['breiner2025', 'password', '123456', 'admin'];
                $works = false;
                
                foreach ($testPasswords as $testPass) {
                    if (verifyPassword($testPass, $password)) {
                        $works = true;
                        break;
                    }
                }
                
                // Si el hash no funciona con ninguna contraseña común, agregar a problemas
                if (!$works) {
                    $problemUsers[] = $user['nombre'];
                }
            }
        }
        
        // Mostrar usuarios con problemas y sugerir solución
        if (!empty($problemUsers)) {
            echo "⚠ Usuarios con problemas de contraseña:\n";
            foreach ($problemUsers as $problemUser) {
                echo "  - $problemUser: Hash válido pero no se puede verificar\n";
            }
            echo "\n💡 Solución: Ejecutar fix_user_password.php para corregir\n";
        } else {
            echo "✓ Todos los usuarios tienen contraseñas funcionando correctamente\n";
        }
    }
    
    // Mensaje final de completación del análisis
    echo "\n--- ANÁLISIS COMPLETADO ---\n";
    echo "La base de datos ha sido analizada completamente.\n";
    echo "Revisa los resultados arriba para identificar problemas.\n";
    
} catch (Exception $e) {
    // Manejar excepciones generales
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Por favor, verifica la configuración\n";
} catch (PDOException $e) {
    // Manejar excepciones específicas de PDO (base de datos)
    echo "\n❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "\n";
    echo "Por favor, verifica la conexión y permisos\n";
}

// Cerrar el bloque pre y agregar enlaces de navegación
echo "</pre>\n";
echo "<p><a href='index.html'>← Volver al inicio</a></p>\n";
echo "<p><a href='fix_user_password.php'>🔧 Corregir Contraseñas</a></p>\n";
echo "<p><a href='test_hash_system.php'>🧪 Probar Sistema Hash</a></p>\n";
?>