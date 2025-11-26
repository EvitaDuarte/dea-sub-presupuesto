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
	    $regreso	= array('success' => false , 'mensaje' => '' , 'parametros'=>$param  ,'idUsu'=>$idUsuario , 'conexion'=>$conn_pdo);
	    $cOpc		= $param["opcion"];

	    switch ($cOpc){
	    	//___________________________________
	    	case "CatalogosUsuarios":
	    		CatalogosUsuarios($param,$regreso);
	    	break;
	    	//___________________________________
	    	case "genera_Salida":
	    		cargar_P_Salida_1($regreso);
	    	break;
	    	//___________________________________
	    	//___________________________________
	    	//___________________________________
	    	//___________________________________
	    	default:
	    		$regreso["mensaje"]= "No esta codificada $cOpc en combinaciones";
	    	break;
	    }


	}catch(Exception $ex){
		$regreso["error"]	= $ex->getMessage();
		$regreso["mensaje"]	= "Se encontro una inconsistencia en combinaciones...";
	}
	header_remove('x-powered-by');							 // remueve el header
	header('Content-type: application/json; charset=utf-8'); // valores en formato JSON caracteres UTF-8
	echo json_encode($regreso);
// ____________________________________________________________________________________________________________
function CatalogosUsuarios($p,&$r){
	$r["unidades"]	= metodos::trae_Id_unidades();
	$r["esquemas"]	= metodos::trae_Id_Des_Esquema();
	$r["success"]	= true;
}
// ____________________________________________________________________________________________________________
// ____________________________________________________________________________________________________________
// ____________________________________________________________________________________________________________
?>