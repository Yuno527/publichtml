// Script de depuración para el chatbot
// Ejecuta esto en la consola del navegador para verificar el estado

console.log('=== DIAGNÓSTICO DEL CHATBOT ===');

// 1. Verificar si el script se cargó
console.log('1. Verificando si BubbleChatbot está definido:', typeof BubbleChatbot !== 'undefined');

// 2. Verificar si bubbleChatbot existe
console.log('2. Verificando si bubbleChatbot está inicializado:', typeof bubbleChatbot !== 'undefined');

// 3. Verificar si el botón existe
const button = document.getElementById('bubble-chatbot-button');
console.log('3. Botón encontrado:', button ? 'SÍ' : 'NO');

// 4. Verificar si el contenedor existe
const container = document.getElementById('bubble-chatbot-container');
console.log('4. Contenedor encontrado:', container ? 'SÍ' : 'NO');

// 5. Verificar body
console.log('5. Body disponible:', !!document.body);

// 6. Intentar crear el botón manualmente si no existe
if (!button && typeof BubbleChatbot !== 'undefined' && document.body) {
    console.log('6. Intentando crear el botón manualmente...');
    try {
        const testChatbot = new BubbleChatbot();
        console.log('✅ Chatbot creado manualmente');
        
        // Verificar después de 1 segundo
        setTimeout(() => {
            const newButton = document.getElementById('bubble-chatbot-button');
            if (newButton) {
                console.log('✅ Botón creado exitosamente');
                console.log('Estilos:', window.getComputedStyle(newButton));
            } else {
                console.error('❌ El botón aún no existe después de crear el chatbot');
            }
        }, 1000);
    } catch (error) {
        console.error('❌ Error al crear chatbot:', error);
        console.error('Stack:', error.stack);
    }
} else if (!button) {
    console.error('❌ No se puede crear el botón porque:');
    if (typeof BubbleChatbot === 'undefined') {
        console.error('   - BubbleChatbot no está definido (el script no se cargó)');
    }
    if (!document.body) {
        console.error('   - document.body no está disponible');
    }
}

// 7. Verificar errores en la consola
console.log('7. Revisa la consola arriba para ver si hay errores de JavaScript');

