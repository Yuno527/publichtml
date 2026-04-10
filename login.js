document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const rememberCheckbox = document.getElementById('remember');

    // Función para mostrar mensajes de error
    function showError(input, message) {
        const formGroup = input.closest('.form-group');
        const existingError = formGroup.querySelector('.error-message');
        
        if (existingError) {
            existingError.remove();
        }
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#e74c3c';
        errorDiv.style.fontSize = '0.85rem';
        errorDiv.style.marginTop = '0.5rem';
        errorDiv.textContent = message;
        
        formGroup.appendChild(errorDiv);
        input.style.borderColor = '#e74c3c';
    }

    // Función para limpiar errores
    function clearError(input) {
        const formGroup = input.closest('.form-group');
        const errorMessage = formGroup.querySelector('.error-message');
        
        if (errorMessage) {
            errorMessage.remove();
        }
        
        input.style.borderColor = '';
    }

    // Función para mostrar mensaje de éxito
    function showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.style.backgroundColor = '#d4edda';
        successDiv.style.color = '#155724';
        successDiv.style.padding = '1rem';
        successDiv.style.borderRadius = '8px';
        successDiv.style.marginBottom = '1rem';
        successDiv.style.border = '1px solid #c3e6cb';
        successDiv.textContent = message;
        
        const form = document.querySelector('.login-form');
        form.insertBefore(successDiv, form.firstChild);
        
        setTimeout(() => {
            successDiv.remove();
        }, 3000);
    }

    // Validación en tiempo real
    usernameInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this, 'El nombre de usuario es requerido');
        } else if (this.value.length < 3) {
            showError(this, 'El nombre de usuario debe tener al menos 3 caracteres');
        } else {
            clearError(this);
        }
    });

    passwordInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this, 'La contraseña es requerida');
        } else if (this.value.length < 6) {
            showError(this, 'La contraseña debe tener al menos 6 caracteres');
        } else {
            clearError(this);
        }
    });

    // Limpiar errores al escribir
    usernameInput.addEventListener('input', function() {
        clearError(this);
    });

    passwordInput.addEventListener('input', function() {
        clearError(this);
    });

    // Manejo del envío del formulario
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Limpiar mensajes de éxito anteriores
        const existingSuccess = document.querySelector('.success-message');
        if (existingSuccess) {
            existingSuccess.remove();
        }
        
        // Validación final
        let isValid = true;
        
        if (!usernameInput.value.trim()) {
            showError(usernameInput, 'El nombre de usuario es requerido');
            isValid = false;
        } else if (usernameInput.value.length < 3) {
            showError(usernameInput, 'El nombre de usuario debe tener al menos 3 caracteres');
            isValid = false;
        }
        
        if (!passwordInput.value.trim()) {
            showError(passwordInput, 'La contraseña es requerida');
            isValid = false;
        } else if (passwordInput.value.length < 6) {
            showError(passwordInput, 'La contraseña debe tener al menos 6 caracteres');
            isValid = false;
        }
        
        if (!isValid) {
            return;
        }
        
        // Simular envío de datos al servidor
        const loginData = {
            username: usernameInput.value.trim(),
            password: passwordInput.value,
            remember: rememberCheckbox.checked
        };
        
        // Mostrar indicador de carga
        const submitBtn = document.querySelector('.login-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';
        submitBtn.disabled = true;
        
        // Enviar datos al servidor
        const formData = new FormData();
        formData.append('username', loginData.username);
        formData.append('password', loginData.password);
        formData.append('remember', loginData.remember);
        
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                console.log('Datos del usuario:', data.data);
                
                // Redirección después de 2 segundos
                setTimeout(() => {
                    if (data.data && data.data.redirect) {
                        window.location.href = data.data.redirect;
                    } else {
                        window.location.href = 'index.html';
                    }
                }, 2000);
            } else {
                // Mostrar error
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.style.backgroundColor = '#f8d7da';
                errorDiv.style.color = '#721c24';
                errorDiv.style.padding = '1rem';
                errorDiv.style.borderRadius = '8px';
                errorDiv.style.marginBottom = '1rem';
                errorDiv.style.border = '1px solid #f5c6cb';
                errorDiv.textContent = data.message;
                
                const form = document.querySelector('.login-form');
                form.insertBefore(errorDiv, form.firstChild);
                
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.style.backgroundColor = '#f8d7da';
            errorDiv.style.color = '#721c24';
            errorDiv.style.padding = '1rem';
            errorDiv.style.borderRadius = '8px';
            errorDiv.style.marginBottom = '1rem';
            errorDiv.style.border = '1px solid #f5c6cb';
            errorDiv.textContent = 'Error de conexión. Intente nuevamente.';
            
            const form = document.querySelector('.login-form');
            form.insertBefore(errorDiv, form.firstChild);
            
            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        })
        .finally(() => {
            // Restaurar botón
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Función para manejar "Recordarme"
    rememberCheckbox.addEventListener('change', function() {
        if (this.checked) {
            console.log('Opción "Recordarme" activada');
        }
    });

    // El enlace "¿Olvidaste tu contraseña?" ya está configurado en el HTML
    // para navegar a forgot_password.html, no necesitamos JavaScript adicional aquí
}); 