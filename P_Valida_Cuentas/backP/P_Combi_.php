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
	    	case "cargar_PtoAuto":
	    		cargar_PtoAuto($param,$regreso);
	    	break;
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
// Insertar en la tabla combinaciones las sextetas clvcos,clvai,clvscta,clvpp,clvspg,clvpy que no estan en esta tabla y que están en la tabla ptoautorizado	
function cargar_PtoAuto($param,&$r){  
	$conexion	= $r["conexion"];
	$cUsu		= $r["idUsu"];
	$ren		= 0;

	// creo un indice de la sexteta en la table grande
	if ( no_existe_indice("ptoautorizado","idx_combina")){
		$sql	= "CREATE INDEX idx_combina ON ptoautorizado (clvcos,clvai,clvscta,clvpp,clvspg,clvpy)";
		$stmt	= $conexion->prepare($sql); // Prepara el SQL
		$res	= $stmt->execute();
	}
	// En la tabla pequeña combinaciones la llave primaria es clvcos,clvai,clvscta,clvpp,clvspg,clvpy
	$sql =	"SELECT DISTINCT t1.clvcos, t1.clvai, t1.clvscta, t1.clvpp, t1.clvspg, t1.clvpy FROM ptoautorizado t1 " .
			"LEFT JOIN combinaciones t2 ON t1.clvcos = t2.clvcos AND t1.clvai = t2.clvai AND t1.clvscta = t2.clvscta ".
			"AND t1.clvpp = t2.clvpp AND t1.clvspg = t2.clvspg AND t1.clvpy = t2.clvpy WHERE t2.clvcos IS NULL;";
	$aCombi = ejecutaSQL_($sql); // trae un arreglo con las sextetas que no estan en la tabla combinaciones provenientes de la tabla ptoautorizado
	foreach ($aCombi as $combi) {
		$cFecha	= date('Y-m-d H:i');
		$sql =	"INSERT INTO public.combinaciones( clvcos, clvai, clvscta, clvpp, clvspg, clvpy, activo, usuario_id, horafecha) ".
				"VALUES (:clvcos, :clvai, :clvscta, :clvpp, :clvspg, :clvpy, :activo, :usuario_id, :horafecha);";
		$par = [":clvcos"=>$combi["clvcos"], ":clvai"=>$combi["clvai"], ":clvscta"=>$combi["clvscta"],
				":clvpp"=>$combi["clvpp"], ":clvspg"=>$combi["clvspg"], ":clvpy"=>$combi["clvpy"], 
				":activo"=>"S", ":usuario_id"=>$cUsu, ":horafecha"=>$cFecha];
		$ren += actualizaSql($sql,$par);
	}
	$r["mensaje"] = "Se actualizaron $ren combinaciones nuevas";
	$r["success"] = true;

}	
// ____________________________________________________________________________________________________________

?>