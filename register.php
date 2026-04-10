<?php
require_once 'config.php';

// Permitir peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Método no permitido');
}

// Obtener datos del formulario
$nombre = isset($_POST['nombre']) ? sanitizeInput($_POST['nombre']) : '';
$empresa = isset($_POST['empresa']) ? sanitizeInput($_POST['empresa']) : '';
$correo = isset($_POST['correo']) ? sanitizeInput($_POST['correo']) : '';
$puesto = isset($_POST['puesto']) ? sanitizeInput($_POST['puesto']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validaciones
$errors = [];

// Validar nombre
if (empty($nombre) || strlen($nombre) < 2) {
    $errors[] = 'El nombre es requerido y debe tener al menos 2 caracteres';
}

// Validar empresa
if (empty($empresa) || strlen($empresa) < 2) {
    $errors[] = 'La empresa es requerida y debe tener al menos 2 caracteres';
}

// Validar correo
if (empty($correo) || !isValidEmail($correo)) {
    $errors[] = 'El correo electrónico es requerido y debe ser válido';
}

// Validar puesto
if (empty($puesto) || strlen($puesto) < 2) {
    $errors[] = 'El puesto es requerido y debe tener al menos 2 caracteres';
}

// Validar contraseña
if (empty($password) || !isValidPassword($password)) {
    $errors[] = 'La contraseña es requerida y debe tener al menos 6 caracteres';
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
    
    // Verificar si el correo ya existe
    $stmt = $pdo->prepare("SELECT Id_Usuario FROM tbl_usuario WHERE correo = ?");
    $stmt->execute([$correo]);
    
    if ($stmt->fetch()) {
        sendResponse(false, 'El correo electrónico ya está registrado');
    }
    
    // Generar hash seguro de la contraseña
    $hashedPassword = hashPassword($password);
    
    // Insertar nuevo usuario
    $stmt = $pdo->prepare("INSERT INTO tbl_usuario (nombre, contraseña, correo, empresa_donde_labora, puesto, fecha_registro, rol) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $fecha_registro = date('Y-m-d');
    $rol = 'usuario'; // Rol por defecto
    
    $result = $stmt->execute([
        $nombre,
        $hashedPassword,
        $correo,
        $empresa,
        $puesto,
        $fecha_registro,
        $rol
    ]);
    
    if (!$result) {
        sendResponse(false, 'Error al insertar datos en la base de datos');
    }
    
    $userId = $pdo->lastInsertId();
    
    if (!$userId) {
        sendResponse(false, 'Error: No se pudo obtener el ID del usuario registrado');
    }
    
    sendResponse(true, 'Usuario registrado exitosamente', [
        'user_id' => $userId,
        'nombre' => $nombre,
        'correo' => $correo
    ]);
    
} catch (PDOException $e) {
    error_log("Error PDO en registro: " . $e->getMessage());
    sendResponse(false, 'Error de base de datos: ' . $e->getMessage());
} catch (Exception $e) {
    error_log("Error inesperado en registro: " . $e->getMessage());
    sendResponse(false, 'Error inesperado: ' . $e->getMessage());
}
?> 