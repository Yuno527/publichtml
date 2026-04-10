// Esperar a que el documento HTML esté completamente cargado y listo
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar el formulario de contacto y el botón de envío
    const contactForm = document.querySelector('.contact-form');
    const submitBtn = document.querySelector('.submit-btn');
    
    // Verificar que ambos elementos existan en el DOM
    if (!contactForm || !submitBtn) {
        console.error('Formulario o botón no encontrado');
        return;
    }
    
    // Función para mostrar mensajes de feedback al usuario
    function showMessage(message, isError = false) {
        // Limpiar mensajes anteriores para evitar duplicados
        const existingMessages = document.querySelectorAll('.message-display');
        existingMessages.forEach(msg => msg.remove());
        
        // Crear nuevo elemento div para el mensaje
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message-display';
        // Aplicar estilos inline para el mensaje
        messageDiv.style.padding = '1rem';
        messageDiv.style.borderRadius = '8px';
        messageDiv.style.marginBottom = '1rem';
        messageDiv.style.fontWeight = 'bold';
        
        // Configurar estilos según el tipo de mensaje (error o éxito)
        if (isError) {
            // Estilos para mensajes de error (rojos)
            messageDiv.style.backgroundColor = '#f8d7da';
            messageDiv.style.color = '#721c24';
            messageDiv.style.border = '1px solid #f5c6cb';
            messageDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
        } else {
            // Estilos para mensajes de éxito (verdes)
            messageDiv.style.backgroundColor = '#d4edda';
            messageDiv.style.color = '#155724';
            messageDiv.style.border = '1px solid #c3e6cb';
            messageDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
        }
        
        // Insertar el mensaje al inicio del formulario
        contactForm.insertBefore(messageDiv, contactForm.firstChild);
        
        // Eliminar automáticamente el mensaje después de 5 segundos
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
    
    // Manejar el evento de envío del formulario
    contactForm.addEventListener('submit', function(e) {
        // Prevenir el comportamiento por defecto del formulario (recarga de página)
        e.preventDefault();
        
        // Crear objeto FormData con los datos del formulario
        const formData = new FormData(contactForm);
        
        // Validación básica en el lado del cliente
        const name = formData.get('name') || '';
        const email = formData.get('email') || '';
        const message = formData.get('message') || '';
        
        // Validar longitud del nombre
        if (name.length < 2) {
            showMessage('El nombre debe tener al menos 2 caracteres', true);
            return;
        }
        
        // Validación básica de email
        if (!email.includes('@') || email.length < 5) {
            showMessage('El correo electrónico no es válido', true);
            return;
        }
        
        // Validar longitud del mensaje
        if (message.length < 10) {
            showMessage('El mensaje debe tener al menos 10 caracteres', true);
            return;
        }
        
        // Cambiar estado del botón durante el envío
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true; // Deshabilitar botón para evitar múltiples envíos
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...'; // Mostrar indicador de carga
        
        // Enviar datos al servidor usando Fetch API
        fetch('contact_working.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Convertir respuesta a JSON
        .then(data => {
            // Manejar respuesta del servidor
            if (data.success) {
                // Mostrar mensaje de éxito y resetear formulario
                showMessage(data.message, false);
                contactForm.reset();
            } else {
                // Manejar errores del servidor
                if (Array.isArray(data.data)) {
                    // Mostrar primer error si hay múltiples
                    showMessage(data.data[0], true);
                } else {
                    // Mostrar mensaje de error general
                    showMessage(data.message, true);
                }
            }
        })
        .catch(error => {
            // Manejar errores de red o conexión
            console.error('Error:', error);
            showMessage('Error de conexión. Por favor, inténtalo de nuevo.', true);
        })
        .finally(() => {
            // Restaurar estado original del botón (siempre se ejecuta)
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});