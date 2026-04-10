<?php
/**
 * Prueba simple del sistema de contacto
 */

echo "<h2>Prueba Simple del Sistema de Contacto</h2>\n";

// Simular datos de prueba
$_POST['name'] = 'Juan Pérez';
$_POST['email'] = 'juan@example.com';
$_POST['company'] = 'Empresa Test';
$_POST['message'] = 'Este es un mensaje de prueba para verificar que el sistema funciona correctamente.';

echo "<p>Datos de prueba:</p>\n";
echo "<ul>\n";
echo "<li>Nombre: " . $_POST['name'] . "</li>\n";
echo "<li>Email: " . $_POST['email'] . "</li>\n";
echo "<li>Empresa: " . $_POST['company'] . "</li>\n";
echo "<li>Mensaje: " . $_POST['message'] . "</li>\n";
echo "</ul>\n";

echo "<p>Ejecutando handler de contacto...</p>\n";

// Capturar la salida del handler
ob_start();
include 'contact_simple.php';
$output = ob_get_clean();

echo "<h3>Respuesta del sistema:</h3>\n";
echo "<pre>" . htmlspecialchars($output) . "</pre>\n";

// Intentar decodificar la respuesta JSON
$response = json_decode($output, true);
if ($response) {
    echo "<h3>Respuesta decodificada:</h3>\n";
    echo "<ul>\n";
    echo "<li>Éxito: " . ($response['success'] ? 'Sí' : 'No') . "</li>\n";
    echo "<li>Mensaje: " . htmlspecialchars($response['message']) . "</li>\n";
    if (isset($response['data'])) {
        echo "<li>Datos: " . print_r($response['data'], true) . "</li>\n";
    }
    echo "</ul>\n";
} else {
    echo "<p>No se pudo decodificar la respuesta JSON.</p>\n";
}

echo "<p><a href='contacto.html'>📧 Probar Formulario de Contacto</a></p>\n";
echo "<p><a href='index.html'>← Volver al inicio</a></p>\n";
?>
