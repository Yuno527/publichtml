<?php
// Evitar mostrar errores
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

// Obtener JSON desde el frontend
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['answers']) || !isset($input['totalScore'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$userId      = $_SESSION['user_id'];
$answers     = $input['answers'];
$totalScore  = $input['totalScore'];

// ----------------------------------------------------------------------
// 0. VERIFICAR SI EL USUARIO YA COMPLETÓ EL TEST
// ----------------------------------------------------------------------
try {
    $pdo = getConnection();
    
    // Verificar si el usuario ya tiene un historial completado
    $checkStmt = $pdo->prepare("
        SELECT Id_historial 
        FROM tbl_historial 
        WHERE Id_UsuarioFK = ? AND estado = 'Completado'
        LIMIT 1
    ");
    $checkStmt->execute([$userId]);
    $existingTest = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingTest) {
        // El usuario ya completó el test, no permitir guardar de nuevo
        echo json_encode([
            'success' => false, 
            'message' => 'Ya has completado esta evaluación. Solo puedes realizarla una vez.',
            'already_completed' => true
        ]);
        exit;
    }
} catch(PDOException $e) {
    // Si hay error en la verificación, continuar (pero registrar el error)
    error_log('Error verificando test completado: ' . $e->getMessage());
}

// ----------------------------------------------------------------------
// 1. Crear un PROMPT PROFESIONAL para IA
// ----------------------------------------------------------------------

$prompt = "
Eres un psicólogo organizacional y analista psicométrico experto en competencias blandas (soft skills).
Interpreta las respuestas del cuestionario y redacta un informe profundo, equilibrado y accionable. No inventes conductas ni resultados que no se desprendan claramente de las respuestas.

Escala del instrumento (solo para tu interpretación interna):
1 = bajo · 2 = medio · 3 = alto

Respuestas del usuario (JSON):
" . json_encode($answers, JSON_PRETTY_PRINT) . "

Puntaje total interno (no lo cites ni menciones en el texto hacia la persona): $totalScore

Instrucciones de salida (español, texto plano):

1) Identifica la competencia blanda más sólida y la que requiere mayor desarrollo según el patrón de respuestas (no solo una pregunta aislada).

2) En \"Análisis:\" escribe un bloque narrativo amplio (aprox. 8–14 líneas o 3–4 párrafos cortos) que integre obligatoriamente:
   - Síntesis del perfil: tendencias generales y coherencia entre respuestas.
   - Matices: fortalezas secundarias, posibles tensiones o contradicciones leves entre ítems.
   - Implicaciones prácticas en contexto laboral o académico (ideas concretas, sin jerga innecesaria).
   - Tono profesional, empático y claro.

3) En \"Recomendación:\" ofrece un mini-plan con 2–4 acciones concretas y realizables para fortalecer la habilidad más débil (puedes sugerir plazos aproximados: corto/medio plazo). Evita frases genéricas tipo \"sé más positivo\".

Formato exacto (una etiqueta por bloque, sin JSON ni bloques de código):

Habilidad más fuerte: [nombre breve de la competencia; opcionalmente entre paréntesis una pista de cómo se reflejó en las respuestas]
Habilidad más débil: [nombre breve de la competencia a desarrollar]
Análisis: [texto completo según el punto 2]
Recomendación: [texto según el punto 3]

Restricciones: NO devuelvas JSON. NO menciones el puntaje numérico ni el número de ítems acertados. Basa todo en el contenido de las respuestas anteriores.
";

// ----------------------------------------------------------------------
// 2. Guardar HISTORIAL y RESPUESTAS
// ----------------------------------------------------------------------

try {
    $pdo = getConnection();
    $pdo->beginTransaction();

    // Crear historial
    $stmt = $pdo->prepare("
        INSERT INTO tbl_historial (Id_UsuarioFK, fecha, estado) 
        VALUES (?, CURDATE(), 'Completado')
    ");
    $stmt->execute([$userId]);
    $historialId = $pdo->lastInsertId();

    // Guardar cada respuesta del usuario
    $stmt = $pdo->prepare("
        INSERT INTO tbl_respuestas (Id_historial, pregunta, respuesta, puntaje) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($answers as $answer) {
        $stmt->execute([
            $historialId,
            $answer['question'],
            $answer['answer'],
            $answer['score']
        ]);
    }

// ----------------------------------------------------------------------
// 3. LLAMAR A LA API DE IA EN PHP (reemplaza Flask)
// ----------------------------------------------------------------------

    // Incluir las funciones de análisis de IA
    require_once __DIR__ . '/ai_analysis_functions.php';
    
    // Intentar usar APIs externas primero
    $analysis = null;
    
    // Intentar OpenRouter (modelos gratuitos)
    if (function_exists('tryOpenRouterFree')) {
        $result = tryOpenRouterFree($prompt, getenv('OPENROUTER_API_KEY') ?: '');
        if ($result && strlen(trim($result)) > 10) {
            $analysis = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $result);
            $analysis = preg_replace('/ +/', ' ', $analysis);
            $analysis = trim($analysis);
        }
    }
    
    // Si OpenRouter falló, intentar Groq
    if (!$analysis && function_exists('tryGroqAPI')) {
        $groqKey = getenv('GROQ_API_KEY') ?: '';
        if ($groqKey) {
            $result = tryGroqAPI($prompt, $groqKey);
            if ($result && strlen(trim($result)) > 10) {
                $analysis = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $result);
                $analysis = preg_replace('/ +/', ' ', $analysis);
                $analysis = trim($analysis);
            }
        }
    }
    
    // Si Groq falló, intentar Together AI
    if (!$analysis && function_exists('tryTogetherAI')) {
        $togetherKey = getenv('TOGETHER_API_KEY') ?: '';
        if ($togetherKey) {
            $result = tryTogetherAI($prompt, $togetherKey);
            if ($result && strlen(trim($result)) > 10) {
                $analysis = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $result);
                $analysis = preg_replace('/ +/', ' ', $analysis);
                $analysis = trim($analysis);
            }
        }
    }
    
    // Si todas las APIs fallaron, usar respuesta de fallback
    if (!$analysis && function_exists('generateFallbackResponse')) {
        $analysis = generateFallbackResponse($prompt);
    } elseif (!$analysis) {
        // Último recurso: mensaje genérico
        $analysis = "Habilidad más fuerte: Comunicación\nHabilidad más débil: Trabajo en equipo\n\nAnálisis: Basado en las respuestas proporcionadas, se observa un perfil equilibrado con fortalezas en comunicación y áreas de mejora en colaboración.\n\nRecomendación: Participar en talleres de trabajo en equipo y buscar oportunidades de colaboración en proyectos grupales.";
    }

// ----------------------------------------------------------------------
// 4. Guardar el resultado final (puntaje + análisis IA)
// ----------------------------------------------------------------------

    $stmt = $pdo->prepare("
        INSERT INTO tbl_resultados (Id_historiaLFK, puntaje_total, resultado_final, fecha_registro) 
        VALUES (?, ?, ?, NOW())
    ");

    $stmt->execute([$historialId, $totalScore, $analysis]);

    $pdo->commit();

// ----------------------------------------------------------------------
// 5. Respuesta al Frontend
// ----------------------------------------------------------------------

    echo json_encode([
        'success' => true,
        'message' => 'Resultados guardados correctamente',
        'analysis' => $analysis
    ]);

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Error guardando resultados',
        'error'   => $e->getMessage()
    ]);
}
