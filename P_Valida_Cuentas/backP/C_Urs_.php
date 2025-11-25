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
		$sql = "select unidad_id," . $cCampo . " from public.unidades where unidad_id>=:urini and unidad_id<=:urfin and activo='S' ";
		$reg = ejecutaSQL_($sql,[":urini"=>$cUrIni,":urfin"=>$cUrFin]);
		return $reg;
	}else{
		return [];
	}
}
//	__________________________________________________________________________________________
public function traeUrs(){
	$sql = "select unidad_id,unidad_desc,unidad_digito from public.unidades where activo='S' ";
	$reg = ejecutaSQL_($sql);
	return $reg;
}
//	__________________________________________________________________________________________
}
?>