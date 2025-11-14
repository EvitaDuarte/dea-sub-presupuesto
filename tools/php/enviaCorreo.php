<?php
//require_once $_SERVER['DOCUMENT_ROOT'] . '/tools/PHPMailer/src/PHPMailer.php'; no funciona porqu en la MV se hizo un alias y el root llega hasta G_OpeFin
//require_once $_SERVER['DOCUMENT_ROOT'] . '/tools/PHPMailer/src/SMTP.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/tools/PHPMailer/src/Exception.php';
// ../../tools/php/enviaCorreo.php";
require_once  '../../tools/PHPMailer/src/PHPMailer.php';
require_once  '../../tools/PHPMailer/src/SMTP.php';
require_once  '../../tools/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function EnviaCorreo($destino, $origen, $asunto, $cuerpo, &$v_mensaje, $archivoAdjunto = null) {
    $mail = new PHPMailer(true); // Modo excepciones

    try {
        $mail->isSMTP();
        $mail->Host       = 'correo.ife.org.mx';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'oficios.dea';
        $mail->Password   = 'hBS13bKxT';
        $mail->Port       = 465;
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // No debe activarse, por como esta configurado la cuenta del INE

        // Remitente y destinatario
        // $mail->setFrom('oficios.dea@ife.org.mx', 'Oficinas Centrales DEA-DRF'); // ❗ No funciono
        $mail->setFrom($origen, 'Oficinas Centrales DEA-DRF'); // ❗ Usa el mismo que el Username
        $mail->addAddress($destino);

        $mail->isHTML(true);
        $mail->Subject = utf8($asunto);
        $mail->Body    = utf8($cuerpo);

        // ✅ Adjuntar archivo si se proporciona
        if ($archivoAdjunto && file_exists($archivoAdjunto)) {
            $mail->addAttachment($archivoAdjunto);
        }
	    // Solicitar acuse de lectura
	    // $mail->addCustomHeader('Disposition-Notification-To', $origen);

        $mail->send();
        $v_mensaje = 'El correo electrónico se envió correctamente.';
        return true;

    } catch (Exception $e) {
        $v_mensaje  = '- El correo electr&oacute;nico no puede enviarse.';
        $v_mensaje .= '<br>Tipo de error: ' . $mail->ErrorInfo;
        $v_mensaje .= '<br>- Favor de ponerse en contacto con el administrador del sistema.';
        return false;
    }
}

/* Esta es para la versión de PHP 5.0
require $_SERVER['DOCUMENT_ROOT'] . '/tools/phpmailer/PHPMailerAutoload.php';
function EnviaCorreo($destino, $origen, $asunto, $cuerpo,&$v_mensaje,$archivoAdjunto=null){
//-----------------------------------------------------------------------------------------------------------------
// Función para el envio de correo electrónico
// Parametros requeridos: Destinatario, Remitente, Copia simple, Copia oculta, Asunto, Cuerpo del mensaje, Archivo
//-----------------------------------------------------------------------------------------------------------------
	$mail			= new PHPMailer; 		// Creación de instancia a clase PHPMailer.
	$mail->isSMTP();                        // Set mailer to use SMTP
	$mail->Host 	= 'correo.ife.org.mx';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                 // Enable SMTP authentication , // true requiere username y password
	$mail->setFrom($origen, $origen,"Oficinas Centrales DEA-DRF");		// Información de quien envia el correo electrónico, Parametros (correo_remitente, nombre_remitente)  
	$mail->addAddress($destino);     		// Add a recipient, Parametros (correo_destinatario, nombre_destinatario ) 
	$mail->isHTML(true);                    // Set email format to HTML
	$mail->Subject		= utf8_decode($asunto);
	$mail->Body			= utf8_decode($cuerpo);
	$mail->Port			= 465 ;
	$mail->Username		= 'oficios.dea'; 
	$mail->Password		= 'hBS13bKxT';
	$v_respuesta		= "";
	$v_regreso			= false;
	
	// ✅ Adjuntar archivo si se proporciona
	if ($archivoAdjunto && file_exists($archivoAdjunto)) {
		$mail->addAttachment($archivoAdjunto);
	}


	if(!$mail->send()) {
		$v_mensaje  = '- El correo electr&oacute;nico no puede enviarse.';
		$v_mensaje .= '<br> Tipo de error: ' . $mail->ErrorInfo . '<br> - Favor de ponerse en contacto con el administrador del sistema.';
		$v_regreso  = false;
	} else {
		$v_mensaje = 'El correo electr&oacute;nico se envi&oacute;.';
		$v_regreso = true;
	}
	
	return $v_regreso;
	
} */

?>