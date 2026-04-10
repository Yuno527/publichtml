<?php
require_once 'user_session.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados - AICROOM</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?php generateUserScript(); ?>
    <style>
        .results-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .results-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .results-header h1 {
            margin: 0 0 10px 0;
            font-size: 2.5em;
        }
        
        .results-header p {
            margin: 0;
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
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
        
        .results-table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-header h2 {
            margin: 0;
            color: #333;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .results-table th,
        .results-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .analysis-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #666;
            font-size: 0.9em;
            cursor: help;
            position: relative;
        }
        
        .analysis-preview:hover {
            color: #667eea;
        }
        
        @media (max-width: 768px) {
            .analysis-preview {
                max-width: 150px;
                font-size: 0.85em;
            }
        }
        
        .results-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .results-table tr:hover {
            background: #f8f9fa;
        }
        
        .result-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
        }
        
        .result-alto {
            background: #d4edda;
            color: #155724;
        }
        
        .result-medio {
            background: #fff3cd;
            color: #856404;
        }
        
        .result-bajo {
            background: #f8d7da;
            color: #721c24;
        }
        
        .view-details-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }
        
        .view-details-btn:hover {
            background: #5a6fd8;
            transform: translateY(-1px);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .details-table th,
        .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .details-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .loading {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        .no-results {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        .filter-btn {
            background: #f8f9fa;
            color: #333;
            border: 1px solid #d1d5db;
            padding: 8px 20px;
            border-radius: 20px;
            margin-right: 10px;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            outline: none;
            box-shadow: 0 2px 6px rgba(102,126,234,0.05);
        }
        .filter-btn:last-child {
            margin-right: 0;
        }
        .filter-btn.active, .filter-btn:focus {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: 1px solid #667eea;
            box-shadow: 0 4px 16px rgba(102,126,234,0.15);
        }
        .filter-btn:hover:not(.active) {
            background: #e9ecef;
            color: #333;
        }
        #filterButtons {
            margin-bottom: 10px;
            text-align: right;
        }
        @media (max-width: 768px) {
            .results-table {
                font-size: 0.9em;
            }
            
            .results-table th,
            .results-table td {
                padding: 10px 8px;
            }
            
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
            #filterButtons {
                text-align: left;
                margin-top: 10px;
            }
            .filter-btn {
                padding: 8px 10px;
                font-size: 0.95em;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body <?php echo generateUserAttributes(); ?> class="<?php echo generateAdminClass(); ?>">
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <a href="index.php" class="logo">
                    <img src="images/logo.jpeg" alt="Logo AICROOM" style="height: 40px; margin-right: 10px;">
                    AICROOM
                </a>
                
                <!-- Menú desktop -->
                <ul class="nav-menu">
                    <li class="nav-item"><a href="index.html" class="nav-link">Inicio</a></li>
                    <li class="nav-item"><a href="empresa.html" class="nav-link">Sobre nosotros</a></li>
                    <li class="nav-item"><a href="contacto.html" class="nav-link">Contacto</a></li>
                    <li class="nav-item admin-only" id="results-desktop"><a href="resultados.php" class="nav-link">Resultados</a></li>
                </ul>
                
                <!-- Menú usuario desktop -->
                <div class="user-menu">
                    <div class="user-name-clickable" id="userDropdown">
                        <span class="user-name">(usuario)</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                        <div class="user-dropdown" id="userDropdownMenu">
                            <div class="dropdown-content">
                                <a href="logout.php" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Cerrar sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botón hamburguesa -->
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            
            <!-- Menú móvil -->
            <div class="mobile-menu" id="mobileMenu">
                <ul class="mobile-nav-menu">
                    <li class="mobile-nav-item"><a href="index.html" class="mobile-nav-link">Inicio</a></li>
                    <li class="mobile-nav-item"><a href="empresa.html" class="mobile-nav-link">Sobre nosotros</a></li>
                    <li class="mobile-nav-item"><a href="contacto.html" class="mobile-nav-link">Contacto</a></li>
                    <li class="mobile-nav-item admin-only" id="results-mobile"><a href="resultados.php" class="mobile-nav-link">Resultados</a></li>
                </ul>
                <div class="mobile-user-section">
                    <div class="mobile-user-name">(usuario)</div>
                    <button class="mobile-logout-btn" onclick="window.location.href='logout.php'">Cerrar sesión</button>
                </div>
            </div>
            
            <!-- Overlay para cerrar menú -->
            <div class="overlay" id="overlay"></div>
        </nav>
    </header>

    <div class="results-container">
        <div class="results-header">
            <h1><i class="fas fa-chart-bar"></i> Panel de Resultados</h1>
            <p>Gestión y análisis de evaluaciones de habilidades blandas</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="totalTests">0</div>
                <div class="stat-label">Total de Evaluaciones</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="avgScore">0</div>
                <div class="stat-label">Puntaje Promedio</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="highLevel">0</div>
                <div class="stat-label">Nivel Alto</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="mediumLevel">0</div>
                <div class="stat-label">Nivel Medio</div>
            </div>
        </div>

        <div class="results-table-container">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Resultados Detallados</h2>
                <div id="filterButtons">
                    <button class="filter-btn" data-filter="all">Todos</button>
                    <button class="filter-btn" data-filter="Nivel alto">Alto</button>
                    <button class="filter-btn" data-filter="Nivel medio">Medio</button>
                    <button class="filter-btn" data-filter="Nivel bajo">Bajo</button>
                </div>
            </div>
            <div id="resultsContent">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Cargando resultados...
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para detalles -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Detalles de la Evaluación</h2>
            <div id="modalContent"></div>
        </div>
    </div>

    <script src="main.js"></script>
    <script src="nav-mobile.js"></script>
    <script src="user-menu.js"></script>
    <script src="resultados.js"></script>
      <!-- Versión local: burbuja que abre chatbot.html en un iframe -->
    <script src="bubble_chatbot_local.js"></script>
    <script>
        // Verificación adicional después de cargar todos los scripts
        window.addEventListener('load', function() {
            setTimeout(function() {
                console.log('=== VERIFICACIÓN FINAL DEL CHATBOT ===');
                const btn = document.getElementById('bubble-chatbot-button');
                if (!btn) {
                  console.error('❌ El botón del chatbot local NO se creó');
                } else {
                  console.log('✅ El botón del chatbot local existe');
                }
            }, 1000);
        });
    </script>
</body>
</html> 