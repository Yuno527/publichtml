<?php
// Inicio del script PHP: declara que el contenido siguiente es código PHP.
/**
 * Manejador del formulario de contacto
 * Envía emails a pemma9962@gmail.com
 */
// Docblock: descripción breve del propósito del archivo (handler de formulario y destinatario).
// Comentario en blanco / separación visual

// Configuración de email
// Comentario: sección donde se definen variables de configuración relacionadas con el email.
$to_email = 'pemma9962@gmail.com';
// Define la dirección de correo que recibirá los mensajes de contacto.
$from_email = 'noreply@aicroom.com'; // Email del remitente (puedes cambiarlo)
// Define la dirección de remitente que se usará en la cabecera From del email.
$subject_prefix = '[AICROOM] Nuevo mensaje de contacto';
// Prefijo fijo que se añadirá al asunto de todos los correos entrantes.

// Función para generar respuesta JSON
// Comentario: definición de función reutilizable para enviar respuestas JSON al cliente.
function sendResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    // Establece la cabecera HTTP indicando que la respuesta será JSON.
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    // Convierte el array asociativo a JSON y lo imprime como cuerpo de la respuesta.
    exit;
    // Finaliza la ejecución del script inmediatamente después de enviar la respuesta.
}

// Función para limpiar datos de entrada
// Comentario: normaliza y escapa entradas para reducir riesgo de XSS e inconsistencias.
function sanitizeInput($data) {
    $data = trim($data);
    // Elimina espacios en los extremos.
    $data = stripslashes($data);
    // Elimina backslashes añadidos por magic quotes u otras fuentes.
    $data = htmlspecialchars($data);
    // Convierte caracteres especiales a entidades HTML para prevenir inyección en HTML.
    return $data;
    // Retorna el dato sanitizado para su uso seguro.
}

// Función para validar email
// Comentario: usa filtros nativos de PHP para validar el formato del email.
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
    // Retorna false si el email no cumple el formato estándar.
}

// Permitir solo peticiones POST
// Comentario: bloque de protección que rechaza peticiones que no sean POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Método no permitido');
    // Responde con error si el método HTTP no es POST.
}

// Debug: Log de datos recibidos
// Comentario: escribe en el log del servidor los datos raw recibidos vía POST (útil para debugging).
error_log("Contact form data received: " . print_r($_POST, true));

// Obtener y limpiar datos del formulario
// Comentario: obtiene parámetros del array POST y los limpia con sanitizeInput para mayor seguridad.
$name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
// Nombre del remitente; si no existe, se asigna cadena vacía.
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
// Correo del remitente; sanitizado.
$company = isset($_POST['company']) ? sanitizeInput($_POST['company']) : '';
// Nombre de la empresa (opcional); sanitizado.
$message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';
// Mensaje del remitente; sanitizado.

// Debug: Log de datos limpiados
// Comentario: log adicional con los datos ya sanitizados para trazar el flujo de datos.
error_log("Cleaned data - Name: '$name', Email: '$email', Company: '$company', Message: '$message'");

// Validaciones
// Comentario: inicializa el array que almacenará mensajes de error de validación.
$errors = [];

// Validar nombre
// Comentario: comprueba que el nombre exista y tenga longitud mínima razonable.
if (empty($name) || strlen($name) < 2) {
    $errors[] = 'El nombre es requerido y debe tener al menos 2 caracteres';
    // Añade mensaje al arreglo de errores si la validación falla.
}

// Validar email
// Comentario: comprueba presencia y formato válido del correo electrónico.
if (empty($email) || !isValidEmail($email)) {
    $errors[] = 'El correo electrónico es requerido y debe ser válido';
    // Añade mensaje de error si el email es inválido o está ausente.
}

// Validar mensaje
// Comentario: exige contenido en el campo mensaje con longitud mínima para evitar mensajes vacíos.
if (empty($message) || strlen($message) < 10) {
    $errors[] = 'El mensaje es requerido y debe tener al menos 10 caracteres';
    // Añade error si el mensaje es demasiado corto o ausente.
}

