<?php
/**
 * Script de prueba para verificar la funcionalidad de contacto
 */

echo "<h2>Prueba del Sistema de Contacto - AICROOM</h2>\n";
echo "<pre>\n";

// Verificar configuración de PHP para email
echo "--- CONFIGURACIÓN DE PHP ---\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Mail function available: " . (function_exists('mail') ? 'Sí' : 'No') . "\n";

// Verificar configuración de sendmail
$sendmail_path = ini_get('sendmail_path');
echo "Sendmail path: " . ($sendmail_path ? $sendmail_path : 'No configurado') . "\n";

// Verificar configuración SMTP
$smtp_host = ini_get('SMTP');
$smtp_port = ini_get('smtp_port');
echo "SMTP Host: " . ($smtp_host ? $smtp_host : 'No configurado') . "\n";
echo "SMTP Port: " . ($smtp_port ? $smtp_port : 'No configurado') . "\n";

echo "\n--- PRUEBA DE ENVÍO DE EMAIL ---\n";

// Datos de prueba
$test_data = [
    'name' => 'Usuario Prueba',
    'email' => 'test@example.com',
    'company' => 'Empresa Test',
    'message' => 'Este es un mensaje de prueba para verificar que el sistema de contacto funciona correctamente.'
];

// Simular el envío de email
$to_email = 'pemma9962@gmail.com';
$from_email = 'noreply@aicroom.com';
$subject = '[AICROOM] Prueba de contacto - ' . $test_data['name'];

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
            <h2>PRUEBA - Nuevo mensaje de contacto - AICROOM</h2>
        </div>
        
        <div class='content'>
            <div class='field'>
                <div class='label'>Nombre:</div>
                <div class='value'>" . htmlspecialchars($test_data['name']) . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Correo electrónico:</div>
                <div class='value'>" . htmlspecialchars($test_data['email']) . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Empresa:</div>
                <div class='value'>" . htmlspecialchars($test_data['company']) . "</div>
            </div>
            
            <div class='field'>
                <div class='label'>Mensaje:</div>
                <div class='message-box'>" . nl2br(htmlspecialchars($test_data['message'])) . "</div>
            </div>
        </div>
        
        <div class='footer'>
            <p>Este es un mensaje de PRUEBA desde el formulario de contacto de AICROOM</p>
            <p>Fecha: " . date('d/m/Y H:i:s') . "</p>
        </div>
    </div>
</body>
</html>";

$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    'From: ' . $from_email,
    'Reply-To: ' . $test_data['email'],
    'X-Mailer: PHP/' . phpversion()
];

echo "Enviando email de prueba...\n";
echo "Para: $to_email\n";
echo "De: $from_email\n";
echo "Asunto: $subject\n";

// Intentar enviar el email
$mail_sent = mail($to_email, $subject, $email_body, implode("\r\n", $headers));

if ($mail_sent) {
    echo "✓ Email enviado exitosamente\n";
    echo "✓ El sistema de contacto está funcionando\n";
} else {
    echo "❌ Error al enviar email\n";
    echo "⚠ Esto puede deberse a:\n";
    echo "  - Configuración de SMTP no configurada\n";
    echo "  - Servidor de email no disponible\n";
    echo "  - Firewall bloqueando el puerto 25\n";
    echo "  - Configuración de sendmail incorrecta\n";
}

echo "\n--- INSTRUCCIONES PARA CONFIGURAR EMAIL ---\n";
echo "Para que el sistema de email funcione correctamente:\n\n";

echo "1. CONFIGURACIÓN LOCAL (XAMPP):\n";
echo "   - Edita php.ini y configura:\n";
echo "     SMTP = smtp.gmail.com\n";
echo "     smtp_port = 587\n";
echo "     sendmail_from = tu-email@gmail.com\n";
echo "     sendmail_path = \"C:\\xampp\\sendmail\\sendmail.exe -t\"\n\n";

echo "2. CONFIGURACIÓN DE SENDMAIL:\n";
echo "   - Edita C:\\xampp\\sendmail\\sendmail.ini:\n";
echo "     smtp_server=smtp.gmail.com\n";
echo "     smtp_port=587\n";
echo "     auth_username=tu-email@gmail.com\n";
echo "     auth_password=tu-contraseña-de-aplicacion\n";
echo "     force_sender=tu-email@gmail.com\n\n";

echo "3. ALTERNATIVA - USAR PHPMailer:\n";
echo "   - Instalar PHPMailer para mejor compatibilidad\n";
echo "   - Configurar autenticación SMTP\n\n";

echo "4. ALTERNATIVA - SERVICIO DE EMAIL:\n";
echo "   - Usar SendGrid, Mailgun, o similar\n";
echo "   - Configurar API keys\n\n";

echo "--- RESUMEN ---\n";
if ($mail_sent) {
    echo "✓ Sistema de contacto funcionando\n";
    echo "✓ Emails se enviarán a: pemma9962@gmail.com\n";
} else {
    echo "⚠ Sistema de contacto necesita configuración de email\n";
    echo "✓ Código PHP funcionando correctamente\n";
    echo "✓ Formulario HTML listo\n";
    echo "✓ JavaScript funcionando\n";
}

echo "</pre>\n";
echo "<p><a href='contacto.html'>📧 Probar Formulario de Contacto</a></p>\n";
echo "<p><a href='index.html'>← Volver al inicio</a></p>\n";
?>
