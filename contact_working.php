<?php
/**
 * Sistema de contacto funcional - Versión simplificada
 * Envía emails a pemma9962@gmail.com
 * Handler optimizado para el formulario de contacto con validación básica
 */

// Configuración de destinatario y remitente predeterminado
$to_email = 'pemma9962@gmail.com';
$from_email = 'noreply@aicroom.com';

// Función para enviar respuesta JSON estandarizada
function sendResponse($success, $message, $data = null) {
    // Establecer cabecera para indicar contenido JSON
    header('Content-Type: application/json');
    // Enviar respuesta estructurada en formato JSON
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    // Finalizar ejecución del script
    exit;
}

// Verificar que la solicitud sea mediante método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Método no permitido');
}

// Obtener datos del formulario directamente con operador de fusión null
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$company = $_POST['company'] ?? '';
$message = $_POST['message'] ?? '';

// Limpiar espacios en blanco al inicio y final de los datos
$name = trim($name);
$email = trim($email);
$company = trim($company);
$message = trim($message);

// Registrar datos recibidos para diagnóstico y debugging
error_log("Datos recibidos - Nombre: '$name', Email: '$email', Empresa: '$company', Mensaje: '$message'");

// Array para almacenar errores de validación
$errors = [];

// Validar longitud mínima del nombre
if (strlen($name) < 2) {
    $errors[] = 'El nombre debe tener al menos 2 caracteres';
}

// Validar formato de email usando filter_var
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El correo electrónico no es válido';
}

// Validar longitud mínima del mensaje
if (strlen($message) < 10) {
    $errors[] = 'El mensaje debe tener al menos 10 caracteres';
}

// Si existen errores de validación, devolverlos al cliente
if (!empty($errors)) {
    sendResponse(false, 'Errores de validación', $errors);
}

// Crear asunto del correo con prefijo y nombre del remitente
$subject = '[AICROOM] Mensaje de contacto de ' . $name;

// Construir cuerpo del correo en formato HTML con estilos CSS embebidos
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

// Incluir campo de empresa solo si no está vacío
if (!empty($company)) {
    $email_body .= "
            <div class='field'>
                <div class='label'>Empresa:</div>
                <div class='value'>" . htmlspecialchars($company) . "</div>
            </div>";
}

// Continuar construcción del cuerpo del mensaje
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

// Configurar cabeceras del correo electrónico
$headers = [
    'MIME-Version: 1.0',                    // Especificar versión MIME
    'Content-type: text/html; charset=UTF-8', // Indicar contenido HTML con codificación UTF-8
    'From: ' . $from_email,                 // Remitente del correo
    'Reply-To: ' . $email,                  // Dirección para respuestas
    'X-Mailer: PHP/' . phpversion()         // Identificar versión de PHP utilizada
];

// Intentar enviar el correo usando la función mail() de PHP
$mail_sent = mail($to_email, $subject, $email_body, implode("\r\n", $headers));

// Verificar resultado del envío y responder apropiadamente
if ($mail_sent) {
    // Registrar envío exitoso en el log de errores
    error_log("Email enviado exitosamente - De: $email, Para: $to_email");
    // Enviar respuesta de éxito al cliente
    sendResponse(true, 'Mensaje enviado exitosamente. Te contactaremos pronto.');
} else {
    // Registrar fallo en el envío en el log de errores
    error_log("Error al enviar email - De: $email, Para: $to_email");
    // Enviar respuesta de error al cliente
    sendResponse(false, 'Error al enviar el mensaje. Por favor, inténtalo de nuevo.');
}
?>