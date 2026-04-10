<?php
require_once 'config.php';

// Permitir peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Método no permitido');
}

// Obtener datos del formulario
$username = isset($_POST['username']) ? sanitizeInput($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$remember = isset($_POST['remember']) ? true : false;

// Validaciones
$errors = [];

// Validar nombre de usuario
if (empty($username)) {
    $errors[] = 'El nombre de usuario es requerido';
} elseif (strlen($username) < 3) {
    $errors[] = 'El nombre de usuario debe tener al menos 3 caracteres';
}

// Validar contraseña
if (empty($password)) {
    $errors[] = 'La contraseña es requerida';
} elseif (strlen($password) < 6) {
    $errors[] = 'La contraseña debe tener al menos 6 caracteres';
}

// Si hay errores, devolverlos
if (!empty($errors)) {
    sendResponse(false, 'Errores de validación', $errors);
}

try {
    // Verificar conexión
    if (!testConnection()) {
        sendResponse(false, 'Error: No se puede conectar a la base de datos');
    }
    
    $pdo = getConnection();
    
    // Verificar si la tabla existe
    if (!tableExists($pdo, 'tbl_usuario')) {
        sendResponse(false, 'Error: La tabla tbl_usuario no existe. Importa el archivo aicroom.sql');
    }
    
    // Buscar usuario por nombre o correo
    $stmt = $pdo->prepare("
        SELECT Id_Usuario, nombre, contraseña, correo, rol, fecha_registro 
        FROM tbl_usuario 
        WHERE nombre = ? OR correo = ?
    ");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        sendResponse(false, 'Credenciales incorrectas');
    }
    
    // Verificar contraseña hasheada
    if (!verifyPassword($password, $user['contraseña'])) {
        sendResponse(false, 'Credenciales incorrectas');
    }
    
    // Iniciar sesión
    session_start();
    $_SESSION['user_id'] = $user['Id_Usuario'];
    $_SESSION['user_name'] = $user['nombre'];
    $_SESSION['user_email'] = $user['correo'];
    $_SESSION['user_role'] = $user['rol'];
    $_SESSION['logged_in'] = true;
    
    // Si marcó "recordarme", crear cookie
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 días
    }
    
    // Registrar el login en el historial (opcional)
    
    sendResponse(true, 'Login exitoso', [
        'user_id' => $user['Id_Usuario'],
        'nombre' => $user['nombre'],
        'correo' => $user['correo'],
        'rol' => $user['rol'],
        'redirect' => 'index.html'
    ]);
    
} catch (PDOException $e) {
    error_log("Error PDO en login: " . $e->getMessage());
    sendResponse(false, 'Error de base de datos: ' . $e->getMessage());
} catch (Exception $e) {
    error_log("Error inesperado en login: " . $e->getMessage());
    sendResponse(false, 'Error inesperado: ' . $e->getMessage());
}
?> 