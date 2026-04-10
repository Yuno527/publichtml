<?php
require_once 'check_auth.php';

// Verificar si el usuario está logueado
requireAuth();

// Obtener datos del usuario actual
$currentUser = getCurrentUser();
?> 