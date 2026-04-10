<?php
/**
 * Debug del formulario de contacto
 */

echo "<h2>Debug del Formulario de Contacto</h2>\n";
echo "<pre>\n";

echo "--- INFORMACIÓN DE LA PETICIÓN ---\n";
echo "Método: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'No definido') . "\n";

echo "\n--- DATOS RECIBIDOS ---\n";
echo "POST data:\n";
print_r($_POST);

echo "\n--- DATOS RAW ---\n";
$raw_data = file_get_contents('php://input');
echo "Raw input: " . $raw_data . "\n";

echo "\n--- HEADERS ---\n";
foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}

echo "\n--- PRUEBA DE ENVÍO MANUAL ---\n";

// Simular datos de prueba
$_POST['name'] = 'Juan Pérez';
$_POST['email'] = 'juan@example.com';
$_POST['company'] = 'Empresa Test';
$_POST['message'] = 'Este es un mensaje de prueba para verificar que el sistema funciona correctamente.';

echo "Datos simulados:\n";
print_r($_POST);

// Incluir el handler de contacto
echo "\n--- EJECUTANDO HANDLER ---\n";
ob_start();
include 'contact_handler.php';
$output = ob_get_clean();
echo $output;

echo "</pre>\n";
?>
