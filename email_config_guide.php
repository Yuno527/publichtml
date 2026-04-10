qwwwwwwa<?php
/**
 * Guía de configuración de email para XAMPP
 */

echo "<h2>Guía de Configuración de Email para XAMPP</h2>\n";
echo "<div style='max-width: 800px; margin: 0 auto; padding: 20px;'>\n";

echo "<h3>📧 Configuración Paso a Paso</h3>\n";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
echo "<h4>1. Configurar php.ini</h4>\n";
echo "<p>Edita el archivo: <code>C:\\xampp\\php\\php.ini</code></p>\n";
echo "<p>Busca y modifica estas líneas:</p>\n";
echo "<pre style='background: #e9ecef; padding: 15px; border-radius: 5px;'>\n";
echo "SMTP = smtp.gmail.com\n";
echo "smtp_port = 587\n";
echo "sendmail_from = tu-email@gmail.com\n";
echo "sendmail_path = \"C:\\xampp\\sendmail\\sendmail.exe -t\"\n";
echo "</pre>\n";
echo "</div>\n";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
echo "<h4>2. Configurar sendmail.ini</h4>\n";
echo "<p>Edita el archivo: <code>C:\\xampp\\sendmail\\sendmail.ini</code></p>\n";
echo "<p>Configura:</p>\n";
echo "<pre style='background: #e9ecef; padding: 15px; border-radius: 5px;'>\n";
echo "smtp_server=smtp.gmail.com\n";
echo "smtp_port=587\n";
echo "auth_username=tu-email@gmail.com\n";
echo "auth_password=tu-contraseña-de-aplicacion\n";
echo "force_sender=tu-email@gmail.com\n";
echo "</pre>\n";
echo "</div>\n";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;'>\n";
echo "<h4>⚠️ Importante: Configurar Gmail</h4>\n";
echo "<ol>\n";
echo "<li>Activa la verificación en 2 pasos en tu cuenta de Gmail</li>\n";
echo "<li>Genera una contraseña de aplicación</li>\n";
echo "<li>Usa esa contraseña en sendmail.ini</li>\n";
echo "</ol>\n";
echo "</div>\n";

echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #17a2b8;'>\n";
echo "<h4>🔄 Reiniciar Apache</h4>\n";
echo "<p>Después de hacer los cambios:</p>\n";
echo "<ol>\n";
echo "<li>Detén Apache en XAMPP Control Panel</li>\n";
echo "<li>Inicia Apache nuevamente</li>\n";
echo "</ol>\n";
echo "</div>\n";

echo "<h3>🧪 Probar el Sistema</h3>\n";
echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745;'>\n";
echo "<p><strong>Prueba 1:</strong> <a href='test_working_contact.php' style='color: #155724; font-weight: bold;'>Probar Sistema de Contacto</a></p>\n";
echo "<p><strong>Prueba 2:</strong> <a href='contacto.html' style='color: #155724; font-weight: bold;'>Formulario de Contacto</a></p>\n";
echo "<p><strong>Prueba 3:</strong> <a href='setup_email.php' style='color: #155724; font-weight: bold;'>Verificar Configuración</a></p>\n";
echo "</div>\n";

echo "<h3>📋 Estado Actual del Sistema</h3>\n";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";

echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>\n";
echo "<p><strong>Mail function:</strong> " . (function_exists('mail') ? '✅ Disponible' : '❌ No disponible') . "</p>\n";

$sendmail_path = ini_get('sendmail_path');
echo "<p><strong>Sendmail path:</strong> " . ($sendmail_path ? $sendmail_path : '❌ No configurado') . "</p>\n";

$smtp_host = ini_get('SMTP');
echo "<p><strong>SMTP Host:</strong> " . ($smtp_host ? $smtp_host : '❌ No configurado') . "</p>\n";

$smtp_port = ini_get('smtp_port');
echo "<p><strong>SMTP Port:</strong> " . ($smtp_port ? $smtp_port : '❌ No configurado') . "</p>\n";

echo "</div>\n";

echo "<h3>🚀 Sistema de Contacto Funcional</h3>\n";
echo "<div style='background: #e2e3e5; padding: 20px; border-radius: 8px; margin: 20px 0;'>\n";
echo "<p>✅ <strong>contact_working.php</strong> - Handler simplificado</p>\n";
echo "<p>✅ <strong>contact_working.js</strong> - JavaScript funcional</p>\n";
echo "<p>✅ <strong>contacto.html</strong> - Formulario actualizado</p>\n";
echo "<p>✅ <strong>Emails se envían a:</strong> pemma9962@gmail.com</p>\n";
echo "</div>\n";

echo "<div style='text-align: center; margin: 30px 0;'>\n";
echo "<a href='contacto.html' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px;'>📧 Probar Formulario</a>\n";
echo "<a href='test_working_contact.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px;'>🧪 Probar Sistema</a>\n";
echo "<a href='index.html' style='background: #6c757d; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px;'>🏠 Inicio</a>\n";
echo "</div>\n";

echo "</div>\n";
?>
