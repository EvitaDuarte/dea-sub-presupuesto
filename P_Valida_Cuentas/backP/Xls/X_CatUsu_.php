<?php
require_once $_SESSION['xls'];
require_once "../backP/U_XlsRutinas_.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// _________________________________________________
function xls_CatUsu($detalle,&$r){
	$heads	 = ["USUARIO","NOMBRE","CORREO","UR","UR INI", "UR FIN", "ESTATUS","ESQUEMA"];
	$cSalida = ipRepo("R_CatUsu",".xls");
	generarXLS($cSalida, $heads,  $detalle);
	$r["mensaje"]	= "reporte generado";
	$r["success"]	= true;
	$r["salida"]	= $cSalida;
}
?>