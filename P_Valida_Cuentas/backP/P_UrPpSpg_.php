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
	require_once("Pdo/C_UrPpSpg_.php");
	try{
		$idUsuario  = $_SESSION['ValCtasClave'];
	    $param	 	= json_decode(file_get_contents("php://input"), true);
	    $regreso	= array('success' => false , 'mensaje' => '' , 'parametros'=>$param  ,'idUsu'=>$idUsuario , 'conexion'=>$conn_pdo);
	    $cOpc		= $param["opcion"];

	    switch ($cOpc){
	    	//___________________________________
	    	case "traeUrPpSpg":
	    		traeUrPpSpg($regreso);
	    	break;
	    	//___________________________________
	    	case "UrPpSpg_Salida":
	    		UrPpSpg_Salida($regreso);
	    	break;
	    	//___________________________________
	    	case "actualizaUrPpSpg":
	    		actualizaUrPpSpg($param,$regreso);
	    	break;
	    	//___________________________________
	    	case "eliminaUrPpSpg":
	    		eliminaUrPpSpg($param,$regreso);
	    	break;
	    	//___________________________________
	    	//___________________________________
	    	default:
	    		$regreso["mensaje"]= "No esta codificada $cOpc en Ur Pp Spg";
	    	break;
	    }


	}catch(Exception $ex){
		$regreso["error"]	= $ex->getMessage();
		$regreso["mensaje"]	= "Se encontro una inconsistencia en UrPpSpg $cOpc ...";
	}
	header_remove('x-powered-by');							 // remueve el header
	header('Content-type: application/json; charset=utf-8'); // valores en formato JSON caracteres UTF-8
	echo json_encode($regreso);
// ____________________________________________________________________________________________________________
// ___________________________________________________
function traeUrPpSpg(&$r){
	$r["urs"]		= metodos::trae_AgZs_Unidades();
	$r["pps"]		= metodos::trae_PPs();
	$r["spgs"]		= metodos::trae_Spgs();
	$r["success"]	= true;
}
// ___________________________________________________
function UrPpSpg_Salida(&$r){
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
	$sql 			 = "select tur, pp, spg, activo from v_ur_pp_spg a order by tur, pp, spg ";
	$res 			 = ejecutaSQL_($sql);
	$r["resultados"] = $res; 

	if ( count($res)>0){
		if ($cSalida==="Excel"){
			require_once("Xls/X_CatUrPpSpg_.php");
			xls_CatUrPpSpg($res,$r);
		}elseif($cSalida==="Pdf"){
			require_once("Pdf/F_CatUrPpSpg_.php");
			pdf_CatUrPpSpg($res,$r);
		}
	}
}
// ___________________________________________________
function actualizaUrPpSpg($p,&$r){
	$oUrPpSpg = new UrPpSpg();
	$oUrPpSpg->cargaDatos($p); // para que funcione deben coincidir las variables JS con las variables declaradas en cargaDatos
	$nRen = $oUrPpSpg->actualizaUrPpSpg();
	if ($nRen>0){
		$cW 		  = $oUrPpSpg->get("dummy");
		$r["mensaje"] = "Se $cW la información solicitada";
		$r["success"] = true;
	}else{
		$r["mensaje"] = "!! No se logro actalizar la información solicitada !!";
	}
}
// ___________________________________________________
function eliminaUrPpSpg($p,&$r){
	$oUrPpSpg = new UrPpSpg();
	$oUrPpSpg->cargaDatos($p);
	$lRes = $oUrPpSpg->elimina_UrPpSpg();
	$cW   = $oUrPpSpg->get("dummy");
	if ($lRes){
		$r["success"] = true;
		$r["mensaje"] = $cW;
	}else{
		$r["mensaje"] = $cW;
	}
}
// ___________________________________________________
// ___________________________________________________




/*

function actualizaUrPpSpg(&$validator){
	$nConta = 0;
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	// $vFecha 	= date('m-d-Y h:i:s a', time()); en esta tba el campo fecha se llena con now() para los insert
	$l_Roll	    = false;// No se tiene que hacer rollback
	// Se hace una seleccion agrupada por UR-PP-SPG ( para JLyJD clvcos siempre es AGZS)
	$sql 		= "select 'AGZS' as clvcos, clvpp , clvspg from ptoautorizado where substring(clvcos,1,2)!='OF' group by clvcos, clvpp,clvspg union ". 
				  "select clvcos as clvcos, clvpp , clvspg from ptoautorizado where substring(clvcos,1,2)='OF'  group by clvcos, clvpp,clvspg ".
				  "order by clvcos,clvpp,clvspg";
	$val 		= ejecutaSQL($sql);
	if ($val!=null){
		for ( $x=0 ; $x<count($val) ; $x++ ){
			$ur   = $val[$x]["clvcos"]; $pp = $val[$x]["clvpp"] ; $spg  = $val[$x]["clvspg"];
			$sql  = "select * from v_ur_pp_spg where tur = '$ur' and pp  = '$pp' and spg  = '$spg' ";
			$val  = ejecutaSQL($sql);
		   	if ($val==null){
		   		$nConta++;
				$sql = "insert into v_ur_pp_spg ( tur, pp, spg,  activo ) values " .
				   	   " ( '$ur' ,  '$pp' , '$spg' , 'S'  )";
				$ins = ejecutaSQL($sql);
		   	}
		}
		$validator["messages"] = "Se adicionaron $nConta combinaciones ur-pp-spg";
		return true;
	}else{
		$validator["messages"] = "No hay presupuesto autorizado";
		return false;
	}
}
function adicionaUrPpSpg(&$validator,$cUr,$cPp,$cSpg,$cAct){
	$tipo = substr($cUr,0,2);
	$tipo = ($tipo=="OF") ? $cUr : "AGZS"; // Ya viene filtrada no ??
	$sql = "select tur, pp, spg, activo from v_ur_pp_spg where tur='$tipo' and pp='$cPp' and spg='$cSpg' ";
	$val = ejecutaSQL_($sql);
	$cW  = "";
	$bAct= $val[0]['activo'];
	if ($val!=null){// Ya existe
		if ( $cAct!=$val[0]["activo"] ){
			$sql = "update v_ur_pp_spg set activo='$cAct' where tur='$tipo' and pp='$cPp' and spg='$cSpg' ";
			$cW  = "Actualizó ";
		}else{
			$validator["messages"]	= "No se detectaron cambios ";
			$validator["success"]	= false; 
		}
	}else{
		$cW  = "Adicionó";
		$sql = "insert into v_ur_pp_spg(tur, pp, spg, activo) values ('$tipo', '$cPp', '$cSpg', '$cAct') ";
	}
	$validator["jujuy"] = $sql;
	if ($cW!==""){
		$validator["messages"]	= "Se " . $cW . " la información ";
		$validator["success"]	= true; 
		$val 					= ejecutaSQL_($sql);
	}
} */

?>