<?php
require_once $_SESSION['vendor'];
require_once "Pdo/C_Pdf_2_.php";

// _____________________________________________
function pdf_CatPy($detalle,&$r){
	$titulos = ["Catálogo Proyectos"];
//	clvcos,clvai,clvscta,clvpp,clvspg,clvpy
	$encabezados = ["CLAVE", "NOMBRE", "GEOGRÁFICO", "ACTIVO" ];


	$anchos = ["10%", "65%", "15%", "10%" ];

	$reporte = new ReportePDFConCSS(
	    $titulos,
	    $encabezados,
	    $detalle,
	    "P",
	    $anchos,
	    $r["parametros"]["where"],
	    $_SESSION["logo"]
	);
	$cSalida = ipRepo("R_CatPy");

	$reporte->generar($cSalida);
	$r["mensaje"]	= "reporte generado";
	$r["success"]	= true;
	$r["salida"]	= $cSalida;
}
?>