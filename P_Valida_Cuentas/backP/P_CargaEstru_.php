<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	// _______________________________	
	define("VALIDA"		, "Estructura Válida");
	define("XREVISAR"	, "Estructura a revisar");
	define("YAEXISTE"	, "Ya existe combinación en siga -----");
	define("IDXEDO"		, 9);
	define("SCTACONTA"	, "99999");
	date_default_timezone_set('America/Mexico_City');
	if (session_status() === PHP_SESSION_NONE) {
	    session_start();
	} 
	// _______________________________________
	if ( !isset($_SESSION['ValCtasClave'])){
		header("Location: ../P_Cuentas00_home.php");exit; return;
	}
	require_once($_SESSION['conW']);
	require_once($_SESSION['_Mail_']);
	require_once("P_rutinas_.php");
	require_once("M_Catalogos_.php");
	require_once("Pdo/C_Estructuras_.php"); // Clase para manejar insert update a epvalidas y epinvalidas(a revisar)
	require_once("Pdo/C_Urs_.php"); 		// Clase para manejar catalogo URs

	try{
		$idUsuario  = $_SESSION['ValCtasClave'];
		$urUsu		= $_SESSION['ValCtasUrUsu']; // Revisar trae un 1 , en lugar de la UR
	    $param	 	= json_decode(file_get_contents("php://input"), true);
	    $cOpc		= $param["opcion"];
	    $regreso	= array('success' => false , 'mensaje' => '' , 'idUsu'=>$idUsuario ,'parametros'=>$param );


	    switch ($cOpc){
	    	//___________________________________
	    	case "traer_UrIni_UrFin":
	    		revisa_indice();
	    		traer_UrIni_UrFin($param,$regreso);
	    	break;
			//___________________________________
			case "trae_CatUrCtas":
				revisa_indice();
				traer_UrIni_UrFin($param,$regreso);
				if ($regreso["success"]){
					$regreso["success"] = false;
					trae_CatUrCtas($regreso);
				}
			break;
			//___________________________________
	    	case "validarCarga":
	    		validar_Carga($param,$regreso);
	    		valida_Siga($param,$regreso);
	    	break;
			//___________________________________
	    	case "EnviarEstructuras":
	    		EnviarEstructuras($param,$regreso);
	    	break;
			//___________________________________
			case "ReEnviaEstructuras":
				ReEnviarEstructuras($param,$regreso);
			breaK;
			//___________________________________
			case "validaEstructura":
				simulaCargaXls($param,$regreso);
	    		validar_Carga($param,$regreso);
	    		valida_Siga($param,$regreso);
	    		$regreso["parametros_"] = $param;
				//validaEstructura($regreso);
			break;
			//___________________________________
	    	default:
	    		$regreso["mensaje"]= "No esta codificada $cOpc en Carga Estructuras";
	    	break;
	    }
	}catch(Exception $ex){
		$regreso["error"]	= $ex->getMessage();
		$regreso["mensaje"]	= "Se encontro una inconsistencia en Carga Estructuras $cOpc ...";
	}
	header_remove('x-powered-by');							 // remueve el header
	header('Content-type: application/json; charset=utf-8'); // valores en formato JSON caracteres UTF-8
	echo json_encode($regreso);
