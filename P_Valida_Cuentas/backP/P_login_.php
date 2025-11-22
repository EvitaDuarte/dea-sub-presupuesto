<?php
/*  
* * * * * * * * * * * * * * * * * * * * * * * * * 
* Autor   : Miguel Ángel Bolaños Guillén        *
* Sistema : Sistema de Operación Bancaria Web   *
* Fecha   : Septiembre 2023                     *
* Descripción : Rutinas para ejecutar codigo    * 
*               relacionado con el login al     *
*               Sistema                         *
* * * * * * * * * * * * * * * * * * * * * * * * *  */
	// ______________________________________________
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	//_______________________________________________
	// ______________________________  Comentar  para producción
	// _______________________________	
	date_default_timezone_set('America/Mexico_City');
	if (session_status() === PHP_SESSION_NONE) {
	    session_start();
	} 
	// _______________________________________
	$_SESSION['conW']	= "../../cgi-bin/con_pg_Cuentas.php";
	$_SESSION['fpdf']	= "../../tools/php/fpdf/mc_table.php";
    $_SESSION['_Mail_'] = "../../tools/php/enviaCorreo.php";
    $_SESSION['xls']	= '../../tools/PhpSpreadsheet-master/vendor/autoload.php';
    $_SESSION['vendor']	= "../../tools/vendor/autoload.php";
    $_SESSION['mpdf']	= "../../tools/vendor/mpdf/";
    $_SESSION["logo"]	= "../assetsF/img/ine_logo_pdf.jpg";

	// _______________________________________
	require_once("P_rutinas_.php");
	$validador = array('success' => false , 'mensaje' => array()  ,  'resultados' => array(),  'parametros' => array() , 'paso' => array() ); 
	$validador["parametros"] = json_decode(file_get_contents("php://input"), true); // array('Usuario'=>$_POST["user_login"] , 'Contra' =>$_POST["password_login"]);
	// _______________ Funcion principal ___________________________
	$vOpc 		= "validaLdap";
	switch ($vOpc) {
		case "validaLdap":
			//var_dump($validador);
			validaCredenciales($validador);
			if ($validador["success"]==true){ // por que hago esto , por que login_.php se invoca por submit??
				//$_SESSION['ValCtasError'] ="";
				//header_remove('x-powered-by');
				//header("location:../P_Cuentas00_00.php"); // Aquí se llama a menú principal
				//echo json_encode($validador);
				//exit;
				//return;
			}else{
				////$_SESSION['ValCtasError'] ="Credenciales Incorrectas o Inactivo";
				//header_remove('x-powered-by');
				//header("location:../P_Cuentas00_home.php");exit;
				//return;
			}
		break;
	}
	header_remove('x-powered-by');
	header('Content-type: application/json; charset=utf-8');
	echo json_encode($validador);
