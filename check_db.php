<?php
// Incluye el archivo de configuración donde están definidas las constantes de conexión y funciones auxiliares
require_once 'config.php';

// Imprime el título principal en el HTML
echo "<h2>Verificación de Base de Datos - Aicroom</h2>";

// Sección para mostrar la configuración actual tomada de config.php
echo "<h3>1. Configuración actual:</h3>";
echo "<ul>";
// Muestra el host definido
echo "<li><strong>Host:</strong> " . DB_HOST . "</li>";
// Muestra el nombre de la base de datos
echo "<li><strong>Base de datos:</strong> " . DB_NAME . "</li>";
// Muestra el usuario configurado
echo "<li><strong>Usuario:</strong> " . DB_USER . "</li>";
// Verifica si la contraseña está vacía o configurada (sin mostrarla directamente por seguridad)
echo "<li><strong>Contraseña:</strong> " . (empty(DB_PASS) ? 'Vacía' : 'Configurada') . "</li>";
echo "</ul>";

// Sección para probar la conexión con la base de datos
echo "<h3>2. Prueba de conexión:</h3>";
try {
    // Intenta obtener una conexión PDO mediante la función getConnection() de config.php
    $pdo = getConnection();
    echo "<p style='color: green;'> Conexión exitosa a la base de datos</p>";
    
    // Verifica si la tabla tbl_usuario existe en la base de datos
    if (tableExists($pdo, 'tbl_usuario')) {
        echo "<p style='color: green;'> La tabla tbl_usuario existe</p>";
        
        // Obtiene la descripción de la tabla (columnas y sus tipos)
        $stmt = $pdo->query("DESCRIBE tbl_usuario");
        $columns = $stmt->fetchAll();
        // Muestra cuántas columnas encontró
        echo "<p>📋 Columnas encontradas: " . count($columns) . "</p>";
        
        // Lista cada columna con su nombre y tipo
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li><strong>" . $column['Field'] . "</strong> - " . $column['Type'] . "</li>";
        }
        echo "</ul>";
        
        // Cuenta la cantidad de usuarios registrados en la tabla
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM tbl_usuario");
        $count = $stmt->fetch();
        echo "<p>👥 Usuarios registrados: " . $count['total'] . "</p>";
        
    } else {
        // Si la tabla no existe, muestra un error y recomienda importar el archivo SQL
        echo "<p style='color: red;'> La tabla tbl_usuario NO existe</p>";
        echo "<p>Importa el archivo aicroom.sql para crear la tabla</p>";
    }
    
} catch (Exception $e) {
    // Captura cualquier error de conexión o ejecución y lo muestra en rojo
    echo "<p style='color: red;'> Error de conexión: " . $e->getMessage() . "</p>";
    // Da recomendaciones para solucionar el error
    echo "<p>Verifica:</p>";
    echo "<ul>";
    echo "<li>Que MySQL esté ejecutándose</li>";
    echo "<li>Las credenciales en config.php</li>";
    echo "<li>Que la base de datos 'aicroom' exista</li>";
    echo "</ul>";
}

// Sección de prueba de registro: se redirige al archivo register.html
echo "<h3>3. Prueba de registro:</h3>";
echo "<p>Para probar el registro, ve a <a href='register.html'>register.html</a></p>";

// Sección de prueba de login: se redirige al archivo login.html
echo "<h3>4. Prueba de login:</h3>";
echo "<p>Para probar el login, ve a <a href='login.html'>login.html</a></p>";

// Línea divisoria y pie de página con la fecha/hora en que se generó la verificación
echo "<hr>";
echo "<p><small>Archivo de verificación generado el " . date('Y-m-d H:i:s') . "</small></p>";
?> 
