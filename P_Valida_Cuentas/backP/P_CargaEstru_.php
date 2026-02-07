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
			case "trae_CatUrs": // para llenar los combos de UrIni, UrFin
				trae_CatUrs($param,$regreso);
			break;
			//___________________________________
			case "actualizaEstado":
				actualiza_Estado($param,$regreso);
			break;
			//___________________________________
			case "traeUrlCtas":
				trae_UrlCtas($regreso);
			break;
			//___________________________________
			case "reEnviarCorreo":
				reEnviar_Correo($param,$regreso);
			break;
			//___________________________________
			case "generaLayOut":
				generaTxtLayOut($param,$regreso);
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
function trae_CatUrs(&$p,&$r){
	$aRen = metodos::trae_Ur_Ini_Fin($r["idUsu"]); // trae las urs permitidas al usuario

	if (count($aRen)>0){
		$r["urIni"]		= $aRen[0]["unidad_inicio"];
		$r["urFin"]		= $aRen[0]["unidad_fin"];
		$oUr  = new Urs();
		$cUri = $r["urIni"];
		$cUrf = $r["urFin"];
		$aReg = $oUr->traeRangoUrs($cUri,$cUrf);
		if (count($aReg)>0){
			$r["urs"] 		= $aReg;
			//$r["success"]	= true;
			trae_UrlCtas($r);
		}else{
			$r["mensaje"] = "No hay rango para Urs [$cUri,$cUrf]";
		}
	}else{
		$r["mensaje"] = "No se logró acceder a datos de urs del usuario " .  $r["idUsu"] ;
	}
}
// _______________________________________________________
function actualiza_Estado(&$p,&$r){
	$where	= "";
	$cEnvio = "";
	$cUrI	= "";
	$cUrF	= "";
	$cTabla = "";
	$url	= $p["url"];
	$aPar	= [];
	$ren 	= 0;
	$cFiltro= $p["filtros"]["tipo"];

	if ($cFiltro==="envio"){
		$cEnvio = $p["filtros"]["numEnvio"];
		$where	= " where noenvio=:noenvio ";
		$aPar	= [":noenvio"=>$cEnvio];

	}elseif($cFiltro==="ur" || $cFiltro==="pendientes"){
		$cUrI	= $p["filtros"]["urI"];
		$cUrF	= $p["filtros"]["urF"];
		$where	= " where clvcos BETWEEN :urI and :urF ";
		$aPar	= [":urI"=>$cUrI,":urF"=>$cUrF];
		if ($cFiltro==="pendientes"){
			$where = $where . " and estado like 'Estructura%' ";
		}
	}

	for ($i=0;$i<=1;$i++){
		$sql = "select ine,clvcos,mayor,subcuenta,clvai,clvpp,clvspg,clvpy,clvpar,estado,noenvio from ";
		if ($i==0){
			$cTabla = " epvalidas ";
			$sql	= $sql . $cTabla . $where;
		}else{
			$cTabla = " epinvalidas ";
			$sql = $sql . $cTabla  . $where;
		}
		//
		$aRen = ejecutaSQL_($sql,$aPar);

		if (count($aRen)>0){

			$ren += metodos::revisaSiga($aRen,$url,trim($cTabla),$r);
			//$r[$cTabla.$i]	= $ren;
			//$r[$cTabla]		= $aRen;
			$r["success"]	= true;
			$r["mensaje"]	= "Se actualizaron $ren estructuras";
		}

	}
}
// _______________________________________________________
function traeDatosCorreos(&$r){
	$aVarCorreo	= metodos::trarCorreoPtoConta();
	if ($aVarCorreo!==null){
		$r["correoPto"]		= $aVarCorreo[0]["valor"];
		$r["correoConta"]	= $aVarCorreo[1]["valor"];
		$r["correoUsu"]		= $aVarCorreo[2]["valor"];
		$r["correoPass"]	= $aVarCorreo[3]["valor"];
		return true;
	}
	return false;
}
// _______________________________________________________
function trae_UrlCtas(&$r){
	$aReg = metodos::trae_urls_soap();

	if (count($aReg)>0){
		$r["urlCtas"]	= $aReg[0]["urlctas"];
		$r["urlPys"]	= $aReg[0]["urlpy"];
		$r["validaPy"]	= $aReg[0]["validapy"];
		$r["success"]	= true;
	}
}
// _______________________________________________________
function reEnviar_Correo(&$p,&$r){
	global $conn_pdo;
	$nRenV = 0; $nRenI = 0;

	if (!traeDatosCorreos($r)){
		$r["mensaje"] = "No se logro traer configuración de correos";
		return false;
	}

	try{

		$cUsuario	  = $r["idUsu"];
		$cCorreoUr    = trim($cUsuario). "@ine.mx";
		$cMailGener   = $r["correoUsu"];
		$cPassGener   = $r["correoPass"];
		$cCorreoPto	  = $r["correoPto"];
		$cCorreoConta = $r["correoConta"];
		$aEstru		  = $r["parametros"]["datos"];
		$claves  	  = ["ine", "clvcos", "mayor", "subcuenta","clvai","clvpp","clvspg","clvpy","clvpar","estado"];
		$oEstruc	  = new Estructura();
		$aPtoEstr	  = [];
		$aContaEstr   = [];
		$cMensaje	  = $cMensaje2 = "";
		foreach ($aEstru as $tEstructura ) {
			// Genera un arreglo referenciado de acuerdo al tamaño de $claves
			$vEstru		= array_combine($claves,array_slice($tEstructura, 0, count($claves)));
			$cArea		= ($vEstru["subcuenta"]===SCTACONTA)?"C":"P";
			$cNoEnvio	= $tEstructura[10];
			$cEdo		= $vEstru["estado"];

			$oEstruc->cargaEstructura($vEstru);
			$oEstruc->cargaComplemento($cNoEnvio,$cUsuario,$cArea);

			if ( $cEdo===VALIDA || $cEdo===XREVISAR ){
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
			$r["mensaje"] = "Se envió la solicitud de alta de  $nRenV estructuras válidas y $nRenI estructuras a Revisar. Número de envío: $cNoEnvio . Correo enviado a < $cMensaje1 $cMensaje2 > Se tiene que esperar su alta en el SIGA por parte  de contabilidad y/o Presupuesto";
		}else{
			$r["mensaje"] =$cMensaje1 ." >><< ". $cMensaje2;
		}
		$r["mensajeenvio1"] = $cMensaje1;
		$r["mensajeenvio2"] = $cMensaje2;
		

	}catch(Exception $e){
		$r["mensaje"] = "Ocurrio una Inconsistencia en EnviarEstructuras ";
		$r["error"]   = $e->getMessage();
	}

}
// _______________________________________________________
function generaTxtLayOut(&$p,&$r){
	global $conn_pdo;
	try{
	 	$tabla		= $p["tabla"];
		$sql		= "select ine, clvcos, mayor, subcuenta, clvai, clvpp, clvspg, clvpy, clvpar from ";
		$where		= "";
		$filtro		= $p["filtros"];
		$cArea		= $filtro["area"]  ?? '';
		$cFilTipo   = $filtro['tipo']  ?? '';
		$params		= [];
		$nombre 	= "_validas_";
		// __________________________________
		if ($tabla=="epvalidas"){
			$sql	= $sql . " epvalidas ";
			$where	= "where estado='" . VALIDA . "' ";

		}elseif($tabla=="epinvalidas"){
			$sql	= $sql . " epinvalidas ";
			$where	= "where  estado='" . XREVISAR . "' ";
			$nombre = "_xrevisar_";
		}else{
			$r["mensaje"] = "No hay programación para " . $tabla;
			return;
		}
		$where .= " AND (numproceso IS NULL OR numproceso = '') ";
		// ________________________________
		if ($cArea==="P"){
			$where .= " and subcuenta='00000' ";
		}elseif($cArea==="C"){
			$where .= " and subcuenta!='00000' ";
		}else{
			$r["mensaje"] = "No hay programación para el área " . $cArea;
			return;
		}
		// ________________________________
		if (!empty($cFilTipo)) {
		    switch ($cFilTipo) {
		    	case 'todas':
		    		// nada que filtrar
		    	break;
		        case 'envio':
		            $where .= " AND noenvio = :envio ";
		            $params[':envio'] = $filtro['numEnvio'] ?? '';
	            break;
		        case 'ur':
		            $where .= " AND clvcos BETWEEN :urI AND :urF ";
		            $params[':urI'] = $filtro['urI'] ?? '';
		            $params[':urF'] = $filtro['urF'] ?? '';
	            break;
		    }
		}
		// ________________________________
		$sql = $sql . $where . " order by fechahora desc ";
		// ________________________________
		$archivo	= "../salidas/{$nombre}_";
		$archivo	= ipRepo($archivo,".txt");
		$fh			= fopen($archivo, 'w');
		$hayDatos	= false;
		$soap		= metodos::nuevaSopa($p["urlCtas"],$r);
		$oEstruc	= new Estructura();
		$numpro 	= date('YmdHis');
		// ________________________________
		$conn_pdo->beginTransaction();
		// ________________________________
		foreach (ejecutaQueryStream($sql, $params) as $row) { // Trae registro x registro evitando usar un arreglo gigante
	    	// Verifico nuevamente que laestructura no este en el SIGA 
	    	list($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda) = array_values($row);

			$params = Array("segment10" => $cIne	, "segment1" => $cUr, "segment2" => $cCta,
			    			 "segment3"	=> $cSubCta	, "segment5" => $ai , "segment6" => $pp,
			    			 "segment7"	=> $spg		, "segment8" => $py , "segment9" => $ptda );
			$ctaSiga = json_decode(json_encode($soap->consultaCuentas($params)),true); 
			$lNoEsta = true;
			if ( isset($ctaSiga["cuentas"]) ){// Ya esta en el SIGA
				$lNoEsta	= false;
				$cEdo		= YAEXISTE;
				$nRen 		= $oEstruc->modificaEstado($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda,$cEdo,$tabla);


			}
			if ( $lNoEsta){
				$hayDatos = true;
	    		fwrite($fh, implode('-', $row) . PHP_EOL);
	    	}
	    	$oEstruc->modificaLayout($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda,$tabla,$numpro);
		}
		$conn_pdo->commit();
		fclose($fh);
		//_________________________________
		if (!$hayDatos) {
		    // No hubo datos → borras archivo vacío
		    @unlink($archivo);
		    $r["mensaje"] = "No hay datos para generar el archivo de layout";
		} else {
		    $r["success"] = true;
		    $r["archivo"] = $archivo;
		}
	}catch(Exception $e){
		$conn_pdo->rollBack();
		$r["mensaje"] = "Se detectaron inconsistencias al generar el layout TXT";
		$r["error"]	  = $e->getMessage();
	}
}
// _______________________________________________________

?>