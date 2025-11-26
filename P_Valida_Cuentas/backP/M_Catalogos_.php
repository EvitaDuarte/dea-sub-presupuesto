<?php
/**
    Clase estatica para invocar metodos generales  */
// _______________________________ Comentar  para producción
error_reporting(E_ALL);
ini_set('display_errors', '1');
// _______________________________  

class metodos{
	//  ______________________________________________________________________________
	public static function trae_Id_unidades(){
		$sql = "select unidad_id as clave from unidades where activo='S' order by unidad_id";
		$res = ejecutaSQL_($sql);
		return $res;
	}
	//  ______________________________________________________________________________
	public static function trae_Id_Des_Esquema(){
		$sql = "select esquema_id as clave ,descripcion,esquema from esquemas order by esquema_id ";
		$res = ejecutaSQL_($sql);
		return $res;
	}
	//  ______________________________________________________________________________
	//  ______________________________________________________________________________
	//  ______________________________________________________________________________
}
?>