return;
// _______________________________________________________
function revisa_indice(){
	global $conn_pdo;
	if ( no_existe_indice("precombi","idx_precombi_clvpy6_geo")){
		$sql	= "CREATE INDEX idx_precombi_clvpy6_geo ON precombi (LEFT(clvpy, 6), geografico);";
		$stmt	= $conn_pdo->prepare($sql); // Prepara el SQL
		$res	= $stmt->execute();
	}
}
// _______________________________________________________
function traer_UrIni_UrFin($p,&$r){
	$aRen = metodos::trae_Ur_Ini_Fin($r["idUsu"]); // trae las urs permitidas al usuario

	if (count($aRen)>0){
		
		$r["urIni"]		= $aRen[0]["unidad_inicio"];
		$r["urFin"]		= $aRen[0]["unidad_fin"];
		$r["urLis"]		= $aRen[0]["listaurs"];
		$r["urUsu"]		= $aRen[0]["unidad_id"];

		$aRen = metodos::trae_urls_soap();

		if (count($aRen)>0){
			$r["urlCtas"]	= $aRen[0]["urlctas"];
			$r["urlPys"]	= $aRen[0]["urlpy"];
			$r["validaPy"]	= $aRen[0]["validapy"];
			$aVarCorreo		= metodos::trarCorreoPtoConta();
			if ($aVarCorreo!==null){
				$r["success"]		= true;
				$r["CorreoPto"]		= $aVarCorreo[0]["valor"];
				$r["CorreoConta"]	= $aVarCorreo[1]["valor"];
				$r["CorreoUsu"]		= $aVarCorreo[2]["valor"];
				$r["CorreoPass"]	= $aVarCorreo[3]["valor"];
			}else{
				$r["mensaje"] = "No estan las variables del correo (Presupuesto, Contabilidad, Usuario, Contras";
			}
		}else{
			$r["mensaje"] = "No estan definidas las url para accceder al SIGA";
		}

	}else{
		$r["mensaje"] = "No se logró acceder a datos de urs del usuario " .  $r["idUsu"] ;
	}
}
// _______________________________________________________
function validar_Carga($p,&$r){

	$aXls		= $p["aXls"];
	$validaPy	= $p["validaPY"];
	$soap		= metodos::nuevaSopa($p["urlPys"],$r);
	$cEdo		= "";

//	Se revisa primero en la tabla de Postgresql
	foreach($aXls as &$xls){

		$vDigito = "";
		$cEdo	 = "";
		list($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda,$cEdo) = $xls;
		$r["ine"] = $cIne;
		if ($cEdo===""){
			if (metodos::laUrEstaActiva($cUr)){
				if ( metodos::valida_CuentaMayor($cCta,$ptda,$cSubCta) ){
					if ( metodos::valida_UrPpSpg($cUr,$ai,$pp,$spg)){
						if ( metodos::valida_ProyectoUr($py,$cUr,$vDigito) ){
							if ( metodos::valida_PySiga($py,$soap,$validaPy)){
					    		if ( metodos::valida_Combinacion($cUr,$ai,$cSubCta,$pp,$spg,$py)){ // En la tabla de combinaciones
					    			$cEdo = VALIDA;
					    		}else{

					    			$cEdo = XREVISAR;
					    		}
					    		$r["trace"] = $cEdo;
					    		if ( metodos::solicitada_Anteriormente($cIne,$cUr,$cCta,$cSubCta,$ai,$pp,$spg,$py,$ptda,$cEdo,$r) ){

					    			$cEdo = "NP Ya fue solicitada anteriormente " . $cEdo ;
					    			$r["Estado"] = $cUr;
					    		}
							}else{
								$cEdo = "NP No se encontro el proyecto $py en SIGA";
							}
						}else{
							$cEdo = "NP El dígito del proyecto $py no corresponde a la UR $cUr ($vDigito)";
						}
					}else{
						$cEdo = "NP Combinación Ur-ProgPresupestario-SubPrograma no reconocida [$cUr-$pp-$spg]";
					}
				}else{
					$cEdo = "NP La partida $ptda no corresponde a la cuenta $cCta";
				}
			}else{
				$cEdo = "NP La Ur $cUr no existe o esta inactiva"; 
			}
			$xls[IDXEDO] = $cEdo;
		}

	}
	$r["aXls"] = $aXls;
	
}
// _______________________________________________________
function valida_Siga($p,&$r){
	// ahora revisar si existe en el SIGA solo las que tienen estado Valida o XRevisar
	$soap = metodos::nuevaSopa($p["urlCtas"],$r);
	$cEdo = "";

	foreach($r["aXls"] as &$xls){
		list($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda,$cEdo) = $xls;
		if ($cEdo==VALIDA || $cEdo==XREVISAR){
			$params = Array("segment10" => $cIne	, "segment1" => $cUr, "segment2" => $cCta,
			    			 "segment3"	=> $cSubCta	, "segment5" => $ai , "segment6" => $pp,
			    			 "segment7"	=> $spg		, "segment8" => $py , "segment9" => $ptda );
			$ctaSiga = json_decode(json_encode($soap->consultaCuentas($params)),true); 
			//$r["ctaSiga"][] = $ctaSiga;
			$lNoEsta = true;
			if ( isset($ctaSiga["cuentas"]) ){
				$cCta	 = $cIne . "-" . $cUr . "-" . $cCta . "-" . $cSubCta . "-" . $ai . "-" . $pp . "-" . $spg . "-" . $py . "-" . $ptda;
				if ($cCta===$ctaSiga["cuentas"][0]["concatenatedSegment"]){
					$lNoEsta	 = false;
					$xls[IDXEDO] = "NP " .$xls[IDXEDO] . " " . YAEXISTE;
				}
			}
			$r["lNoEsta"] = $lNoEsta;
			//if ($lNoEsta && $lVal5Seg && $aCtas[$k][9+$nIni]==XREVISAR && $nIni==0){
			if ($lNoEsta && $cEdo==XREVISAR){ // Se busca a 5 segmentos
				$params = Array(
					"segment10" 	=> $cIne,
					"segment5"		=> $ai,
					"segment6"		=> $pp,
		    		"segment7"		=> $spg,
		    		"segment8"		=> $py
				);

				$ctaSiga = json_decode(json_encode($soap->consultaSegmentoUnion1($params)),true);
				//var_ dump($ctaSiga); return;  // ["noregistros"][0]["concatenatedSegment"]
				$r["ctaSiga"] = $ctaSiga;
				if ( isset($ctaSiga["noregistros"])  ){
					$xls[IDXEDO] = VALIDA; // .
				}
			}
		}
	}
	$r["Edo"]		= $cEdo; // util solo para la captura manual
	$r["success"]	= true;
}
// _______________________________________________________
function EnviarEstructuras($p,&$r){
	global $conn_pdo;
	$nRenV = 0; $nRenI = 0;
	try{
		$conn_pdo->beginTransaction();
		$cNoEnvio	  = metodos::numeroEnvio($p["urUsu"],date('Y')); //  date('Y-m-d-h:i:s', time());
		$cUsuario	  = $r["idUsu"];
		$cCorreoUr    = trim($cUsuario). "@ine.mx";
		$cMailGener   = $p["correo"]["correoUsu"];
		$cPassGener   = $p["correo"]["correoPass"];
		$cCorreoPto	  = $p["correo"]["correoPto"];
		$cCorreoConta = $p["correo"]["correoConta"];
		$aEstru		  = $p["datos"];
		$oEstruc	  = new Estructura();
		$aPtoEstr	  = [];
		$aContaEstr   = [];
		$cMensaje	  = $cMensaje2 = "";
		foreach ($aEstru as $vEstru ) {
			$cArea = ($vEstru["subcuenta"]===SCTACONTA)?"C":"P";
			$cEdo  = $vEstru["estado"];
			$oEstruc->cargaEstructura($vEstru);
			$oEstruc->cargaComplemento($cNoEnvio,$cUsuario,$cArea);
			if ( $oEstruc->actualizaEstructura() ){
				if ($cEdo===VALIDA){
					$nRenV++;
				}else{
					$nRenI++;					
				}
				if ($cArea=="C"){
					array_push($aContaEstr,$vEstru);
				}else{
					array_push($aPtoEstr,$vEstru);
				}
			}
		}
		$lEnvio = true;
		if (count($aPtoEstr)>0){
			$lEnvio = $oEstruc->enviaEstructuras($aPtoEstr,$cNoEnvio,$cCorreoUr,$cCorreoPto,$cMailGener,$cPassGener,$cMensaje1);
		}
		if ($lEnvio){
			if (count($aContaEstr)>0){
				$lEnvio = $oEstruc->enviaEstructuras($aContaEstr,$cNoEnvio,$cCorreoUr,$cCorreoConta,$cMailGener,$cPassGener,$cMensaje2);
			}
		}
		$r["success"] = $lEnvio;
		if ($lEnvio){
			$conn_pdo->commit();
			//$conn_pdo->rollBack();
			$r["mensaje"] = "Se envió la solicitud de alta de  $nRenV estructuras válidas y $nRenI estructuras a Revisar. Número de envío: $cNoEnvio . Correo enviado a < $cMensaje1 $cMensaje2 > Se tiene que esperar su alta en el SIGA por parte  de contabilidad y/o Presupuesto";
		}else{
			$conn_pdo->rollBack();
			$r["mensaje"] =$cMensaje1 ." >><< ". $cMensaje2;
		}
		$r["mensajeenvio1"] = $cMensaje1;
		$r["mensajeenvio2"] = $cMensaje2;
		

	}catch(Exception $e){
		$r["mensaje"] = "Ocurrio una Inconsistencia en EnviarEstructuras ";
		$r["error"]   = $e->getMessage();
		$conn_pdo->rollBack();
	}

}
// _______________________________________________________
function ReEnviarEstructuras($p,&$r){
	global $conn_pdo;
	$nRenV = 0; $nRenI = 0;
	try{
		//$conn_pdo->beginTransaction(); No hay actualizaciones a tablas
		$aEstru		  = $p["datos"];
		$texto		  = $aEstru[0]["estado"]; // NP Ya fue solicitada anteriormente OF162025000048_Estructura Válida
		$cNoEnvio	  = explode('_', explode('anteriormente ', $texto)[1])[0];	// metodos::numeroEnvio($p["urUsu"],date('Y')); //  date('Y-m-d-h:i:s', time());
		$cUsuario	  = $r["idUsu"];
		$cCorreoUr    = trim($cUsuario). "@ine.mx";
		$cMailGener   = $p["correo"]["correoUsu"];
		$cPassGener   = $p["correo"]["correoPass"];
		$cCorreoPto	  = $p["correo"]["correoPto"];
		$cCorreoConta = $p["correo"]["correoConta"];

		$oEstruc	  = new Estructura();
		$aPtoEstr	  = [];
		$aContaEstr   = [];
		$cMensaje	  = $cMensaje2 = "";
		foreach ($aEstru as $vEstru ) {
			$cArea = ($vEstru["subcuenta"]===SCTACONTA)?"C":"P";
			$cEdo  = $vEstru["estado"];
			$oEstruc->cargaEstructura($vEstru);
			$oEstruc->cargaComplemento($cNoEnvio,$cUsuario,$cArea);
			//if ( $oEstruc->actualizaEstructura() ){
				if ( str_contains($cEdo, VALIDA) ){
					$nRenV++;
				}else{
					$nRenI++;					
				}
				if ($cArea=="C"){
					array_push($aContaEstr,$vEstru);
				}else{
					array_push($aPtoEstr,$vEstru);
				}
			//}
		}
		$lEnvio = true;
		if (count($aPtoEstr)>0){
			$lEnvio = $oEstruc->enviaEstructuras($aPtoEstr,$cNoEnvio,$cCorreoUr,$cCorreoPto,$cMailGener,$cPassGener,$cMensaje1);
		}
		if ($lEnvio){
			if (count($aContaEstr)>0){
				$lEnvio = $oEstruc->enviaEstructuras($aContaEstr,$cNoEnvio,$cCorreoUr,$cCorreoConta,$cMailGener,$cPassGener,$cMensaje2);
			}
		}
		$r["success"] = $lEnvio;
		if ($lEnvio){
			//$conn_pdo->commit(); No hay actualizaciones a tablas
			//$conn_pdo->rollBack();
			$r["mensaje"] = "Se vuelve a solicitar el alta de $nRenV estructuras válidas y $nRenI estructuras a Revisar. Número de envío: $cNoEnvio . Correo enviado a < $cMensaje1 $cMensaje2 > Se tiene que esperar su alta en el SIGA por parte  de contabilidad y/o Presupuesto";
		}else{
			//$conn_pdo->rollBack(); No hay actualizaciones a tablas
			$r["mensaje"] =$cMensaje1 ." >><< ". $cMensaje2;
		}
		$r["mensajeenvio1"] = $cMensaje1; 
		$r["mensajeenvio2"] = $cMensaje2;
		

	}catch(Exception $e){
		$r["mensaje"] = "Ocurrio una Inconsistencia en ReEnviarEstructuras ";
		$r["error"]   = $e->getMessage();
		//$conn_pdo->rollBack(); No hay actualizaciones a tablas
	}

}
// _______________________________________________________
function trae_CatUrCtas(&$r){
	$aReg = metodos::trae_CuentasMayor();
	if( count($aReg)>0){
		$r["cuentas"] = $aReg;
		$oUr  = new Urs();
		$cUri = $r["urIni"];
		$cUrf = $r["urFin"];
		$aReg = $oUr->traeRangoUrs($cUri,$cUrf);
		if (count($aReg)>0){
			$r["urs"] 		= $aReg;
			$r["success"]	= true;
		}else{
			$r["mensaje"] = "No hay rango para Urs";
		}
	}else{
		$r["mensaje"] = "No hay cuentas de mayor";
	}

}
// _______________________________________________________
// function validaEstructura(&$r){
// 	$estructura = $r["parametros"]["estructura"];

