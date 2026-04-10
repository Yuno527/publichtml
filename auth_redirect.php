<?php
require_once 'check_auth.php';

// Si se accede directamente a este archivo, verificar autenticación
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    
    // Verificar si el usuario está logueado
    if (!isLoggedIn()) {
        // Redirigir al login si no está autenticado
        header('Location: login.html');
        exit;
    }
    
    // Si está autenticado, incluir la página solicitada
    if (file_exists($page)) {
        // Incluir la página con los datos del usuario disponibles
        $currentUser = getCurrentUser();
        include $page;
    } else {
        // Si la página no existe, redirigir al index
        header('Location: index.html');
        exit;
    }
} else {
    // Si se accede directamente sin parámetros, verificar autenticación
    requireAuth();
    $currentUser = getCurrentUser();
}
?> 