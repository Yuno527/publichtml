<?php
/**
 * Prueba del sistema de contacto funcional
 */

echo "<h2>Prueba del Sistema de Contacto Funcional</h2>\n";

// Simular datos de prueba
$_POST['name'] = 'Juan Pérez';
$_POST['email'] = 'juan@example.com';
$_POST['company'] = 'Empresa Test';
$_POST['message'] = 'Este es un mensaje de prueba para verificar que el sistema funciona correctamente.';

echo "<p><strong>Datos de prueba:</strong></p>\n";
echo "<ul>\n";
echo "<li>Nombre: " . $_POST['name'] . "</li>\n";
echo "<li>Email: " . $_POST['email'] . "</li>\n";
echo "<li>Empresa: " . $_POST['company'] . "</li>\n";
echo "<li>Mensaje: " . $_POST['message'] . "</li>\n";
echo "</ul>\n";

echo "<p><strong>Ejecutando sistema de contacto...</strong></p>\n";

// Capturar la salida
ob_start();
include 'contact_working.php';
$output = ob_get_clean();

echo "<h3>Respuesta del sistema:</h3>\n";
echo "<pre>" . htmlspecialchars($output) . "</pre>\n";

// Decodificar respuesta
$response = json_decode($output, true);
if ($response) {
    echo "<h3>Resultado:</h3>\n";
    if ($response['success']) {
        echo "<p style='color: green; font-weight: bold;'>✓ " . htmlspecialchars($response['message']) . "</p>\n";
    } else {
        echo "<p style='color: red; font-weight: bold;'> " . htmlspecialchars($response['message']) . "</p>\n";
        if (isset($response['data'])) {
            echo "<p>Errores:</p>\n";
            echo "<ul>\n";
            foreach ($response['data'] as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>\n";
            }
            echo "</ul>\n";
        }
    }
} else {
    echo "<p style='color: red;'> No se pudo decodificar la respuesta JSON.</p>\n";
}

echo "<hr>\n";
echo "<p><a href='contacto.html' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📧 Probar Formulario de Contacto</a></p>\n";
echo "<p><a href='index.html' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Volver al inicio</a></p>\n";
?>
