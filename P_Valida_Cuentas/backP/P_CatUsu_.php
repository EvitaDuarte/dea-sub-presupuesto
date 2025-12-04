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
	require_once("Pdo/C_Usuarios_.php");
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
	    	case "Usuarios_Salida":
	    		Usuarios_Salida($regreso);
	    	break;
	    	//___________________________________
	    	case "validaLdapUsu":
	    		verificaUsuario($regreso);
	    	break;
	    	//___________________________________
	    	case "actualizaUsuario":
	    		actualiza_Usuario($param,$regreso);
	    	break;
	    	//___________________________________
	    	case "cambiaStatusUsuario":
	    		modificaStatusUsuario($param,$regreso);
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
function CatalogosUsuarios($p,&$r){
	$r["unidades"]	= metodos::trae_Id_unidades();
	$r["esquemas"]	= metodos::trae_Id_Des_Esquema();
	$r["success"]	= true;
}
// ____________________________________________________________________________________________________________
function Usuarios_Salida(&$r){

	$cSalida = $r["parametros"]["salida"];
	$cWhere  = $r["parametros"]["where"];
	if ($cWhere!==""){
		if (!validaWhereSec($cWhere)){
			$r["mensaje"] = "Se detecto problemas en el where";
			return false;
		}
		$cWhere = " where " . $cWhere ;
	}
	// debe llevar el alias usuarios a
	$sql = "select usuario_id,initcap(nombre_completo) as nombre_completo,correo, unidad_id, unidad_inicio, unidad_fin, estatus, b.esquema " .
			" from usuarios a JOIN esquemas b ON a.esquema_id = b.esquema_id " . $cWhere . "  order by unidad_id,usuario_id";
	$res = ejecutaSQL_($sql);
	$r["resultados"] = $res; 

	if ( count($res)>0){
		if ($cSalida==="Excel"){
			require_once("Xls/X_CatUsu_.php");
			xls_CatUsu($res,$r);
		}elseif($cSalida==="Pdf"){
			require_once("Pdf/F_CatUsu_.php");
			pdf_CatUsu($res,$r);
		}
	}


}
// ________________________________________________________
function verificaUsuario(&$r){
	global $conn_pdo;
	$oUsu = new Usuarios($conn_pdo);
	valida_LdapUsu($r);
	if ($r["success"]){	// Si es empleado activo del INE
		if ($oUsu->traeDatosUsuario($r["parametros"]["idUsu"])){ // Ya esta en la tabla de usuarios
			$r["lDap"]["esquema_id"] = $oUsu->get("esquema_id");
			$r["lDap"]["estatus"]	 = $oUsu->get("estatus");
			$r["lDap"]["listaurs"]	 = $oUsu->get("listaurs");
		}else{	// No esta en la tabla de Usuarios
			$cUr = 
			$r["lDap"]["esquema_id"] = "2"; // Por default esquema de JLE aunque sea de OF16
			$r["lDap"]["estatus"]	 = "ACTIVO";
			$r["lDap"]["listaurs"]	 = "";
		}
	}else{ // No es empleado del INE
		$r["lDap"]["esquema_id"]		= "";
		$r["lDap"]["estatus"]			= "";
		$r["lDap"]["listaurs"]			= "";
		$r["lDap"]["nombre"]			= "";
		$r["lDap"]["mail"]				= "";
		$r["lDap"]["unidad_id"]			= "";
		$r["lDap"]["unidad_inicio"]		= "";
		$r["lDap"]["unidad_fin"]		= "";
	}
}
// ________________________________________________________
function actualiza_Usuario($p,&$r){
	$oUsu	= new Usuarios(null);
	$aDatos = [
		"usuario_id"=>$p["usuario_id"]	, "nombre_completo"=>$p["nombre_completo"]	, "correo"=>$p["correo"],
		"unidad_id"=>$p["unidad_id"]	, "unidad_inicio"=>$p["unidad_inicio"]	  	, "unidad_fin"=>$p["unidad_fin"],
		"estatus"=>$p["estatus"]		, "usuario_captura"=>$r["idUsu"]			, "esquema_id"=>$p["esquema_id"],
		"listaurs"=>$p["listaurs"]
	];
	$oUsu->cargaDatosUsuario($aDatos);
	$lAdiciona = $oUsu->noExisteUsuario($p["usuario_id"]);
	$rUsu = $oUsu->actualizaUsuario($lAdiciona);
	if ($rUsu["ok"]===true && $rUsu["filas"]>0){
		$r["success"] = true;
		$r["mensaje"] = "Se ha actualizado los datos del usuario solicitado";
	}else{
		$r["mensaje"]	= "No se actualizaron los datos del usuario";
		$r["error"]		= $rUsu["error"];
		$r["sql"]		= $rUsu["sql"];
		$r["par"]		= $rUsu["par"];
		$r["dat"]		= $aDatos;
	}

}
// ________________________________________________________
function modificaStatusUsuario($p,&$r){
	$oUsu = new Usuarios(null);
	$lAct = $oUsu->modificaEstatus($p["usuario_id"],$p["estatus"]);
	if ($lAct){
		$r["success"] = true;
		$r["estatus"] = $p["estatus"];
		$r["mensaje"] = "Se ha cambiado el estatus a " . $p["estatus"] . " del usuario " . $p["usuario_id"];
	}else{
		$r["mensaje"] = "No procede cambio de estatus .....";
	}
}
// ________________________________________________________
?>