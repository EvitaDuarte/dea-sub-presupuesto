<?php
require_once $_SESSION['vendor'];
require_once "C_Pdf_2_.php";

// _____________________________________________
function pdf_PreCombi($detalle,&$r){
	$titulos = ["PreCombinaciones"];
//	clvcos,clvai,clvscta,clvpp,clvspg,clvpy
	$encabezados = ["TIPO", "AI", "SCTA", "PP" , "SPG", "PY","GEO","ACT"];


	$anchos = ["12%", "12%", "12%", "12%" ,"12%" , "16%" , "12%" , "12%" ];

	$reporte = new ReportePDFConCSS(
	    $titulos,
	    $encabezados,
	    $detalle,
	    "P",
	    $anchos,
	    $r["parametros"]["where"],
	    $_SESSION["logo"]
	);
	$cSalida = ipRepo("R_02_04");

	$reporte->generar($cSalida);
	$r["mensaje"]	= "reporte generado";
	$r["success"]	= true;
	$r["salida"]	= $cSalida;
}
?>