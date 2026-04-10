<?php
/**
 * Manejador simplificado del formulario de contacto
 * Envía emails a pemma9962@gmail.com
 * Versión robusta con manejo de errores y validación mejorada
 */

// Configuración de email - destinatario y remitente predeterminado
$to_email = 'pemma9962@gmail.com';
$from_email = 'noreply@aicroom.com';
$subject_prefix = '[AICROOM] Nuevo mensaje de contacto';

// Función para generar respuesta JSON estandarizada
function sendResponse($success, $message, $data = null) {
    // Establecer cabecera para respuesta JSON
    header('Content-Type: application/json');
    // Enviar estructura JSON consistente para el frontend
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    // Finalizar ejecución después de enviar respuesta
    exit;
}

// Función para limpiar y sanitizar datos de entrada
function sanitizeInput($data) {
    // Manejar valores nulos
    if (is_null($data)) return '';
    // Eliminar espacios en blanco al inicio y final
    $data = trim($data);
    // Eliminar barras invertidas añadidas por magic_quotes
    $data = stripslashes($data);
    // Convertir caracteres especiales a entidades HTML
    $data = htmlspecialchars($data);
    return $data;
}

// Función para validar formato de email
function isValidEmail($email) {
    // Usar filter_var para validación estándar de email
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Verificar que la solicitud sea mediante método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Método no permitido');
}

// Debug: Registrar datos recibidos para diagnóstico
error_log("Contact form data received: " . print_r($_POST, true));

// Inicializar variables para datos del formulario
$name = '';
$email = '';
$company = '';
$message = '';

// Obtener y limpiar datos del formulario de manera segura
if (isset($_POST['name'])) {
    $name = sanitizeInput($_POST['name']);
}
if (isset($_POST['email'])) {
    $email = sanitizeInput($_POST['email']);
}
if (isset($_POST['company'])) {
    $company = sanitizeInput($_POST['company']);
}
if (isset($_POST['message'])) {
    $message = sanitizeInput($_POST['message']);
}

// Si no hay datos en POST, intentar obtener del input raw (para AJAX)
if (empty($name) && empty($email) && empty($message)) {
    // Leer datos sin procesar del cuerpo de la solicitud
    $raw_data = file_get_contents('php://input');
    if (!empty($raw_data)) {
        // Convertir datos raw a array asociativo
        parse_str($raw_data, $parsed_data);
        $name = isset($parsed_data['name']) ? sanitizeInput($parsed_data['name']) : '';
        $email = isset($parsed_data['email']) ? sanitizeInput($parsed_data['email']) : '';
        $company = isset($parsed_data['company']) ? sanitizeInput($parsed_data['company']) : '';
        $message = isset($parsed_data['message']) ? sanitizeInput($parsed_data['message']) : '';
    }
}

// Debug: Registrar datos limpios para verificar procesamiento
error_log("Cleaned data - Name: '$name', Email: '$email', Company: '$company', Message: '$message'");

// Array para almacenar errores de validación
$errors = [];

// Validar campo nombre - requerido y longitud mínima
if (empty($name) || strlen($name) < 2) {
    $errors[] = 'El nombre es requerido y debe tener al menos 2 caracteres';
}

// Validar campo email - requerido y formato válido
if (empty($email) || !isValidEmail($email)) {
    $errors[] = 'El correo electrónico es requerido y debe ser válido';
}

// Validar campo mensaje - requerido y longitud mínima
if (empty($message) || strlen($message) < 10) {
    $errors[] = 'El mensaje es requerido y debe tener al menos 10 caracteres';
}

// Debug: Registrar errores de validación si existen
if (!empty($errors)) {
    error_log("Validation errors: " . print_r($errors, true));
}

// Si hay errores de validación, devolverlos al cliente
if (!empty($errors)) {
    sendResponse(false, 'Errores de validación', $errors);
}

// Bloque try-catch para manejar excepciones durante el envío
try {
    // Preparar asunto del email con prefijo y nombre
    $subject = $subject_prefix . ' - ' . $name;
    
    // Construir cuerpo del email en HTML con estilos CSS
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
    
    // Incluir campo empresa solo si tiene valor
    if (!empty($company)) {
        $email_body .= "
                <div class='field'>
                    <div class='label'>Empresa:</div>
                    <div class='value'>" . htmlspecialchars($company) . "</div>
                </div>";
    }
    
    // Continuar construcción del cuerpo del email
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
    
    // Configurar cabeceras del email para formato HTML
    $headers = [
        'MIME-Version: 1.0',                    // Especificar versión MIME
        'Content-type: text/html; charset=UTF-8', // Indicar contenido HTML
        'From: ' . $from_email,                 // Remitente del correo
        'Reply-To: ' . $email,                  // Dirección para respuestas
        'X-Mailer: PHP/' . phpversion()         // Identificar sistema de envío
    ];
    
    // Intentar enviar el email usando función mail() de PHP
    $mail_sent = mail($to_email, $subject, $email_body, implode("\r\n", $headers));
    
    // Verificar resultado del envío
    if ($mail_sent) {
        // Registrar envío exitoso en logs
        error_log("Email de contacto enviado exitosamente - De: $email, Para: $to_email");
        
        // Enviar respuesta de éxito con datos adicionales
        sendResponse(true, 'Mensaje enviado exitosamente. Te contactaremos pronto.', [
            'name' => $name,
            'email' => $email,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        // Registrar fallo en envío en logs
        error_log("Error al enviar email de contacto - De: $email, Para: $to_email");
        
        // Enviar respuesta de error
        sendResponse(false, 'Error al enviar el mensaje. Por favor, inténtalo de nuevo o contáctanos directamente.');
    }
    
} catch (Exception $e) {
    // Registrar error inesperado en logs
    error_log("Error inesperado en contact_simple.php: " . $e->getMessage());
    
    // Enviar respuesta de error genérico
    sendResponse(false, 'Error inesperado. Por favor, inténtalo de nuevo.');
}
?>