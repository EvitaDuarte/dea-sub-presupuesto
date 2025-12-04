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
	public static function trae_Ur_Ini_Fin($cUsu){
		$sql1 = "select unidad_inicio, unidad_fin, listaurs from public.usuarios where usuario_id=:usuario_id";
		$aRen = ejecutaSQL_($sql1,[":usuario_id"=>$cUsu]);
		return $aRen;
	}
	//  ___________________________________________________________________
	public static function trae_urls_soap(){
		$sql1 = "select urlctas, urlpy, validapy from public.soap where activo='S'";
		$aRen = ejecutaSQL_($sql1);
		return $aRen;
	}
	//  ___________________________________________________________________
	public static function laUrEstaActiva($cUr){
		$sql1 = "select unidad_id from public.unidades where unidad_id=:unidad_id and activo='S'";
		$aRen = ejecutaSQL_($sql1,[":unidad_id"=>$cUr]);
		return count($aRen)>0;	// true si existe y esta activa
	}
	//  ___________________________________________________________________
	public static function validaCuentaMayor($cta,$ptda,$scta){
		$sql1 = "select compramenor,ordencompra,contactamay from partidas where partida=:ptda'  ";
		$aRen = ejecutaSQL($sql,[":ptda"=>$ptda]);
		if (count($aRen)>0){
			if ($scta=="00000"){
				if ( ( trim($cta)==$aRen[0]["compramenor"] ) or (trim($cta)==$aRen[0]["ordencompra"] )  ){
					return true;
				}else{
				}
			}else{
				if ( ( trim($cta)==$aRen[0]["compramenor"] ) or (trim($cta)==$aRen[0]["ordencompra"] ) or (trim($cta)==$aRen[0]["contactamay"] ) ){
					return true;
				}
			}
		}
		return false;
	}
	//  ___________________________________________________________________
	public static function nuevaSopa($url, &$r){
	    try {
	        $soap = new SoapClient($url, self::aOpcionesWs($url));
	        return $soap;

	    } catch (Exception $fault) {
	    	$cError			= "Falla Conexión SIGA: (Código: {$fault->faultcode}, Descripción: {$fault->faultstring})";
	        $r["messages"]	= "No se logró la conexión con el SIGA";
	        $r["error"]		= $cError;

	        // En PHP 8 es mejor lanzar excepción personalizada:
	        throw new Exception($cError);
	    }
	}
	//  ___________________________________________________________________
	public static function aOpcionesWs($url){
		return     Array(
			"uri"					=> $url,			"style"					=> SOAP_RPC,
			"use"					=> SOAP_ENCODED, 	"soap_version"			=> SOAP_1_1,
			"cache_wsdl"			=> WSDL_CACHE_BOTH, "connection_timeout" 	=> 15,
			"trace" 				=> true, 			"encoding" 				=> "UTF-8",
			"exceptions" 			=> true, 			"features" 				=> SOAP_SINGLE_ELEMENT_ARRAYS
		);
	}
	//  ___________________________________________________________________
	public static function validaUrPpSpg($ur,$ai,$pp,$spg){
		$tipo  = substr($ur,0,2);
		$tipo  = ($tipo=="OF") ? $ur : "AGZS";
		$sql1  = "select tur, pp, spg from v_ur_pp_spg where tur=:tipo and pp=:pp and spg=:spg ";
		$aRen  = ejecutaSQL($sql1,[":tipo"=>$tipo,":pp"=>$pp,":spg"=>$spg]);
		if (count($aRen)>0){
			return true;
		}else{
			if (substr($ur,0,2)=="OF"){
				$tipo  = "OFCE";	// Hay combinaciones que son para todas las OF y asi estan etiquetadas en la tabla $v_ur_pp_spg
				$sql1  = "select tur, pp, spg from v_ur_pp_spg where tur=:tipo and pp=:pp and spg=:spg ";
				$aRen  = ejecutaSQL($sql1, [":tipo"=>$tipo,":pp"=>$pp,":spg"=>$spg] );
				if (count($aRen)>0){
					return true;
				}
			}
		}
		return false;
	}
	//  ___________________________________________________________________
	public static function validaProyectoUr($py,$ur,&$vDigito){
		$vDig  = substr($py,-1,1); $py = substr($py,0,6);  
		// Buscar en las precombi el proyecto a 6 posiciones
		$sql = "select clvpy from precombi where left(clvpy,6)='$py' and geografico='SI' "; 
		// var_ dump("Sql=$sql vDig=$vDig Py=$py");
		if ( ejecutaSQL($sql)!=null ){ // Si es geográfico
			$vDigito = getCampo("select unidad_digito as salida from unidades where unidad_id='" . $ur. "' ");
			return ( $vDig==$vDigito );
		}else{ // No esta, no es geografico
			return true;
		}
	} 
	//  ___________________________________________________________________
	//  ___________________________________________________________________
	//  ___________________________________________________________________
	//  ___________________________________________________________________
	//  ___________________________________________________________________
	//  ___________________________________________________________________
}
?>
