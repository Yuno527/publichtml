
<?php
/**
 * Configuración de email para XAMPP
 */

echo "<h2>Configuración de Email para XAMPP</h2>\n";
echo "<pre>\n";

echo "--- CONFIGURACIÓN ACTUAL DE PHP ---\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Mail function available: " . (function_exists('mail') ? 'Sí' : 'No') . "\n";

$sendmail_path = ini_get('sendmail_path');
echo "Sendmail path: " . ($sendmail_path ? $sendmail_path : 'No configurado') . "\n";

$smtp_host = ini_get('SMTP');
$smtp_port = ini_get('smtp_port');
echo "SMTP Host: " . ($smtp_host ? $smtp_host : 'No configurado') . "\n";
echo "SMTP Port: " . ($smtp_port ? $smtp_port : 'No configurado') . "\n";

echo "\n--- INSTRUCCIONES DE CONFIGURACIÓN ---\n";
echo "Para configurar el envío de emails en XAMPP:\n\n";

echo "1. EDITA EL ARCHIVO php.ini:\n";
echo "   Ubicación: C:\\xampp\\php\\php.ini\n";
echo "   Busca y modifica estas líneas:\n";
echo "   SMTP = smtp.gmail.com\n";
echo "   smtp_port = 587\n";
echo "   sendmail_from = tu-email@gmail.com\n";
echo "   sendmail_path = \"C:\\xampp\\sendmail\\sendmail.exe -t\"\n\n";

echo "2. EDITA EL ARCHIVO sendmail.ini:\n";
echo "   Ubicación: C:\\xampp\\sendmail\\sendmail.ini\n";
echo "   Configura:\n";
echo "   smtp_server=smtp.gmail.com\n";
echo "   smtp_port=587\n";
echo "   auth_username=tu-email@gmail.com\n";
echo "   auth_password=tu-contraseña-de-aplicacion\n";
echo "   force_sender=tu-email@gmail.com\n\n";

echo "3. CONFIGURAR GMAIL:\n";
echo "   - Activar verificación en 2 pasos\n";
echo "   - Generar contraseña de aplicación\n";
echo "   - Usar la contraseña de aplicación en sendmail.ini\n\n";

echo "4. REINICIAR APACHE:\n";
echo "   - Detener Apache en XAMPP Control Panel\n";
echo "   - Iniciar Apache nuevamente\n\n";

echo "--- PRUEBA DE ENVÍO ---\n";

// Datos de prueba
$to_email = 'pemma9962@gmail.com';
$from_email = 'noreply@aicroom.com';
$subject = '[AICROOM] Prueba de configuración';
$message = 'Esta es una prueba de configuración de email desde AICROOM.';

$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    'From: ' . $from_email,
    'X-Mailer: PHP/' . phpversion()
];

echo "Enviando email de prueba...\n";
echo "Para: $to_email\n";
echo "De: $from_email\n";
echo "Asunto: $subject\n";

$mail_sent = mail($to_email, $subject, $message, implode("\r\n", $headers));

if ($mail_sent) {
    echo "✓ Email enviado exitosamente\n";
    echo "✓ La configuración está funcionando\n";
} else {
    echo "❌ Error al enviar email\n";
    echo "⚠ Revisa la configuración de sendmail\n";
}

echo "\n--- ALTERNATIVA: USAR PHPMailer ---\n";
echo "Si el método nativo no funciona, puedes usar PHPMailer:\n";
echo "1. Descargar PHPMailer desde: https://github.com/PHPMailer/PHPMailer\n";
echo "2. Extraer en una carpeta 'phpmailer' en tu proyecto\n";
echo "3. Usar el siguiente código:\n\n";

echo "<?php\n";
echo "require 'phpmailer/PHPMailerAutoload.php';\n";
echo "\$mail = new PHPMailer;\n";
echo "\$mail->isSMTP();\n";
echo "\$mail->Host = 'smtp.gmail.com';\n";
echo "\$mail->SMTPAuth = true;\n";
echo "\$mail->Username = 'tu-email@gmail.com';\n";
echo "\$mail->Password = 'tu-contraseña-de-aplicacion';\n";
echo "\$mail->SMTPSecure = 'tls';\n";
echo "\$mail->Port = 587;\n";
echo "\$mail->setFrom('noreply@aicroom.com', 'AICROOM');\n";
echo "\$mail->addAddress('pemma9962@gmail.com');\n";
echo "\$mail->isHTML(true);\n";
echo "\$mail->Subject = 'Asunto';\n";
echo "\$mail->Body = 'Contenido del email';\n";
echo "\$mail->send();\n";
echo "?>\n";

echo "</pre>\n";
echo "<p><a href='contacto.html'>📧 Probar Formulario de Contacto</a></p>\n";
echo "<p><a href='test_simple_contact.php'>🧪 Probar Sistema Simple</a></p>\n";
echo "<p><a href='index.html'>← Volver al inicio</a></p>\n";
?>
