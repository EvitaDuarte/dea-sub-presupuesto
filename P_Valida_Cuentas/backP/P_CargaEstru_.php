<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	// _______________________________	
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
	    		if ( no_existe_indice("idx_precombi_clvpy6_geo","idx_py")){

					$sql	= "CREATE INDEX idx_precombi_clvpy6_geo ON precombi (LEFT(clvpy, 6), geografico);";
					$stmt	= $conn_pdo->prepare($sql); // Prepara el SQL
					$res	= $stmt->execute();
				}
	    		traer_UrIni_UrFin($param,$regreso);
	    	break;
			//___________________________________
	    	case "validarCarga":
	    		validar_Carga($param,$regreso);
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
	$soap		= metodos::nuevaSopa($url,$r);

	foreach($aXls as &$xls){
		$vDigito = "";
		$cEdo	 = "";
		list($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda) = $xls;
		if (metodos::laUrEstaActiva($cUr)){
			if ( metodos::validaCuentaMayor($cCta,$ptda,$cSubCta) ){
				if ( validaUrPpSpg($cUr,$ai,$pp,$spg)){
					if ( validaProyectoUr($py,$cUr,$vDigito) ){
						if ( validaPySiga($py,$soap,$validaPy)){
				    		if ( validaCombinacion($cUr,$ai,$cSubCta,$pp,$spg,$py)){ // En la tabla de combinaciones
				    			$cEdo = VALIDA;
				    		}else{

				    			$cEdo = XREVISAR;
				    		}
				    		if ( solicitadaAnteriormente($cIne,$cUr,$cCta,$cSubCta,$ai,$pp,$spg,$py,$ptda,$cEdo) ){
				    			$cEdo = "Ya fue solicitada anteriormente " . $cEdo ;
				    		}
						}else{
							$cEdo = "No se encontro el proyecto $py en SIGA";
						}

					}else{
						$cEdo = "El dígito del proyecto $py no corresponde a la UR $cUr ($vDigito)";
					}
				}else{
					$cEdo = "Combinación Ur-ProgPresupestario-SubPrograma no reconocida [$cUr-$pp-$spg]";
				}
			}else{
				$cEdo = "La partida $ptda no corresponde a la cuenta $cCta";
			}
		}else{
			$cEdo = "La Ur $cUr no existe o esta inactiva"; 
		}

	}

}
// _______________________________________________________
// _______________________________________________________
// _______________________________________________________
// _______________________________________________________

?>