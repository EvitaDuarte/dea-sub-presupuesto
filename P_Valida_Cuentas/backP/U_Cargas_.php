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

	try{
	    // ──────────────────────────────────────────
	    // 1. Obtener la data enviada desde JS
	    // ──────────────────────────────────────────
	    $param	 = json_decode(file_get_contents("php://input"), true);
	    $regreso = array(	'success' => false , 'mensaje' => ''  , 'soap' => '', 'parametros'=>$param , 'cata'=>'' ); 

	    $cOpc	 = $param["opcion"];
	    switch ($cOpc){
	    	//___________________________________
	    	case "cargaCatalogos":
	    		traeCatalogos($param,$regreso);
	    	break;
	    	//___________________________________
	    	default:
	    		$regreso["mensaje"]= "No esta codificada $cOpc en Cargas";
	    	break;
	    }
	}catch(Exception $ex){
		$regreso["error"] 	= $ex->getMessage();
		$regreso["mensaje"]	= "Revisar rutina de cargas";
	}
	header_remove('x-powered-by');							 // remueve el header
	header('Content-type: application/json; charset=utf-8'); // valores en formato JSON caracteres UTF-8
	echo json_encode($regreso);
// ____________________________________________________________________________________________________________
function traeCatalogos($param,&$r,$soloId=true){
	global $conn_pdo;
	try{
		$r["cata"] = array();
		$sql = "select unidad_id as clave " . ($soloId?"" : ",unidad_desc as descrpcion ") . " from unidades order by unidad_id asc";
		$r["cata"]["urs"] = ejecutaSQL_($sql);

		$sql = "select clvai as clave " . ($soloId?"" : ",descai as descrpcion ") . " from actividadins order by clvai asc";
		$r["cata"]["ais"] = ejecutaSQL_($sql);

		$sql ="select clvscta as clave " . ($soloId?"" : ",descscta as descrpcion ") . " from subcuentas order by clvscta asc";
		$r["cata"]["sctas"] = ejecutaSQL_($sql);

		$sql ="select clvpp as clave " . ($soloId?"" : ",descpp as descrpcion ") . " from presupuestarios order by clvpp asc";
		$r["cata"]["pps"] = ejecutaSQL_($sql);

		$sql ="select clvspg as clave " . ($soloId?"" : ",descspg as descrpcion ") . " from subprogramas order by clvspg asc";
		$r["cata"]["spgs"] = ejecutaSQL_($sql);	

		$sql ="select clvpy as clave " . ($soloId?"" : ",despy as descrpcion ") . " from proyectos order by clvpy asc";
		$r["cata"]["pys"] = ejecutaSQL_($sql);	

		$sql = "select DISTINCT  tipour as clave from precombi order by tipour asc ";
		$r["cata"]["tipos"] = ejecutaSQL_($sql);

		$r["success"] = true;

	}catch(Exception $ex){
		$r["error"] 	= $ex->getMessage();
		$r["mensaje"]	= "Revisar carga de catalogos";
	}
}
// ____________________________________________________________________________________________________________
?>