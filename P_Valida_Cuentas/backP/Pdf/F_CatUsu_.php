<?php
require_once $_SESSION['vendor'];
require_once "Pdo/C_Pdf_2_.php";

// _____________________________________________
function pdf_CatUsu($detalle,&$r){
	$titulos	 = ["Catálogo Usuarios"];
	$encabezados = ["USUARIO", "NOMBRE", "CORREO", "UR" , "URINI" , "URFIN", "ESTATUS" , "ESQUEMA" ];
	$anchos 	 = ["10%", "40%", "18%", "5%" , "5%" , "5%" , "8%" , "9%" ];

	$reporte = new ReportePDFConCSS(
	    $titulos,
	    $encabezados,
	    $detalle,
	    "P",
	    $anchos,
	    $r["parametros"]["where"],
	    $_SESSION["logo"]
	);
	$cSalida = ipRepo("R_CatUsu");

	$reporte->generar($cSalida);
	$r["mensaje"]	= "reporte generado";
	$r["success"]	= true;
	$r["salida"]	= $cSalida;
}
?>