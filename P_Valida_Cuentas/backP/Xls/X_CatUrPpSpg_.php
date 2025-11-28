<?php
require_once $_SESSION['xls'];
require_once "../backP/U_XlsRutinas_.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// _________________________________________________
function xls_CatUrPpSpg($detalle,&$r){
	$heads	 = ["UR","PP","SPG","ACTIVO"];
	$cSalida = ipRepo("R_CatUrPpSpg",".xls");
	generarXLS($cSalida, $heads,  $detalle);
	$r["mensaje"]	= "reporte generado";
	$r["success"]	= true;
	$r["salida"]	= $cSalida;
}
?>