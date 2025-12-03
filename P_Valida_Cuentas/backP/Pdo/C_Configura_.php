<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
//	________________________________________________________
class Configura{
private $id;
private $nombre;
private $valor;
private $tipo;
private $usuario;
private $fecha;
private $dummy;
//	________________________________________________________
public function __construct($conexion=null) {
	if ($conexion==null){
		// Solo construye el objeto
	}else{
		$this->conexion	= $conexion;
	}
}
//	________________________________________________________
public function cargaDatos($aDatos){
	$this->id		= ($aDatos["id"]); 
	$this->nombre	= ($aDatos["nombre"]); 
	$this->valor	= ($aDatos["valor"]); 
	$this->tipo		= ($aDatos["tipo"]); 
}
// _________________________________________________________
public function creaCampoTipo(){
	$sql	= "ALTER TABLE public.configuracion ADD COLUMN IF NOT EXISTS tipo VARCHAR";
    $nRen	= actualizaSql($sql);
    $sql	= "SELECT 1 FROM information_schema.columns WHERE table_name = 'configuracion' AND column_name = 'tipo';" ;
    $aRen	= ejecutaSQL_($sql);
    if (count($aRen)>0){
    	$nRen = 0;
    	$aSql = ["UPDATE configuracion SET tipo='soloCorreoIne'		WHERE id = 1  or id=2; ", 	// Correo presupuesto o Contabilidad
    			 "UPDATE configuracion SET tipo='soloLetras'		WHERE id = 10 or id=11;", 	// Títulos
    			 "UPDATE configuracion SET tipo='soloDominio'		WHERE id = 20; ",			// Correo para PHPMailer
    			 "UPDATE configuracion SET tipo='soloPassword'		WHERE id = 21; "];			// Clave Correo para PHPMailer
    	foreach($aSql as $sql1){
			$nRen += actualizaSql($sql1);
		}
		return $nRen>0;
    }
    return false;
}
// _________________________________________________________
public function traeConfiguracion(){
	$sql = "select id,nombre,valor, tipo from public.configuracion order by id";
	$aRen= ejecutaSQL_($sql);
	return $aRen;
}
// _________________________________________________________
public function actualizaValor($cId,$cValor){
	$sql1 = "update public.configuracion set valor=:valor where id=:id";
	$nRen = actualizaSql($sql1,[":valor"=>$cValor, ":id"=>$cId]);
	return $nRen > 0;
}
// _________________________________________________________
public function NoExisteId($cId=""){
	if ($cId===""){
		$cId = ($this->id);
	}
	$sql1 = "select id from public.configuracion where id=:id";
	$aRen = ejecutaSQL_($sql1,[":id"=>$cId]);

	if (count($aRen)>0){
		return false; 	// Si existe el Id
	}
	return true;		// NO existe el Id
}
// _________________________________________________________
public function actualizaConfiguracion($aValores=null){
	if ( $aValores!==null){
		$this->cargaDatos($aValores);
	}
	if ($this->NoExisteId()){	// Adiciona
		$sql1 = "insert into public.configuracion (id,nombre,valor,tipo) values(:id,:nombre,:valor,:tipo)";
	}else{						// Modifica
		$sql1 = "update public.configuracion set nombre=:nombre, valor=:valor , tipo=:tipo where id=:id ";
	}
	$aPar = [":id"=>$this->id,":nombre"=>$this->nombre,":valor"=>$this->valor,":tipo"=>$this->tipo];

	$nRen = actualizaSql($sql1,$aPar);

	return $nRen>0;
}
// _________________________________________________________
public function eliminaConfiguracion($cId=null){
	if ($cId===null){
		$cId = $this->id;
	}
	if ( !($this->NoExisteId($cId)) ){
		$sql1 = "delete from public.configuracion where id=:id";
		$nRen = actualizaSql($sql1,[":id"=>$cId]);
		return $nRen>0;
	}
}
// _________________________________________________________
}
?>