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
	require_once("Pdo/C_Urs_.php");
	require_once("Pdo/C_Combinacion_.php");
	try{
		$idUsuario  = $_SESSION['ValCtasClave'];
	    $param	 	= json_decode(file_get_contents("php://input"), true);
	    $regreso	= array('success' => false , 'mensaje' => '' , 'parametros'=>$param  ,'idUsu'=>$idUsuario , 'conexion'=>$conn_pdo);
	    $cOpc		= $param["opcion"];

	    switch ($cOpc){
	    	//___________________________________
	    	case "agregarCombi":
	    		agregar_Combi($param,$regreso);
	    	break;
	    	//___________________________________
	    	case "genera_Salida":
	    		cargar_P_Salida_1($regreso);
	    	break;
	    	//___________________________________
	    	case "generaUrCombi":
	    		generaUr_Combi($param,$regreso);
	    	break;
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
function agregar_Combi($p,&$r){
	$val = $p["aVal"];
	// Verificar si existe la nueva combinación solicitada
	$sql =	"select tipour,clvai,clvscta, clvpp,clvspg,clvpy,geografico from precombi where ".
			"tipour=:tipoUr and clvai=:clvai and clvscta=:clvscta and clvpp=:clvpp and clvspg=:clvspg and clvpy=:clvpy and geografico=:geografico";
	$aPar= [":tipoUr"=>$val["tipoUr"], ":clvai"=>$val["cveAi"]  , ":clvscta"=>$val["cveScta"], 
			":clvpp"=>$val["cvePp"]  , ":clvspg"=>$val["cveSpg"], ":clvpy"=>$val["cvePy"]    , ":geografico"=>$val["geografico"]  ];

	$aReg = ejecutaSQL_($sql,$aPar);

	if (count($aReg)>0){
		$r["mensaje"] = 'La combinación solicitada $val["tipoUr"]-$val["clvai"]-$val["clvscta"]-$val["clvpp"]-$val["clvspg"]-$val["clvpy"]-$val["geografico"] ya existe';
	}else{
		$sql =	"insert into precombi" . 
				"(tipour , clvai, clvscta, clvpp, clvspg, clvpy, geografico) values" . 
				"(:tipoUr,:clvai,:clvscta,:clvpp,:clvspg,:clvpy,:geografico)";
		$ren = actualizaSql($sql,$aPar);
		if ($ren>0){
			$r["mensaje"] = "Se ha dado de alta la combinación solicitada";
			$r["success"] = true;
			return true;
		}else{
			$r["mensaje"] = "No se logró hacer el alta de la combinación";
		}
	}
	return false;
}
// ____________________________________________________________________________________________________________
function generaUr_Combi($p,&$r){
	global $conn_pdo;
	$oUr	= new Urs(null); 
	$oCombi = new Combinacion(null);
	$cUrIni = $p["urIni"]; $cUrFin = $p["urFin"];
	$cCampo = "unidad_digito";
	$cFecha = date("Y-m-d H:i");
	$nRen	= 0;
	try{
		$conn_pdo->beginTransaction();

		$aUrs = $oUr->traeUrCampo($cUrIni,$cUrFin,$cCampo);

		foreach($aUrs as $Ur){
			$cUr	= $Ur["unidad_id"];
			$cDig	= $Ur[$cCampo];
			$cTipo	= "JL/JD";
			if ( substr($cUr,0,2)=="OF"){
				$cTipo = $cUr;
			}
			$sql = "select tipour, clvai,clvscta,clvpp,clvspg,clvpy,geografico from public.precombi where procesar='S' and tipour=:tipo";
			$aPre= ejecutaSQL_($sql,[":tipo"=>$cTipo]);

			if (count($aPre)>0){ // por cada Ur debe recorrer las preCombi habilitadas
				foreach($aPre as $preC)
				$cPy = $preC["clvpy"];
				if (substr($cPy, -1)=="?"){
					$cPy  = substr($cPy,0,6) . $cDig;
				}
				$aDatos = ["clvcos"=>$cUr, "clvai"=>$preC["clvai"], "clvscta"=>$preC["clvscta"], "clvpp"=>$preC["clvpp"], "clvspg"=>$preC["clvspg"], "clvpy"=>$cPy, "activo"=>'S', "usuario"=>$r["idUsu"], "horafecha"=>$cFecha];
				$oCombi->cargaDatos($aDatos);
				$nRen += $oCombi->actualiza();
			}
		}
		$conn_pdo->commit();
		$r["mensaje"] = "Se actualizaron $nRen combinaciones";
		$r["success"] = true;
	}catch(Exception $e){
		$conn_pdo->rollBack();
		$r["sql"] = "$sql ";
		$r["exepción"] = "Error generaUr_Combi " . $e->getMessage();
	}

}
// ____________________________________________________________________________________________________________ 
function cargar_P_Salida_1(&$r){
	$cSalida = $r["parametros"]["salida"];
	$cWhere  = $r["parametros"]["where"];
	if ($cWhere!==""){
		if (!validaWhereSec($cWhere)){
			$r["mensaje"] = "Se detecto problemas en el where";
			return false;
		}
		$cWhere = " where " . $cWhere ;
	}
	// debe llevar el alias precombi a
	$sql = "select tipour,clvai,clvscta,clvpp,clvspg,clvpy,geografico,procesar from precombi a " . $cWhere;
	$res = ejecutaSQL_($sql);
	$r["resultados"] = $res; 

	if ( count($res)>0){
		if ($cSalida==="Excel"){
			require_once("Xls/X_GenCombi_02_04_.php");
			xls_PreCombi($res,$r);
		}elseif($cSalida==="Pdf"){
			require_once("Pdf/F_GenCombi_02_04_.php");
			pdf_PreCombi($res,$r);
		}
	}
}
// ____________________________________________________________________________________________________________ 
?>