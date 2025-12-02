<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
//	__________________________________________________________________________________________
class UrPpSpg{
private $tur;
private $pp;
private $spg;
private $activo;
private $conexion;
private $dummy;
//	__________________________________________________________________________________________
public function __construct($conexion=null) {
	if ($conexion==null){
		// Solo construye el objeto
	}else{
		$this->conexion	= $conexion;
	}
}
//	__________________________________________________________________________________________
public function cargaDatos($aDatos){
	$this->tur		= ($aDatos["tur"]); 
	$this->pp		= ($aDatos["pp"]); 
	$this->spg		= ($aDatos["spg"]); 
	$this->activo	= ($aDatos["activo"]); 
}
//	__________________________________________________________________________________________
public function traeUrPpSpg($lActivas=true){
	$where	= $lActiva?" where activo='S' ":"";
	$sql	= "select tur, pp , spg, activo from public.v_ur_pp_spg  " . $where . " order by tur,pp,spg";
	$aVal	= ejecutaSQL_($sql);
	return $aVal;
}
//	__________________________________________________________________________________________
public function noExisteUrPpSpg($cUr,$cPp,$cSpg){
	$sql1 = "select activo from public.v_ur_pp_spg where ".
			"tur=:tur and pp=:pp and spg=:spg ";
	$aVal = ejecutaSQL_($sql1,[":tur"=>$cUr, ":pp"=>$cPp,":spg"=>$cSpg]);
	if (count($aVal)>0){
		return false; 	// si existe
	}
	return true; 		// No Existe
}
//	__________________________________________________________________________________________
public function actualiza_UrPpSpg(){
	if ( $this->noExisteUrPpSpg($this->tur,$this->pp,$this->spg) ){
		$sql1 = "insert into public.v_ur_pp_spg (tur,pp,spg,activo) values( " .
				":tur , :pp, :spg, :activo )";
		$cW   = "adiciono";
	}else{
		$sql1 = "update public.v_ur_pp_spg set activo=:activo where " .
				"tur=:tur and pp=:pp and spg=:spg ";
		$cW   = "actualizó";
	}
	$this->dummy = $cW;

	$par	= [":tur"=>$this->tur, ":pp"=>$this->pp, ":spg"=>$this->spg, ":activo"=>$this->activo];
	$nRen	= actualizaSql($sql1,$par);
	return $nRen;
}
// __________________________________________________
public function elimina_UrPpSpg(){
	if (!$this->noExisteUrPpSpg($this->tur,$this->pp,$this->spg)){
		$sql  ="delete from public.v_ur_pp_spg where tur=:tur and pp=:pp and spg=:spg ";
		$nRen = actualizaSql($sql,[":tur"=>$this->tur, ":pp"=>$this->pp, ":spg"=>$this->spg]);
		if ($nRen>0){
			$this->dummy = "Se eliminó ($nRen) movimiento(s)";
			return true;
		}else{
			$this->dummy = "No se realizó la operación de borrado";
			return false;
		}
	}else{
		$this->dummy = "No existe la clave";
		return false;
	}
}
// __________________________________________________
public function traeUrPpSpgAuto(){
	$sql1 = "select 'AGZS' as tur, clvpp as pp , clvspg as spg, 'SI' as activo ".
			" from ptoautorizado where substring(clvcos,1,2)!='OF' group by clvcos, clvpp,clvspg ".
			" union ". 
			"select clvcos as tur, clvpp as pp, clvspg as spg, 'SI' as activo ".
			" from ptoautorizado where substring(clvcos,1,2)='OF'  group by clvcos, clvpp,clvspg ".
			"order by tur,pp,spg";
	$aVal = ejecutaSQL_($sql1);
	return $aVal;	
}
// __________________________________________________
public function actualizaAutoUrPpSpg($aReg){
	$nRen = 0;
	foreach ($aReg as $reg ) {
		$this->tur = $reg["tur"]; $this->pp = $reg["pp"]; $this->spg = $reg["spg"]; $this->activo = 'SI';
		if ($this->noExisteUrPpSpg($this->tur,$this->pp,$this->spg)){
			$nRen += $this->actualiza_UrPpSpg();
		}
	}
	return $nRen;
}
// __________________________________________________
public function get($prop) {
    if (property_exists($this, $prop)) {
        return $this->$prop;
    }
    return null; // o lanzar excepción
}
// __________________________________________________
//	__________________________________________________________________________________________
//	__________________________________________________________________________________________
//	__________________________________________________________________________________________
//	__________________________________________________________________________________________
//	__________________________________________________________________________________________
//	__________________________________________________________________________________________
}
?>