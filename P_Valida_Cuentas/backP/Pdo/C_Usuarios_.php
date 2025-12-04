<?php
Class Usuarios{
private $usuario_id;
private $nombre_completo;
private $correo;
private $fecha_alta;
private $unidad_id;
private $unidad_inicio;
private $unidad_fin;
private $estatus;
private $fecha_baja;
private $usuario_captura;
private $esquema_id;
private $listaurs;
private $conexion;

//	__________________________________________________
public function __construct($conexion) {
	if ($conexion==null){
		// Solo construye el objeto
	}else{
		$this->conexion	= $conexion;
	}
}
//	__________________________________________________
public function cargaDatosUsuario($aDatos){
	$this->usuario_id		= $aDatos["usuario_id"];
	$this->nombre_completo	= $aDatos["nombre_completo"];
	$this->correo			= $aDatos["correo"];
	$this->fecha_alta		= date("Y-m-d H:i");
	$this->unidad_id		= $aDatos["unidad_id"];
	$this->unidad_inicio	= $aDatos["unidad_inicio"];
	$this->unidad_fin		= $aDatos["unidad_fin"];
	$this->estatus			= $aDatos["estatus"];
	$this->fecha_baja		= ""; // $aDatos[""];
	$this->usuario_captura	= $aDatos["usuario_captura"];
	$this->esquema_id		= $aDatos["esquema_id"];
	$this->listaurs			= $aDatos["listaurs"];
}
// __________________________________________________
public function noExisteUsuario($cUsuario_Id){
	$sql = "select nombre_completo from public.usuarios where usuario_id=:usuario_id ";
	$usu = ejecutaSQL_($sql,[":usuario_id"=>$cUsuario_Id]);
	if (count($usu)>0){
		return false;	// Existe el usuario
	}
	return true; 		// No existe el usuario
}
// __________________________________________________
public function actualizaUsuario($lAgregaUsuario){
	if ($lAgregaUsuario){
		$sql1 = "insert into public.usuarios ( ".
				" usuario_id, nombre_completo, correo, fecha_alta, unidad_id, unidad_inicio, unidad_fin, estatus , usuario_captura, esquema_id, listaurs )".
				" values (".
				":usuario_id,:nombre_completo,:correo,:fecha_alta,:unidad_id,:unidad_inicio,:unidad_fin,:estatus ,:usuario_captura,:esquema_id,:listaurs )";
		$par1 = [":usuario_id"=>$this->usuario_id,":nombre_completo"=>$this->nombre_completo,":correo"=>$this->correo,
				 ":fecha_alta"=>$this->fecha_alta,":unidad_id"=>$this->unidad_id,":unidad_inicio"=>$this->unidad_inicio,
				 ":unidad_fin"=>$this->unidad_fin,":estatus"=>$this->estatus,":usuario_captura"=>$this->usuario_captura,
				 ":esquema_id"=>$this->esquema_id,":listaurs"=>$this->listaurs];
	}else{
		$sql1 = "update public.usuarios set " .
				"nombre_completo=:nombre_completo, correo=:correo, unidad_id=:unidad_id, unidad_inicio=:unidad_inicio, unidad_fin=:unidad_fin, ".
				"estatus=:estatus , esquema_id=:esquema_id, listaurs=:listaurs ".
				" where usuario_id=:usuario_id";
		$par1 = [":usuario_id"=>$this->usuario_id,":nombre_completo"=>$this->nombre_completo,":correo"=>$this->correo,
				 ":unidad_id"=>$this->unidad_id,":unidad_inicio"=>$this->unidad_inicio,
				 ":unidad_fin"=>$this->unidad_fin,":estatus"=>$this->estatus,
				 ":esquema_id"=>$this->esquema_id,":listaurs"=>$this->listaurs];
	}
	try {
		$nRen = actualizaSql($sql1,$par1);
		return [
			"ok"		=>true,
			"filas"		=>$nRen,
            "error"     => null,
            "sql"       => $sql1,
            "par"		=> "",
            "accion"    => $lAgregaUsuario ? "INSERT" : "UPDATE"
		];
	}catch(Exception $e){
		return [
			"ok"		=> false,
			"filas"		=> 0,
            "error"     => $e->getMessage(),
            "sql"       => $sql1,
            "par"		=> $par1,
            "accion"    => $lAgregaUsuario ? "INSERT" : "UPDATE"
		];
	}
}
// __________________________________________________
public function modificaEstatus($cUsuario_Id,$cEstatus){
	if ($this->noExisteUsuario($cUsuario_Id)){
		return false; 
	}else{
		try{
			$sql1 = "update public.usuarios set estatus=:estatus where usuario_id=:usuario_id";
			$ren  = actualizaSql($sql1,[":estatus"=>$cEstatus,":usuario_id"=>$cUsuario_Id]);
			return $ren>0;
		}catch(Exception $e){
			return false;
		}
	}
}
// __________________________________________________
public function traeUsuarios(){
	$sql1 = "select usuario_id, nombre_completo, correo, unidad_id, unidad_inicio, unidad_fin, estatus , esquema_id,".
			"listaurs, fecha_alta, usuario_captura from public.usuarios order by unidad_id,usuario_id";
	$aRen = ejecutaSQL_($sql1);
	return $aRen;
}
// __________________________________________________
public function traeDatosUsuario($cUsuario_Id){
	$sql1 = "select usuario_id, nombre_completo, correo, unidad_id, unidad_inicio, unidad_fin, estatus , esquema_id,".
			"listaurs from public.usuarios where usuario_id=:usuario_id";
	$aRen = ejecutaSQL_($sql1,[":usuario_id"=>$cUsuario_Id]);
	if (count($aRen)>0){
		$this->usuario_id		= $aRen[0]["usuario_id"];
		$this->nombre_completo	= $aRen[0]["nombre_completo"];
		$this->correo			= $aRen[0]["correo"];
		$this->unidad_id		= $aRen[0]["unidad_id"];
		$this->unidad_inicio	= $aRen[0]["unidad_inicio"];
		$this->unidad_fin		= $aRen[0]["unidad_fin"];
		$this->estatus			= $aRen[0]["estatus"];
		$this->esquema_id		= $aRen[0]["esquema_id"];
		$this->listaurs			= $aRen[0]["listaurs"];
		return true;
	}else{
		return false;
	}
}
// __________________________________________________
public function get($prop) {
    if (property_exists($this, $prop)) {
        return $this->$prop;
    }
    return null; // o lanzar excepción
}
// __________________________________________________
}
?>