return;
// __________________________________________
// __________________________________________
// __________________________________________
// __________________________________________
// __________________________________________
// __________________________________________
// __________________________________________
function validaCredenciales(&$validador){
	//$vUsu		= $validador["parametros"]["Usuario"];
	//$v_Pass     = json_decode(json_encode(( $validador["parametros"]["Contra"] )));

	if (session_status() === PHP_SESSION_NONE) {
	    session_start();
	} 

    $vUsu		  = $validador["parametros"]["Usuario"];
    $vPassCifrada = $validador["parametros"]["Contra"]; // Ya viene cifrada en base64
    $v_Pass		  = desencriptaNativo($vPassCifrada);
    $cOriginal	  = $v_Pass;

	if ($cOriginal !== false) {
	    $validador["descifrada"] =  "Dato original desencriptado correctamente"; //Dato original desencriptado (\$cOriginal): [" . $cOriginal . "]";
	} else {
		$validador["success"]	  = false;
	    $validador["mensaje"] 	  = "Error al desencriptar. Verifica el formato, las claves o la configuración de OpenSSL.\n";
	    $_SESSION['ValCtasError'] = $validador["mensaje"];
	}

	if(validaLdap($vUsu, $v_Pass) == '9'){
		
		$validador["mensaje"] = "Credenciales Validas";
		if(getPermisos(($vUsu))){ // Busca en la tabla de usuarios
			$sql	= "select a.esquema as salida from esquemas a, usuarios b " .
					  "where b.usuario_id=:idUsu and a.esquema_id = b.esquema_id ";
			$rol	= getCampo($sql,[":idUsu"=>$vUsu]); 
			$alias = obtenAliasUsuario($vUsu);
			if ($rol!=""){
				$v_Datos	= getDatos( $vUsu, $v_Pass);// regresa cadena de datos separada por |
				$v_Datos 	= explode("|", $v_Datos);					 // Se convierte en arreglo
				$salida		= $v_Datos[0];
				if ( $salida=="1"){
					$validador["paso"]			  = $rol;
					$validador["resultados"]	  = $v_Datos;
					$validador["success"] 		  = true;
					// Genera variables de Sesión
					$_SESSION['ValCtasClave']		= $v_Datos[1];
					$_SESSION['ValCtasApellidos']	= $v_Datos[3];
					$_SESSION['ValCtasNombres']		= $v_Datos[4];
					$_SESSION['ValCtasCurp']		= $v_Datos[6];
					$_SESSION['ValCtasNC']			= $v_Datos[10]; // Nombre completo(Empezando por apellidos)
					$_SESSION["ValCtasPuesto"]		= $v_Datos[11];
					$_SESSION["ValCtasEsquema"]		= $rol;
					$_SESSION['ValCtasTituloS']		= "Sistema de Validación de Cuentas";
					$_SESSION["ValCtasError"]		= "";
					$_SESSION['tiempo']				= time();
					$_SESSION['alias']				= $alias;
				}else{
					$validador["success"] = false;
					$validador["mensaje"] = "Credenciales inválidas";
				}
			}else{
				$validador["mensaje"] = "Credenciales inválidas (1)"; // No hay rol
			}
		}else{
			$validador["mensaje"] = "Credenciales inválidas (2)";		// No esta el usuario
		}

	}else{
		$validador["success"] = false;
		$validador["mensaje"] = "**Credenciales InVálidas**";
	}
}
// __________________________________________
function validaLdap($username, $password){
	//return "9";
	//$key = '123456'; // Esto es lo que hay que proteger para el veracode
	// $decrypted = decrypt($password, $key);
	// error_reporting(E_ERROR);
	//$password	= decryptPasswordFromBase64($password64,$_SESSION['login_nonce']);
	$salida		= "0";
	if($connect = @ldap_connect('ldap://autenticacion.ife.org.mx')){
			ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($connect, LDAP_OPT_REFERRALS,0);
			if(($bind = @ldap_bind($connect)) == false){
			  $salida = "2";
			  return $salida;
			}
			if (($res_id = ldap_search( $connect,"ou=people,dc=ife.org.mx","uid=$username")) == false) {
			  //"failure: search in LDAP-tree failed<br>";
			  return "3";
			}
			if (ldap_count_entries($connect, $res_id) != 1) {
			  //"failure: username $username found more than once<br>\n";
			  return "4";;
			}
			if (( $entry_id = ldap_first_entry($connect, $res_id))== false) {
			  //"failur: entry of searchresult couln't be fetched<br>\n";
			  return "5";
			}
			if (( $user_dn = ldap_get_dn($connect, $entry_id)) == false) {
			  //"failure: user-dn coulnd't be fetched<br>\n";
			  return "6";
			}
			/* Authentifizierung des User */
			//if (($link_id = @ldap_bind($connect, $user_dn, $decrypted)) == false) {
			if (($link_id = @ldap_bind($connect, $user_dn, $password)) == false) {
				//"failue: username, password didn't match: $user_dn<br>\n";
				return "7";
			}
			return "9";
			@ldap_close($connect);
	}else{
		$salida = "1"; //Si no hay conexion con el servidor
	}
	@ldap_close($connect);
	return $salida;
}
// __________________________________________
function getDatos($username, $password){
	$salida = "";
	// EL Ldap solo funciona en la intranet del INE
	if($connect = @ldap_connect('ldap://autenticacion.ife.org.mx')){
		if(ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3)){}
		if(($bind = @ldap_bind($connect)) == false){
		  return "0";
		}
	
		$res_id   = ldap_search( $connect, "ou=people,dc=ife.org.mx", "uid=$username");
		$entry_id = ldap_first_entry($connect, $res_id);
		
		if($entry_id){
			$salida = "1|";
			$valores  = ldap_get_values($connect, $entry_id, "uid");
				$salida = $salida . $valores[0] ."|";
			$valores  = ldap_get_values($connect, $entry_id, "mail");
				$salida = $salida . $valores[0] ."|";
			$valores  = ldap_get_values($connect, $entry_id, "sn");
				$salida = $salida . $valores[0] ."|";
			$valores  = ldap_get_values($connect, $entry_id, "givenname");
				$salida = $salida . $valores[0] ."|";
			$valores  = ldap_get_values($connect, $entry_id, "ou");
				$salida = $salida . $valores[0] ."|";
			$valores  = ldap_get_values($connect, $entry_id, "curp");
				$salida = $salida . $valores[0] ."|";
			$valores  = ldap_get_values($connect, $entry_id, "idEstado");
				$salida = $salida . $valores[0] ."|";
			$valores  = ldap_get_values($connect, $entry_id, "idDistrito");
				$salida = $salida . $valores[0] ."|";
			$valores  = getURAdscripcion(
										ldap_get_values($connect, $entry_id, "idEstado")[0], 
										ldap_get_values($connect, $entry_id, "idDistrito")[0],
										ldap_get_values($connect, $entry_id, "ou")[0]);
				$salida = $salida . $valores ."|";
			$valores  = ldap_get_values($connect, $entry_id, "cn");
				$salida = $salida . $valores[0] . "|";
			$valores  = ldap_get_values($connect, $entry_id, "personalTitle");
				$salida = $salida . $valores[0];

			return $salida;
		}else{
			return "0";
		}
    	@ldap_close($connect);

	}else{
		$salida = "0"; //Si no hay conexion con el servidor
	}
	
	@ldap_close($connect);
	return $salida;
}
// __________________________________________
function decryptPasswordFromBase64($encryptedBase64, string $privatePem, string $nonce) {
    $privateKey = openssl_pkey_get_private($privatePem); // mover aquí la conversión

    if (!$privateKey) {
        throw new Exception('No se pudo obtener la clave privada');
    }

    $encrypted = base64_decode($encryptedBase64);

    if (!openssl_private_decrypt($encrypted, $decrypted, $privateKey, OPENSSL_PKCS1_OAEP_PADDING)) {
        throw new Exception('Error al descifrar con clave privada');
    }

    [$password, $receivedNonce] = explode(':', $decrypted, 2);

    if (!hash_equals($nonce, $receivedNonce)) {
        throw new Exception('Nonce inválido');
    }

    return $password;
}

