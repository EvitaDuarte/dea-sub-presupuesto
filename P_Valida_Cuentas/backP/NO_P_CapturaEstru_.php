<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	// _______________________________	
	define("VALIDA"		, "Estructura Válida");
	define("XREVISAR"	, "Estructura a revisar");
	define("YAEXISTE"	, "Ya existe combinación en siga -----");
	define("IDXEDO"		, 9);
	define("SCTACONTA"	, "99999");
	date_default_timezone_set('America/Mexico_City');
	if (session_status() === PHP_SESSION_NONE) {
	    session_start();
	} 
	// _______________________________________
	if ( !isset($_SESSION['ValCtasClave'])){
		header("Location: ../P_Cuentas00_home.php");exit; return;
	}
	require_once($_SESSION['conW']);
	require_once($_SESSION['_Mail_']);
	require_once("P_rutinas_.php");
	require_once("M_Catalogos_.php");
	require_once("Pdo/C_Estructuras_.php"); // Clase para manejar insert update a epvalidas y epinvalidas(a revisar)

	try{
		$idUsuario  = $_SESSION['ValCtasClave'];
		$urUsu		= $_SESSION['ValCtasUrUsu']; // Revisar trae un 1 , en lugar de la UR
	    $param	 	= json_decode(file_get_contents("php://input"), true);
	    $cOpc		= $param["opcion"];
	    $regreso	= array('success' => false , 'mensaje' => '' , 'idUsu'=>$idUsuario ,'parametros'=>$param );


	    switch ($cOpc){
	    	//___________________________________
	    	case "trae_CatUrCtas":
	    		if ( no_existe_indice("precombi","idx_precombi_clvpy6_geo")){

					$sql	= "CREATE INDEX idx_precombi_clvpy6_geo ON precombi (LEFT(clvpy, 6), geografico);";
					$stmt	= $conn_pdo->prepare($sql); // Prepara el SQL
					$res	= $stmt->execute();
				}
	    		trae_CatUrCtas($regreso);
	    	break;
			//___________________________________
	    	case "validarCaptura":
	    		validar_Carga($param,$regreso);
	    		valida_Siga($param,$regreso);
	    	break;
			//___________________________________
	    	case "EnviarEstructuras":
	    		EnviarEstructuras($param,$regreso);
	    	break;
			//___________________________________
			case "ReEnviaEstructuras":
				ReEnviarEstructuras($param,$regreso);
			breaK;
			//___________________________________
	    	default:
	    		$regreso["mensaje"]= "No esta codificada $cOpc en Captura Estructuras";
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
function trae_CatUrCtas(){
	
}
// _______________________________________________________
?>