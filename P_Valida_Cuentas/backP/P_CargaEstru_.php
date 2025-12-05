<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	// _______________________________	
	define("VALIDA"		, "Estructura Válida");
	define("XREVISAR"	, "Estructura a revisar");
	define("YAEXISTE"	, "Ya existe combinación en siga -----");
	define("IDXEDO"		, 9);
	date_default_timezone_set('America/Mexico_City');
	if (session_status() === PHP_SESSION_NONE) {
	    session_start();
	} 
	// _______________________________________
	if ( !isset($_SESSION['ValCtasClave'])){
		header("Location: ../P_Cuentas00_home.php");exit; return;
	}
	require_once($_SESSION['conW']);
	require_once("P_rutinas_.php");
	require_once("M_Catalogos_.php");
	try{
		$idUsuario  = $_SESSION['ValCtasClave'];
	    $param	 	= json_decode(file_get_contents("php://input"), true);
	    $cOpc		= $param["opcion"];
	    $regreso	= array('success' => false , 'mensaje' => '' , 'idUsu'=>$idUsuario , 'parametros'=>$param );


	    switch ($cOpc){
	    	//___________________________________
	    	case "traer_UrIni_UrFin":
	    		if ( no_existe_indice("precombi","idx_precombi_clvpy6_geo")){

					$sql	= "CREATE INDEX idx_precombi_clvpy6_geo ON precombi (LEFT(clvpy, 6), geografico);";
					$stmt	= $conn_pdo->prepare($sql); // Prepara el SQL
					$res	= $stmt->execute();
				}
	    		traer_UrIni_UrFin($param,$regreso);
	    	break;
			//___________________________________
	    	case "validarCarga":
	    		validar_Carga($param,$regreso);
	    		valida_Siga($param,$regreso);
	    	break;
			//___________________________________

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
// _______________________________________________________
function traer_UrIni_UrFin($p,&$r){
	$aRen = metodos::trae_Ur_Ini_Fin($r["idUsu"]); // trae las urs permitidas al usuario

	if (count($aRen)>0){
		
		$r["urIni"]		= $aRen[0]["unidad_inicio"];
		$r["urFin"]		= $aRen[0]["unidad_fin"];
		$r["urLis"]		= $aRen[0]["listaurs"];

		$aRen = metodos::trae_urls_soap();

		if (count($aRen)>0){
			$r["success"]	= true;
			$r["urlCtas"]	= $aRen[0]["urlctas"];
			$r["urlPys"]	= $aRen[0]["urlpy"];
			$r["validaPy"]	= $aRen[0]["validapy"];
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
					    		if ( metodos::solicitada_Anteriormente($cIne,$cUr,$cCta,$cSubCta,$ai,$pp,$spg,$py,$ptda,$cEdo) ){
					    			$cEdo = "NP Ya fue solicitada anteriormente " . $cEdo ;
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
				if ( isset($ctaSiga["noregistros"])  ){
					$xls[IDXEDO] = VALIDA; // .
				}
			}
		}
	}

	$r["success"]	= true;
}
// _______________________________________________________
// _______________________________________________________
// _______________________________________________________

?>