// Debug: Log de errores
// Comentario: si existen errores, se registran en el log para diagnóstico.
if (!empty($errors)) {
    error_log("Validation errors: " . print_r($errors, true));
}

// Si hay errores, devolverlos
// Comentario: respuesta inmediata con el listado de errores si las validaciones fallaron.
if (!empty($errors)) {
    sendResponse(false, 'Errores de validación', $errors);
    // Se utiliza sendResponse para retornar JSON y terminar ejecución.
}

try {
    // Preparar el contenido del email
    // Comentario: construye el asunto concatenando el prefijo definido y el nombre del remitente.
    $subject = $subject_prefix . ' - ' . $name;
    
    // Crear el cuerpo del email en HTML
    // Comentario: a continuación se genera una plantilla HTML multi-línea para el cuerpo del mensaje.
    $email_body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; }
            .content { background-color: #f9f9f9; padding: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #2c3e50; }
            .value { margin-top: 5px; }
            .message-box { background-color: white; padding: 15px; border-left: 4px solid #3498db; margin-top: 10px; }
            .footer { background-color: #34495e; color: white; padding: 15px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Nuevo mensaje de contacto - AICROOM</h2>
            </div>
            
            <div class='content'>
                <div class='field'>
                    <div class='label'>Nombre:</div>
                    <div class='value'>" . htmlspecialchars($name) . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>Correo electrónico:</div>
                    <div class='value'>" . htmlspecialchars($email) . "</div>
                </div>";
    // Fin de la asignación inicial de $email_body; se continúa concatenando condicionalmente más secciones.

    if (!empty($company)) {
        $email_body .= "
                <div class='field'>
                    <div class='label'>Empresa:</div>
                    <div class='value'>" . htmlspecialchars($company) . "</div>
                </div>";
        // Si se proporcionó empresa, se añade ese bloque al cuerpo del email (sanitizando con htmlspecialchars).
    }
    
    $email_body .= "
                <div class='field'>
                    <div class='label'>Mensaje:</div>
                    <div class='message-box'>" . nl2br(htmlspecialchars($message)) . "</div>
                </div>
            </div>
            
            <div class='footer'>
                <p>Este mensaje fue enviado desde el formulario de contacto de AICROOM</p>
                <p>Fecha: " . date('d/m/Y H:i:s') . "</p>
            </div>
        </div>
    </body>
    </html>";
    // Se cierra la plantilla HTML y se completa $email_body; nl2br convierte saltos de línea en <br>.

    // Configurar headers del email
    // Comentario: prepara un array de cabeceras adecuadas para envío de email en formato HTML.
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $from_email,
        'Reply-To: ' . $email,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    // Enviar el email
    // Comentario: utiliza la función nativa mail() pasando cabeceras unidas por CRLF.
    $mail_sent = mail($to_email, $subject, $email_body, implode("\r\n", $headers));
    
    if ($mail_sent) {
        // Log del envío exitoso
        // Comentario: registra en el log que el envío fue exitoso incluyendo remitente y destinatario.
        error_log("Email de contacto enviado exitosamente - De: $email, Para: $to_email");
        
        sendResponse(true, 'Mensaje enviado exitosamente. Te contactaremos pronto.', [
            'name' => $name,
            'email' => $email,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        // Responde con éxito y datos útiles para el frontend (incluye timestamp).
    } else {
        // Log del error
        // Comentario: registra en el log el fallo en el envío para diagnóstico posterior.
        error_log("Error al enviar email de contacto - De: $email, Para: $to_email");
        
        sendResponse(false, 'Error al enviar el mensaje. Por favor, inténtalo de nuevo o contáctanos directamente.');
        // Responde al cliente indicando fallo en el envío.
    }
    
} catch (Exception $e) {
    // Log del error
    // Comentario: captura cualquier excepción imprevista, la registra y devuelve respuesta genérica.
    error_log("Error inesperado en contact_handler.php: " . $e->getMessage());
    
    sendResponse(false, 'Error inesperado. Por favor, inténtalo de nuevo.');
    // Respuesta genérica para no exponer detalles internos al cliente.
}
?>
