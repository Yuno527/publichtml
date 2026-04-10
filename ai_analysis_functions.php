<?php
/**
 * Funciones de Análisis de IA en PHP
 * Reemplaza la funcionalidad de Flask para funcionar directamente en Hostinger
 * 
 * Este archivo contiene las funciones que pueden ser incluidas directamente
 * o usadas a través de ai_analysis.php como API HTTP
 */

if (!function_exists('tryOpenRouterFree')) {
    /**
     * Intenta usar OpenRouter con modelos gratuitos
     */
    function tryOpenRouterFree($prompt, $apiKey = '') {
        $freeModels = [
            'google/gemini-flash-1.5-8b:free',
            'meta-llama/llama-3.2-3b-instruct:free',
            'qwen/qwen-2.5-7b-instruct:free',
            'mistralai/mistral-7b-instruct:free'
        ];
        
        $headers = [
            'Content-Type: application/json',
            'HTTP-Referer: ' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'),
            'X-Title: AI Skills Analyzer'
        ];
        
        if ($apiKey) {
            $headers[] = 'Authorization: Bearer ' . $apiKey;
        }
        
        foreach ($freeModels as $model) {
            $payload = [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un analista experto en psicometría y comportamiento humano. Responde de forma clara y profesional.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000
            ];
            
            $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 45);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode == 200) {
                $result = json_decode($response, true);
                if (isset($result['choices'][0]['message']['content'])) {
                    $content = trim($result['choices'][0]['message']['content']);
                    if (strlen($content) > 10) {
                        return $content;
                    }
                }
            } elseif ($httpCode == 401 && !$apiKey) {
                continue; // Modelo requiere API key, probar siguiente
            }
        }
        
        return null;
    }
}

if (!function_exists('tryGroqAPI')) {
    /**
     * Intenta usar Groq API
     */
    function tryGroqAPI($prompt, $apiKey) {
        if (!$apiKey) {
            return null;
        }
        
        $payload = [
            'model' => 'llama-3.1-8b-instant',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Eres un analista experto en psicometría y comportamiento humano. Responde de forma clara y profesional.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000
        ];
        
        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            $result = json_decode($response, true);
            if (isset($result['choices'][0]['message']['content'])) {
                return trim($result['choices'][0]['message']['content']);
            }
        }
        
        return null;
    }
}

if (!function_exists('tryTogetherAI')) {
    /**
     * Intenta usar Together AI
     */
    function tryTogetherAI($prompt, $apiKey) {
        if (!$apiKey) {
            return null;
        }
        
        $payload = [
            'model' => 'meta-llama/Llama-3-8b-chat-hf',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Eres un analista experto en psicometría y comportamiento humano. Responde de forma clara y profesional.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000
        ];
        
        $ch = curl_init('https://api.together.xyz/v1/chat/completions');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            $result = json_decode($response, true);
            if (isset($result['choices'][0]['message']['content'])) {
                return trim($result['choices'][0]['message']['content']);
            }
        }
        
        return null;
    }
}

