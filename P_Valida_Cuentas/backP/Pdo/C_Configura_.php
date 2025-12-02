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
    	$sql = "UPDATE configuracion SET tipo='solocorreo'		WHERE id = 1  or id=2; 
    			UPDATE configuracion SET tipo='sololetras'		WHERE id = 10 or id=11; 
    			UPDATE configuracion SET tipo='solodominio'		WHERE id = 20; 
    			UPDATE configuracion SET tipo='solopassword'	WHERE id = 21; 
				";
		$nRen = actualizaSql($sql);
    }
}
// _________________________________________________________
// _________________________________________________________
// _________________________________________________________
// _________________________________________________________
// _________________________________________________________
// _________________________________________________________
}
?>