<?php
class Combinacion{
private $clvcos;
private $clvai;
private $clvscta;
private $clvpp;
private $clvspg;
private $clvpy;
private $activo;
private $usuario;
private $horafecha;
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
	$this->clvcos		= ($aDatos["clvcos"]); 
	$this->clvai		= ($aDatos["clvai"]); 
	$this->clvscta		= ($aDatos["clvscta"]); 
	$this->clvpp		= ($aDatos["clvpp"]); 
	$this->clvspg		= ($aDatos["clvspg"]); 
	$this->clvpy		= ($aDatos["clvpy"]); 
	$this->activo		= ($aDatos["activo"]); 
	$this->usuario		= ($aDatos["usuario"]); 
	$this->horafecha	= ($aDatos["horafecha"]); 
}
//	__________________________________________________________________________________________
public function actualiza(){
	$aPar = [];
	$sql  = "select clvcos from public.combinaciones where clvcos=:clvcos and clvai=:clvai and clvscta=:clvscta and clvpp=:clvpp and clvspg=:clvspg and clvpy=:clvpy";
	$aPar = [":clvcos"=>$this->clvcos, ":clvai"=>$this->clvai, ":clvscta"=>$this->clvscta, ":clvpp"=>$this->clvpp, ":clvspg"=>$this->clvspg, ":clvpy"=>$this->clvpy];
	$aRen = ejecutaSQL_($sql,$aPar);

	if (count($aRen)==0){
		$sql  =	"INSERT INTO public.combinaciones( clvcos, clvai, clvscta, clvpp, clvspg, clvpy, activo, usuario_id, horafecha) ".
				"VALUES (:clvcos, :clvai, :clvscta, :clvpp, :clvspg, :clvpy, :activo, :usuario_id, :horafecha);";
		$aPar = [":clvcos"=>$this->clvcos, ":clvai"=>$this->clvai, ":clvscta"=>$this->clvscta, ":clvpp"=>$this->clvpp, ":clvspg"=>$this->clvspg, ":clvpy"=>$this->clvpy, ":activo"=>$this->activo, ":usuario_id"=>$this->usuario, ":horafecha"=>$this->horafecha];
	}else{
		$sql  = "update public.combinaciones set activo=:activo where clvcos=:clvcos and clvai=:clvai and clvscta=:clvscta and clvpp=:clvpp and clvspg=:clvspg and clvpy=:clvpy" ;
		$aPar = [":clvcos"=>$this->clvcos, ":clvai"=>$this->clvai, ":clvscta"=>$this->clvscta, ":clvpp"=>$this->clvpp, ":clvspg"=>$this->clvspg, ":clvpy"=>$this->clvpy, ":activo"=>$this->activo];
	}
	$ren = actualizaSql($sql,$aPar);
	return $ren;
}
//	__________________________________________________________________________________________
}
?>