// __________________________________________
/**
 * Descifra un ciphertext Base64 cifrado con RSA-OAEP (SHA-1 OAEP en PHP < 8.0; OPENSSL_PKCS1_OAEP_PADDING usa OAEP).
 *
 * @param string $cipherB64  Ciphertext en Base64 (emitido por la función JS anterior)
 * @param string $privatePem Contenido PEM de la clave privada (p. ej. file_get_contents('/ruta/private.pem'))
 * @return array ['password' => string, 'nonce' => string]
 * @throws RuntimeException si falla el descifrado o el formato
 */
// function decryptPasswordFromBase64(string $cipherB64, string $privatePem): array {
//     $cipher = base64_decode($cipherB64, true);
//     if ($cipher === false) {
//         throw new \RuntimeException('Base64 inválido');
//     }

//     // Obtener recurso de clave privada
//     $pkey = openssl_pkey_get_private($privatePem);
//     if ($pkey === false) {
//         throw new \RuntimeException('Clave privada inválida');
//     }

//     // Descifrar con OAEP
//     $ok = openssl_private_decrypt($cipher, $plaintext, $pkey, OPENSSL_PKCS1_OAEP_PADDING);
//     // liberar recursos (no estrictamente necesario pero limpio)
//     openssl_pkey_free($pkey);

