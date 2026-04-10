// Sistema de menú de usuario dinámico
document.addEventListener('DOMContentLoaded', function() {
    // Función para obtener el nombre del usuario actual
    function getCurrentUserName() {
        // Opción 1: Verificar localStorage (si se guarda el nombre ahí)
        const userName = localStorage.getItem('userName');
        if (userName) {
            return userName;
        }
        
        // Opción 2: Verificar si existe un elemento que indique el nombre
        const nameIndicator = document.querySelector('[data-user-name]');
        if (nameIndicator) {
            return nameIndicator.getAttribute('data-user-name');
        }
        
        // Opción 3: Verificar si hay una variable global
        if (typeof window.currentUserName !== 'undefined') {
            return window.currentUserName;
        }
        
        // Opción 4: Verificar el texto actual del elemento de usuario
        const currentUserElement = document.querySelector('.user-name');
        if (currentUserElement && currentUserElement.textContent !== 'EMA (usuario)') {
            return currentUserElement.textContent.replace(' (usuario)', '');
        }
        
        // Por defecto, usar un nombre genérico
        return 'Usuario';
    }
    
    // Variable global para almacenar información del usuario
    let userInfo = null;
    
    // Función para obtener información del usuario desde PHP
    async function fetchUserInfo() {
        try {
            const response = await fetch('check_user.php');
            const data = await response.json();
            userInfo = data;
            return data;
        } catch (error) {
            return null;
        }
    }
    
    // Función para verificar si el usuario es administrador
    function isAdmin() {
        // Opción 1: Verificar información del usuario desde PHP
        if (userInfo && userInfo.is_admin) {
            return true;
        }
        
        // Opción 2: Verificar localStorage (para testing)
        const userRole = localStorage.getItem('userRole');
        if (userRole === 'admin') {
            return true;
        }
        
        // Opción 3: Verificar si hay un elemento que indique el rol desde PHP
        const roleIndicator = document.querySelector('[data-user-role]');
        if (roleIndicator) {
            const phpRole = roleIndicator.getAttribute('data-user-role');
            if (phpRole === 'admin') {
                return true;
            }
        }
        
        // Opción 4: Verificar si hay una variable global desde PHP
        if (typeof window.userRole !== 'undefined' && window.userRole === 'admin') {
            return true;
        }
        
        // Opción 5: Verificar si el nombre contiene "admin" o "Admin"
        const userName = getCurrentUserName();
        if (userName && (userName.toLowerCase().includes('admin') || userName.includes('Admin'))) {
            return true;
        }
        
        // Opción 6: Verificar si hay un elemento con clase admin-indicator
        const adminIndicator = document.querySelector('.admin-indicator');
        if (adminIndicator) {
            return true;
        }
        
        return false;
    }
    
    // Función para actualizar el nombre del usuario en la interfaz
    function updateUserName() {
        const userName = getCurrentUserName();
        const userElements = document.querySelectorAll('.user-name, .mobile-user-name');
        
        userElements.forEach(element => {
            element.textContent = userName;
        });
    }
    
    // Función para inicializar el menú desplegable
    function initializeDropdownMenu() {
        const userMenu = document.querySelector('.user-menu');
        const mobileUserSection = document.querySelector('.mobile-user-section');
        
        if (userMenu) {
            // Crear el menú desplegable para desktop
            const dropdown = document.createElement('div');
            dropdown.className = 'user-dropdown';
            dropdown.innerHTML = `
                <div class="dropdown-content">
                    <a href="#" class="dropdown-item" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </div>
            `;
            
            // Insertar el dropdown después del user-name
            const userNameElement = userMenu.querySelector('.user-name');
            const logoutBtn = userMenu.querySelector('.logout-btn');
            
            if (userNameElement && logoutBtn) {
                // Remover el botón de logout original
                logoutBtn.remove();
                
                // Hacer el nombre clickeable
                userNameElement.classList.add('user-name-clickable');
                userNameElement.innerHTML = `
                    <span class="user-name-text">${userNameElement.textContent}</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                `;
                
                // Insertar el dropdown
                userMenu.appendChild(dropdown);
                
                // Agregar evento de click
                userNameElement.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleDropdown();
                });
            }
        }
        
        if (mobileUserSection) {
            // Crear el menú desplegable para móvil
            const mobileDropdown = document.createElement('div');
            mobileDropdown.className = 'mobile-user-dropdown';
            mobileDropdown.innerHTML = `
                <div class="mobile-dropdown-content">
                    <a href="#" class="mobile-dropdown-item" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </div>
            `;
            
            const mobileUserName = mobileUserSection.querySelector('.mobile-user-name');
            const mobileLogoutBtn = mobileUserSection.querySelector('.mobile-logout-btn');
            
            if (mobileUserName && mobileLogoutBtn) {
                // Remover el botón de logout original
                mobileLogoutBtn.remove();
                
                // Hacer el nombre clickeable
                mobileUserName.classList.add('mobile-user-name-clickable');
                mobileUserName.innerHTML = `
                    <span class="mobile-user-name-text">${mobileUserName.textContent}</span>
                    <i class="fas fa-chevron-down mobile-dropdown-arrow"></i>
                `;
                
                // Insertar el dropdown
                mobileUserSection.appendChild(mobileDropdown);
                
                // Agregar evento de click
                mobileUserName.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleMobileDropdown();
                });
            }
        }
    }
    
    // Función para alternar el dropdown de desktop
    function toggleDropdown() {
        const dropdown = document.querySelector('.user-dropdown');
        const arrow = document.querySelector('.dropdown-arrow');
        
        if (dropdown && arrow) {
            dropdown.classList.toggle('active');
            arrow.classList.toggle('rotated');
        }
    }
    
    // Función para alternar el dropdown móvil
    function toggleMobileDropdown() {
        const dropdown = document.querySelector('.mobile-user-dropdown');
        const arrow = document.querySelector('.mobile-dropdown-arrow');
        
        if (dropdown && arrow) {
            dropdown.classList.toggle('active');
            arrow.classList.toggle('rotated');
        }
    }
    
    // Cerrar dropdowns al hacer click fuera
    document.addEventListener('click', function(e) {
        const userMenu = document.querySelector('.user-menu');
        const mobileUserSection = document.querySelector('.mobile-user-section');
        
        if (userMenu && !userMenu.contains(e.target)) {
            const dropdown = document.querySelector('.user-dropdown');
            const arrow = document.querySelector('.dropdown-arrow');
            if (dropdown && arrow) {
                dropdown.classList.remove('active');
                arrow.classList.remove('rotated');
            }
        }
        
        if (mobileUserSection && !mobileUserSection.contains(e.target)) {
            const dropdown = document.querySelector('.mobile-user-dropdown');
            const arrow = document.querySelector('.mobile-dropdown-arrow');
            if (dropdown && arrow) {
                dropdown.classList.remove('active');
                arrow.classList.remove('rotated');
            }
        }
    });
    
    // Función global para cerrar sesión
    window.logout = function() {
        // Limpiar datos del usuario
        localStorage.removeItem('userName');
        localStorage.removeItem('userRole');
        
        // Redirigir al login
        window.location.href = 'login.html';
    };
    
    // Función para cambiar el rol del usuario (para testing)
    window.setUserRole = function(role) {
        localStorage.setItem('userRole', role);
        
        // Actualizar elementos de admin usando la función isAdmin
        const adminElements = document.querySelectorAll('.admin-only');
        const userIsAdmin = isAdmin();
        
        adminElements.forEach(element => {
            if (userIsAdmin) {
                element.classList.remove('hide-for-users');
                element.classList.add('show-for-admin');
                element.style.display = 'block';
            } else {
                element.classList.add('hide-for-users');
                element.classList.remove('show-for-admin');
                element.style.display = 'none';
            }
        });
    };
    
    // Función para obtener el rol actual
    window.getCurrentRole = function() {
        return localStorage.getItem('userRole') || 'user';
    };
    
    // Función global para verificar si es admin
    window.isAdmin = function() {
        return isAdmin();
    };
    
    // Función para cambiar el nombre del usuario (para testing)
    window.setUserName = function(name) {
        localStorage.setItem('userName', name);
        updateUserName();
        
        // Actualizar también los elementos clickeables
        const userNameTexts = document.querySelectorAll('.user-name-text, .mobile-user-name-text');
        userNameTexts.forEach(element => {
            element.textContent = name;
        });
        
    };
    
    // Función para obtener el nombre actual
    window.getCurrentUserName = function() {
        return getCurrentUserName();
    };
    
    // Función para inicializar elementos de admin
    function initializeAdminElements() {
        const adminElements = document.querySelectorAll('.admin-only');
        const userIsAdmin = isAdmin();
        
        adminElements.forEach(element => {
            if (userIsAdmin) {
                element.classList.remove('hide-for-users');
                element.classList.add('show-for-admin');
                element.style.display = 'block';
            } else {
                element.classList.add('hide-for-users');
                element.classList.remove('show-for-admin');
                element.style.display = 'none';
            }
        });
    }
    
    // Función para inicializar el sistema completo
    async function initializeSystem() {
        // Obtener información del usuario desde PHP
        await fetchUserInfo();
        
        // Actualizar nombre del usuario
        if (userInfo && userInfo.logged_in) {
            const userElements = document.querySelectorAll('.user-name, .mobile-user-name');
            userElements.forEach(element => {
                element.textContent = userInfo.name;
            });
        } else {
            updateUserName();
        }
        
        // Inicializar menú desplegable
        initializeDropdownMenu();
        
        // Inicializar elementos de admin
        initializeAdminElements();
    }
    
    // Inicializar el sistema
    initializeSystem();
});

// Función global para simular login de diferentes usuarios (para testing)
function loginAsUser(userName) {
    localStorage.setItem('userName', userName);
    location.reload();
}
