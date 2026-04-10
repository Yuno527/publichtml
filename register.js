document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const nombreInput = document.getElementById('nombre');
    const empresaInput = document.getElementById('empresa');
    const correoInput = document.getElementById('correo');
    const puestoInput = document.getElementById('puesto');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');

    // Verificar que todos los elementos existen
    if (!registerForm) {
        console.error('Formulario de registro no encontrado');
        return;
    }
    if (!nombreInput) {
        console.error('Campo nombre no encontrado');
        return;
    }
    if (!empresaInput) {
        console.error('Campo empresa no encontrado');
        return;
    }
    if (!correoInput) {
        console.error('Campo correo no encontrado');
        return;
    }
    if (!puestoInput) {
        console.error('Campo puesto no encontrado');
        return;
    }
    if (!passwordInput) {
        console.error('Campo password no encontrado');
        return;
    }
    if (!confirmPasswordInput) {
        console.error('Campo confirmPassword no encontrado');
        return;
    }


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
        
        const form = document.querySelector('.register-form');
        form.insertBefore(successDiv, form.firstChild);
        
        setTimeout(() => {
            successDiv.remove();
        }, 5000);
    }

    // Validación de email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Validación de contraseña
    function isValidPassword(password) {
        return password.length >= 6;
    }

    // Validación en tiempo real
    nombreInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this, 'El nombre es requerido');
        } else if (this.value.length < 2) {
            showError(this, 'El nombre debe tener al menos 2 caracteres');
        } else {
            clearError(this);
        }
    });

    empresaInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this, 'La empresa es requerida');
        } else if (this.value.length < 2) {
            showError(this, 'La empresa debe tener al menos 2 caracteres');
        } else {
            clearError(this);
        }
    });

    correoInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this, 'El correo electrónico es requerido');
        } else if (!isValidEmail(this.value)) {
            showError(this, 'Ingrese un correo electrónico válido');
        } else {
            clearError(this);
        }
    });

    puestoInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this, 'El puesto es requerido');
        } else if (this.value.length < 2) {
            showError(this, 'El puesto debe tener al menos 2 caracteres');
        } else {
            clearError(this);
        }
    });

    passwordInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this, 'La contraseña es requerida');
        } else if (!isValidPassword(this.value)) {
            showError(this, 'La contraseña debe tener al menos 6 caracteres');
        } else {
            clearError(this);
        }
    });

    confirmPasswordInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError(this, 'Confirme su contraseña');
        } else if (this.value !== passwordInput.value) {
            showError(this, 'Las contraseñas no coinciden');
        } else {
            clearError(this);
        }
    });

    // Limpiar errores al escribir
    nombreInput.addEventListener('input', function() {
        clearError(this);
    });

    empresaInput.addEventListener('input', function() {
        clearError(this);
    });

    correoInput.addEventListener('input', function() {
        clearError(this);
    });

    puestoInput.addEventListener('input', function() {
        clearError(this);
    });

    passwordInput.addEventListener('input', function() {
        clearError(this);
        // Validar confirmación de contraseña en tiempo real
        if (confirmPasswordInput.value && confirmPasswordInput.value !== this.value) {
            showError(confirmPasswordInput, 'Las contraseñas no coinciden');
        } else {
            clearError(confirmPasswordInput);
        }
    });

    confirmPasswordInput.addEventListener('input', function() {
        clearError(this);
    });

    // Manejo del envío del formulario
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Obtener el botón de envío
        const submitBtn = document.querySelector('.register-btn');
        const originalText = submitBtn.innerHTML;
        
        // Deshabilitar botón y mostrar loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando...';
        
        // Limpiar mensajes de error anteriores
        const existingErrors = document.querySelectorAll('.error-message');
        existingErrors.forEach(error => error.remove());
        
        // Limpiar mensajes de éxito anteriores
        const existingSuccess = document.querySelector('.success-message');
        if (existingSuccess) {
            existingSuccess.remove();
        }
        
        // Validación final
        let isValid = true;
        
        // Validar nombre
        if (!nombreInput.value.trim()) {
            showError(nombreInput, 'El nombre es requerido');
            isValid = false;
        } else if (nombreInput.value.length < 2) {
            showError(nombreInput, 'El nombre debe tener al menos 2 caracteres');
            isValid = false;
        }
        
        // Validar empresa
        if (!empresaInput.value.trim()) {
            showError(empresaInput, 'La empresa es requerida');
            isValid = false;
        } else if (empresaInput.value.length < 2) {
            showError(empresaInput, 'La empresa debe tener al menos 2 caracteres');
            isValid = false;
        }
        
        // Validar correo
        if (!correoInput.value.trim()) {
            showError(correoInput, 'El correo electrónico es requerido');
            isValid = false;
        } else if (!isValidEmail(correoInput.value)) {
            showError(correoInput, 'Ingrese un correo electrónico válido');
            isValid = false;
        }
        
        // Validar puesto
        if (!puestoInput.value.trim()) {
            showError(puestoInput, 'El puesto es requerido');
            isValid = false;
        } else if (puestoInput.value.length < 2) {
            showError(puestoInput, 'El puesto debe tener al menos 2 caracteres');
            isValid = false;
        }
        
        // Validar contraseña
        if (!passwordInput.value.trim()) {
            showError(passwordInput, 'La contraseña es requerida');
            isValid = false;
        } else if (!isValidPassword(passwordInput.value)) {
            showError(passwordInput, 'La contraseña debe tener al menos 6 caracteres');
            isValid = false;
        }
        
        // Validar confirmación de contraseña
        if (!confirmPasswordInput.value.trim()) {
            showError(confirmPasswordInput, 'Confirme su contraseña');
            isValid = false;
        } else if (confirmPasswordInput.value !== passwordInput.value) {
            showError(confirmPasswordInput, 'Las contraseñas no coinciden');
            isValid = false;
        }
        

        
        if (!isValid) {
            return;
        }
        
        // Enviar datos al backend
        const formData = new FormData();
        formData.append('nombre', nombreInput.value.trim());
        formData.append('empresa', empresaInput.value.trim());
        formData.append('correo', correoInput.value.trim());
        formData.append('puesto', puestoInput.value.trim());
        formData.append('password', passwordInput.value);
        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('¡Registro exitoso! Ahora puedes iniciar sesión.');
                registerForm.reset();
                // Limpiar todos los errores visuales
                [nombreInput, empresaInput, correoInput, puestoInput, passwordInput, confirmPasswordInput].forEach(input => {
                    clearError(input);
                });
                // Redirigir al login después de 2 segundos
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            } else {
                // Limpiar errores anteriores
                [nombreInput, empresaInput, correoInput, puestoInput, passwordInput, confirmPasswordInput].forEach(input => {
                    clearError(input);
                });
                
                if (Array.isArray(data.data)) {
                    // Mostrar el primer error como mensaje general
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.style.backgroundColor = '#f8d7da';
                    errorDiv.style.color = '#721c24';
                    errorDiv.style.padding = '1rem';
                    errorDiv.style.borderRadius = '8px';
                    errorDiv.style.marginBottom = '1rem';
                    errorDiv.style.border = '1px solid #f5c6cb';
                    errorDiv.textContent = data.data[0];
                    
                    const form = document.querySelector('.register-form');
                    form.insertBefore(errorDiv, form.firstChild);
                    
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 5000);
                } else {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.style.backgroundColor = '#f8d7da';
                    errorDiv.style.color = '#721c24';
                    errorDiv.style.padding = '1rem';
                    errorDiv.style.borderRadius = '8px';
                    errorDiv.style.marginBottom = '1rem';
                    errorDiv.style.border = '1px solid #f5c6cb';
                    errorDiv.textContent = data.message;
                    
                    const form = document.querySelector('.register-form');
                    form.insertBefore(errorDiv, form.firstChild);
                    
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 5000);
                }
            }
        })
        .catch(error => {
            console.error('Error en el registro:', error);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.style.backgroundColor = '#f8d7da';
            errorDiv.style.color = '#721c24';
            errorDiv.style.padding = '1rem';
            errorDiv.style.borderRadius = '8px';
            errorDiv.style.marginBottom = '1rem';
            errorDiv.style.border = '1px solid #f5c6cb';
            errorDiv.textContent = 'Error de conexión. Intenta de nuevo.';
            
            const form = document.querySelector('.register-form');
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


}); 