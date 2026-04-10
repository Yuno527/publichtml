<?php
// Script de prueba para verificar la navegación
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Navegación PHP</title>
</head>
<body>
    <h1>Prueba de Navegación PHP</h1>
    <p>Si puedes ver esta página, el servidor PHP funciona correctamente.</p>
    <p>Archivos disponibles:</p>
    <ul>
        <li><a href="login.html">login.html</a></li>
        <li><a href="login_simple.html">login_simple.html (sin verificación de autenticación)</a></li>
        <li><span style="color:#888;">forgot_password.html (eliminado)</span></li>
        <li><a href="test_navigation.html">test_navigation.html</a></li>
    </ul>
    <p><a href="login.html">Volver al Login</a></p>
    <p><a href="login_simple.html">Volver al Login Simple</a></p>
</body>
</html>
