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
	public static function trae_AgZs_Unidades(){
	    $regreso = [];

	    // Valor fijo
	    $regreso[] = ["clave" => "AGZS"];
	    $regreso[] = ["clave" => "OFCE"];

	    $sql = "SELECT unidad_id FROM public.unidades WHERE substr(unidad_id, 1,2) = 'OF' ORDER BY unidad_id ASC";

	    $res = ejecutaSQL_($sql);

	    foreach($res as $r){
	        $regreso[] = ["clave" => $r["unidad_id"]];
	    }
		

	    return $regreso;
	}

	//  ______________________________________________________________________________
	public static function trae_PPs(){
		$sql = "select clvpp as clave from public.presupuestarios order by clvpp";
		$res = ejecutaSQL_($sql);
		return $res;
	}
	//  ______________________________________________________________________________
	public static function trae_Spgs(){
		$sql = "select clvspg as clave from public.subprogramas order by clvspg";
		$res = ejecutaSQL_($sql);
		return $res;
	}
	//  ___________________________________________________________________
}
?>
