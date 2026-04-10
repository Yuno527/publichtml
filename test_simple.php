<?php
// Test simple para verificar que get_user_status.php funciona
echo "<h2>Test Simple - get_user_status.php</h2>";

// Simular una llamada a get_user_status.php
$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/get_user_status.php';

echo "<p>Probando URL: <code>$url</code></p>";

// Hacer la llamada
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json'
    ]
]);

$response = file_get_contents($url, false, $context);

echo "<h3>Respuesta recibida:</h3>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Intentar decodificar JSON
$data = json_decode($response, true);
if ($data === null) {
    echo "<p style='color: red;'>❌ Error: No se pudo decodificar JSON</p>";
    echo "<p>Error JSON: " . json_last_error_msg() . "</p>";
} else {
    echo "<p style='color: green;'>✅ JSON válido</p>";
    echo "<h3>Datos decodificados:</h3>";
    echo "<pre>" . print_r($data, true) . "</pre>";
}
?> 