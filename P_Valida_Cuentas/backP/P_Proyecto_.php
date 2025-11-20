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
	require_once("C_Proyecto_.php");

	try{
	    // ──────────────────────────────────────────
	    // 1. Obtener la data enviada desde JS
	    // ──────────────────────────────────────────
	    $param	 = json_decode(file_get_contents("php://input"), true);
	    $regreso = array(	'success' => false , 'mensaje' => ''  , 'soap' => '', 'parametros'=>$param ); 

	    $cOpc	 = $param["opcion"];
	    switch ($cOpc){
	    	//___________________________________
	    	case "PyVerificaSiga":
	    		VerificaSoapPy($param,$regreso);
	    	break;
	    	//___________________________________
	    	default:
	    		$regreso["mensaje"]= "No esta codificada $cOpc en proyectos";
	    	break;
	    }
	}catch(Exception $ex){
		$regreso["error"]	= $ex->getMessage();
		$regreso["mensaje"]	= "Se encontro una inconsistencia en proyectos";
	}
	header_remove('x-powered-by');							 // remueve el header
	header('Content-type: application/json; charset=utf-8'); // valores en formato JSON caracteres UTF-8
	echo json_encode($regreso);
// ____________________________________________________________________________________________________________
function VerificaSoapPy(&$par,&$r){
global $conn_pdo;
	try{
		$cPy	= $par["idPy"];		$cDes	= $par["nombre"];	$cGeo = $par["geografico"];		$cActivo = $par["activo"];
		// Verificar si va a ser una alta de proyecto o una actualización
		$sql	= "select clvpy, despy, geografico, activo from proyectos where clvpy=:clvpy";
		$val	= ejecutaSQL_($sql,[":clvpy"=>$cPy]);
		$lNuevo = empty($val); // true, No esta dado de alta en PostgreSql
		// ____________________________________________________
		$url	= trim( getCampo("select urlpy as salida from soap where activo='S' ") );
		if ($url==""){
			$r["mensaje"] = "No esta definida una url activa de Pys SIGA";
			return false;
		}
		//_________________________________________________________________________
		$soap = new SoapClient($url, aOpcionesWs($url));	//var_ dump($aCtas);
		$r["pyPostgreSql"] = $val[0];
		// ________________________________________________________________________
		$valSiga = obtenPySiga($soap,$cPy,$r);
		if ($r["estatus"]==="ok"){
			$r["pySiga"]["geografico"] = $r["pyPostgreSql"]["geografico"];
		}
		$objPy = new Proyecto($conn_pdo);
		$objPy->cargaDatos($r["pySiga"]);
		$ope = $objPy->actualizaPy($lNuevo);
		if ($ope["ok"]){
			$r["success"]	= true;
			$r["mensaje"]	= "todo ok";
			$r["ope"]		= $ope;
		}else{
			$r["mensaje"] = $ope["error"];
		}
	}catch(SoapFault $fault){
		$r["mensaje"]	= "No se logró la conexión con el SIGA";
		$r["error"]		= "Falla Conexión SIGA: Código: {$fault->faultcode}, Descripción: {$fault->faultstring})";
		return false;
	}
}
// ____________________________________________________________________________
function obtenPySiga($soap,$cPy,&$r){
	try{
		$params  = Array("PROYECTO"=> $cPy);		// PROYECTO se definio en el WS
		$aPy     = json_decode(json_encode($soap->consultaProyectos($params)),true); // json_decode(json_encode( convierte arreglo de objetos a arreglo normal, debe de ir si no hay que manejar ->proyectos en lugar de "proyectos"
		if (isset($aPy["proyectos"])){
			$vPy 		 = $aPy["proyectos"][0]; 				// si fueran objetos $aPy->proyectos[0]
			$cAct 		 = $vPy["ACTIVADO"]==="Y"?true:false;	// si fueran objetos $vPy->ACTIVADO
			$r["pySiga"] = array("clvpy"=>$vPy["PROYECTO"],"despy"=>$vPy["DESC_PROYECTO"],"geografico"=>"?","activo"=>$cAct);
			$r["estatus"]= "ok";
		}else{
			$r["mensaje"] = "No se encontro el proyecto $cPy en el SIGA";
			$r["estatus"] = "no";
			return [];
		}
	}catch(SoapFault $fault) {
    	$r["error"]		= "Falla Conexión SIGA verificaPySiga: (Código: {$fault->faultcode}, Descripción: {$fault->faultstring})";
    	$r["estatus"]	= "error";
    	return null;
	}
}
// ____________________________________________________________________________
// ____________________________________________________________________________
// ____________________________________________________________________________
// ____________________________________________________________________________

?>