// 	$r["c1Ine"]  = $cIne = $estructura['cIne'];
// 	$r["c2Ur"]   = $cUr  = $estructura['cUr'];
// 	$r["c3Cta"]  = $cCta = $estructura['cCta'];
// 	$r["c4Sub"]  = $cSub = $estructura['cScta'];
// 	$r["c5Ai"]   = $cAi  = $estructura['cAi'];
// 	$r["c6Pp"]   = $cPp  = $estructura['cPp'];
// 	$r["c7Spg"]  = $cSpg = $estructura['cSpg'];
// 	$r["c8Py"]   = $cPy  = $estructura['cPy'];
// 	$r["c9Ptda"] = $cPtda= $estructura['cPtda'];
// 	$validaPy    = $r["parametros"]["gValidaPY"];
// 	$soap		 = metodos::nuevaSopa($r["parametros"]["urlPys"],$r);
// 	$vDigito	 = "";
// 	$cEdo		 = "";


// 	if (metodos::laUrEstaActiva($cUr)){
// 		if ( metodos::valida_CuentaMayor($cCta,$cPtda,$cSub) ){
// 			if ( metodos::valida_UrPpSpg($cUr,$cAi,$cPp,$cSpg)){
// 				if ( metodos::valida_ProyectoUr($cPy,$cUr,$vDigito) ){
// 					if ( metodos::valida_PySiga($cPy,$soap,$validaPy)){
// 			    		if ( metodos::valida_Combinacion($cUr,$cAi,$cSub,$cPp,$cSpg,$cPy)){ // En la tabla de combinaciones
// 			    			$cEdo = VALIDA;
// 			    		}else{

