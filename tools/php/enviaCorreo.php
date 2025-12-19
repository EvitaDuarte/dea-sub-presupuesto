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

function EnviaCorreo($destino, $origen, $asunto, $cuerpo, &$v_mensaje, $v_UsuGene, $v_PassGene, $archivoAdjunto = null){
    $v_mensaje = "";

    // Separar varios correos
    $aCorreos = array_map('trim', explode(";", $destino));

    for ($i = 0; $i < 3; $i++) {// Realizar tres intentos

        try {

            $mail = new PHPMailer(true);

            // Config SMTP
            $mail->isSMTP();
            $mail->CharSet    = 'UTF-8';
            $mail->Host       = 'correo.ife.org.mx';
            $mail->SMTPAuth   = true;
            $mail->Username   = $v_UsuGene;
            $mail->Password   = $v_PassGene;
            $mail->Port       = 465;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 587;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Remitente
            $mail->setFrom($origen, ('Validación de Cuentas DEA-DRF'));
            $mail->isHTML(true);

            $mail->Subject = $asunto;
            $mail->Body    = $cuerpo;

            // Para efectos de depuración
            //$mail->SMTPDebug = 2;
            //$mail->Debugoutput = 'html';

            // Adjuntar archivo
            if ($archivoAdjunto && file_exists($archivoAdjunto)) {
                $mail->addAttachment($archivoAdjunto);
            }

            // Destinatarios
            foreach ($aCorreos as $corr) {
                $corr = trim($corr);
                if (!empty($corr)){
                    if (filter_var($corr, FILTER_VALIDATE_EMAIL)) {
                        $mail->addAddress($corr);
                        $v_mensaje .="[$corr]";
                    } else {
                        $v_mensaje .= "Dirección de correo inválida: [$corr]";
                    }
                }
            }

            // Intento de envío
            if ($mail->send()) {
                // $v_mensaje = "<br>El correo electrónico se envió correctamente a " . $v_mensaje;
                return true;
            } else {
                $v_mensaje .= "<br>Fallo intento ".($i+1)." al enviar correo: ".$mail->ErrorInfo;
            }

        } catch (Exception $e) {
            $v_mensaje .= "Error en intento ".($i+1).": ".$e->getMessage();
        }

    }

    // Si llega aquí, fallaron los 3 intentos
    $v_mensaje .= "<br>❌ No se logró enviar el correo después de 3 intentos.";
    return false;
}


// function EnviaCorreo($destino, $origen, $asunto, $cuerpo, &$v_mensaje, $v_UsuGene, $v_PassGene, $archivoAdjunto = null)
// {
//     $mail		= new PHPMailer(true);
// 	$v_mensaje	= "";
//     try {

//         // 🔹 Separar múltiples correos del destino
//         $aCorreos = array_map('trim', explode(";", $destino));

//         $mail->isSMTP();
//         $mail->Host       = 'correo.ife.org.mx';
//         $mail->SMTPAuth   = true;
//         $mail->Username   = $v_UsuGene;
//         $mail->Password   = $v_PassGene;
//         $mail->Port       = 465;
//         $mail->Subject = utf8($asunto);
//         $mail->Body    = utf8($cuerpo);
//         // Remitente
//         $mail->setFrom($origen, utf8('Validación de Cuentas DEA-DRF'));
//         $mail->isHTML(true);


//         // 🔹 Adjuntar archivo
//         if ($archivoAdjunto && file_exists($archivoAdjunto)) {
//             $mail->addAttachment($archivoAdjunto);
//         }

//         // 🔹 Agregar cada correo por separado
//         foreach ($aCorreos as $correo) {
//             if (!empty($corr) && filter_var($corr, FILTER_VALIDATE_EMAIL)) {
//                 $mail->addAddress($correo);
//             }else{
//             	$v_mensaje .= '<br> Dirección de correo inválida: [' . $correo . "]";
//             }
//         }
//         for($i=0;$i<3;$i++){
//         	if ($mail->send() ){
// 		        $v_mensaje = 'El correo electrónico se envió correctamente.';
//         		return true;
//         	}else{
//         		$v_mensaje .= "Se envía correo($i+1)";
//         	}
//         }
//         return false;

//     } catch (Exception $e) {

//         $v_mensaje  = '- El correo electr&oacute;nico no puede enviarse.';
//         $v_mensaje .= '<br>Tipo de error: ' . $mail->ErrorInfo;
//         $v_mensaje .= '<br>- Favor de ponerse en contacto con el administrador del sistema.';

//         return false;
//     }
// }


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