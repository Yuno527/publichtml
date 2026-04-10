// Chatbot personalizado para AICROOM
class CustomChatbot {
    constructor() {
        this.currentQuestion = 0;
        this.answers = [];
        this.totalScore = 0;
        this.isCompleted = false;
        this.userId = null;
        this.testAlreadyCompleted = false; // Rastrear si el usuario ya completó el test

        // Banco de 20 preguntas con sus opciones y puntajes
        this.allQuestions = [
            {
                question: "Cuando un grupo necesita dirección para avanzar, ¿cómo sueles contribuir?",
                options: [
                    { text: "Espero instrucciones claras antes de actuar.", score: 1 },
                    { text: "Comparto sugerencias cuando es necesario.", score: 2 },
                    { text: "Tomo iniciativa y ayudo a organizar las tareas del grupo.", score: 3 }
                ]
            },
            {
                question: "Si recibes una tarea nueva que requiere habilidades desconocidas, ¿qué haces primero?",
                options: [
                    { text: "Evito comenzar hasta sentirme seguro.", score: 1 },
                    { text: "Busco referencias y ayuda para entender lo básico.", score: 2 },
                    { text: "Investigo, practico y pido retroalimentación para aprender rápido.", score: 3 }
                ]
            },
            {
                question: "Cuando alguien cuestiona tu idea en una reunión, ¿cómo respondes?",
                options: [
                    { text: "Evito discutir y dejo el tema atrás.", score: 1 },
                    { text: "Aclaro mi punto para evitar malentendidos.", score: 2 },
                    { text: "Defiendo mi postura con argumentos y apertura al diálogo.", score: 3 }
                ]
            },
            {
                question: "Si notas que el equipo se apresura y podría cometer un error importante, ¿qué haces?",
                options: [
                    { text: "Sigo la decisión del equipo para no retrasar.", score: 1 },
                    { text: "Menciono mi duda, pero dejo que el equipo decida.", score: 2 },
                    { text: "Presento los riesgos y propongo revisar antes de avanzar.", score: 3 }
                ]
            },
            {
                question: "Cuando cometes un error que impacta un proyecto, ¿cómo actúas?",
                options: [
                    { text: "Intento evitar discutirlo para no generar problemas.", score: 1 },
                    { text: "Informo del error y ayudo en lo posible.", score: 2 },
                    { text: "Asumo total responsabilidad y planteo acciones correctivas.", score: 3 }
                ]
            },
            {
                question: "En una reunión con poca participación, ¿cómo contribuyes?",
                options: [
                    { text: "Me mantengo en silencio esperando instrucciones.", score: 1 },
                    { text: "Comparto alguna idea si el ambiente lo permite.", score: 2 },
                    { text: "Propongo preguntas o dinámicas para estimular la participación.", score: 3 }
                ]
            },
            {
                question: "Cuando un cliente o usuario se comunica con tono tenso, ¿cómo manejas la situación?",
                options: [
                    { text: "Respondo con el mismo nivel de tensión.", score: 1 },
                    { text: "Intento mantener la calma, pero me pongo a la defensiva.", score: 2 },
                    { text: "Mantengo la calma, escucho y busco comprender la causa.", score: 3 }
                ]
            },
            {
                question: "Cuando debes organizar varias tareas simultáneamente, ¿cómo gestionas tu tiempo?",
                options: [
                    { text: "Trabajo en lo primero que me llama la atención.", score: 1 },
                    { text: "Organizo parcialmente y ajusto sobre la marcha.", score: 2 },
                    { text: "Prioritizo, planifico y asigno tiempos para cada actividad.", score: 3 }
                ]
            },
            {
                question: "Si notas tensión o desacuerdo entre miembros del equipo, ¿qué acostumbras hacer?",
                options: [
                    { text: "Me mantengo al margen para evitar problemas.", score: 1 },
                    { text: "Intento mediar si veo espacio para ello.", score: 2 },
                    { text: "Escucho ambas partes y promuevo un diálogo constructivo.", score: 3 }
                ]
            },
            {
                question: "Cuando te piden entregar algo con mucha prisa, comprometiendo calidad, ¿qué haces?",
                options: [
                    { text: "Entrego rápido aunque no sea lo mejor posible.", score: 1 },
                    { text: "Intento balancear velocidad y calidad.", score: 2 },
                    { text: "Explico las limitaciones y propongo alternativas viables.", score: 3 }
                ]
            },
            {
                question: "Cuando recibes una crítica que no esperabas, ¿cómo la manejas?",
                options: [
                    { text: "Me molesta y me cuesta continuar.", score: 1 },
                    { text: "Me incomoda, pero trato de reflexionar.", score: 2 },
                    { text: "La analizo con apertura para mejorar mi desempeño.", score: 3 }
                ]
            },
            {
                question: "Si tu equipo no completa su parte del trabajo, afectando tus tareas, ¿cómo reaccionas?",
                options: [
                    { text: "Me molesto pero prefiero no decir nada.", score: 1 },
                    { text: "Hago lo que falta para cumplir.", score: 2 },
                    { text: "Converso con el equipo para corregir el rumbo juntos.", score: 3 }
                ]
            },
            {
                question: "En ambientes de alta presión o cambios frecuentes, ¿cómo respondes?",
                options: [
                    { text: "Mi rendimiento disminuye notablemente.", score: 1 },
                    { text: "Me adapto gradualmente.", score: 2 },
                    { text: "Me ajusto rápido y busco soluciones prácticas.", score: 3 }
                ]
            },
            {
                question: "Si debes delegar trabajo, ¿cómo lo haces?",
                options: [
                    { text: "Prefiero hacerlo yo mismo para evitar errores.", score: 1 },
                    { text: "Delego, pero reviso cada detalle.", score: 2 },
                    { text: "Asigno tareas de forma clara y doy seguimiento oportuno.", score: 3 }
                ]
            },
            {
                question: "Cuando recibes tareas repetitivas, ¿cómo reaccionas?",
                options: [
                    { text: "Las hago sin motivación.", score: 1 },
                    { text: "Busco alternar tareas cuando es posible.", score: 2 },
                    { text: "Propongo mejoras o formas de hacerlas más eficientes.", score: 3 }
                ]
            },
            {
                question: "Al trabajar con personas con estilos muy diferentes al tuyo, ¿cómo actúas?",
                options: [
                    { text: "Evito interactuar demasiado.", score: 1 },
                    { text: "Me adapto progresivamente.", score: 2 },
                    { text: "Valoro la diversidad y la uso para enriquecer el trabajo.", score: 3 }
                ]
            },
            {
                question: "Si un compañero atraviesa un momento personal difícil, ¿cómo sueles responder?",
                options: [
                    { text: "Prefiero mantener distancia.", score: 1 },
                    { text: "Le ofrezco apoyo si me lo pide.", score: 2 },
                    { text: "Me acerco, escucho y me muestro disponible.", score: 3 }
                ]
            },
            {
                question: "Cuando necesitas aprender una habilidad rápidamente, ¿qué haces?",
                options: [
                    { text: "Me cuesta iniciar y lo pospongo.", score: 1 },
                    { text: "Busco ayuda y practico cuando puedo.", score: 2 },
                    { text: "Creo un plan de estudio y practico de manera intensiva.", score: 3 }
                ]
            },
            {
                question: "Si debes trabajar con alguien con quien tuviste conflictos previos, ¿cómo procedes?",
                options: [
                    { text: "Limito al mínimo la interacción.", score: 1 },
                    { text: "Manejo la relación con respeto y distancia.", score: 2 },
                    { text: "Intento reconstruir la relación profesionalmente.", score: 3 }
                ]
            },
            {
                question: "Cuando hay un cambio inesperado de última hora, ¿cómo reaccionas?",
                options: [
                    { text: "Me frustro y me cuesta ajustarme.", score: 1 },
                    { text: "Me adapto aunque no me agrade.", score: 2 },
                    { text: "Reorganizo mis planes rápidamente y sigo adelante.", score: 3 }
                ]
            }
        ];

        // Seleccionar 10 preguntas aleatorias para esta sesión
        this.questions = this.shuffleArray([...this.allQuestions]).slice(0, 10);

        this.init();
    }

