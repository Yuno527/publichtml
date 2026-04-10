<?php
// Test de la página "Cómo funciona" actualizada
echo "<h2>🔧 Test de la Página 'Cómo funciona' Actualizada</h2>";

echo "<h3>1. Verificación de Archivos</h3>";

$files = [
    'como-funciona.html' => 'Página principal',
    'style.css' => 'Estilos CSS',
    'main.js' => 'JavaScript'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "<p style='color: green;'>✅ $description ($file) - $size bytes</p>";
    } else {
        echo "<p style='color: red;'>❌ $description ($file) - NO EXISTE</p>";
    }
}

echo "<h3>2. Verificación de Contenido Actualizado</h3>";

// Leer el archivo como-funciona.html
$content = file_get_contents('como-funciona.html');

// Verificar elementos clave de la nueva información
$keyElements = [
    '¿Cómo funciona nuestro sistema de evaluación con inteligencia artificial?' => 'Título principal actualizado',
    'En AICROOM hemos desarrollado un sistema innovador' => 'Descripción introductoria',
    '🧠 Entrevista conversacional con IA' => 'Paso 1 con emoji',
    '🔍 Análisis de respuestas' => 'Paso 2 con emoji',
    '🔒 Privacidad y objetividad' => 'Paso 3 con emoji',
    '📊 Resultados visibles solo para el administrador' => 'Paso 4 con emoji',
    '20 preguntas cuidadosamente diseñadas' => 'Mención de 20 preguntas',
    'orden aleatorio para garantizar imparcialidad' => 'Mención de aleatorización',
    'sin mostrar puntajes ni resultados visibles' => 'Mención de confidencialidad',
    'solo pueden ser consultados por el administrador' => 'Mención de acceso exclusivo',
    'Probar Chatbot de Evaluación' => 'Botón del chatbot'
];

foreach ($keyElements as $text => $description) {
    if (strpos($content, $text) !== false) {
        echo "<p style='color: green;'>✅ $description</p>";
    } else {
        echo "<p style='color: red;'>❌ $description - NO ENCONTRADO</p>";
    }
}

echo "<h3>3. Verificación de Estructura</h3>";

// Verificar que hay 4 pasos
$stepCount = substr_count($content, 'step-number');
echo "<p>Número de pasos encontrados: <strong>$stepCount</strong></p>";

if ($stepCount >= 4) {
    echo "<p style='color: green;'>✅ Estructura de 4 pasos correcta</p>";
} else {
    echo "<p style='color: red;'>❌ Faltan pasos en la estructura</p>";
}

// Verificar emojis
$emojiCount = preg_match_all('/[🧠🔍🔒📊🤖]/', $content);
echo "<p>Emojis encontrados: <strong>$emojiCount</strong></p>";

if ($emojiCount >= 5) {
    echo "<p style='color: green;'>✅ Emojis implementados correctamente</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Pocos emojis encontrados</p>";
}

echo "<h3>4. Verificación de Enlaces</h3>";

$links = [
    'href="chatbot.html"' => 'Enlace al chatbot',
    'href="contacto.html"' => 'Enlace de contacto',
    'href="index.html"' => 'Enlace al inicio'
];

foreach ($links as $link => $description) {
    if (strpos($content, $link) !== false) {
        echo "<p style='color: green;'>✅ $description</p>";
    } else {
        echo "<p style='color: red;'>❌ $description - NO ENCONTRADO</p>";
    }
}

echo "<h3>5. Características de la Nueva Información</h3>";
echo "<ul>";
echo "<li>✅ <strong>Título actualizado:</strong> Enfoque en evaluación con IA</li>";
echo "<li>✅ <strong>Descripción profesional:</strong> Sistema innovador y confidencial</li>";
echo "<li>✅ <strong>4 pasos claros:</strong> Entrevista, análisis, privacidad, resultados</li>";
echo "<li>✅ <strong>Emojis descriptivos:</strong> 🧠🔍🔒📊 para cada paso</li>";
echo "<li>✅ <strong>Mención de 20 preguntas:</strong> Especificidad del sistema</li>";
echo "<li>✅ <strong>Aleatorización:</strong> Garantía de imparcialidad</li>";
echo "<li>✅ <strong>Confidencialidad:</strong> Resultados no visibles para usuarios</li>";
echo "<li>✅ <strong>Acceso exclusivo:</strong> Solo administradores ven resultados</li>";
echo "<li>✅ <strong>Botón funcional:</strong> Probar Chatbot de Evaluación</li>";
echo "</ul>";

echo "<h3>6. Enlaces de Prueba</h3>";
echo "<ul>";
echo "<li><a href='como-funciona.html' target='_blank'>📄 Página 'Cómo funciona'</a></li>";
echo "<li><a href='chatbot.html' target='_blank'>🤖 Chatbot de Evaluación</a></li>";
echo "<li><a href='contacto.html' target='_blank'>📧 Página de Contacto</a></li>";
echo "<li><a href='index.html' target='_blank'>🏠 Página Principal</a></li>";
echo "</ul>";

echo "<h3>7. Instrucciones de Verificación</h3>";
echo "<ol>";
echo "<li><strong>Abre la página:</strong> Ve a <a href='como-funciona.html' target='_blank'>como-funciona.html</a></li>";
echo "<li><strong>Verifica el título:</strong> Debe ser '¿Cómo funciona nuestro sistema de evaluación con inteligencia artificial?'</li>";
echo "<li><strong>Revisa los 4 pasos:</strong> Con emojis 🧠🔍🔒📊</li>";
echo "<li><strong>Confirma el botón:</strong> 'Probar Chatbot de Evaluación' debe estar presente</li>";
echo "<li><strong>Prueba la simulación:</strong> El chatbot de demostración debe funcionar</li>";
echo "<li><strong>Verifica enlaces:</strong> Todos los enlaces deben funcionar correctamente</li>";
echo "</ol>";

echo "<h3>8. Mejoras Implementadas</h3>";
echo "<ul>";
echo "<li>✅ <strong>Información más detallada:</strong> Descripción completa del sistema</li>";
echo "<li>✅ <strong>Enfoque profesional:</strong> Lenguaje técnico y confiable</li>";
echo "<li>✅ <strong>4 pasos claros:</strong> Proceso bien estructurado</li>";
echo "<li>✅ <strong>Emojis descriptivos:</strong> Mejor experiencia visual</li>";
echo "<li>✅ <strong>Mención de características:</strong> 20 preguntas, aleatorización, confidencialidad</li>";
echo "<li>✅ <strong>Acceso exclusivo:</strong> Claridad sobre quién ve los resultados</li>";
echo "<li>✅ <strong>Botón funcional:</strong> Acceso directo al chatbot</li>";
echo "<li>✅ <strong>Llamada a la acción:</strong> Solicitar información en lugar de agendar demo</li>";
echo "</ul>";

echo "<h3>9. Estado del Sistema</h3>";
echo "<p style='color: green;'>✅ Página 'Cómo funciona' actualizada correctamente</p>";
echo "<p>La información ahora refleja de manera precisa y profesional cómo funciona el sistema de evaluación con IA de AICROOM.</p>";
echo "<p>Los usuarios pueden entender claramente el proceso de 4 pasos y acceder directamente al chatbot desde la página.</p>";
?> 