<?php
// Evitar que se muestren errores PHP en la salida
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    try {
        $pdo = getConnection();
        
        $stmt = $pdo->prepare("SELECT Id_Usuario, nombre, correo, rol FROM tbl_usuario WHERE Id_Usuario = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo json_encode([
                'logged_in' => true,
                'user_id' => $user['Id_Usuario'],
                'user_name' => $user['nombre'],
                'user_email' => $user['correo'],
                'user_role' => $user['rol']
            ]);
        } else {
            echo json_encode([
                'logged_in' => false,
                'message' => 'Usuario no encontrado'
            ]);
        }
    } catch(PDOException $e) {
        echo json_encode([
            'logged_in' => false,
            'message' => 'Error de base de datos'
        ]);
    }
} else {
    echo json_encode([
        'logged_in' => false,
        'message' => 'No autenticado'
    ]);
}
?> 