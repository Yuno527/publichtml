<?php
session_start();

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Función para redirigir al login si no está autenticado
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.html');
        exit;
    }
}

// Función para redirigir al index si ya está logueado
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: index.html');
        exit;
    }
}

// Función para obtener datos del usuario logueado
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
    return null;
}
?> 