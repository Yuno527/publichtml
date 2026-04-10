<?php
// Iniciar la sesión para mantener el estado del usuario entre páginas
session_start();
// Incluir el archivo de configuración con las credenciales de la base de datos
require_once 'config.php';

// Mostrar el título principal de la página
echo "<h2>Verificación del Usuario Admin</h2>";

// Bloque try-catch para manejar errores de la base de datos
try {
    // Obtener conexión a la base de datos usando PDO
    $pdo = getConnection();
    
    // Consultar todos los usuarios de la base de datos ordenados por ID
    $stmt = $pdo->query("SELECT Id_Usuario, nombre, correo, rol FROM tbl_usuario ORDER BY Id_Usuario");
    // Obtener todos los resultados como array asociativo
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mostrar sección de usuarios encontrados
    echo "<h3>Usuarios en la base de datos:</h3>";
    // Crear tabla HTML para mostrar los usuarios
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    // Encabezados de la tabla
    echo "<tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th></tr>";
    
    // Iterar sobre cada usuario y mostrarlo en la tabla
    foreach ($users as $user) {
        // Resaltar filas de usuarios admin con color verde
        $rowColor = $user['rol'] === 'admin' ? 'background-color: #d4edda;' : '';
        echo "<tr style='$rowColor'>";
        echo "<td>" . $user['Id_Usuario'] . "</td>";
        echo "<td>" . $user['nombre'] . "</td>";
        echo "<td>" . $user['correo'] . "</td>";
        echo "<td>" . $user['rol'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Contar cuántos usuarios admin existen en la base de datos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tbl_usuario WHERE rol = 'admin'");
    $stmt->execute();
    $adminCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Mostrar resumen estadístico
    echo "<h3>Resumen:</h3>";
    echo "<p>Total de usuarios: " . count($users) . "</p>";
    echo "<p>Usuarios admin: $adminCount</p>";
    
    // Si no hay usuarios admin, mostrar formulario para crear uno
    if ($adminCount == 0) {
        echo "<p style='color: red;'>❌ No hay usuarios admin. Necesitas crear uno.</p>";
        echo "<h3>Crear usuario admin:</h3>";
        // Formulario para crear nuevo usuario admin
        echo "<form method='post'>";
        echo "<p>Nombre: <input type='text' name='nombre' value='Breiner' required></p>";
        echo "<p>Correo: <input type='email' name='correo' value='breiner@admin.com' required></p>";
        echo "<p>Contraseña: <input type='password' name='password' value='admin123' required></p>";
        echo "<p><input type='submit' name='create_admin' value='Crear Admin'></p>";
        echo "</form>";
    } else {
        // Mensaje de confirmación si ya existen usuarios admin
        echo "<p style='color: green;'> Hay usuarios admin disponibles.</p>";
    }
    
    // Procesar el formulario cuando se envía para crear un admin
    if (isset($_POST['create_admin'])) {
        // Obtener datos del formulario
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $password = $_POST['password'];
        
        // Bloque try-catch para manejar errores en la inserción
        try {
            // Preparar e ejecutar consulta para insertar nuevo usuario admin
            $stmt = $pdo->prepare("INSERT INTO tbl_usuario (nombre, correo, contraseña, rol) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$nombre, $correo, $password]);
            
            // Mensaje de éxito
            echo "<p style='color: green;'> Usuario admin creado exitosamente!</p>";
            // Mostrar credenciales del nuevo usuario (en producción esto no sería seguro)
            echo "<p>Credenciales: $correo / $password</p>";
            
        } catch (Exception $e) {
            // Mostrar error si falla la creación del usuario
            echo "<p style='color: red;'> Error creando admin: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    // Manejar errores generales de conexión o consulta
    echo "<p style='color: red;'> Error: " . $e->getMessage() . "</p>";
}
?>