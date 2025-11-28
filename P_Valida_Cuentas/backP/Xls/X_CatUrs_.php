<?php
require_once $_SESSION['xls'];
require_once "../backP/U_XlsRutinas_.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// _________________________________________________
function xls_CatUrs($detalle,&$r){
	$heads	 = ["CLAVE","NOMBRE","DIGITO","ACTIVO"];
	$cSalida = ipRepo("R_CatUrs",".xls");
	generarXLS($cSalida, $heads,  $detalle);
	$r["mensaje"]	= "reporte generado";
	$r["success"]	= true;
	$r["salida"]	= $cSalida;
}
?>