if (!function_exists('generateFallbackResponse')) {
    /**
     * Genera una respuesta de fallback variada y personalizada
     * (Esta es la misma función que está en ai_analysis.php)
     */
    function generateFallbackResponse($prompt) {
        // Buscar puntaje total
        preg_match('/puntaje total[:\s]+(\d+)/i', strtolower($prompt), $scoreMatch);
        $totalScore = isset($scoreMatch[1]) ? (int)$scoreMatch[1] : null;
        
        // Analizar respuestas individuales del JSON
        $answers = [];
        if (preg_match('/\[.*?\]/s', $prompt, $jsonMatch)) {
            $answersData = json_decode($jsonMatch[0], true);
            if (is_array($answersData)) {
                $answers = $answersData;
            }
        }
        
        // Analizar patrones en las respuestas
        $skillScores = [
            'liderazgo' => 0,
            'comunicacion' => 0,
            'trabajo_equipo' => 0,
            'adaptabilidad' => 0,
            'gestion_tiempo' => 0,
            'resolucion_problemas' => 0,
            'empatia' => 0,
            'creatividad' => 0,
            'responsabilidad' => 0,
            'autocontrol' => 0
        ];
        
        // Palabras clave para identificar habilidades
        $keywords = [
            'liderazgo' => ['lider', 'organizar', 'asumo', 'responsable', 'equipo', 'decisión', 'motivar'],
            'comunicacion' => ['comunicar', 'explicar', 'expresar', 'presentar', 'defender', 'argumento', 'diálogo'],
            'trabajo_equipo' => ['equipo', 'colaborar', 'grupo', 'compañero', 'juntos', 'consenso'],
            'adaptabilidad' => ['adaptar', 'cambiar', 'flexible', 'improvisar', 'ajustar', 'nuevo'],
            'gestion_tiempo' => ['tiempo', 'organizar', 'priorizar', 'planificar', 'agenda', 'urgente'],
            'resolucion_problemas' => ['problema', 'solución', 'resolver', 'analizar', 'alternativa', 'estrategia'],
            'empatia' => ['escuchar', 'entender', 'compañero', 'ayudar', 'apoyo', 'comprender'],
            'creatividad' => ['idea', 'innovar', 'creativo', 'propuesta', 'nuevo enfoque', 'original'],
            'responsabilidad' => ['responsable', 'compromiso', 'cumplir', 'asumir', 'entregar', 'calidad'],
            'autocontrol' => ['calma', 'control', 'paciencia', 'tranquilo', 'serenidad', 'equilibrio']
        ];
        
        // Analizar cada respuesta
        foreach ($answers as $answer) {
            if (is_array($answer)) {
                $answerText = strtolower(($answer['answer'] ?? '') . ' ' . ($answer['question'] ?? ''));
                $score = $answer['score'] ?? 0;
                
                foreach ($keywords as $skill => $keyWords) {
                    foreach ($keyWords as $keyword) {
                        if (strpos($answerText, $keyword) !== false) {
                            $skillScores[$skill] += $score;
                            break;
                        }
                    }
                }
            }
        }
        
        // Si no hay respuestas analizadas, usar puntaje total
        if (empty($answers) && $totalScore) {
            if ($totalScore > 23) { // Nivel alto (24-30)
                $skillScores['liderazgo'] = rand(8, 12);
                $skillScores['comunicacion'] = rand(7, 11);
                $skillScores['trabajo_equipo'] = rand(5, 9);
            } elseif ($totalScore > 16) { // Nivel medio (17-23)
                $skillScores['trabajo_equipo'] = rand(7, 10);
                $skillScores['adaptabilidad'] = rand(6, 9);
                $skillScores['comunicacion'] = rand(5, 8);
            } else { // Nivel bajo (10-16)
                $skillScores['adaptabilidad'] = rand(4, 7);
                $skillScores['responsabilidad'] = rand(3, 6);
            }
        }
        
        // Determinar habilidad más fuerte y más débil
        arsort($skillScores);
        $sortedSkills = array_keys($skillScores);
        $strongSkillKey = $sortedSkills[0] ?? 'comunicacion';
        $weakSkillKey = end($sortedSkills) ?: 'trabajo_equipo';
        
        // Mapeo de habilidades a nombres legibles
        $skillNames = [
            'liderazgo' => ['Liderazgo', 'Capacidad de liderazgo', 'Habilidades directivas', 'Liderazgo estratégico'],
            'comunicacion' => ['Comunicación efectiva', 'Expresión verbal', 'Comunicación asertiva', 'Habilidades comunicativas'],
            'trabajo_equipo' => ['Trabajo en equipo', 'Colaboración', 'Sinergia grupal', 'Espíritu colaborativo'],
            'adaptabilidad' => ['Adaptabilidad', 'Flexibilidad', 'Versatilidad', 'Capacidad de adaptación'],
            'gestion_tiempo' => ['Gestión del tiempo', 'Organización temporal', 'Planificación', 'Administración del tiempo'],
            'resolucion_problemas' => ['Resolución de problemas', 'Pensamiento crítico', 'Análisis de situaciones', 'Solución de conflictos'],
            'empatia' => ['Empatía', 'Inteligencia emocional', 'Comprensión interpersonal', 'Sensibilidad social'],
            'creatividad' => ['Creatividad', 'Innovación', 'Pensamiento creativo', 'Originalidad'],
            'responsabilidad' => ['Responsabilidad', 'Compromiso', 'Confiabilidad', 'Seriedad profesional'],
            'autocontrol' => ['Autocontrol', 'Gestión emocional', 'Equilibrio personal', 'Control de impulsos']
        ];
        
        // Seleccionar nombres variados
        $strongSkill = $skillNames[$strongSkillKey][array_rand($skillNames[$strongSkillKey])] ?? 'Comunicación';
        $weakSkill = $skillNames[$weakSkillKey][array_rand($skillNames[$weakSkillKey])] ?? 'Trabajo en equipo';
        
        // Generar análisis variado
        if ($totalScore) {
            if ($totalScore > 23) { // Nivel alto
                $analysisVariants = [
                    "El perfil evidencia competencias sólidas en " . strtolower($strongSkill) . ". ",
                    "El análisis de las respuestas revela un perfil destacado, con fortalezas especialmente notables en " . strtolower($strongSkill) . ". ",
                    "Se identifica un perfil profesional con competencias destacadas en " . strtolower($strongSkill) . ". "
                ];
                $midTexts = [
                    "Las respuestas demuestran capacidad para tomar decisiones informadas y guiar equipos hacia objetivos comunes. ",
                    "Se observa una tendencia hacia la proactividad y la toma de iniciativa en situaciones complejas. ",
                    "El perfil muestra habilidades para influir positivamente y generar resultados de alto impacto. "
                ];
                $weakTexts = [
                    "Sin embargo, se identifica una oportunidad de crecimiento en " . strtolower($weakSkill) . ", área que puede potenciarse mediante desarrollo específico. ",
                    "Se recomienda enfocar esfuerzos en fortalecer " . strtolower($weakSkill) . " para lograr un perfil aún más completo. ",
                    "El área de " . strtolower($weakSkill) . " presenta potencial de mejora que, al desarrollarse, complementará las fortalezas existentes. "
                ];
            } elseif ($totalScore > 16) { // Nivel medio
                $analysisVariants = [
                    "El perfil muestra un desarrollo equilibrado con fortalezas en " . strtolower($strongSkill) . ". ",
                    "El análisis indica que sobresale la capacidad en " . strtolower($strongSkill) . ". ",
                    "Se observa un perfil con potencial y fortalezas en " . strtolower($strongSkill) . ". "
                ];
                $midTexts = [
                    "Las respuestas reflejan una actitud colaborativa y disposición para trabajar en conjunto. ",
                    "Se evidencia capacidad para adaptarse a diferentes contextos y situaciones laborales. ",
                    "El perfil muestra equilibrio entre iniciativa personal y trabajo colaborativo. "
                ];
                $weakTexts = [
                    "Existe una oportunidad de mejora en " . strtolower($weakSkill) . ", que puede desarrollarse con práctica dirigida. ",
                    "Se sugiere trabajar en " . strtolower($weakSkill) . " para alcanzar un nivel de competencia más alto. ",
                    "El área de " . strtolower($weakSkill) . " requiere atención para fortalecer el perfil profesional general. "
                ];
            } else { // Nivel bajo
                $analysisVariants = [
                    "El perfil muestra áreas de desarrollo con potencial en " . strtolower($strongSkill) . ". ",
                    "El análisis revela fortalezas incipientes en " . strtolower($strongSkill) . ". ",
                    "Se identifica " . strtolower($strongSkill) . " como un área con potencial de crecimiento. "
                ];
                $midTexts = [
                    "Las respuestas indican disposición para aprender y mejorar continuamente. ",
                    "Se observa apertura al cambio y capacidad de adaptación a nuevas situaciones. ",
                    "El perfil muestra motivación para desarrollarse profesionalmente. "
                ];
                $weakTexts = [
                    "Es fundamental trabajar en " . strtolower($weakSkill) . " mediante programas de desarrollo estructurados. ",
                    "Se recomienda priorizar el desarrollo de " . strtolower($weakSkill) . " para fortalecer el perfil. ",
                    "El área de " . strtolower($weakSkill) . " requiere atención inmediata y desarrollo continuo. "
                ];
            }
        } else {
            $analysisVariants = [
                "Basado en el análisis de las respuestas, se identifica " . strtolower($strongSkill) . " como fortaleza principal. ",
                "El perfil muestra competencias destacadas en " . strtolower($strongSkill) . " según las respuestas proporcionadas. ",
                "Las respuestas revelan que " . strtolower($strongSkill) . " es una de las áreas más desarrolladas. "
            ];
            $midTexts = [
                "Se observa capacidad para interactuar efectivamente en diferentes contextos. ",
                "El perfil demuestra habilidades interpersonales y capacidad de trabajo colaborativo. ",
                "Las respuestas indican un enfoque equilibrado hacia el desarrollo profesional. "
            ];
            $weakTexts = [
                "Se identifica " . strtolower($weakSkill) . " como área de mejora que puede desarrollarse mediante práctica. ",
                "Existe potencial de crecimiento en " . strtolower($weakSkill) . " que puede potenciarse con dedicación. ",
                "El área de " . strtolower($weakSkill) . " presenta oportunidades de desarrollo profesional. "
            ];
        }
        
        $analysisText = $analysisVariants[array_rand($analysisVariants)];
        $analysisText .= $midTexts[array_rand($midTexts)];
        $analysisText .= $weakTexts[array_rand($weakTexts)];
        
        // Recomendaciones variadas
        $recommendations = [
            'liderazgo' => [
                "Participar en programas de liderazgo, asumir proyectos de mayor responsabilidad y buscar mentoría de líderes experimentados.",
                "Tomar cursos de gestión de equipos, liderar iniciativas voluntarias y practicar la delegación efectiva en proyectos.",
                "Desarrollar habilidades de liderazgo mediante la participación en grupos de trabajo y la toma de decisiones estratégicas."
            ],
            'comunicacion' => [
                "Practicar presentaciones en público, participar en talleres de comunicación asertiva y buscar feedback constante sobre el estilo comunicativo.",
                "Tomar cursos de oratoria, practicar la escucha activa y desarrollar habilidades de negociación y persuasión.",
                "Participar en grupos de debate, mejorar la expresión escrita y verbal, y practicar la comunicación en diferentes contextos."
            ],
            'trabajo_equipo' => [
                "Involucrarse en proyectos colaborativos, participar en actividades grupales y desarrollar habilidades de mediación y consenso.",
                "Unirse a equipos multidisciplinarios, practicar la colaboración activa y aprender técnicas de trabajo en grupo efectivo.",
                "Participar en talleres de team building, desarrollar habilidades de coordinación y practicar la sinergia grupal."
            ],
            'adaptabilidad' => [
                "Buscar experiencias en diferentes contextos, enfrentar nuevos desafíos regularmente y desarrollar mentalidad de crecimiento.",
                "Exponerse a situaciones cambiantes, practicar la flexibilidad mental y desarrollar capacidad de respuesta rápida.",
                "Participar en proyectos diversos, adaptarse a diferentes culturas organizacionales y mantener apertura al cambio."
            ],
            'gestion_tiempo' => [
                "Aprender técnicas de planificación como Pomodoro o GTD, establecer prioridades claras y usar herramientas de organización.",
                "Tomar cursos de productividad, desarrollar sistemas de organización personal y practicar la gestión eficiente de tareas.",
                "Implementar calendarios y agendas estructuradas, aprender a decir 'no' cuando sea necesario y optimizar rutinas diarias."
            ],
            'resolucion_problemas' => [
                "Practicar análisis de casos complejos, desarrollar pensamiento crítico y aprender metodologías estructuradas de resolución.",
                "Participar en talleres de pensamiento estratégico, resolver problemas prácticos regularmente y desarrollar creatividad en soluciones.",
                "Estudiar casos de éxito, practicar el análisis de situaciones complejas y desarrollar habilidades de diagnóstico."
            ],
            'empatia' => [
                "Practicar la escucha activa, desarrollar inteligencia emocional y participar en actividades que requieran comprensión de otros.",
                "Tomar cursos de inteligencia emocional, practicar la perspectiva de otros y desarrollar habilidades de mediación.",
                "Participar en voluntariados, desarrollar habilidades de coaching y practicar la comprensión de diferentes puntos de vista."
            ],
            'creatividad' => [
                "Participar en sesiones de brainstorming, explorar diferentes perspectivas y desarrollar pensamiento lateral.",
                "Tomar cursos de innovación, practicar técnicas creativas y buscar inspiración en diferentes campos.",
                "Involucrarse en proyectos innovadores, experimentar con nuevas ideas y desarrollar capacidad de pensamiento original."
            ],
            'responsabilidad' => [
                "Asumir compromisos claros, cumplir plazos sistemáticamente y desarrollar cultura de accountability personal.",
                "Establecer metas medibles, crear sistemas de seguimiento y desarrollar disciplina en el cumplimiento de objetivos.",
                "Tomar responsabilidad de proyectos completos, desarrollar confiabilidad y practicar la entrega consistente de resultados."
            ],
            'autocontrol' => [
                "Practicar técnicas de mindfulness, desarrollar gestión emocional y aprender estrategias de regulación personal.",
                "Tomar cursos de inteligencia emocional, practicar meditación y desarrollar habilidades de autorregulación.",
                "Desarrollar técnicas de relajación, practicar el control de impulsos y aprender a manejar situaciones de presión."
            ]
        ];
        
        $recommendation = $recommendations[$weakSkillKey][array_rand($recommendations[$weakSkillKey])] ?? 
            "Participar en talleres especializados, buscar oportunidades de práctica y solicitar retroalimentación constante.";
        
        // Construir la respuesta
        $response = "Habilidad más fuerte: $strongSkill\n";
        $response .= "Habilidad más débil: $weakSkill\n\n";
        $response .= "Análisis: $analysisText\n\n";
        $response .= "Recomendación: $recommendation";
        
        return $response;
    }
}
?>

