// Script para mostrar enlaces de administrador dinámicamente
class AdminNavigation {
    constructor() {
        this.init();
    }
    
    async init() {
        await this.checkUserStatus();
    }
    
    async checkUserStatus() {
        try {
            const response = await fetch('get_user_status.php');
            const data = await response.json();
            
            if (data.logged_in) {
                this.showUserLinks(data.user_role, data.user_name);
            } else {
                this.showLoginLinks();
            }
        } catch (error) {
            console.error('Error verificando estado del usuario:', error);
        }
    }
    
    showUserLinks(userRole, userName) {
        // Buscar todos los elementos de navegación
        const navLinks = document.querySelectorAll('.nav-links');
        
        navLinks.forEach(nav => {
            // Limpiar enlaces existentes de usuario
            this.removeExistingUserLinks(nav);
            
            // Agregar enlaces según el rol
            if (userRole === 'admin') {
                this.addAdminLinks(nav);
            }
            
            // Agregar enlaces de usuario
            this.addUserLinks(nav, userName);
        });
    }
    
    showLoginLinks() {
        const navLinks = document.querySelectorAll('.nav-links');
        
        navLinks.forEach(nav => {
            // Limpiar enlaces existentes de usuario
            this.removeExistingUserLinks(nav);
            
            // Agregar enlaces de login
            this.addLoginLinks(nav);
        });
    }
    
    removeExistingUserLinks(nav) {
        // Remover enlaces de resultados, usuario y logout existentes
        const existingLinks = nav.querySelectorAll('a[href="resultados.php"], .user-menu, .logout-btn, .login-btn');
        existingLinks.forEach(link => {
            if (link.parentElement) {
                link.parentElement.remove();
            }
        });
    }
    
    addAdminLinks(nav) {
        // Verificar si ya existe el enlace de resultados
        const existingResultsLink = nav.querySelector('a[href="resultados.php"]');
        if (!existingResultsLink) {
            // Crear el enlace de resultados
            const resultsLi = document.createElement('li');
            resultsLi.innerHTML = '<a href="resultados.php"><i class="fas fa-chart-bar"></i> Resultados</a>';
            
            // Insertar antes del último elemento (que suele ser el CTA)
            const lastLi = nav.querySelector('li:last-child');
            if (lastLi) {
                nav.insertBefore(resultsLi, lastLi);
            } else {
                nav.appendChild(resultsLi);
            }
        }
    }
    
    addUserLinks(nav, userName) {
        // Crear menú de usuario
        const userLi = document.createElement('li');
        userLi.className = 'user-menu';
        userLi.innerHTML = `
            <div class="user-dropdown">
                <a href="#" class="user-link">
                    <i class="fas fa-user-circle"></i>
                    <span>${userName}</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="user-dropdown-content">
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        `;
        
        // Insertar al final
        nav.appendChild(userLi);
        
        // Agregar funcionalidad del dropdown
        this.setupDropdown(userLi);
    }
    
    addLoginLinks(nav) {
        // Crear enlaces de login/registro
        const loginLi = document.createElement('li');
        loginLi.innerHTML = '<a href="login.html" class="login-btn"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>';
        
        const registerLi = document.createElement('li');
        registerLi.innerHTML = '<a href="register.html" class="register-btn"><i class="fas fa-user-plus"></i> Registrarse</a>';
        
        // Insertar al final
        nav.appendChild(loginLi);
        nav.appendChild(registerLi);
    }
    
    setupDropdown(userLi) {
        const userLink = userLi.querySelector('.user-link');
        const dropdownContent = userLi.querySelector('.user-dropdown-content');
        
        userLink.addEventListener('click', (e) => {
            e.preventDefault();
            dropdownContent.classList.toggle('show');
        });
        
        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!userLi.contains(e.target)) {
                dropdownContent.classList.remove('show');
            }
        });
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new AdminNavigation();
    });
} else {
    new AdminNavigation();
} 