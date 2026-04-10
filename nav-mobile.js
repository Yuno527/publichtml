// Navegación móvil funcional
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('overlay');
    const body = document.body;
    
    if (hamburger && mobileMenu && overlay) {
        // Función para abrir/cerrar menú
        function toggleMenu() {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Prevenir scroll del body cuando el menú está abierto
            if (mobileMenu.classList.contains('active')) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = '';
            }
        }
        
        // Función para cerrar menú
        function closeMenu() {
            hamburger.classList.remove('active');
            mobileMenu.classList.remove('active');
            overlay.classList.remove('active');
            body.style.overflow = '';
        }
        
        // Abrir/cerrar menú al hacer clic en hamburguesa
        hamburger.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleMenu();
        });
        
        // Cerrar menú al hacer clic en overlay
        overlay.addEventListener('click', function() {
            closeMenu();
        });
        
        // Cerrar menú al hacer clic en enlaces del menú móvil
        const mobileLinks = mobileMenu.querySelectorAll('.mobile-nav-link');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                closeMenu();
            });
        });
        
        // Cerrar menú al hacer clic en botón de logout móvil
        const mobileLogoutBtn = mobileMenu.querySelector('.mobile-logout-btn');
        if (mobileLogoutBtn) {
            mobileLogoutBtn.addEventListener('click', function() {
                closeMenu();
            });
        }
        
        // Cerrar menú con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                closeMenu();
            }
        });
        
        // Cerrar menú al redimensionar ventana (si se vuelve desktop)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                closeMenu();
            }
        });
        
        // Animación del botón hamburguesa
        hamburger.addEventListener('click', function() {
            const spans = hamburger.querySelectorAll('span');
            if (hamburger.classList.contains('active')) {
                // Animación para cerrar (X)
                spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
            } else {
                // Animación para abrir (hamburguesa)
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });
    }
});
