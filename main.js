// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    // Verificar estado de autenticación solo si estamos en una página con navegación
    const navLinks = document.querySelector('.nav-links');
    if (navLinks) {
        checkAuthStatus();
    }
    
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', function() {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
        });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                
                // Close mobile menu if open
                if (window.innerWidth <= 768 && navLinks) {
                    navLinks.style.display = 'none';
                }
            }
        });
    });
    
    // Form submission
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would typically send the form data to a server
            // For this example, we'll just show an alert
            alert('¡Gracias por tu mensaje! Nos pondremos en contacto contigo pronto.');
            this.reset();
        });
    }
    
    // Dynamic year in footer
    const yearElement = document.querySelector('.footer-bottom p');
    if (yearElement) {
        const currentYear = new Date().getFullYear();
        yearElement.textContent = yearElement.textContent.replace('2025', currentYear);
    }
});

// Función para verificar el estado de autenticación
function checkAuthStatus() {
    fetch('get_user_status.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            updateNavigation(data);
        })
        .catch(error => {
            console.error('Error checking auth status:', error);
            // En caso de error, mostrar navegación para usuarios no logueados
            updateNavigation({ logged_in: false, user: null });
        });
}

// Función para actualizar la navegación según el estado de autenticación
function updateNavigation(authData) {
    const navLinks = document.querySelector('.nav-links');
    if (!navLinks) return;
    
    if (authData && authData.logged_in) {
        // Usuario logueado - mostrar opciones de usuario
        const userName = authData.user_name || 'Usuario';
        const userRole = authData.user_role;
        
        // Buscar enlaces de login/register y reemplazarlos
        const loginLink = navLinks.querySelector('a[href="login.html"]');
        const registerLink = navLinks.querySelector('a[href="register.html"]');
        
        if (loginLink) {
            loginLink.innerHTML = `<i class="fas fa-user"></i> ${userName}`;
            loginLink.href = '#';
            loginLink.onclick = function(e) {
                e.preventDefault();
                showUserMenu();
            };
        }
        
        if (registerLink) {
            registerLink.innerHTML = '<i class="fas fa-sign-out-alt"></i> Cerrar Sesión';
            registerLink.href = 'logout.php';
            registerLink.className = 'cta-button';
            registerLink.onclick = function(e) {
                e.preventDefault();
                if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                    window.location.href = 'logout.php';
                }
            };
        }
        
        // Agregar enlace de resultados solo para administradores
        if (userRole === 'admin') {
            // Buscar si ya existe el enlace de resultados
            let resultadosLink = navLinks.querySelector('a[href="resultados.php"]');
            if (!resultadosLink) {
                // Crear el enlace de resultados
                resultadosLink = document.createElement('li');
                resultadosLink.innerHTML = '<a href="resultados.php"><i class="fas fa-chart-bar"></i> Resultados</a>';
                navLinks.appendChild(resultadosLink);
            }
        }
        
        // Agregar botón de cerrar sesión
        let logoutLink = navLinks.querySelector('a[href="logout.php"]');
        if (!logoutLink) {
            logoutLink = document.createElement('li');
            logoutLink.innerHTML = '<a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>';
            navLinks.appendChild(logoutLink);
            
            // Agregar evento de confirmación al botón de cerrar sesión
            const logoutBtn = logoutLink.querySelector('.logout-btn');
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showLogoutModal();
            });
        }
    } else {
        // Usuario no logueado - mostrar enlaces de login/register
        const loginLink = navLinks.querySelector('a[href="login.html"]');
        const registerLink = navLinks.querySelector('a[href="register.html"]');
        
        if (loginLink) {
            loginLink.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
            loginLink.href = 'login.html';
            loginLink.onclick = null; // Remover onclick si existe
        }
        
        if (registerLink) {
            registerLink.innerHTML = '<i class="fas fa-user-plus"></i> Registrarse';
            registerLink.href = 'register.html';
            registerLink.className = 'cta-button';
        }
        
        // Remover enlace de resultados si existe
        const resultadosLink = navLinks.querySelector('a[href="resultados.php"]');
        if (resultadosLink) {
            resultadosLink.parentElement.remove();
        }
    }
}

// Función para mostrar menú de usuario
function showUserMenu() {
    // Aquí podrías implementar un dropdown con opciones del usuario
    alert('Menú de usuario - En desarrollo');
}

