<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');  // Define la constante DB_HOST con el valor 'localhost' (servidor de la BD).
define('DB_NAME', 'public');    // Define la constante DB_NAME con el nombre de la base de datos 'aicroom'.
define('DB_USER', 'root');       // Define la constante DB_USER con el usuario de conexión, en este caso 'root'.
define('DB_PASS', '');           // Define la constante DB_PASS con la contraseña del usuario, aquí vacía.

// Crear conexión
function getConnection() {
    try {
        // Construye el Data Source Name (DSN) para PDO con host, base de datos y charset utf8mb4.
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        // Crea un objeto PDO con parámetros de configuración avanzados.
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Activa las excepciones para manejo de errores.
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Establece el modo de fetch por defecto como asociativo.
            PDO::ATTR_EMULATE_PREPARES => false,                // Desactiva la emulación de consultas preparadas para mayor seguridad.
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4" // Asegura que la conexión use UTF-8 multibyte.
        ]);
        
        return $pdo; // Retorna el objeto PDO ya configurado para usar en consultas.
    } catch (PDOException $e) {
        // Registra en el log el error de conexión sin mostrarlo al usuario.
        error_log("Error de conexión a la base de datos: " . $e->getMessage());
        // Lanza una excepción genérica para no exponer detalles técnicos al cliente.
        throw new Exception("No se pudo conectar a la base de datos. Verifica la configuración.");
    }
}

// Función para verificar conexión
function testConnection() {
    try {
        $pdo = getConnection();           // Intenta obtener una conexión a la base de datos.
        $stmt = $pdo->query("SELECT 1");  // Ejecuta una consulta mínima para validar la conexión.
        return true;                      // Si funciona, retorna true indicando conexión válida.
    } catch (Exception $e) {
        return false;                     // Si ocurre error, retorna false.
    }
}

// Función para validar email
function isValidEmail($email) {
    // Usa filter_var con el flag FILTER_VALIDATE_EMAIL para verificar formato válido de email.
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para validar edad
function isValidAge($age) {
    // Verifica que sea un número, mayor o igual a 1 y menor o igual a 120 (rango lógico de edades).
    return is_numeric($age) && $age >= 1 && $age <= 120;
}

// Función para validar contraseña
function isValidPassword($password) {
    // Comprueba que la contraseña tenga al menos 6 caracteres.
    return strlen($password) >= 6;
}

// Función para generar hash seguro de contraseña
function hashPassword($password) {
    // Aplica password_hash con algoritmo por defecto (bcrypt) para mayor seguridad.
    return password_hash($password, PASSWORD_DEFAULT);
}

// Función para verificar contraseña hasheada
function verifyPassword($password, $storedHash) {
    // Compara una contraseña en texto plano con su hash almacenado.
    return password_verify($password, $storedHash);
}

// Función para generar respuesta JSON
function sendResponse($success, $message, $data = null) {
    header('Content-Type: application/json'); // Define el encabezado HTTP como JSON.
    echo json_encode([                        // Convierte el array asociativo en JSON.
        'success' => $success,                // Indica si la operación fue exitosa.
        'message' => $message,                // Mensaje descriptivo de la respuesta.
        'data' => $data                       // Datos adicionales (opcional).
    ]);
    exit; // Finaliza el script inmediatamente tras enviar la respuesta.
}

// Función para limpiar datos de entrada
function sanitizeInput($data) {
    $data = trim($data);              // Elimina espacios en blanco al inicio y final.
    $data = stripslashes($data);      // Elimina barras invertidas (\) si existen.
    $data = htmlspecialchars($data);  // Convierte caracteres especiales en entidades HTML seguras.
    return $data;                      // Retorna el dato limpio.
}

// Función para verificar si la tabla existe
function tableExists($pdo, $tableName) {
    try {
        // Ejecuta consulta para buscar la tabla en la base de datos.
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        // Retorna true si existe al menos una coincidencia (la tabla existe).
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        // Si ocurre error, retorna false (asumiendo que no existe o error de permisos).
        return false;
    }
}
?>
