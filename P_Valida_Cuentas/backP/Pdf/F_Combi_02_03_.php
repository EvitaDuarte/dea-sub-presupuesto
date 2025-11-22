<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
// _______________________________	
date_default_timezone_set('America/Mexico_City');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 
// _______________________________________
if ( !isset($_SESSION['ValCtasClave'])){
	header("Location: ../P_Cuentas00_home.php");exit; return;
}

require_once $_SESSION['vendor'];
require_once "C_Pdf_2_.php";

// _____________________________________________
function pdf_Combi($detalle,&$r){
	$titulos = ["Dirección Ejecutiva de Administración","Subdirección de Presupuesto","Combinaciones permitidas"];
//	clvcos,clvai,clvscta,clvpp,clvspg,clvpy
	$encabezados = ["UR", "AI", "SCTA", "PP" , "SPG", "PY"];


	$anchos = ["16%", "16%", "16%", "16%" ,"16%" , "20%" ];

	$reporte = new ReportePDFConCSS(
	    $titulos,
	    $encabezados,
	    $detalle,
	    "P",
	    $anchos,
	    $r["parametros"]["where"],
	    $_SESSION["logo"]
	);
	$cSalida = ipRepo("R_02_03");

	$reporte->generar($cSalida);
	$r["mensaje"]	= "reporte generado";
	$r["success"]	= true;
	$r["salida"]	= $cSalida;
}
// _____________________________________________
?>