// Función para mostrar modal de confirmación de cierre de sesión
function showLogoutModal() {
    const modal = document.createElement('div');
    modal.className = 'logout-modal';
    modal.innerHTML = `
        <div class="logout-modal-content">
            <div class="logout-modal-header">
                <h3><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</h3>
            </div>
            <div class="logout-modal-body">
                <p>¿Estás seguro de que quieres cerrar sesión?</p>
                <p>Se cerrará tu sesión actual y serás redirigido al login.</p>
            </div>
            <div class="logout-modal-footer">
                <button class="btn-cancel" onclick="closeLogoutModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="btn-confirm" onclick="confirmLogout()">
                    <i class="fas fa-sign-out-alt"></i> Sí, Cerrar Sesión
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Cerrar modal con Escape
    const handleEscape = function(e) {
        if (e.key === 'Escape') {
            closeLogoutModal();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
    
    // Cerrar modal haciendo clic fuera
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeLogoutModal();
        }
    });
    
    // Agregar estilos al modal
    const style = document.createElement('style');
    style.textContent = `
        .logout-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
        }
        
        .logout-modal-content {
            background: white;
            border-radius: 12px;
            padding: 0;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
        }
        
        .logout-modal-header {
            background: #e74c3c;
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }
        
        .logout-modal-header h3 {
            margin: 0;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .logout-modal-body {
            padding: 25px 20px;
            text-align: center;
        }
        
        .logout-modal-body p {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .logout-modal-footer {
            padding: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .btn-cancel, .btn-confirm {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-cancel {
            background: #95a5a6;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #7f8c8d;
        }
        
        .btn-confirm {
            background: #e74c3c;
            color: white;
        }
        
        .btn-confirm:hover {
            background: #c0392b;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
}

// Función para cerrar el modal
function closeLogoutModal() {
    const modal = document.querySelector('.logout-modal');
    if (modal) {
        modal.remove();
    }
}

// Función para confirmar el cierre de sesión
function confirmLogout() {
    window.location.href = 'logout.php';
}

// --- Simulación de entrevista tipo chatbot ---
const questions = [
    {
        text: "¿Qué harías si un compañero de equipo no cumple con sus responsabilidades y eso afecta el proyecto?",
        answers: [
            "Hablaría con él para entender qué sucede y buscaría una solución conjunta.",
            "Lo reportaría directamente a un superior.",
            "Haría su parte yo mismo para evitar problemas."
        ],
        feedback: [
            "Excelente. Muestra empatía, iniciativa y trabajo en equipo.",
            "A veces necesario, pero falta comunicación directa y resolución de conflictos.",
            "Demuestra compromiso, pero no aborda el problema de fondo."
        ]
    },
    {
        text: "Un cliente solicita algo técnicamente imposible en el tiempo propuesto. ¿Qué haces?",
        answers: [
            "Le explico con claridad por qué no es posible y propongo una alternativa.",
            "Le digo que no se puede, sin muchos detalles.",
            "Le digo que sí para evitar discusión, aunque no estoy seguro si se puede cumplir."
        ],
        feedback: [
            "Excelente enfoque. Combina comunicación y orientación a soluciones.",
            "Honesto, pero puede parecer poco empático.",
            "Riesgoso. Puede generar frustración y comprometer la confianza."
        ]
    },
    {
        text: "Te asignan una tarea fuera de tu zona de confort. ¿Cuál es tu reacción inicial?",
        answers: [
            "Acepto el reto y pido ayuda si es necesario.",
            "Me quejo con mis compañeros, pero igual lo hago.",
            "Digo que no la haré porque no tengo experiencia."
        ],
        feedback: [
            "Muestra adaptabilidad, proactividad y actitud de crecimiento.",
            "Cumples, pero con resistencia. Falta compromiso positivo.",
            "Es honesto, pero demuestra falta de apertura al aprendizaje."
        ]
    }
];

let current = 0;

function nextQuestion(selected) {
    const feedbackElement = document.getElementById("feedback");
    const questionElement = document.getElementById("question");
    const buttons = document.querySelectorAll('.chatbot-options button');
    const chatbotQuestion = document.querySelector(".chatbot-question");
    const chatbotOptions = document.querySelector(".chatbot-options");
    
    if (feedbackElement) {
        feedbackElement.innerText = questions[current].feedback[selected];
    }
    
    current++;
    
    if (current < questions.length) {
        setTimeout(() => {
            if (questionElement) {
                questionElement.innerText = `Pregunta ${current + 1}: ${questions[current].text}`;
            }
            
            buttons.forEach((btn, idx) => {
                btn.innerText = questions[current].answers[idx];
            });
            
            if (feedbackElement) {
                feedbackElement.innerText = "";
            }
        }, 2000);
    } else {
        setTimeout(() => {
            if (chatbotQuestion) {
                chatbotQuestion.innerText = "🎉 ¡Simulación completada! Tus respuestas indican un perfil con alto potencial humano.";
            }
            
            if (chatbotOptions) {
                chatbotOptions.style.display = "none";
            }
            
            if (feedbackElement) {
                feedbackElement.innerText = "";
            }
        }, 2000);
    }
}