    init() {
        this.checkUserStatus();
        this.createChatbotInterface();
        this.showWelcomeMessage();
    }

    // Verificar si el usuario está logueado y si ya completó el test
    async checkUserStatus() {
        try {
            const response = await fetch('get_user_status.php');
            const data = await response.json();

            if (data.logged_in) {
                this.userId = data.user_id;
                this.userEmail = data.user_email;
                this.userName = data.user_name;

                // Verificar si ya completó el test
                const testResponse = await fetch('check_test_completion.php');
                const testData = await testResponse.json();

                if (testData.completed) {
                    this.testAlreadyCompleted = true;
                    this.showAlreadyCompletedMessage();
                } else {
                    this.testAlreadyCompleted = false;
                    // Mostrar mensaje de bienvenida solo si no ha completado el test
                    this.showWelcomeMessage();
                }
            } else {
                this.showLoginRequiredMessage();
            }
        } catch (error) {
            console.error('Error verificando estado del usuario:', error);
            this.showLoginRequiredMessage();
        }
    }

    // Crear la interfaz del chatbot
    createChatbotInterface() {
        const chatbotContainer = document.createElement('div');
        chatbotContainer.id = 'custom-chatbot';
        chatbotContainer.innerHTML = `
            <div class="chatbot-header">
                <div class="chatbot-title">
                    <i class="fas fa-robot"></i>
                    <span>Evaluación de Habilidades Blandas</span>
                </div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <div class="question-counter" id="questionCounter">0/20</div>
                </div>
            </div>
            
            <div class="chatbot-content">
                <div class="welcome-message" id="welcomeMessage">
                    <div class="message-content">
                        <h3>¡Hola! Bienvenido/a a tu evaluación de habilidades blandas.</h3>
                        <p>Esta prueba consta de 10 preguntas diseñadas para conocer cómo enfrentas situaciones reales en tu entorno laboral o académico.</p>
                        <p>Responde con sinceridad. No hay respuestas correctas o incorrectas, cada respuesta refleja tu estilo personal.</p>
                        <p>El test tomará solo unos minutos.</p>
                        <p>Haz clic en las opciones que más se adapten a ti.</p>
                        <p>Cuando estés listo/a, haz clic en "Comenzar" para empezar.</p>
                        <p>¡Éxitos!</p>
                        <button class="start-btn" onclick="chatbot.startTest()">
                            <i class="fas fa-play"></i> Comenzar
                        </button>
                    </div>
                </div>
                
                <div class="question-container" id="questionContainer" style="display: none;">
                    <div class="timer" id="timer" style="font-weight:bold;color:#667eea;margin-bottom:10px;"></div>
                    <div class="question-text" id="questionText"></div>
                    <div class="options-container" id="optionsContainer"></div>
                </div>
                
                <div class="completion-message" id="completionMessage" style="display: none;">
                    <div class="message-content">
                        <h3>¡Gracias por completar la prueba!</h3>
                        <p>Tus respuestas han sido registradas exitosamente.</p>
                        <p>Nuestro equipo las revisará y las tendrá en cuenta para los procesos correspondientes.</p>
                        <p>Recuerda que toda la información proporcionada será tratada con confidencialidad.</p>
                        <p>Puedes cerrar esta ventana.</p>
                        <p>¡Te deseamos muchos éxitos!</p>
                        <button class="close-btn" onclick="chatbot.closeChatbot()">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
                
                <div class="analysis-section" id="analysisSection" style="display: none;">
                    <div class="analysis-content">
                        <!-- El contenido se llenará dinámicamente -->
                    </div>
                </div>
                
                <div class="login-required" id="loginRequired" style="display: none;">
                    <div class="message-content">
                        <h3>Acceso Requerido</h3>
                        <p>Para realizar la evaluación de habilidades blandas, necesitas iniciar sesión.</p>
                        <a href="login.html" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                    </div>
                </div>
                
                <div class="already-completed" id="alreadyCompleted" style="display: none;">
                    <div class="message-content">
                        <h3>Evaluación Completada</h3>
                        <p>Ya has completado la evaluación de habilidades blandas.</p>
                        <p>Gracias por tu participación.</p>
                        <button class="close-btn" onclick="chatbot.closeChatbot()">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(chatbotContainer);

        // Agregar estilos CSS
        this.addStyles();
    }

    // Agregar estilos CSS
    addStyles() {
        const style = document.createElement('style');
        style.textContent = `
            #custom-chatbot {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 450px;
                height: 650px;
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.15);
                z-index: 1000;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                border: 1px solid rgba(102, 126, 234, 0.1);
            }
            
            .chatbot-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: relative;
                overflow: hidden;
            }
            
            .chatbot-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
                opacity: 0.3;
            }
            
            .chatbot-title {
                display: flex;
                align-items: center;
                gap: 12px;
                font-weight: 700;
                font-size: 1.1em;
                position: relative;
                z-index: 1;
            }
            
            .chatbot-title i {
                font-size: 1.3em;
                animation: pulse 2s infinite;
            }
            
            .progress-container {
                display: flex;
                align-items: center;
                gap: 15px;
                flex: 1;
                margin: 0 20px;
                position: relative;
                z-index: 1;
            }
            
            .progress-bar {
                flex: 1;
                height: 8px;
                background: rgba(255,255,255,0.2);
                border-radius: 4px;
                overflow: hidden;
                position: relative;
            }
            
            .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #fff, #f0f8ff);
                width: 0%;
                transition: width 0.5s ease;
                border-radius: 4px;
                position: relative;
            }
            
            .progress-fill::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
                animation: shimmer 2s infinite;
            }
            
            .question-counter {
                font-size: 13px;
                font-weight: 600;
                background: rgba(255,255,255,0.2);
                padding: 4px 12px;
                border-radius: 15px;
                white-space: nowrap;
            }
            
            .chatbot-content {
                flex: 1;
                padding: 25px;
                overflow-y: auto;
                background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            }
            
            .chatbot-content::-webkit-scrollbar {
                width: 6px;
            }
            
            .chatbot-content::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }
            
            .chatbot-content::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 3px;
            }
            
            .chatbot-content::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }
            
            .message-content {
                text-align: center;
                padding: 20px 0;
            }
            
            .message-content h3 {
                color: #2c3e50;
                margin-bottom: 20px;
                font-size: 1.4em;
                font-weight: 700;
            }
            
            .message-content p {
                color: #5a6c7d;
                margin-bottom: 15px;
                line-height: 1.6;
                font-size: 1em;
            }
            
            .start-btn, .login-btn, .close-btn {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                padding: 15px 30px;
                border-radius: 30px;
                cursor: pointer;
                font-size: 15px;
                font-weight: 600;
                margin-top: 25px;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
                position: relative;
                overflow: hidden;
            }
            
            .start-btn::before, .login-btn::before, .close-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }
            
            .start-btn:hover::before, .login-btn:hover::before, .close-btn:hover::before {
                left: 100%;
            }
            
            .start-btn:hover, .login-btn:hover, .close-btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            }
            
            .question-text {
                font-size: 17px;
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 25px;
                line-height: 1.5;
                padding: 20px;
                background: white;
                border-radius: 15px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                border-left: 4px solid #667eea;
            }
            
            .options-container {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
            
            .option-btn {
                background: white;
                border: 2px solid #e9ecef;
                padding: 18px 20px;
                border-radius: 12px;
                cursor: pointer;
                text-align: left;
                transition: all 0.3s ease;
                font-size: 15px;
                line-height: 1.5;
                position: relative;
                overflow: hidden;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            }
            
            .option-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 4px;
                height: 100%;
                background: #667eea;
                transform: scaleY(0);
                transition: transform 0.3s ease;
            }
            
            .option-btn:hover {
                border-color: #667eea;
                background: #f8f9ff;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15);
            }
            
            .option-btn:hover::before {
                transform: scaleY(1);
            }
            
            .option-btn.selected {
                border-color: #667eea;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            }
            
            .option-btn.selected::before {
                transform: scaleY(1);
                background: white;
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            
            @keyframes shimmer {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }
            
            /* Estilos para la sección de análisis */
            .analysis-section {
                padding: 20px;
                overflow-y: auto;
                max-height: calc(650px - 80px);
            }
            
            .analysis-content {
                animation: fadeIn 0.5s ease-in;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .analysis-header {
                text-align: center;
                margin-bottom: 25px;
                padding-bottom: 20px;
                border-bottom: 2px solid #667eea;
            }
            
            .analysis-header h2 {
                color: #667eea;
                font-size: 1.5em;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }
            
            .analysis-header p {
                color: #666;
                font-size: 0.95em;
                margin-bottom: 15px;
            }
            
            .score-badge {
                display: inline-block;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 8px 20px;
                border-radius: 25px;
                font-size: 1em;
                font-weight: bold;
            }
            
            .analysis-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-bottom: 20px;
            }
            
            .analysis-card {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                border-radius: 12px;
                padding: 20px;
                text-align: center;
                transition: transform 0.3s ease;
            }
            
            .analysis-card:hover {
                transform: translateY(-3px);
            }
            
            .analysis-card.strong {
                background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            }
            
            .analysis-card.weak {
                background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            }
            
            .card-icon {
                font-size: 2.5em;
                margin-bottom: 10px;
                color: #667eea;
            }
            
            .card-title {
                font-size: 0.85em;
                color: #666;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 8px;
                font-weight: 600;
            }
            
            .card-content {
                font-size: 1.1em;
                color: #333;
                font-weight: bold;
            }
            
            .analysis-section-item {
                margin-bottom: 20px;
            }
            
            .section-title {
                font-size: 1.2em;
                color: #667eea;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 600;
            }
            
            .section-content {
                background: #f8f9fa;
                border-left: 4px solid #667eea;
                padding: 15px;
                border-radius: 8px;
                line-height: 1.7;
                color: #555;
                font-size: 0.95em;
            }
            
            .recommendation-box {
                background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
                border-radius: 12px;
                padding: 20px;
                border-left: 5px solid #f39c12;
            }
            
            .recommendation-box .section-title {
                color: #d35400;
            }
            
            .recommendation-box .section-content {
                background: rgba(255, 255, 255, 0.7);
                border-left: none;
                color: #333;
            }
            
            .analysis-actions {
                display: flex;
                justify-content: center;
                margin-top: 25px;
                padding-top: 20px;
                border-top: 2px solid #e9ecef;
            }
            
            .btn-home {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 12px 30px;
                border-radius: 25px;
                text-decoration: none;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            }
            
            .btn-home:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            }
            
            @media (max-width: 768px) {
                #custom-chatbot {
                    width: calc(100vw - 40px);
                    height: 600px;
                    bottom: 10px;
                    right: 20px;
                    border-radius: 15px;
                }
                
                .chatbot-header {
                    padding: 15px;
                }
                
                .chatbot-content {
                    padding: 20px;
                }
                
                .question-text {
                    font-size: 16px;
                    padding: 15px;
                }
                
                .option-btn {
                    padding: 15px 18px;
                    font-size: 14px;
                }
                
                .analysis-grid {
                    grid-template-columns: 1fr;
                }
                
                .analysis-header h2 {
                    font-size: 1.3em;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Mostrar mensaje de bienvenida
    showWelcomeMessage() {
        // Solo mostrar si el usuario no ha completado el test
        if (this.testAlreadyCompleted) {
            this.showAlreadyCompletedMessage();
            return;
        }
        
        document.getElementById('welcomeMessage').style.display = 'block';
        document.getElementById('questionContainer').style.display = 'none';
        document.getElementById('completionMessage').style.display = 'none';
        document.getElementById('loginRequired').style.display = 'none';
        document.getElementById('alreadyCompleted').style.display = 'none';
        
        // Asegurarse de que el botón "Comenzar" esté habilitado
        const startBtn = document.querySelector('.start-btn');
        if (startBtn) {
            startBtn.disabled = false;
            startBtn.style.opacity = '1';
            startBtn.style.cursor = 'pointer';
        }
    }

    // Mostrar mensaje de login requerido
    showLoginRequiredMessage() {
        document.getElementById('welcomeMessage').style.display = 'none';
        document.getElementById('questionContainer').style.display = 'none';
        document.getElementById('completionMessage').style.display = 'none';
        document.getElementById('loginRequired').style.display = 'block';
        document.getElementById('alreadyCompleted').style.display = 'none';
    }

    // Mostrar mensaje de ya completado
    showAlreadyCompletedMessage() {
        document.getElementById('welcomeMessage').style.display = 'none';
        document.getElementById('questionContainer').style.display = 'none';
        document.getElementById('completionMessage').style.display = 'none';
        document.getElementById('loginRequired').style.display = 'none';
        document.getElementById('alreadyCompleted').style.display = 'block';
    }

    // Iniciar el test
    async startTest() {
        // Verificar nuevamente si el usuario ya completó el test antes de iniciar
        if (this.testAlreadyCompleted) {
            this.showAlreadyCompletedMessage();
            return;
        }

        // Verificación adicional antes de iniciar
        try {
            const testResponse = await fetch('check_test_completion.php');
            const testData = await testResponse.json();

            if (testData.completed) {
                this.testAlreadyCompleted = true;
                this.showAlreadyCompletedMessage();
                return;
            }
        } catch (error) {
            console.error('Error verificando estado del test:', error);
            // Continuar con el test si hay error en la verificación
        }

        // Si todo está bien, iniciar el test
        document.getElementById('welcomeMessage').style.display = 'none';
        document.getElementById('questionContainer').style.display = 'block';
        this.showQuestion();
    }

    // Mostrar pregunta actual
    showQuestion() {
        if (this.currentQuestion >= this.questions.length) {
            this.completeTest();
            return;
        }
        // Limpiar cualquier temporizador anterior
        if (this.timerInterval) clearInterval(this.timerInterval);
        this.timeLeft = 35;
        document.getElementById('timer').textContent = `Tiempo restante: ${this.timeLeft}s`;
        this.timerInterval = setInterval(() => {
            this.timeLeft--;
            document.getElementById('timer').textContent = `Tiempo restante: ${this.timeLeft}s`;
            if (this.timeLeft <= 0) {
                clearInterval(this.timerInterval);
                this.selectOption({ text: 'Sin respuesta', score: 0, timeout: true });
            }
        }, 1000);

        const question = this.questions[this.currentQuestion];
        document.getElementById('questionText').textContent = question.question;

        // Randomizar las opciones
        const shuffledOptions = this.shuffleArray([...question.options]);

        const optionsContainer = document.getElementById('optionsContainer');
        optionsContainer.innerHTML = '';

        shuffledOptions.forEach((option, index) => {
            const button = document.createElement('button');
            button.className = 'option-btn';
            button.textContent = option.text;
            button.onclick = () => this.selectOption(option);
            optionsContainer.appendChild(button);
        });

        // Actualizar progreso
        this.updateProgress();
    }

    // Randomizar array
    shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }

    // Seleccionar opción
    selectOption(option) {
        if (this.timerInterval) clearInterval(this.timerInterval);
        // Guardar respuesta
        this.answers.push({
            question: this.questions[this.currentQuestion].question,
            answer: option.text,
            score: option.score
        });

        this.totalScore += option.score;
        this.currentQuestion++;

        // Mostrar siguiente pregunta
        setTimeout(() => {
            this.showQuestion();
        }, 300);
    }

    // Actualizar barra de progreso
    updateProgress() {
        const progress = (this.currentQuestion / this.questions.length) * 100;
        document.getElementById('progressFill').style.width = progress + '%';
        document.getElementById('questionCounter').textContent = `${this.currentQuestion}/${this.questions.length}`;
    }

    // Completar test
    async completeTest() {
        // Verificar si el usuario ya completó el test antes de guardar
        if (this.testAlreadyCompleted) {
            this.showAlreadyCompletedMessage();
            return;
        }

        // Verificación adicional antes de guardar
        try {
            const testResponse = await fetch('check_test_completion.php');
            const testData = await testResponse.json();

            if (testData.completed) {
                this.testAlreadyCompleted = true;
                this.showAlreadyCompletedMessage();
                return;
            }
        } catch (error) {
            console.error('Error verificando estado del test:', error);
            // Continuar con el guardado si hay error en la verificación
        }

        this.isCompleted = true;

        // Determinar resultado final
        // Rango de puntuación: 10-30 (10 preguntas × 1-3 puntos cada una)
        let resultFinal;
        if (this.totalScore <= 16) {
            resultFinal = "Nivel bajo";
        } else if (this.totalScore <= 23) {
            resultFinal = "Nivel medio";
        } else {
            resultFinal = "Nivel alto";
        }

        // Guardar en base de datos
        try {
            const response = await fetch('save_test_results.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    userId: this.userId,
                    answers: this.answers,
                    totalScore: this.totalScore,
                    resultFinal: resultFinal
                })
            });

            const data = await response.json();

            // Marcar el test como completado después de guardar exitosamente
            if (data.success || data.analysis) {
                this.testAlreadyCompleted = true;
            }

            // === Mostrar automáticamente el análisis de IA ===
            if (data.analysis) {
                // Guardar en localStorage para la página de análisis (por si se necesita)
                localStorage.setItem('aiAnalysis', data.analysis);
                localStorage.setItem('totalScore', this.totalScore);

                // Mostrar automáticamente la sección de análisis
                this.showAnalysisSection(data.analysis, this.totalScore);
            } else {
                // Si no hay análisis, mostrar mensaje de completado normal
                this.showCompletionMessage();
                this.addMessage(`
            <div style="padding:15px; border-radius:8px; background:#fff1f1; margin-top:10px;">
                ⚠ No se pudo generar el análisis de la IA.
            </div>
        `);
            }

        } catch (error) {
            console.error('Error guardando resultados:', error);
            this.showCompletionMessage();
        }

    }

    // Agregar mensaje al chatbot (método auxiliar)
    addMessage(htmlContent) {
        const completionMessage = document.getElementById('completionMessage');
        if (completionMessage) {
            const messageContent = completionMessage.querySelector('.message-content');
            if (messageContent) {
                // Crear un div para el mensaje adicional
                const additionalMessage = document.createElement('div');
                additionalMessage.className = 'additional-message';
                additionalMessage.innerHTML = htmlContent;
                messageContent.appendChild(additionalMessage);
            }
        }
    }

    // Parsear el análisis de la IA
    parseAnalysis(analysisText) {
        const result = {
            strongSkill: '',
            weakSkill: '',
            analysis: '',
            recommendation: ''
        };

        if (!analysisText) {
            return result;
        }

        // Buscar "Habilidad más fuerte:"
        const strongMatch = analysisText.match(/habilidad\s+más\s+fuerte[:\s]+([^\n]+)/i);
        if (strongMatch) {
            result.strongSkill = strongMatch[1].trim();
        }

        // Buscar "Habilidad más débil:" o "Habilidad a mejorar:"
        const weakMatch = analysisText.match(/habilidad\s+más\s+débil[:\s]+([^\n]+)/i) ||
            analysisText.match(/habilidad\s+a\s+mejorar[:\s]+([^\n]+)/i);
        if (weakMatch) {
            result.weakSkill = weakMatch[1].trim();
        }

        // Buscar "Análisis:"
        const analysisMatch = analysisText.match(/análisis[:\s]+([^\n]+(?:\n(?!recomendación)[^\n]+)*)/i);
        if (analysisMatch) {
            result.analysis = analysisMatch[1].trim();
        }

        // Buscar "Recomendación:"
        const recommendationMatch = analysisText.match(/recomendación[:\s]+(.+)/is);
        if (recommendationMatch) {
            result.recommendation = recommendationMatch[1].trim();
        }

        return result;
    }

    // Mostrar sección de análisis automáticamente
    showAnalysisSection(analysisText, totalScore) {
        // Ocultar todas las secciones
        document.getElementById('welcomeMessage').style.display = 'none';
        document.getElementById('questionContainer').style.display = 'none';
        document.getElementById('completionMessage').style.display = 'none';
        document.getElementById('loginRequired').style.display = 'none';
        document.getElementById('alreadyCompleted').style.display = 'none';

        // Parsear el análisis
        const parsed = this.parseAnalysis(analysisText);

        // Obtener el contenedor de análisis
        const analysisSection = document.getElementById('analysisSection');
        const analysisContent = analysisSection.querySelector('.analysis-content');

        // Construir el HTML del análisis
        analysisContent.innerHTML = `
            <div class="analysis-header">
                <h2><i class="fas fa-brain"></i> Análisis de Habilidades Blandas</h2>
                <p>Resultado de tu evaluación personalizada</p>
            </div>
            
            <div class="analysis-grid">
                <div class="analysis-card strong">
                    <div class="card-icon"><i class="fas fa-star"></i></div>
                    <div class="card-title">Habilidad Más Fuerte</div>
                    <div class="card-content">${parsed.strongSkill || 'No especificada'}</div>
                </div>
                
                <div class="analysis-card weak">
                    <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="card-title">Habilidad a Mejorar</div>
                    <div class="card-content">${parsed.weakSkill || 'No especificada'}</div>
                </div>
            </div>
            
            <div class="analysis-section-item">
                <div class="section-title">
                    <i class="fas fa-clipboard-list"></i> Análisis Profesional
                </div>
                <div class="section-content">
                    ${(parsed.analysis || analysisText).replace(/\n/g, '<br>')}
                </div>
            </div>
            
            <div class="analysis-section-item recommendation-box">
                <div class="section-title">
                    <i class="fas fa-lightbulb"></i> Recomendación
                </div>
                <div class="section-content">
                    ${(parsed.recommendation || 'Continúa trabajando en tu desarrollo personal y profesional.').replace(/\n/g, '<br>')}
                </div>
            </div>
            
            <div class="analysis-actions">
                <a href="index.html" class="btn-home">
                    <i class="fas fa-home"></i> Volver al Inicio
                </a>
            </div>
        `;

        // Mostrar la sección de análisis
        analysisSection.style.display = 'block';

        // Scroll suave hacia arriba
        analysisSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Mostrar mensaje de completado
    showCompletionMessage() {
        document.getElementById('welcomeMessage').style.display = 'none';
        document.getElementById('questionContainer').style.display = 'none';
        document.getElementById('completionMessage').style.display = 'block';
        document.getElementById('analysisSection').style.display = 'none';
        document.getElementById('loginRequired').style.display = 'none';
        document.getElementById('alreadyCompleted').style.display = 'none';
    }

    // Cerrar chatbot
    closeChatbot() {
        const chatbot = document.getElementById('custom-chatbot');
        if (chatbot) {
            chatbot.remove();
        }
    }
}

// Inicializar chatbot cuando el DOM esté listo
let chatbot;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        chatbot = new CustomChatbot();
    });
} else {
    chatbot = new CustomChatbot();
} 