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
	require_once("Pdo/C_Configura_.php");
	try{
		$idUsuario  = $_SESSION['ValCtasClave'];
	    $param	 	= json_decode(file_get_contents("php://input"), true);
	    $regreso	= array('success' => false , 'mensaje' => '' , 'parametros'=>$param  ,'idUsu'=>$idUsuario , 'conexion'=>$conn_pdo);
	    $cOpc		= $param["opcion"];

	    switch ($cOpc){
	    	//___________________________________
	    	case "CrearCampoTipo":
	    		crear_CampoTipo($param,$regreso);
	    	break;
			//___________________________________
			case "ConfigurarActualizar":
				Configurar_Actualizar($param,$regreso);
			break;
			//___________________________________
			case "ConfigurarAdicionar":
				Configurar_Adicionar($param,$regreso);
			break;
			//___________________________________
	    	default:
	    		$regreso["mensaje"]= "No esta codificada $cOpc en Configuración";
	    	break;
	    }
	}catch(Exception $ex){
		$regreso["error"]	= $ex->getMessage();
		$regreso["mensaje"]	= "Se encontro una inconsistencia en Configura $cOpc ...";
	}
	header_remove('x-powered-by');							 // remueve el header
	header('Content-type: application/json; charset=utf-8'); // valores en formato JSON caracteres UTF-8
	echo json_encode($regreso);
return;
// _______________________________________________________
function crear_CampoTipo($p,&$r){
	$oConfig  = new Configura(null);
	$lRegreso = $oConfig->creaCampoTipo();

	$r["success"] = $lRegreso;
	$r["mensaje"] = $lRegreso?"":"No se logró crear el campo tipo";
	if ($lRegreso){
		$r["configura"] = $oConfig->traeConfiguracion();
	}
}
// _______________________________________________________
function Configurar_Actualizar($p,&$r){
	$oConfig  		= new Configura(null);
	$lRegreso 		= $oConfig->actualizaValor($p["id"],$p["valor"]);
	$r["success"]	= $lRegreso;
	$r["mensaje"]	= $lRegreso?"":"No se logró actualizar el valor de la configuración";
}
// _______________________________________________________
function Configurar_Adicionar($p,&$r){
	$oConfig  		= new Configura(null);
	// Actualiza datos del objeto
	$oConfig->cargaDatos($p);
	$lRegreso		= $oConfig->actualizaConfiguracion();
	$r["success"]	= $lRegreso;
	$r["mensaje"]	= $lRegreso?"Se actualizó la información solicitada":"No se logró actualizar la información en la configuración";
}
// _______________________________________________________
// _______________________________________________________
// _______________________________________________________
// _______________________________________________________
// _______________________________________________________
?>