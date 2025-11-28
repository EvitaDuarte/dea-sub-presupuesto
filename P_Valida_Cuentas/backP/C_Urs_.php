<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
//	__________________________________________________________________________________________
class Urs{
private $unidad_id;
private $unidad_desc;
private $unidad_digito;
private $activo;
private $conexion;
//	__________________________________________________________________________________________
public function __construct($conexion) {
	if ($conexion==null){
		// Solo construye el objeto
	}else{
		$this->conexion	= $conexion;
	}
}
//	__________________________________________________________________________________________
public function cargaDatos($aDatos){
	$this->unidad_id		= ($aDatos["unidad_id"]); 
	$this->unidad_desc		= ($aDatos["unidad_desc"]); 
	$this->unidad_digito	= ($aDatos["unidad_digito"]); 
	$this->activo			= ($aDatos["activo"]); 
}
//	__________________________________________________________________________________________
public function traeUrCampo($cUrIni,$cUrFin,$cCampo){
	if ($cUrIni>$cUrFin){
		$cUr	= $curFin;
		$cUrFin = $cUrIni;
		$cUrIni = $cUr;
	}
	if (in_array(strtolower($cCampo), ["unidad_desc","unidad_digito"])) {
		$sql = "select unidad_id," . $cCampo . " from public.unidades where unidad_id>=:urini and unidad_id<=:urfin and activo='S'  order by unidad_id ";
		$reg = ejecutaSQL_($sql,[":urini"=>$cUrIni,":urfin"=>$cUrFin]);
		return $reg;
	}else{
		return [];
	}
}
//	__________________________________________________________________________________________
public function traeUrs(){ // Solo trae las activas
	$sql = "select unidad_id,unidad_desc,unidad_digito from public.unidades where activo='S' order by unidad_id ";
	$reg = ejecutaSQL_($sql);
	return $reg;
}
//	__________________________________________________________________________________________
public function traeUnidades(){ // también trae las inactivas , por si hay que cambiar su estatus
	$sql = "select unidad_id,unidad_desc,unidad_digito,activo from public.unidades order by unidad_id ";
	$reg = ejecutaSQL_($sql);
	return $reg;
}
//	_________________________________________________________________________________________
public function noExisteUr($cUr){
	$sql = "select unidad_desc from public.unidades where unidad_id=:unidad_id ";
	$aUr = ejecutaSQL_($sql,[":unidad_id"=>$cUr]);
	if (count($aUr)>0){
		return false;	// Existe el usuario
	}
	return true; 		// No existe el usuario
}
//	_________________________________________________________________________________________
public function cambiaActivoUr($cUr,$cSta){
	try{
		$sql = "update public.unidades set activo=:activo where unidad_id=:unidad_id";
		$ren = actualizaSql($sql,[":activo"=>$cSta,":unidad_id"=>$cUr]);
		if ($ren>0){
			return [
				"resultado"	=> "ok",
				"error"		=> "",
				"sql"		=> "",
				"para"		=> ""
			];
		}else{
			return[
				"resultado"	=> "mal",
				"error"		=> "No se realizó la actualización",
				"sql"		=> $sql,
				"para"		=> "Ur:$cUr , Activo:$cSta"
			];
		}
	}catch(Exception $e){
		return [
			"resultado"	=> "exepción",
			"error"		=> $e->getMessage(),
			"sql"		=> $sql,
			"para"		=> "Ur:$cUr , Activo:$cSta"
		];
	}

}
//	_________________________________________________________________________________________
}
?>