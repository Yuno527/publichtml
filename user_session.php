<?php
// Archivo para obtener información del usuario desde la sesión
session_start();

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Función para obtener información del usuario actual
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? 'Usuario',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'user'
        ];
    }
    return null;
}

// Función para verificar si el usuario es administrador
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

// Función para generar JavaScript con la información del usuario
function generateUserScript() {
    $user = getCurrentUser();
    
    if ($user) {
        $userName = htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
        $userRole = htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8');
        $userEmail = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
        
        echo "<script>
        // Información del usuario desde PHP
        window.currentUserName = '{$userName}';
        window.userRole = '{$userRole}';
        window.userEmail = '{$userEmail}';
        window.isLoggedIn = true;
        
        console.log('Usuario cargado desde PHP:', {
            name: '{$userName}',
            role: '{$userRole}',
            email: '{$userEmail}'
        });
        </script>";
    } else {
        echo "<script>
        // Usuario no logueado
        window.currentUserName = 'Usuario';
        window.userRole = 'user';
        window.isLoggedIn = false;
        
        console.log('Usuario no logueado');
        </script>";
    }
}

// Función para generar atributos HTML con información del usuario
function generateUserAttributes() {
    $user = getCurrentUser();
    
    if ($user) {
        $userName = htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
        $userRole = htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8');
        
        return "data-user-name='{$userName}' data-user-role='{$userRole}'";
    }
    
    return "data-user-name='Usuario' data-user-role='user'";
}

// Función para generar clase CSS si es admin
function generateAdminClass() {
    return isAdmin() ? 'admin-indicator' : '';
}
?>
