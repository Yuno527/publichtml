<?php
// Incluir el archivo de configuración con las credenciales de la base de datos
require_once 'config.php';

// Script para crear un usuario administrador
// IMPORTANTE: Ejecutar este script una sola vez y luego eliminarlo por seguridad

// Bloque try-catch para manejar errores de base de datos
try {
    // Obtener conexión a la base de datos
    $pdo = getConnection();
    
    // Verificar si ya existe un usuario administrador en la base de datos
    $stmt = $pdo->prepare("SELECT Id_Usuario FROM tbl_usuario WHERE rol = 'admin'");
    $stmt->execute();
    
    // Si se encuentra un administrador existente, mostrar mensaje y terminar ejecución
    if ($stmt->fetch()) {
        echo "Ya existe un usuario administrador en la base de datos.<br>";
        echo "Si necesitas crear otro administrador, modifica este script.<br>";
        exit;
    }
    
    // Preparar consulta SQL para insertar el nuevo usuario
    $stmt = $pdo->prepare("
        INSERT INTO tbl_usuario (nombre, contraseña, correo, direccion, edad, rol, fecha_registro) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    // Ejecutar la inserción con los datos del nuevo usuario
    $result = $stmt->execute([
        $adminData['nombre'],
        $adminData['contraseña'],
        $adminData['correo'],
        $adminData['direccion'],
        $adminData['edad'],
        $adminData['rol'],
        $adminData['fecha_registro']
    ]);
    
    // Verificar si la inserción fue exitosa
    if ($result) {
        // Obtener el ID del administrador recién creado
        $adminId = $pdo->lastInsertId();
        // Mostrar mensaje de éxito con los detalles del administrador
        echo "<h2>Usuario Administrador Creado Exitosamente</h2>";
        echo "<p><strong>ID:</strong> $adminId</p>";
        echo "<p><strong>Nombre:</strong> {$adminData['nombre']}</p>";
        echo "<p><strong>Email:</strong> {$adminData['correo']}</p>";
        echo "<p><strong>Contraseña:</strong> {$adminData['contraseña']}</p>";
        echo "<p><strong>Rol:</strong> {$adminData['rol']}</p>";
        echo "<br>";
        // Mostrar advertencias de seguridad importantes
        echo "<p><strong> IMPORTANTE:</strong></p>";
        echo "<ul>";
        echo "<li>Guarda estas credenciales en un lugar seguro</li>";
        echo "<li>Cambia la contraseña después del primer login</li>";
        echo "<li>Elimina este archivo (create_admin.php) por seguridad</li>";
        echo "</ul>";
        echo "<br>";
        // Enlace para ir a la página de login
        echo "<p><a href='login.html'>Ir al Login</a></p>";
    } else {
        // Mostrar mensaje de error si la inserción falló
        echo "<h2>Error al crear el administrador</h2>";
        echo "<p>Verifica la conexión a la base de datos y los permisos.</p>";
    }
    
} catch (Exception $e) {
    // Manejar excepciones y mostrar mensajes de error detallados
    echo "<h2> Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Verifica que:</p>";
    echo "<ul>";
    echo "<li>La base de datos esté creada</li>";
    echo "<li>Las tablas existan (ejecuta aicroom.sql)</li>";
    echo "<li>Las credenciales en config.php sean correctas</li>";
    echo "</ul>";
}
?>