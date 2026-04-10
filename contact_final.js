// Espera a que todo el contenido del DOM esté cargado antes de ejecutar el script
document.addEventListener('DOMContentLoaded', function() {
    // Selecciona el formulario de contacto por su clase
    const contactForm = document.querySelector('.contact-form');
    // Selecciona el botón de envío por su clase
    const submitBtn = document.querySelector('.submit-btn');
    
    // Verifica si existen los elementos antes de continuar
    if (!contactForm || !submitBtn) {
        console.error('Formulario o botón no encontrado'); // Muestra error en consola si faltan
        return; // Detiene la ejecución del script
    }
    
    // Función para mostrar mensajes de error o éxito
    function showMessage(message, isError = false) {
        // Elimina mensajes anteriores existentes en el DOM
        const existingMessages = document.querySelectorAll('.message-display');
        existingMessages.forEach(msg => msg.remove());
        
        // Crea un nuevo contenedor <div> para el mensaje
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message-display'; // Clase identificadora
        // Estilos comunes del mensaje
        messageDiv.style.padding = '1rem';
        messageDiv.style.borderRadius = '8px';
        messageDiv.style.marginBottom = '1rem';
        messageDiv.style.fontWeight = 'bold';
        messageDiv.style.textAlign = 'center';
        
        // Estilos y contenido dependiendo del tipo de mensaje
        if (isError) {
            // Estilos de error (rojo)
            messageDiv.style.backgroundColor = '#f8d7da';
            messageDiv.style.color = '#721c24';
            messageDiv.style.border = '1px solid #f5c6cb';
            messageDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
        } else {
            // Estilos de éxito (verde)
            messageDiv.style.backgroundColor = '#d4edda';
            messageDiv.style.color = '#155724';
            messageDiv.style.border = '1px solid #c3e6cb';
            messageDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
        }
        
        // Inserta el mensaje al inicio del formulario
        contactForm.insertBefore(messageDiv, contactForm.firstChild);
        
        // Elimina automáticamente el mensaje después de 5 segundos
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
    
    // Manejo del evento de envío del formulario
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Previene el envío tradicional (recarga de página)
        
        // Obtiene los datos del formulario usando FormData
        const formData = new FormData(contactForm);
        
        // Extracción de valores individuales con validación básica
        const name = formData.get('name') || '';
        const email = formData.get('email') || '';
        const message = formData.get('message') || '';
        
        // Validación de nombre (mínimo 2 caracteres)
        if (name.length < 2) {
            showMessage('El nombre debe tener al menos 2 caracteres', true);
            return;
        }
        
        // Validación de email (contiene '@' y longitud mínima de 5)
        if (!email.includes('@') || email.length < 5) {
            showMessage('El correo electrónico no es válido', true);
            return;
        }
        
        // Validación de mensaje (mínimo 10 caracteres)
        if (message.length < 10) {
            showMessage('El mensaje debe tener al menos 10 caracteres', true);
            return;
        }
        
        // Cambia el estado del botón mientras se procesa
        const originalText = submitBtn.innerHTML; // Guarda el texto original
        submitBtn.disabled = true; // Desactiva el botón para evitar envíos múltiples
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...'; // Muestra spinner
        
        // Envío de datos mediante fetch al archivo PHP backend
        fetch('contact_final.php', {
            method: 'POST', // Método POST
            body: formData  // Datos del formulario
        })
        .then(response => response.json()) // Convierte la respuesta en JSON
        .then(data => {
            // Si la respuesta indica éxito
            if (data.success) {
                showMessage('Mensaje enviado exitosamente. Te contactaremos pronto.', false);
                contactForm.reset(); // Resetea el formulario
            } else {
                // Si hay errores, muestra el primero si es un array o el mensaje general
                if (Array.isArray(data.data)) {
                    showMessage(data.data[0], true);
                } else {
                    showMessage(data.message, true);
                }
            }
        })
        .catch(error => {
            // Manejo de errores de conexión o fetch
            console.error('Error:', error);
            showMessage('Error de conexión. Por favor, inténtalo de nuevo.', true);
        })
        .finally(() => {
            // Restaura el botón a su estado original
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
