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
		$idUsuario  = $_SESSION['ValCtasClave'];
	    $param	 	= json_decode(file_get_contents("php://input"), true);
	    $regreso	= array('success' => false , 'mensaje' => '' , 'parametros'=>$param  ,'idUsu'=>$idUsuario , 'conexion'=>$conn_pdo);
	    $cOpc		= $param["opcion"];

	    switch ($cOpc){
	    	//___________________________________
	    	case "altaCampo":
	    		alta_Campo($param,$regreso);
	    	break;
	    	//___________________________________
	    	//___________________________________
	    	default:
	    		$regreso["mensaje"]= "No esta codificada $cOpc en U_Dbu_";
	    	break;
	    }


	}catch(Exception $ex){
		$regreso["error"]	= $ex->getMessage();
		$regreso["mensaje"]	= "Se encontro una inconsistencia en U_Dbu_...";
	}
	header_remove('x-powered-by');							 // remueve el header
	header('Content-type: application/json; charset=utf-8'); // valores en formato JSON caracteres UTF-8
	echo json_encode($regreso);
// ____________________________________________________________________________________________________________
// __________________________________________________________
function alta_Campo($p, &$r) {
    $cRegreso	  = no_existe_campo($p["tabla"], $p["campo"], $p["tipo"], $p["long"], $p["esquema"]);
    $r["regreso"] = $cRegreso;

    if ($cRegreso === "Adicionado") {

        $cTabla = validarIdentificador($p["tabla"]);
        $cCampo = validarIdentificador($p["campo"]);
        $cTipo  = validarIdentificador($p["tipo"]);

        $sql = "UPDATE $cTabla SET $cCampo = :valor";

        if (in_array(strtoupper($cTipo), ['VARCHAR', 'CHAR'])) {
            $where = " WHERE $cCampo IS NULL OR $cCampo = '' ";
        } else {
            $where = " WHERE $cCampo IS NULL ";
        }

        $sql .= $where;

        $ren = actualizaSql($sql, [":valor" => $p["valUp"]]);
        if ($ren > 0) {
            $r["mensajeUp"] = "Se actualizaron $ren movimientos";
        }
    }
}

// __________________________________________________________

?>