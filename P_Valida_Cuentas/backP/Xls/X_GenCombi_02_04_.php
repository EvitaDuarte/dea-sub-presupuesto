<?php
require_once $_SESSION['xls'];
require_once "../backP/U_XlsRutinas_.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// _________________________________________________
function xls_PreCombi($detalle,&$r){
	$heads	 = ["Tipo","AI","SCTA","PP","SPG","PY","GEO","ACTIVO"];
	$cSalida = ipRepo("R_02_04",".xls");
	generarXLS($cSalida, $heads,  $detalle);
	$r["mensaje"]	= "reporte generado";
	$r["success"]	= true;
	$r["salida"]	= $cSalida;

}
?>