<?php
require_once $_SESSION['xls'];
require_once "../backP/U_XlsRutinas_.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// _________________________________________________
function xls_Combi($detalle,&$r){
	$heads =["UR","AI","SCTA","PP","SPG","PY"];
	generarXLS("prueba.xls", $heads,  $detalle);

}
// _________________________________________________

?>