// 			    			$cEdo = XREVISAR;
// 			    		}
// 			    		if ( metodos::solicitada_Anteriormente($cIne,$cUr,$cCta,$cSub,$cAi,$cPp,$cSpg,$cPy,$cPtda,$cEdo) ){
// 			    			$cEdo = "NP Ya fue solicitada anteriormente " . $cEdo ;
// 			    		}
// 					}else{
// 						$cEdo = "NP No se encontro el proyecto $py en SIGA";
// 					}

// 				}else{
// 					$cEdo = "NP El dígito del proyecto $py no corresponde a la UR $cUr ($vDigito)";
// 				}
// 			}else{
// 				$cEdo = "NP Combinación Ur-ProgPresupestario-SubPrograma no reconocida [$cUr-$pp-$spg]";
// 			}
// 		}else{
// 			$cEdo = "NP La partida $ptda no corresponde a la cuenta $cCta";
// 		}
// 	}else{
// 		$cEdo = "NP La Ur $cUr no existe o esta inactiva"; 
// 	}
// 	$r["cZEdo"] = $cEdo;
// }
// _______________________________________________________
function simulaCargaXls(&$p,&$r){
	$e			= $r["parametros"]["estructura"];
	$cEdo		= "";
	$r["aXls"]	= [[$e["cIne"],$e["cUr"],$e["cCta"],$e["cScta"],$e["cAi"],$e["cPp"],$e["cSpg"],$e["cPy"],$e["cPtda"],$cEdo]];
	$p["aXls"]	= $r["aXls"];
}
// _______________________________________________________
// _______________________________________________________

?>