//     if ($ok === false) {
//         throw new \RuntimeException('Descifrado fallido');
//     }

//     if (strpos($plaintext, ':') === false) {
//         throw new \RuntimeException('Formato de plaintext inválido');
//     }

//     list($password, $nonce) = explode(':', $plaintext, 2);

//     // No devolver datos sensibles en excepciones / logs.
//     return ['password' => $password, 'nonce' => $nonce];
// }
// __________________________________________
// function decrypt($jsonStr, $passphrase){
// 	$json = json_decode($jsonStr, true);
// 	$salt = hex2bin($json["s"]);
// 	$iv = hex2bin($json["iv"]);
// 	$ct = base64_decode($json["ct"]);
// 	$concatedPassphrase = $passphrase . $salt;
// 	$md5 = [];
// 	$md5[0] = md5($concatedPassphrase, true);
// 	$result = $md5[0];
// 	for ($i = 1; $i < 3; $i++) {
// 		$md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
// 		$result .= $md5[$i];
// 	}
// 	$key = substr($result, 0, 32);
// 	$data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
// 	return json_decode($data, true);
// }
// ___________________________________________
function obtenAliasUsuario($vUsu){
	// se cambio select alias x select area, hasta ver para que se usa ese alias creo es para generar archivos con el alias
	$sql   = "select area as salida from public.usuarios where usuario_id=:usuario_id";
	$alias = getCampo($sql,[":usuario_id"=>$vUsu]);
	if ($alias==""){
		$alias = $_SERVER['REMOTE_ADDR']; // toma la ip
		$alias = str_replace(".", "", $alias);
	}
	return $alias;
}
// ___________________________________________
/**
 * Desencripta un string cifrado por la función JS 'encriptaNativo' usando AES-256-CBC y OpenSSL.
 *
 * @param string $cEncriptado - El string formateado (base64_IV|base64_Key|base64_Texto_Cifrado)
 * @return string|false El dato original (A) o false si falla la desencriptación.
 */
function desencriptaNativo(string $cEncriptado): string|false {
    // 1. Separar los componentes (IV, Clave, Texto Cifrado)
    $partes = explode('|', $cEncriptado);

    if (count($partes) !== 3) {
        return false; // Formato inválido
    }

    list($base64_iv, $base64_key, $base64_cifrado) = $partes;

    // 2. Decodificar de Base64 a binario (importante: PHP usa base64_decode)
    $iv_binario = base64_decode($base64_iv);
    $key_binario = base64_decode($base64_key);
    $texto_cifrado_binario = base64_decode($base64_cifrado);

    // 3. Definir el método de cifrado (debe coincidir con JS: 256 bits)
    $metodo = 'AES-256-CBC';

    // 4. Verificaciones de seguridad/longitud
    // OpenSSL requiere que la clave sea de 32 bytes para AES-256 y el IV de 16 bytes.
    if (strlen($key_binario) !== 32 || strlen($iv_binario) !== 16) {
        return false; // Clave o IV con longitud incorrecta
    }

    // 5. Desencriptar usando openssl_decrypt
    $cOriginal = openssl_decrypt(
        $texto_cifrado_binario,
        $metodo,
        $key_binario,
        OPENSSL_RAW_DATA, // Indica que los datos cifrados están en formato binario raw
        $iv_binario
    );

    // 6. Retornar el resultado
    return $cOriginal;
}
// ___________________________________________
?>