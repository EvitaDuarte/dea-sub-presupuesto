<?php
class Estructura{
private $noenvio;		// timestamp without time zone DEFAULT now(),
private $ine;			// character varying(3)
private $clvcos;		// character varying(4)
private $mayor;			// character varying(5)
private $subcuenta;		// character varying(5)
private $clvai;			// character varying(3
private $clvpp;			// character varying(4
private $clvspg;		// character varying(3)
private $clvpy;			// character varying(7)
private $clvpar;		// character varying(5)
private $procesado;		// boolean,
private $enviado;		// boolean,
private $noprocede;		// character varying(1) 
private $numproceso;	// character varying(15) 
private $usuariosol;	// character varying(30) 
private $usuariodea;	// character varying(30) 
private $consecutivo;	// bigint NOT NULL DEFAULT nextval('epvalidas_consecutivo_seq'::regclass),
private $estado;		// character varying(50) COLLATE pg_catalog."default" DEFAULT 'Estructura Válida'::character varying,
private $area;			// character varying(1) COLLATE pg_catalog."default",
private $fechahora;		// timestamp without time zone DEFAULT now(),
private $conexion;

// ___________________________________________
public function __construct($conexion=null) {
	if ($conexion==null){
		// Solo construye el objeto
	}else{
		$this->conexion	= $conexion;
	}
}
// ____________________________________________
public function cargaDatos($aDatos){
	$this->noenvio		= ($aDatos["noenvio"]); 
	$this->ine			= ($aDatos["ine"]); 
	$this->clvcos		= ($aDatos["clvcos"]); 
	$this->mayor		= ($aDatos["mayor"]); 
	$this->subcuenta	= ($aDatos["subcuenta"]); 
	$this->clvai		= ($aDatos["clvai"]); 
	$this->clvpp		= ($aDatos["clvpp"]); 
	$this->clvspg		= ($aDatos["clvspg"]); 
	$this->clvpy		= ($aDatos["clvpy"]); 
	$this->clvpar		= ($aDatos["clvpar"]); 
	$this->procesado	= ($aDatos["procesado"]); 
	$this->enviado		= ($aDatos["enviado"]); 
	$this->noprocede	= ($aDatos["noprocede"]); 
	$this->numproceso	= ($aDatos["numproceso"]); 
	$this->usuariosol	= ($aDatos["usuariosol"]); 
	$this->usuariodea	= ($aDatos["usuariodea"]); 
	$this->estado		= ($aDatos["estado"]); 
	$this->area			= ($aDatos["area"]); 
}
// ____________________________________________
public function cargaEstructura($aDatos){
	$this->ine			= ($aDatos["ine"]);
	$this->clvcos		= ($aDatos["clvcos"]); 
	$this->mayor		= ($aDatos["mayor"]); 
	$this->subcuenta	= ($aDatos["subcuenta"]); 
	$this->clvai		= ($aDatos["clvai"]); 
	$this->clvpp		= ($aDatos["clvpp"]); 
	$this->clvspg		= ($aDatos["clvspg"]); 
	$this->clvpy		= ($aDatos["clvpy"]); 
	$this->clvpar		= ($aDatos["clvpar"]); 
	$this->estado		= ($aDatos["estado"]);
}
// ____________________________________________
public function cargaComplemento($cNoEnvio,$cUsuario,$cArea){
	$this->noenvio		= $cNoEnvio; 
	$this->usuariosol	= $cUsuario;
	$this->area			= $cArea;
}
// ____________________________________________
public function regresaArregloVacio(){
	$aDatos = [];
	$aDatos["noenvio"]		= "";
	$aDatos["ine"] 			= ""; 
	$aDatos["clvcos"]		= ""; 
	$aDatos["mayor"]		= "";
	$aDatos["subcuenta"]	= ""; 
	$aDatos["clvai"]		= ""; 
	$aDatos["clvpp"]		= ""; 
	$aDatos["clvspg"]		= ""; 
	$aDatos["clvpy"]		= ""; 
	$aDatos["clvpar"]		= ""; 
	$aDatos["procesado"]	= null; 
	$aDatos["enviado"]		= null; 
	$aDatos["noprocede"]	= "";
	$aDatos["numproceso"]	= ""; 
	$aDatos["usuariosol"]	= "";; 
	$aDatos["usuariodea"]	= ""; 
	$aDatos["estado"]		= "";
	$aDatos["area"]			= ""; 

	return $aDatos;
}
// ____________________________________________
public function regresaVariables(&$cIne,&$cUr,&$cta,&$scta,&$ai,&$pp,&$spg,&$py,&$ptda,&$cEdo){
	$cIne	= $this->ine;
	$cUr	= $this->clvcos;
	$cta	= $this->mayor;
	$scta	= $this->subcuenta;
	$ai		= $this->clvai;
	$pp		= $this->clvpp;
	$spg	= $this->clvspg;
	$py		= $this->clvpy;
	$ptda	= $this->clvpar;
	$cEdo	= $this->estado;
}
// ____________________________________________
public function actualizaEstructura(){
	$lValida = $this->estado===VALIDA;
	if ( $this->noExisteEstructura($lValida) ){ // realizar el insert , no debe haber update
		$lAdiciono = $this->adicionaEstructura($lValida);
		return $lAdiciono;
	}
	return false; // Si no se pone regresara un null

}
// ____________________________________________
public function noExisteEstructura($lValida){
	$cIne=$cUr=$cta=$scta=$ai=$pp=$spg=$py=$ptda=$cEdo= "";
	$this->regresaVariables($cIne,$cUr,$cta,$scta,$ai,$pp,$spg,$py,$ptda,$cEdo);

	$lNoExiste	= false;
	$sql1		= "select (noenvio || '_' || estado ) as salida from  " ;

	if ($lValida){
		$sql1 .= " epvalidas ";
	}else{
		$sql1 .= " epinvalidas ";
	}
	$sql1 .= " where ine=:ine and clvcos=:ur and mayor=:cta and subcuenta=:scta and clvai=:ai and clvpp=:pp and clvspg=:spg and clvpy=:py and clvpar=:ptda ";
	$aPar  = [":ine"=>$cIne,":ur"=>$cUr,":cta"=>$cta,":scta"=>$scta,":ai"=>$ai,":pp"=>$pp,":spg"=>$spg,":py"=>$py,":ptda"=>$ptda];

	$aRen  = ejecutaSQL_($sql1,$aPar);

	return count($aRen)==0; // true si no encontró, false si si lo encontro

	//return $lNoExiste;
}
// ____________________________________________
public function adicionaEstructura($lValida){
	// El campo estado, consecutivo tienen un valor por default un valor definido en PostGreSQL por eso no se incluyen en el Insert
	$cIne=$cUr=$cta=$scta=$ai=$pp=$spg=$py=$ptda=$cEdo= "";
	$this->regresaVariables($cIne,$cUr,$cta,$scta,$ai,$pp,$spg,$py,$ptda,$cEdo);

	$sql1 = "Insert into " .   ($lValida?" epvalidas ": " epinvalidas " );
	$sql1.=	"( noenvio, ine, clvcos, mayor, subcuenta, clvai, clvpp, clvspg, clvpy, clvpar, " .
			"procesado, enviado, noprocede, numproceso, usuariosol, usuariodea, area ) ". 
			" VALUES  " .
			"( :noenvio, :ine, :ur, :cta, :scta, :ai, :pp, :spg, :py, :ptda, " .
			":procesado, :enviado, :noprocede, :numproceso, :usuariosol, :usuariodea, :area ) ";
	$aPar = [":ine"=>$cIne,":ur"=>$cUr,":cta"=>$cta,":scta"=>$scta,":ai"=>$ai,":pp"=>$pp,":spg"=>$spg,":py"=>$py,":ptda"=>$ptda,
			":noenvio"=>$this->noenvio,":procesado"=>false,":enviado"=>false,":noprocede"=>null,":numproceso"=>null,
			":usuariosol"=>$this->usuariosol,":usuariodea"=>null,":area"=>$this->area];
	$nRen = actualizaSql($sql1,$aPar);
	return $nRen>0;

	// try { si ocurre algun error la excepción se queda aquí y no llega al try catch que tiene el rollback y haría siempre el commit
	// 	$nRen = actualizaSql($sql1,$aPar);
	// 	return true;
	// }catch(Exception $e){
	// 	throw new Exception("Inconsistencia en adicionaEstructura " . $e->getMessage(), 1);
	// 	return false;
	// }
}
// ____________________________________________
public function enviaEstructuras($aEstruc,$cNumeroEnvio,$correoOrigen,$correosDestino,$usuCorreoGenerico,$passCorreoGenerico,&$v_mensaje){
	$v_mensaje = "";
	$cAsunto   = ("Solicitud de alta de Estructuras programáticas");
	$cDetalle  = "<p style='font-size: large;font-family:courier;'>";
	$cDetalle .= ("Envío : ") . $cNumeroEnvio . "<br><br>" ;
	$cDetalle .= ("Se solicita revisar y dar de alta las siguientes estructuras programáticas.<br>");
	foreach($aEstruc as $cW){
		$cW = $cW["ine"]."-".$cW["clvcos"]."-".$cW["mayor"]."-".$cW["subcuenta"]."-".$cW["clvai"]."-".$cW["clvpp"]."-".$cW["clvspg"]."-".
			  $cW["clvpy"]."-".$cW["clvpar"]."    ".($cW["estado"])."<br>";
		$cDetalle .= $cW;
	}
	$cDetalle .= "<br><br>Saludos</p>";

	$lEnvio = EnviaCorreo($correosDestino, $correoOrigen, $cAsunto, $cDetalle,$v_mensaje,$usuCorreoGenerico,$passCorreoGenerico);
	return $lEnvio;
}
// ____________________________________________
//public function modificaEstado($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda,$cEdo,$tabla,&$aSql){
public function modificaEstado($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda,$cEdo,$tabla,&$debug){
	$sql = "";
	if ($tabla==="epvalidas"){
		$sql = "update public.epvalidas ";
	}elseif($tabla=="epinvalidas"){
		$sql = "update public.epinvalidas";
	}
	if ($sql!==""){
		$sql	= $sql . " set estado=:estado where ine=:ine and clvcos=:ur and mayor=:cta and subcuenta=:scta and clvai=:ai and clvpp=:pp and clvspg=:spg and clvpy=:py and clvpar=:ptda ";
		$par	= [":estado"=>$cEdo,":ine"=>$cIne,":ur"=>$cUr,":cta"=>$cCta,":scta"=>$cSubCta,":ai"=>$ai,":pp"=>$pp,":spg"=>$spg,":py"=>$py,":ptda"=>$ptda];
		//$aSql[] = $sql;
		//$aSql[] = $par;
		$nRen	= actualizaSql($sql,$par);
		return ($nRen);
	}
	return 0;
}
// ____________________________________________
// ____________________________________________
// ____________________________________________
// ____________________________________________
} // Fin de clase
?>