<?php
// Desactiva la visualización de errores PHP en la salida para evitar exponer información sensible
error_reporting(0);
ini_set('display_errors', 0);

// Inicia la sesión para acceder a las variables de sesión del usuario
session_start();
// Incluye el archivo de configuración con la conexión a la base de datos
require_once 'config.php';

// Indica que la respuesta será en formato JSON
header('Content-Type: application/json');

// Verifica si el usuario está autenticado comprobando la variable de sesión
if (!isset($_SESSION['user_id'])) {
    // Si no hay usuario en sesión, devuelve un JSON indicando que no está autenticado
    echo json_encode(['completed' => false, 'message' => 'Usuario no autenticado']);
    exit; // Finaliza la ejecución del script
}

// Guarda el ID del usuario autenticado desde la sesión
$userId = $_SESSION['user_id'];

try {
    // Obtiene la conexión PDO mediante la función definida en config.php
    $pdo = getConnection();
    
    // Prepara una consulta para verificar si el usuario tiene al menos un historial con estado "Completado"
    $stmt = $pdo->prepare("
        SELECT h.Id_historial 
        FROM tbl_historial h 
        WHERE h.Id_UsuarioFK = ? AND h.estado = 'Completado'
        LIMIT 1
    ");
    
    // Ejecuta la consulta con el ID del usuario actual
    $stmt->execute([$userId]);
    // Obtiene el primer resultado en forma de array asociativo
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Si existe un resultado, significa que el usuario ya tiene un historial completado
    if ($result) {
        echo json_encode(['completed' => true]);
    } else {
        // Si no hay registros, se indica que aún no tiene historial completado
        echo json_encode(['completed' => false]);
    }
    
} catch(PDOException $e) {
    // Si ocurre un error en la base de datos, se devuelve un JSON con error
    echo json_encode(['completed' => false, 'error' => 'Error de base de datos']);
}
?> 
