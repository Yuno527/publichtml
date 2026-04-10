<?php
session_start();

// Verificar si el usuario está logueado
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $user_name = $_SESSION['user_name'] ?? 'Usuario';
    $user_role = $_SESSION['user_role'] ?? 'user';
    
    echo json_encode([
        'logged_in' => true,
        'name' => $user_name,
        'role' => $user_role,
        'is_admin' => ($user_role === 'admin')
    ]);
} else {
    echo json_encode([
        'logged_in' => false,
        'name' => 'Usuario',
        'role' => 'user',
        'is_admin' => false
    ]);
}
?>
