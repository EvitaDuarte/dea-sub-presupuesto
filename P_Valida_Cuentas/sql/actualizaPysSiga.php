<?php
try{
	//
	require_once("../../cgi-bin/con_pg_CuentasProd.php");
	require_once("../backP/M_Catalogos_.php");
	require_once("../backP/P_rutinas_.php");
	$idUsuario = "miguel.bolanos";
	$param		= [];
	$r		= array('success' => false , 'mensaje' => '' , 'idUsu'=>$idUsuario ,'parametros'=>$param );
	$soap	= metodos::nuevaSopa("https://soa-oci.ine.mx/soa-infra/services/default/AppSegmentoProyectos/MediatorProyectos_ep?wsdl",$r);
	$validaPy = "S";

	$sql = "select * from pyscarga";
	$aRen= ejecutaSQL_($sql);
	if (count($aRen)==0){
		echo "No hay registros para $sql ";
	}
	foreach ($aRen as $ren) {
		$py		= $ren["clvpy"];
		$nombre = "";
		if ( revisa_PySiga($py,$soap,$nombre)){
			$sql = "update pyscarga set despy=:despy where clvpy=:py";
			$nRg = actualizaSql($sql,[":despy"=>$nombre,":py"=>$py]);
			echo "Actualizando $py con $nombre<br>\n";
		}
	}
	echo "Fin<br>";
}catch(Exception $e){

}
// ---------------------------------------
function revisa_PySiga($vPy,$soap,&$nombre){
	$lRegresa = false;
	try{
		$params  = Array("PROYECTO"=> $vPy);		// PROYECTO se definio en el WS
		$aPy     = json_decode(json_encode($soap->consultaProyectos($params)),true); 
		if (isset($aPy["proyectos"])){
			if ( count($aPy["proyectos"]) > 0 ){ // Se encontró el proyecto
				$nombre = $aPy["proyectos"][0]["DESC_PROYECTO"];
				$lRegresa = true;
			}
		}
	}catch(Exception $fault) {
		$cError = "Falla Conexión SIGA verificaPySiga: (Código: {$fault->faultcode}, Descripción: {$fault->faultstring})";
		throw new Exception($cError);
	}
	return $lRegresa;
}
// ---------------------------------------
?>