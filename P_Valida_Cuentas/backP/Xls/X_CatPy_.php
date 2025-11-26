<?php
require_once $_SESSION['xls'];
require_once "../backP/U_XlsRutinas_.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// _________________________________________________
function xls_CatPy($detalle,&$r){
	$heads	 = ["CLAVE","NOMBRE","GEOGRÁFICO","ACTIVO"];
	$cSalida = ipRepo("R_CatPy",".xls");
	generarXLS($cSalida, $heads,  $detalle);
	$r["mensaje"]	= "reporte generado";
	$r["success"]	= true;
	$r["salida"]	= $cSalida;

}
?>