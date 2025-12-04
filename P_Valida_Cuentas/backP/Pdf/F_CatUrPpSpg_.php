<?php
require_once $_SESSION['vendor'];
require_once "Pdo/C_Pdf_2_.php";

function pdf_CatUrPpSpg($detalle,&$r){
	$titulos	 = ["Catálogo Ur-Pp-Spg"];
	$encabezados = ["Centro de Costo", "Programa Presupuestario", "SubPrograma", "Activo?"  ];
	$anchos 	 = ["25%", "25%", "25%", "25%" ];
	$alineacion  = ["c" , "c" , "c" ,"c"];

	$reporte = new ReportePDFConCSS(
	    $titulos,
	    $encabezados,
	    $detalle,
	    "P",
	    $anchos,
	    $r["parametros"]["where"],
	    $_SESSION["logo"],
	    $alineacion
	);
	$cSalida = ipRepo("R_CatUrPpSpg");

	$reporte->generar($cSalida);
	$r["mensaje"]	= "reporte generado";
	$r["success"]	= true;
	$r["salida"]	= $cSalida;

	// Para mandar a un archivo log personalizado, se debe declarar en cada PHP o meterlo en rutinas.PHP o en un require
	// $r["estado log"] = ini_get('log_errors'); // me devuelve 1
	// $logfile = __DIR__ . '/logs/mi_error_log.log'; // No funciono
	// $logfile = "C:\\tmp\\mi_error_log.log"; // Si funciono
	// ini_set('log_errors', 'On');  
	// ini_set('error_log',$logfile);  
	// error_log("Esto se escribe en la ruta personalizada ". print_r($r,true));
}  
?>