<?php
/**
 * API de Análisis de IA en PHP (Endpoint HTTP)
 * Reemplaza la funcionalidad de Flask para funcionar directamente en Hostinger
 * 
 * Este archivo actúa como endpoint HTTP que puede ser llamado desde save_test_results.php
 */

// Desactivar salida de errores en HTML para no romper el JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    // Incluir las funciones
    require_once __DIR__ . '/ai_analysis_functions.php';

    // Obtener el prompt desde POST (JSON)
    $rawInput = file_get_contents('php://input');
    $input    = json_decode($rawInput, true);

    if (!$input || !isset($input['prompt'])) {
        echo json_encode(['text' => 'No se recibió el prompt para el análisis.']);
        exit;
    }

    $prompt = $input['prompt'];

    // Configuración de APIs (opcional - pueden estar vacías)
    $GROQ_API_KEY      = getenv('GROQ_API_KEY') ?: '';
    $TOGETHER_API_KEY  = getenv('TOGETHER_API_KEY') ?: '';
    $OPENROUTER_API_KEY = getenv('OPENROUTER_API_KEY') ?: '';

    // Intentar con cada API disponible en orden de preferencia
    $apis = [
        ['OpenRouter', 'tryOpenRouterFree', $OPENROUTER_API_KEY],
        ['Groq', 'tryGroqAPI', $GROQ_API_KEY],
        ['Together AI', 'tryTogetherAI', $TOGETHER_API_KEY]
    ];

    foreach ($apis as $api) {
        list($apiName, $apiFunc, $apiKey) = $api;
        try {
            if (!function_exists($apiFunc)) {
                continue;
            }

            $result = $apiFunc($prompt, $apiKey);
            if ($result && strlen(trim($result)) > 10) {
                // Limpiar el resultado
                $result = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $result);
                $result = preg_replace('/ +/', ' ', $result);
                $result = trim($result);

                echo json_encode(['text' => $result]);
                exit;
            }
        } catch (Exception $e) {
            // Continuar con la siguiente API
            continue;
        }
    }

    // Si todas las APIs fallan o no hay APIs configuradas, usar respuesta de fallback
    if (function_exists('generateFallbackResponse')) {
        $fallbackResponse = generateFallbackResponse($prompt);
    } else {
        $fallbackResponse = "Habilidad más fuerte: Comunicación\nHabilidad más débil: Trabajo en equipo\n\nAnálisis: No se pudo contactar con ningún servicio de IA, por lo que se muestra un análisis genérico basado en el test.\n\nRecomendación: Participar en actividades colaborativas y solicitar retroalimentación para fortalecer el trabajo en equipo.";
    }

    echo json_encode(['text' => $fallbackResponse]);
} catch (Throwable $e) {
    // En caso de cualquier error inesperado, devolver mensaje genérico en formato texto
    $fallback = "Habilidad más fuerte: Comunicación\nHabilidad más débil: Trabajo en equipo\n\nAnálisis: Ocurrió un error interno al procesar el análisis de IA, por lo que se muestra este mensaje genérico.\n\nRecomendación: Continuar desarrollando habilidades blandas mediante práctica constante y retroalimentación profesional.";
    echo json_encode(['text' => $fallback]);
}
?>
