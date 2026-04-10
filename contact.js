// Esperar a que el documento HTML esté completamente cargado y listo
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar el formulario de contacto y el botón de envío
    const contactForm = document.querySelector('.contact-form');
    const submitBtn = document.querySelector('.submit-btn');
    
    // Verificar que el formulario de contacto exista en el DOM
    if (!contactForm) {
        console.error('Formulario de contacto no encontrado');
        return;
    }
    
    // Verificar que el botón de envío exista en el DOM
    if (!submitBtn) {
        console.error('Botón de envío no encontrado');
        return;
    }
    
    // Función para mostrar mensajes de error al usuario
    function showError(message) {
        // Crear elemento div para el mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        // Aplicar estilos CSS inline para el mensaje de error
        errorDiv.style.backgroundColor = '#f8d7da';
        errorDiv.style.color = '#721c24';
        errorDiv.style.padding = '1rem';
        errorDiv.style.borderRadius = '8px';
        errorDiv.style.marginBottom = '1rem';
        errorDiv.style.border = '1px solid #f5c6cb';
        // Agregar icono y texto del mensaje
        errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
        
        // Insertar el mensaje de error al inicio del formulario
        const form = document.querySelector('.contact-form');
        form.insertBefore(errorDiv, form.firstChild);
        
        // Eliminar automáticamente el mensaje después de 5 segundos
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
    
    // Función para mostrar mensajes de éxito al usuario
    function showSuccess(message) {
        // Crear elemento div para el mensaje de éxito
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        // Aplicar estilos CSS inline para el mensaje de éxito
        successDiv.style.backgroundColor = '#d4edda';
        successDiv.style.color = '#155724';
        successDiv.style.padding = '1rem';
        successDiv.style.borderRadius = '8px';
        successDiv.style.marginBottom = '1rem';
        successDiv.style.border = '1px solid #c3e6cb';
        // Agregar icono y texto del mensaje
        successDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
        
        // Insertar el mensaje de éxito al inicio del formulario
        const form = document.querySelector('.contact-form');
        form.insertBefore(successDiv, form.firstChild);
        
        // Eliminar automáticamente el mensaje después de 5 segundos
        setTimeout(() => {
            successDiv.remove();
        }, 5000);
    }
    
    // Función para validar formato de email usando expresión regular
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Función para limpiar todos los mensajes anteriores del formulario
    function clearMessages() {
        const existingMessages = document.querySelectorAll('.error-message, .success-message');
        existingMessages.forEach(msg => msg.remove());
    }
    
    // Manejar el evento de envío del formulario
    contactForm.addEventListener('submit', function(e) {
        // Prevenir el comportamiento por defecto del formulario (recarga de página)
        e.preventDefault();
        
        // Limpiar mensajes anteriores antes de validar
        clearMessages();
        
        // Obtener datos del formulario usando FormData
        const formData = new FormData(contactForm);
        // Obtener y limpiar cada campo del formulario
        const name = formData.get('name') ? formData.get('name').trim() : '';
        const email = formData.get('email') ? formData.get('email').trim() : '';
        const company = formData.get('company') ? formData.get('company').trim() : '';
        const message = formData.get('message') ? formData.get('message').trim() : '';
        
        // Registrar datos en consola para debugging
        console.log('Datos del formulario:', {
            name: name,
            email: email,
            company: company,
            message: message
        });
        
        // Realizar validaciones del lado del cliente
        let isValid = true;
        
        // Validar campo nombre - requerido y longitud mínima
        if (!name || name.length < 2) {
            showError('El nombre es requerido y debe tener al menos 2 caracteres');
            isValid = false;
        }
        
        // Validar campo email - requerido y formato válido
        if (!email || !isValidEmail(email)) {
            showError('El correo electrónico es requerido y debe ser válido');
            isValid = false;
        }
        
        // Validar campo mensaje - requerido y longitud mínima
        if (!message || message.length < 10) {
            showError('El mensaje es requerido y debe tener al menos 10 caracteres');
            isValid = false;
        }
        
        // Si hay errores de validación, detener el envío
        if (!isValid) {
            return;
        }
        
        // Cambiar estado del botón durante el envío
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true; // Deshabilitar botón para evitar envíos múltiples
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...'; // Mostrar indicador de carga
        
        // Enviar datos al servidor usando Fetch API
        fetch('contact_simple.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Convertir respuesta a JSON
        .then(data => {
            // Manejar respuesta del servidor
            if (data.success) {
                // Mostrar mensaje de éxito y resetear formulario
                showSuccess(data.message);
                contactForm.reset();
                
                // Opcional: Configurar redirección después de éxito
                setTimeout(() => {
                    // Puedes agregar redirección aquí si lo deseas
                    // window.location.href = 'index.html';
                }, 3000);
            } else {
                // Manejar errores del servidor
                if (Array.isArray(data.data)) {
                    // Mostrar el primer error de la lista
                    showError(data.data[0]);
                } else {
                    // Mostrar mensaje de error general
                    showError(data.message);
                }
            }
        })
        .catch(error => {
            // Manejar errores de red o conexión
            console.error('Error en el envío:', error);
            showError('Error de conexión. Por favor, inténtalo de nuevo o contáctanos directamente.');
        })
        .finally(() => {
            // Restaurar estado original del botón (siempre se ejecuta)
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Validación en tiempo real para el campo email
    const emailInput = document.getElementById('email');
    if (emailInput) {
        // Validar cuando el campo pierde el foco (blur)
        emailInput.addEventListener('blur', function() {
            if (this.value && !isValidEmail(this.value)) {
                // Cambiar borde a rojo si el email no es válido
                this.style.borderColor = '#e74c3c';
            } else {
                // Restaurar borde por defecto si es válido o está vacío
                this.style.borderColor = '';
            }
        });
    }
    
    // Validación en tiempo real para el campo nombre
    const nameInput = document.getElementById('name');
    if (nameInput) {
        // Validar cuando el campo pierde el foco (blur)
        nameInput.addEventListener('blur', function() {
            if (this.value && this.value.length < 2) {
                // Cambiar borde a rojo si el nombre es muy corto
                this.style.borderColor = '#e74c3c';
            } else {
                // Restaurar borde por defecto si es válido o está vacío
                this.style.borderColor = '';
            }
        });
    }
    
    // Validación en tiempo real para el campo mensaje
    const messageInput = document.getElementById('message');
    if (messageInput) {
        // Validar cuando el campo pierde el foco (blur)
        messageInput.addEventListener('blur', function() {
            if (this.value && this.value.length < 10) {
                // Cambiar borde a rojo si el mensaje es muy corto
                this.style.borderColor = '#e74c3c';
            } else {
                // Restaurar borde por defecto si es válido o está vacío
                this.style.borderColor = '';
            }
        });
    }
});