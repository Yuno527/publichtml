<?php
require_once 'config.php';

// Configurar headers para mostrar HTML
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Estadísticas del Sistema AICROOM</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            font-size: 1.1em;
        }
        .section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .section h3 {
            color: #333;
            margin-top: 0;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        .table tr:hover {
            background: #f5f5f5;
        }
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
        }
        .badge-high {
            background: #d4edda;
            color: #155724;
        }
        .badge-medium {
            background: #fff3cd;
            color: #856404;
        }
        .badge-low {
            background: #f8d7da;
            color: #721c24;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        .danger { color: #dc3545; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>📊 Estadísticas del Sistema AICROOM</h1>
            <p>Panel de análisis y monitoreo de evaluaciones de habilidades blandas</p>
        </div>
        
        <div class='content'>";

try {
    $pdo = getConnection();
    
    // Obtener estadísticas generales
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_evaluaciones,
            AVG(r.puntaje_total) as promedio_puntaje,
            COUNT(CASE WHEN r.resultado_final = 'Nivel alto' THEN 1 END) as nivel_alto,
            COUNT(CASE WHEN r.resultado_final = 'Nivel medio' THEN 1 END) as nivel_medio,
            COUNT(CASE WHEN r.resultado_final = 'Nivel bajo' THEN 1 END) as nivel_bajo,
            MIN(r.fecha_registro) as primera_evaluacion,
            MAX(r.fecha_registro) as ultima_evaluacion
        FROM tbl_resultados r
        JOIN tbl_historial h ON r.Id_historiaLFK = h.Id_historial
        WHERE h.estado = 'Completado'
    ");
    $stmt->execute();
    $stats = $stmt->fetch();
    
    // Obtener total de usuarios
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_usuarios FROM tbl_usuario");
    $stmt->execute();
    $userStats = $stmt->fetch();
    
    // Obtener evaluaciones recientes
    $stmt = $pdo->prepare("
        SELECT 
            u.nombre,
            u.correo,
            r.puntaje_total,
            r.resultado_final,
            r.fecha_registro
        FROM tbl_resultados r
        JOIN tbl_historial h ON r.Id_historiaLFK = h.Id_historial
        JOIN tbl_usuario u ON h.Id_UsuarioFK = u.Id_Usuario
        WHERE h.estado = 'Completado'
        ORDER BY r.fecha_registro DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recentEvaluations = $stmt->fetchAll();
    
    // Calcular porcentajes
    $total = $stats['total_evaluaciones'];
    $porcentajeAlto = $total > 0 ? round(($stats['nivel_alto'] / $total) * 100, 1) : 0;
    $porcentajeMedio = $total > 0 ? round(($stats['nivel_medio'] / $total) * 100, 1) : 0;
    $porcentajeBajo = $total > 0 ? round(($stats['nivel_bajo'] / $total) * 100, 1) : 0;
    
    echo "<div class='stats-grid'>
        <div class='stat-card'>
            <div class='stat-number'>{$stats['total_evaluaciones']}</div>
            <div class='stat-label'>Total Evaluaciones</div>
        </div>
        <div class='stat-card'>
            <div class='stat-number'>" . round($stats['promedio_puntaje'], 1) . "</div>
            <div class='stat-label'>Puntaje Promedio</div>
        </div>
        <div class='stat-card'>
            <div class='stat-number'>{$userStats['total_usuarios']}</div>
            <div class='stat-label'>Usuarios Registrados</div>
        </div>
        <div class='stat-card'>
            <div class='stat-number'>" . ($total > 0 ? round($total / $userStats['total_usuarios'], 1) : 0) . "</div>
            <div class='stat-label'>Evaluaciones por Usuario</div>
        </div>
    </div>";
    
    // Distribución por niveles
    echo "<div class='section'>
        <h3>📈 Distribución por Niveles</h3>
        <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;'>
            <div class='stat-card'>
                <div class='stat-number'>{$stats['nivel_alto']}</div>
                <div class='stat-label'>Nivel Alto ({$porcentajeAlto}%)</div>
                <div class='progress-bar'>
                    <div class='progress-fill' style='width: {$porcentajeAlto}%'></div>
                </div>
            </div>
            <div class='stat-card'>
                <div class='stat-number'>{$stats['nivel_medio']}</div>
                <div class='stat-label'>Nivel Medio ({$porcentajeMedio}%)</div>
                <div class='progress-bar'>
                    <div class='progress-fill' style='width: {$porcentajeMedio}%'></div>
                </div>
            </div>
            <div class='stat-card'>
                <div class='stat-number'>{$stats['nivel_bajo']}</div>
                <div class='stat-label'>Nivel Bajo ({$porcentajeBajo}%)</div>
                <div class='progress-bar'>
                    <div class='progress-fill' style='width: {$porcentajeBajo}%'></div>
                </div>
            </div>
        </div>
    </div>";
    
    // Evaluaciones recientes
    echo "<div class='section'>
        <h3>🕒 Evaluaciones Recientes</h3>";
    
    if (count($recentEvaluations) > 0) {
        echo "<table class='table'>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Puntaje</th>
                    <th>Resultado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach ($recentEvaluations as $eval) {
            $badgeClass = '';
            switch($eval['resultado_final']) {
                case 'Nivel alto': $badgeClass = 'badge-high'; break;
                case 'Nivel medio': $badgeClass = 'badge-medium'; break;
                case 'Nivel bajo': $badgeClass = 'badge-low'; break;
            }
            
            echo "<tr>
                <td>{$eval['nombre']}</td>
                <td>{$eval['correo']}</td>
                <td><strong>{$eval['puntaje_total']}/60</strong></td>
                <td><span class='badge {$badgeClass}'>{$eval['resultado_final']}</span></td>
                <td>" . date('d/m/Y H:i', strtotime($eval['fecha_registro'])) . "</td>
            </tr>";
        }
        
        echo "</tbody></table>";
    } else {
        echo "<p style='text-align: center; color: #666;'>No hay evaluaciones registradas aún.</p>";
    }
    
    echo "</div>";
    
    // Información del sistema
    echo "<div class='section'>
        <h3>⚙️ Información del Sistema</h3>
        <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;'>
            <div>
                <h4>📅 Período de Actividad</h4>
                <p><strong>Primera evaluación:</strong> " . ($stats['primera_evaluacion'] ? date('d/m/Y H:i', strtotime($stats['primera_evaluacion'])) : 'N/A') . "</p>
                <p><strong>Última evaluación:</strong> " . ($stats['ultima_evaluacion'] ? date('d/m/Y H:i', strtotime($stats['ultima_evaluacion'])) : 'N/A') . "</p>
            </div>
            <div>
                <h4>🎯 Rendimiento</h4>
                <p><strong>Promedio general:</strong> " . round($stats['promedio_puntaje'], 1) . "/60 puntos</p>";
    
    // Calcular evaluaciones por día
    $dias = 0;
    if ($total > 0 && $stats['primera_evaluacion'] && $stats['ultima_evaluacion']) {
        $dias = max(1, (strtotime($stats['ultima_evaluacion']) - strtotime($stats['primera_evaluacion'])) / 86400);
    }
    $evalsPorDia = $dias > 0 ? round($total / $dias, 1) : 0;
    
    echo "<p><strong>Evaluaciones por día:</strong> {$evalsPorDia}</p>
            </div>
        </div>
    </div>";
    
    // Estado del sistema
    echo "<div class='section'>
        <h3>✅ Estado del Sistema</h3>
        <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;'>
            <div style='text-align: center; padding: 15px; background: #d4edda; border-radius: 8px;'>
                <div style='font-size: 2em; color: #155724;'>✅</div>
                <div style='color: #155724; font-weight: bold;'>Base de Datos</div>
                <div style='color: #155724;'>Conectada</div>
            </div>
            <div style='text-align: center; padding: 15px; background: #d4edda; border-radius: 8px;'>
                <div style='font-size: 2em; color: #155724;'>✅</div>
                <div style='color: #155724; font-weight: bold;'>Webhook Handler</div>
                <div style='color: #155724;'>Activo</div>
            </div>
            <div style='text-align: center; padding: 15px; background: #d4edda; border-radius: 8px;'>
                <div style='font-size: 2em; color: #155724;'>✅</div>
                <div style='color: #155724; font-weight: bold;'>Panel Admin</div>
                <div style='color: #155724;'>Funcionando</div>
            </div>
            <div style='text-align: center; padding: 15px; background: #d4edda; border-radius: 8px;'>
                <div style='font-size: 2em; color: #155724;'>✅</div>
                <div style='color: #155724; font-weight: bold;'>APIs</div>
                <div style='color: #155724;'>Operativas</div>
            </div>
        </div>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='section' style='background: #f8d7da; border-left-color: #dc3545;'>
        <h3 style='color: #721c24;'>❌ Error del Sistema</h3>
        <p style='color: #721c24;'>" . $e->getMessage() . "</p>
    </div>";
}

echo "</div>
        
        <div class='footer'>
            <p><strong>Sistema AICROOM</strong> - Panel de Estadísticas</p>
            <p>Desarrollado por Breiner | breiner@aicroom.com</p>
            <p>Última actualización: " . date('d/m/Y H:i:s') . "</p>
        </div>
    </div>
</body>
</html>";
?> 