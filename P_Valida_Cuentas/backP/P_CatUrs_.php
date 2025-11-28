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
	require_once("C_Urs_.php");
	try{
		$idUsuario  = $_SESSION['ValCtasClave'];
	    $param	 	= json_decode(file_get_contents("php://input"), true);
	    $regreso	= array('success' => false , 'mensaje' => '' , 'parametros'=>$param  ,'idUsu'=>$idUsuario , 'conexion'=>$conn_pdo);
	    $cOpc		= $param["opcion"];

	    switch ($cOpc){
	    	//___________________________________
	    	case "CatalogosCentrosCostos":
	    		CatalogosCentrosCostos($param,$regreso);
	    	break;
	    	//___________________________________
	    	case "Unidades_Salida":
	    		Unidades_Salida($regreso);
	    	break;
	    	//___________________________________
	    	//___________________________________
	    	//___________________________________
	    	case "cambiaStatusCentroCosto":
	    		modificaStatusUnidad($param,$regreso);
	    	break;
	    	//___________________________________
	    	default:
	    		$regreso["mensaje"]= "No esta codificada $cOpc en usuarios";
	    	break;
	    }


	}catch(Exception $ex){
		$regreso["error"]	= $ex->getMessage();
		$regreso["mensaje"]	= "Se encontro una inconsistencia en usuarios $cOpc ...";
	}
	header_remove('x-powered-by');							 // remueve el header
	header('Content-type: application/json; charset=utf-8'); // valores en formato JSON caracteres UTF-8
	echo json_encode($regreso);
// ____________________________________________________________________________________________________________
function CatalogosCentrosCostos($p,&$r){
	$oUrs = new Urs(null);
	$r["unidades"]	= $oUrs->traeUnidades();
	$r["success"]	= true;
}
// ____________________________________________________________________________________________________________
function Unidades_Salida(&$r){

	$cSalida = $r["parametros"]["salida"];
	$cWhere  = $r["parametros"]["where"];
	if ($cWhere!==""){
		if (!validaWhereSec($cWhere)){
			$r["mensaje"] = "Se detecto problemas en el where";
			return false;
		}
		$cWhere = " where " . $cWhere ;
	}
	// debe llevar el alias unidades a
	$sql 			 = "select unidad_id, unidad_desc,unidad_digito, activo from unidades a order by unidad_id ";
	$res 			 = ejecutaSQL_($sql);
	$r["resultados"] = $res; 

	if ( count($res)>0){
		if ($cSalida==="Excel"){
			require_once("Xls/X_CatUrs_.php");
			xls_CatUrs($res,$r);
		}elseif($cSalida==="Pdf"){
			require_once("Pdf/F_CatUrs_.php");
			pdf_CatUrs($res,$r);
		}
	}
}
// _____________________________________________________________________________________________________________
function modificaStatusUnidad($p,&$r){
	$oUrs = new Urs(null);
	$cUr  = $p["unidad_id"];
	$cSta = $p["activo"];
	if ($oUrs->noExisteUr($cUr)){
		return false; 
	}else{
		$res = $oUrs->cambiaActivoUr($cUr,$cSta);
		if ($res["resultado"]=="ok"){
			$r["success"] = true;
			$r["mensaje"] = "Se cambio el estado Activo de la UR";
		}else{
			$r["mensaje"]	= "No se logró el cambio del estado activo";
			$r["sql"]		= $res["sql"];
			$r["para"]		= $res["para"];
			$r["error"]		= $res["error"];
			$r["falla"]		= $res["resultado"];
		}
	}
}
// _____________________________________________________________________________________________________________
// _____________________________________________________________________________________________________________
// _____________________________________________________________________________________________________________
// _____________________________________________________________________________________________________________
?>