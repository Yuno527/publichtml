<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Probar conexión básica
    if (!testConnection()) {
        echo json_encode([
            'success' => false,
            'message' => 'No se puede conectar a la base de datos. Verifica las credenciales en config.php',
            'data' => [
                'host' => DB_HOST,
                'database' => DB_NAME,
                'user' => DB_USER
            ]
        ]);
        exit;
    }
    
    $pdo = getConnection();
    
    // Verificar si la tabla existe
    if (!tableExists($pdo, 'tbl_usuario')) {
        echo json_encode([
            'success' => false,
            'message' => 'La tabla tbl_usuario no existe. Importa el archivo aicroom.sql',
            'data' => null
        ]);
        exit;
    }
    
    // Verificar estructura básica de la tabla
    $stmt = $pdo->query("DESCRIBE tbl_usuario");
    $columns = $stmt->fetchAll();
    
    if (count($columns) < 8) {
        echo json_encode([
            'success' => false,
            'message' => 'La tabla tbl_usuario no tiene todas las columnas necesarias',
            'data' => [
                'columns_found' => count($columns),
                'columns_expected' => 8
            ]
        ]);
        exit;
    }
    
    // Verificar columnas específicas
    $columnNames = array_column($columns, 'Field');
    $requiredColumns = ['Id_Usuario', 'nombre', 'contraseña', 'correo', 'direccion', 'edad', 'rol', 'fecha_registro'];
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    if (!empty($missingColumns)) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan columnas en la tabla: ' . implode(', ', $missingColumns),
            'data' => [
                'missing_columns' => array_values($missingColumns),
                'existing_columns' => $columnNames
            ]
        ]);
        exit;
    }
    
    // Probar inserción de prueba
    $testData = [
        'nombre' => 'Test User',
        'contraseña' => 'test123',
        'correo' => 'test@test.com',
        'direccion' => 'Test Address',
        'edad' => 25,
        'rol' => 'usuario',
        'fecha_registro' => date('Y-m-d')
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO tbl_usuario (nombre, contraseña, correo, direccion, edad, rol, fecha_registro) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $testData['nombre'],
        $testData['contraseña'],
        $testData['correo'],
        $testData['direccion'],
        $testData['edad'],
        $testData['rol'],
        $testData['fecha_registro']
    ]);
    
    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al insertar datos de prueba',
            'data' => null
        ]);
        exit;
    }
    
    $testUserId = $pdo->lastInsertId();
    
    // Eliminar el usuario de prueba
    $stmt = $pdo->prepare("DELETE FROM tbl_usuario WHERE Id_Usuario = ?");
    $stmt->execute([$testUserId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Conexión a la base de datos exitosa',
        'data' => [
            'database' => DB_NAME,
            'host' => DB_HOST,
            'table_exists' => true,
            'columns_ok' => true,
            'insert_test' => 'passed',
            'columns_found' => count($columns)
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos: ' . $e->getMessage(),
        'data' => [
            'error_code' => $e->getCode(),
            'database' => DB_NAME,
            'host' => DB_HOST
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error inesperado: ' . $e->getMessage(),
        'data' => null
    ]);
}
?> 