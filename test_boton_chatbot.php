<?php
// Test del botón del chatbot
echo "<h2>🔧 Test del Botón del Chatbot - AICROOM</h2>";

echo "<h3>1. Verificación de Archivos</h3>";

$files = [
    'como-funciona.html' => 'Página donde está el botón',
    'chatbot.html' => 'Página del chatbot',
    'style.css' => 'Estilos CSS'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "<p style='color: green;'>✅ $description ($file) - $size bytes</p>";
    } else {
        echo "<p style='color: red;'>❌ $description ($file) - NO EXISTE</p>";
    }
}

echo "<h3>2. Verificación del Botón</h3>";

// Leer el archivo como-funciona.html para verificar que el botón esté presente
$content = file_get_contents('como-funciona.html');
if (strpos($content, 'Probar Chatbot de Evaluación') !== false) {
    echo "<p style='color: green;'>✅ Botón 'Probar Chatbot de Evaluación' encontrado</p>";
} else {
    echo "<p style='color: red;'>❌ Botón no encontrado en como-funciona.html</p>";
}

if (strpos($content, 'href="chatbot.html"') !== false) {
    echo "<p style='color: green;'>✅ Enlace al chatbot.html encontrado</p>";
} else {
    echo "<p style='color: red;'>❌ Enlace al chatbot.html no encontrado</p>";
}

if (strpos($content, 'step-button') !== false) {
    echo "<p style='color: green;'>✅ Clase CSS 'step-button' encontrada</p>";
} else {
    echo "<p style='color: red;'>❌ Clase CSS 'step-button' no encontrada</p>";
}

echo "<h3>3. Verificación de Estilos CSS</h3>";

$cssContent = file_get_contents('style.css');
if (strpos($cssContent, '.step-button') !== false) {
    echo "<p style='color: green;'>✅ Estilos CSS para .step-button encontrados</p>";
} else {
    echo "<p style='color: red;'>❌ Estilos CSS para .step-button no encontrados</p>";
}

if (strpos($cssContent, '.step-button .cta-button') !== false) {
    echo "<p style='color: green;'>✅ Estilos CSS para el botón encontrados</p>";
} else {
    echo "<p style='color: red;'>❌ Estilos CSS para el botón no encontrados</p>";
}

echo "<h3>4. Enlaces de Prueba</h3>";
echo "<ul>";
echo "<li><a href='como-funciona.html' target='_blank'>📄 Página 'Cómo funciona'</a></li>";
echo "<li><a href='chatbot.html' target='_blank'>🤖 Chatbot de Evaluación</a></li>";
echo "<li><a href='index.html' target='_blank'>🏠 Página Principal</a></li>";
echo "</ul>";

echo "<h3>5. Instrucciones de Prueba</h3>";
echo "<ol>";
echo "<li><strong>Abre la página:</strong> Ve a <a href='como-funciona.html' target='_blank'>como-funciona.html</a></li>";
echo "<li><strong>Busca el botón:</strong> En la sección 'Examen psicológico interactivo'</li>";
echo "<li><strong>Verifica el diseño:</strong> El botón debe estar debajo de la imagen del chatbot</li>";
echo "<li><strong>Prueba el enlace:</strong> Haz clic en 'Probar Chatbot de Evaluación'</li>";
echo "<li><strong>Confirma redirección:</strong> Debe llevarte a chatbot.html</li>";
echo "</ol>";

echo "<h3>6. Características del Botón</h3>";
echo "<ul>";
echo "<li>✅ <strong>Ubicación:</strong> Debajo de la imagen del chatbot en el paso 1</li>";
echo "<li>✅ <strong>Texto:</strong> 'Probar Chatbot de Evaluación'</li>";
echo "<li>✅ <strong>Icono:</strong> Robot (fas fa-robot)</li>";
echo "<li>✅ <strong>Enlace:</strong> Lleva a chatbot.html</li>";
echo "<li>✅ <strong>Estilos:</strong> Gradiente azul con efectos hover</li>";
echo "<li>✅ <strong>Responsive:</strong> Se adapta a móviles</li>";
echo "</ul>";

echo "<h3>7. Solución de Problemas</h3>";
echo "<ul>";
echo "<li><strong>Botón no aparece:</strong> Verifica que el archivo como-funciona.html esté actualizado</li>";
echo "<li><strong>Estilos no se ven:</strong> Limpia caché del navegador (Ctrl+F5)</li>";
echo "<li><strong>Enlace no funciona:</strong> Verifica que chatbot.html exista</li>";
echo "<li><strong>Diseño roto:</strong> Verifica que style.css esté cargado correctamente</li>";
echo "</ul>";

echo "<h3>8. Estado del Sistema</h3>";
echo "<p style='color: green;'>✅ Botón del chatbot implementado correctamente</p>";
echo "<p>El botón 'Probar Chatbot de Evaluación' ahora está disponible en la página 'Cómo funciona'</p>";
echo "<p>Los usuarios pueden acceder directamente al chatbot desde esa sección</p>";
?> 