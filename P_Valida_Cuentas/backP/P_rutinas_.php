<?php
/*
* * * * * * * * * * * * * * * * * * * * * * * * * 
* Autor   : Miguel Ángel Bolaños Guillén        *
* Sistema : Sistema de Operación Bancaria Web   *
* Fecha   : Enero 2024							*
* Descripción : Rutinas para ejecutar codigo    * 
*               auxiliar en el Sistema          *
*                                               *
*                                               *
* * * * * * * * * * * * * * * * * * * * * * * * *  */
//define('Letra', 'Helvetica');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 

require_once($_SESSION['conW']);

// _____________________________________________________________________________
function getPermisos($clave){
    global $conn_pdo;
    $salida = false;
    $sql 	= "select usuario_id from usuarios where usuario_id =:usuario_id and estatus = 'ACTIVO' ";
	$stmt 	= $conn_pdo->prepare($sql); 
	$stmt->execute([":usuario_id"=>$clave]);//  or die ("1 No se pudo ejecutar la consulta, $sql");
	$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
	foreach ($resultados as $fila) { // Si encontro información
		$salida = true;
	}
    $resultados = null;
    return $salida;
}	
// _____________________________________________________________________________
// _____________________________________________________________________________
function getCampo($sql, $params = []) {
	global $conn_pdo;
	$salida = "";
	$stmt = $conn_pdo->prepare($sql);
	$stmt->execute($params);// or die ("No se pudo ejecutar la consulta, $sql");
	$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($resultados as $fila) {
		$salida = $fila['salida'];
	}
	return $salida;
}
// _____________________________________________________________________________
function ejecutaSQL_($sql, $params = []){	// Regresa un arreglo
	global $conn_pdo;// Si no se lo pongo me manda error en el $sql 
	$regreso = [];
	$stmt 	 = $conn_pdo->prepare($sql);
	$stmt->execute($params);//  or die ("1 No se pudo ejecutar la consulta, $sql");
	$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
 	foreach ($resultados as $fila) { // Si encontro información
		$regreso[] = $fila;
	}
    $resultados = null;
    return $regreso;
}
// _____________________________________________________________________________
function ejecutaSQL_C($sql,$conexion,$params=[]){	// Regresa un arreglo
	$regreso = null;
	$stmt 	 = $conexion->prepare(trim($sql));
	$stmt->execute($params);
	$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
 	foreach ($resultados as $fila) { // Si encontro información
		$regreso[] = $fila;
	}
    $resultados = null;
    return $regreso;
}
// _____________________________________________________________________________
function ejecutaSQL_P($sql,$params,$conexion){	// Regresa un arreglo
    $regreso = null;
    $stmt = $conexion->prepare(trim($sql));
    $stmt->execute($params);  // ← 🚨 Aquí es donde se blindan los datos
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultados as $fila) {
        $regreso[] = $fila;
    }
	$resultados = null;
    return $regreso;
}
// _____________________________________________________________________________
/*function ejecutaSQL_fetch($sql){	// regresa recorset
	global $conn_pdo;// Si no se lo pongo me manda error en el $sql 
	$regreso = null;
	$stmt 	= $conn_pdo->prepare($sql);
	$stmt->execute() or die ("1 No se pudo ejecutar la consulta, $sql");
	$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultados;
}*/
// _____________________________________________________________________________
function  actualizaSql($sql,$params=[]){ // Update, Insert, Delete
	global $conn_pdo;// Si no se lo pongo me manda error en el $sql 
	$regreso	= null;
	$stmt 		= $conn_pdo->prepare($sql); // Prepara el SQL
	$stmt->execute($params);//  or die ("1 No se pudo ejecutar la consulta, $sql");
	$regreso	= $stmt->rowCount();// Número de renglones afectados
	return $regreso;
}
// _____________________________________________________________________________
function contar($sql){ // Usar para contar con select 
	global $conn_pdo;// Si no se lo pongo me manda error en el $sql 
	$regreso	= null;
	$stmt 		= $conn_pdo->prepare($sql); // Prepara el SQL
	$stmt->execute();//  or die ("1 No se pudo ejecutar la consulta, $sql");
	$regreso	= $stmt->fetchColumn();;// Número de renglones afectados
	return $regreso;
}
// _____________________________________________________________________________
// Se utiliza para obtener la UR a partir de los datos que arroja el LDap
function getURAdscripcion($idEstado, $idDistrito, $unidadResponsable){ 
	$urAdscripcion = "";
	$idDistritoPadded = str_pad($idDistrito, 2, "0", STR_PAD_LEFT);
	$arrEstados = array(
		"0" => "OC",		"1" => "AG",		"2" => "BC",
		"3" => "BS",		"4" => "CC",		"5" => "CL",
		"6" => "CM",		"7" => "CS",		"8" => "CH",
		"9" => "MX",		"10" => "DG",		"11" => "GT",
		"12" => "GR",		"13" => "HG",		"14" => "JC",
		"15" => "MC",		"16" => "MN",		"17" => "MS",
		"18" => "NT",		"19" => "NL",		"20" => "OC",
		"21" => "PL",		"22" => "QT",		"23" => "QR",
		"24" => "SP",		"25" => "SL",		"26" => "SR",
		"27" => "TC",		"28" => "TS",		"29" => "TL",
		"30" => "VZ",		"31" => "YN",		"32" => "ZS"
	);

	$arrOficinas = array(
		"PRESIDENCIA DEL CONSEJO DEL INSTITUTO FEDERAL ELECTORAL" => "01",
		"CONSEJEROS ELECTORALES" => "02",
		"SECRETARIA EJECUTIVA" => "03",
		"COORDINACION NACIONAL DE COMUNICACION SOCIAL" => "04",
		"COORDINACION DE ASUNTOS INTERNACIONALES" => "05",
		"DIRECCION DEL SECRETARIADO" => "06",
		"CONTRALORIA GENERAL" => "07",
		"DIRECCION JURIDICA" => "08",
		"UNIDAD DE SERVICIOS DE INFORMATICA" => "09",
		"DIRECCION EJECUTIVA DEL REGISTRO FEDERAL DE ELECTORES" => "11",
		"DIRECCION EJECUTIVA DE PRERROGATIVAS Y PARTIDOS POLITICOS" => "12",
		"DIRECCION EJECUTIVA DE ORGANIZACION ELECTORAL" => "13",
		"DIRECCION EJECUTIVA DEL SERVICIO PROFESIONAL ELECTORAL" => "14",
		"DIRECCION EJECUTIVA DE CAPACITACION ELECTORAL Y EDUCACION CIVICA" => "15",
		"DIRECCION EJECUTIVA DE ADMINISTRACION" => "16",
		"UNIDAD TECNICA DE TRANSPARENCIA Y PROTECCION DE DATOS PERSONALES" => "18",
		"UNIDAD TECNICA DE FISCALIZACION" => "20",
		"UNIDAD TECNICA DE PLANEACION" => "21",
		"UNIDAD TECNICA DE IGUALDAD DE GENERO Y NO DISCRIMINACION" => "22",
		"UNIDAD TECNICA DE VINCULACION CON LOS ORGANISMOS PUBLICOS LOCALES" => "23"
	);

    if($idEstado != "0"){
			$urAdscripcion = $arrEstados[$idEstado] . $idDistritoPadded;
    } else {
		$urAdscripcion = "OF".$arrOficinas[$unidadResponsable];
    }
    
    return $urAdscripcion;
}
// _____________________________________________________________________________
// function bitacora($conexion,$vUsr,$vCtaBan,$vPanta,$vOpera,$vImpo){
// 	$sql = 	"INSERT INTO bitacora( " .
// 			"id usuario, idcuentabancaria, pantalla, operacion, importe) ".
// 			"VALUES (:id usuario, :idcuentabancaria, :pantalla, :operacion, :importe)";
// 	$stmt = $conexion->prepare($sql);
// 	$stmt->bindParam(':id usuario'			, $vUsr 	, PDO::PARAM_STR);
// 	$stmt->bindParam(':idcuentabancaria'	, $vCtaBan	, PDO::PARAM_STR);
// 	$stmt->bindParam(':pantalla'			, $vPanta	, PDO::PARAM_STR);
// 	$stmt->bindParam(':operacion'			, $vOpera	, PDO::PARAM_STR);
// 	$stmt->bindParam(':importe'				, $vImpo	);
// 	//
// 	return $stmt->execute();
// }
// _____________________________________________________________________________
function convierteFecha($cFecha){ // cFecha en frmato dd/mm/yyyy
	return strtotime(str_replace('/', '-', $cFecha));
}
// _____________________________________________________________________________
function volteFecha($cFecha){ // de dd/mm/yyyy a yyyy-mm-dd
	return substr($cFecha,-4) . "-" . substr($cFecha, 3,2) . "-" . substr($cFecha,0,2);
}
// _____________________________________________________________________________
function ddmmyyyy($cFecha){ // de yyyy-mm-dd a dd/mm/yyyy
	$cFecha = trim($cFecha);
	$cFecha = substr($cFecha,-2) . "/" . substr($cFecha,5,2) . "/" . substr($cFecha,0,4);
	return $cFecha;
}
// _____________________________________________________________________________
// function ejecutaSQL_conn_pg($conn_pg,$sql,$params=[]){
// 	//echo $sql;
// 	$regreso = null;
// 	$result	 = pg_query_params($conn_pg, $sql,$params);//  or die ("Error en " . "\n" . $sql);;
//     if (!$result) {
//         throw new Exception("Error en consulta:\n" . pg_last_error($conn_pg) . "\nSQL: $sql");
//     }

//     // Procesar resultados si los hay
//     if (pg_num_rows($result) > 0) {
//         $regreso = [];
//         while ($row = pg_fetch_assoc($result)) {
//             $regreso[] = $row;
//         }
//     }

//     return $regreso;
// }
// _____________________________________________________________________________
function conComas($cNumero,$nDecimal=2){
	return number_format($cNumero, $nDecimal, '.', ',');
}
// _____________________________________________________________________________
function abreArchivoPdf($cArch){
	$cArch = "../" . $cArch;
	// Verificar si el archivo existe
	if (file_exists($cArch)) {
	    // Configurar las cabeceras para indicar que se trata de un archivo PDF
	    header('Content-Type: application/pdf');
	    header('Content-Disposition: inline; filename="' . basename($cArch) . '"');
	    header('Content-Transfer-Encoding: binary');
	    header('Content-Length: ' . filesize($cArch));

	    // Enviar el contenido del archivo al navegador
	    readfile($cArch);
	} else {
	    // El archivo no existe
	    //echo "El archivo no existe. [" . $cArch . "]";
	}
}
// _____________________________________________________________________________
function arregloCtasBancarias($cCtaIni,$cCtaFin){
    $sql  = "select idcuentabancaria from cuentasbancarias where " .
            "idcuentabancaria>=:ctaIni and idcuentabancaria<=:ctaFin order by idcuentabancaria"; 
    $aDat = ejecutaSQL_($sql,[":ctaIni"=>$cCtaIni,":ctaFin"=>$cCtaFin]);
    return $aDat;
}
// _____________________________________________________________________________
//function strFecha($cFecha){ strftime depreciada desde PHP 8.1
//	return strftime( "%d de " . mesesEspanol(date("m", strtotime($cFecha))) . " del %Y", strtotime($cFecha));
//}
// _____________________________________________________________________________
function strFecha($cFecha) {
    $fecha = new DateTime($cFecha);
    $dia   = $fecha->format('d');
    $mes   = mesesEspanol($fecha->format('m'));
    $anio  = $fecha->format('Y');

    return "$dia de $mes del $anio";
}
// _____________________________________________________________________________
function mesesEspanol($mes) {
  $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
  return $meses[$mes - 1];
}	
// _____________________________________________________________________________
function fechaLetra($cFecha){
	list($vAnio, $vMes, $vDia) = explode('-', $cFecha);
	return $vDia . " DE " . strtoupper(mesesEspanol($vMes)) . " DEL " . $vAnio;
}
// _____________________________________________________________________________
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Solo manejar errores de tipo NOTICE y WARNING
    if ($errno == E_NOTICE || $errno == E_WARNING) {
        // Lanzar una excepción con la información del error
        throw new Exception("Error: [$errno] $errstr en $errfile:$errline");
    }
    // Deja que el manejador de errores predeterminado maneje otros errores
    return false;
}
// _____________________________________________________________________________
function ipRepo($cNombre=""){
	$ip 	= calcula_Ip(); // $_SERVER['REMOTE_ADDR'];
	$cArch	= "R_" . str_replace(".", "", $ip) . ".pdf";
	if ($cNombre!=""){
		$cArch = ($cNombre ."_" . str_replace(".", "", $ip) . ".pdf");
	}
	return $cArch;
}
// _____________________________________________________________________________
// ___________________________________________________________________________
function logoInstitucional1($pdf){ // Para impresión en pdf
    $imagePath = '../assetsF/img/ine_logo_pdf.jpg';
    $x 			= 5;
    $y 			= 5;
    $width 		= 30;
    $height 	= 0; // 0 para mantener la proporción del tamaño original
    $pdf->Image($imagePath, $x, $y, $width, $height);
}
// _____________________________________________________________________________
// _____________________________________________________________________________ Debería ser iso en lugar de utf8
function utf8($cadena,$codOrigen='ISO-8859-1'){ // Solo para pdf , convierte utf8 a ISO ya que ISO es el que requiere fpdf para los acentos
	return mb_convert_encoding($cadena,$codOrigen,'UTF-8');
}
// _____________________________________________________________________________
function calculaTamanio($result){
	$total_size = ""; // 0;
	$i = pg_num_fields($result);
	for ($j = 0; $j < $i; $j++) {
		//$total_size += pg_field_size($result, $j);
		//$size = pg_field_size($result, $j);
		//$total_size .= "Campo " . pg_field_name($result, $j) . " es: $size bytes\n";

		$field_value = pg_fetch_result($result, 0, $j); // Primer registro (índice 0) y campo $j
        $size = strlen($field_value);  // Calcular el tamaño real en bytes
        //echo "El tamaño real del campo " . pg_field_name($result, $j) . " es: $size bytes\n";
        $total_size += $size;

	}      
    return $total_size;
}
// _____________________________________________________________________________ $cTabla = nombreTabla($cCta); // NombreTabla ya tiene validaTabla
function nombreTablaAnt($cCtaBan){
	$cTabla = "atablas.t_" . trim($cCtaBan);
	if (!validaTablas($cTabla)){
		return ""; // validaTablas lanzara una excepción a procesar en el catch más próximo
	}
	return $cTabla;
}
// _____________________________________________________________________________
function nombreTabla($cCtaBan) {
    $cCtaBan = trim($cCtaBan);

    // Verifica si ya comienza con 't_'
    if (stripos($cCtaBan, 't_') !== 0) {
        $cCtaBan = 't_' . $cCtaBan;
    }

    $cTabla = 'atablas.' . $cCtaBan;

    if (!validaTablas($cTabla)) {
        return ""; // validaTablas lanzará una excepción que debe atraparse
    }

    return $cTabla;
}

// _____________________________________________________________________________
function tieneMovimientos($cCampo,$cValor,&$r){ // Revisa si existe información en todas las tablas del esquema atablas
	$cBus	= "t_%";
	$cEsq	= "atablas";
	$par	= [":tablas"=>$cBus , ":esquema"=>'atablas'];
    $sql	= "select table_name from information_schema.tables where table_name like :tablas and table_schema = :esquema";
	$tablas = ejecutaSQL_($sql,$par);

	if (!validaCampo($cCampo)){
		return false; // validaCampo lanzara una excepción si detecta alguna vulnerabilidad en $cCampo codigo malicioso
	}
    foreach ($tablas as $tabla) {
        $tableName = $tabla['table_name'];
        if (!validaTablas($tableName)){
        	return false; // validaTablas lanzara una excepción si detecta alguna vulnerabilidad en $tableName codigo malicioso
        }
    }

    foreach ($tablas as $tabla) {
        $tableName	= $tabla['table_name'];
        $cTabla 	= nombreTabla($tableName); // lanzara una excepción si detecta alguna posible inyección
        
        // Construir la consulta dinámica para verificar el valor en el campo
        $sql = "Select COUNT(*) as cantidad from " . $cTabla . " where " . $cCampo. " = :valor ";
        $reg = ejecutaSQL_($sql,[":valor"=>$cValor]);
        $r["tm"] = $sql;
        $r["tm1"] = $reg;

        if ( $reg[0]["cantidad"] > 0 ){
        	return true; // Existe información
        }
        
    }
    return false;
    
}
// _____________________________________________________________________________
function traeTablas($cCtaI,$cCtaF){
	$sql =  "select idcuentabancaria,nombre from cuentasbancarias ".
			"where idcuentabancaria>=:ctaI and idcuentabancaria<=:ctaF ".
			"order by idcuentabancaria";
	$res = ejecutaSQL_($sql,[":ctaI"=>$cCtaI , ":ctaF"=>$cCtaF]);
    if ( $res!=null){
        $regreso = [];
        foreach ($res as $r ){  // llena el combo con la clave y nombre de la operación bancaria
        	$cCta      = $r["idcuentabancaria"];
            $regreso[] = ["cuenta"=>$cCta ,"nombre"=>$r["nombre"] , "tabla"=>"atablas.t_".$cCta ];
        }
        return $regreso;
    }
    return null;
}
// _____________________________________________________________________________
function EjecutaSqlBin($sql, $parametros, $valores) {
    try {
    	global $conn_pdo;
        // Preparar la sentencia SQL
        $stmt = $conn_pdo->prepare($sql);

        // Asociar cada parámetro con su valor usando bindParam
        foreach ($parametros as $i => $param) {
            $stmt->bindValue(':' . $param, $valores[$i]); // usar bindValue (más flexible que bindParam)
        }

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los resultados como arreglo asociativo
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            "status" => count($resultados) ? true : false,
            "mensaje" => count($resultados) ? "Consulta ejecutada correctamente." : "No se encontraron resultados.",
            "valores" => $resultados
        ];
    } catch (Exception $e) {
        return [
            "status" => false,
            "mensaje" => "Error en la ejecución de la consulta: " . $e->getMessage(),
            "valores" => []
        ];
    }
}
// _________________________________________________________________________________
function iniciaPdfH($cHoja="P"){
	ob_start();
    // _______________________________________________________________
    $pdf = new PDF_MC_Table($cHoja,'mm','Letter');
	$pdf->SetAutoPageBreak(true, 1); 	// 1 de margen inferior para el footer
	$pdf->AliasNbPages('{nb}');	// Define el alias para el número total de páginas
	$pdf->SetTopMargin(7);
	return $pdf;
}
// _________________________________________________________________________________
function encabezadoPrin($pdf,$cTitulo,$cSubT="",$cTit3="",&$aPag=null){

    $margen = 2;
    $x1		= $pdf->GetX();
    $pdf->AddPage();							// Configurar el encabezado
    $pdf->SetHeights(3); 						// Reducir la altura de línea a n unidades
    $pdf->SetSpaceLine(5);						// Este valor afecta la posición de los rectangluos
    $pdf->SetFont(Letra, 'B', 11, '' , true);	// Tambien este valor afecta la posición de los rectangluos
    $pdf->SetLeftMargin($margen);
    // ____________________________________________________________
    logoInstitucional1($pdf);					// Coloca el logo del INE
    $cDea	 = utf8("DIRECCIÓN EJECUTIVA DE ADMINISTRACIÓN");
    $cSub	 = utf8("SUBDIRECCIÓN DE OPERACIÓN BANCARIA");
    $cTit3	 = utf8($cTit3);
    $cTitulo = utf8($cTitulo);
    // ___________________________________________________________
    $pdf->SetX($x1); // para que salgan bien centrados los siguientes encabezados
    // ___________________________________________________________
    if ($aPag!=null){
	    $cPag = utf8("Página : ") . $aPag[0] . " de ";
	    $tPag = $aPag[1];
	    $aPag[0] +=1;
    }else{
	    $cPag = utf8("Página: ") . $pdf->PageNo() . " de ";// . '[ de {total Pages}]');
	    $tPag = '{nb}';
	}
    // Arreglo del Encabezado
    $aCabeza = [
    	[" "                    ,$cDea		,   "Hora  : " . date("H:i:s")	] ,
    	[" "                    ,$cSub		, 	"Fecha : " . date("d/m/Y")	],
    	[" "                    ,$cTit3		,   $cPag . $tPag				]
	];
    // ___________________________________________________________
	// Configurar anchos proporcionales para los títulos principales de la página
	$anchoTotal = $pdf->w; // Ancho total de la página
	$anchoPrimeraColumna = $anchoTotal * 0.45; // % del ancho total
	$anchoSegundaColumna = ($anchoTotal - $anchoPrimeraColumna)/2; // El resto del ancho

	$pdf->SetWidths(array($anchoSegundaColumna,$anchoPrimeraColumna, $anchoSegundaColumna-10));
    $pdf->SetAligns(['L','C', 'R']);
    foreach ($aCabeza as $letrero) {
        $pdf->RowSinCuadro($letrero);
	}
	// ___________________________________________________________
	// Obtener el ancho total utilizable (restando márgenes)
	$anchoUtil = $pdf->w - 2 * $margen; // $margen definido arriba
	$pdf->SetFont(Letra, 'B', 10, '' , true);
	// Fila centrada con el título (una sola columna)
	$pdf->SetWidths([$anchoUtil]);
	$pdf->SetAligns(['C']);
	$pdf->RowSinCuadro([$cTitulo]); // RowSinCuadro espera un array
	if ($cSubT!==""){
		// Fila justificada a la izquierda para colocar cSub (una sola columna)
		$pdf->SetAligns(['L']);
		$pdf->RowSinCuadro([$cSubT]);
	}

}
// _________________________________________________________________________________
function subEncaPrin($pdf,$aJusTit,$aJusDeta,$lLinSepa=true){
	global $aAnchos,$aCabeza,$pageWidth;
	// _________________________________________________
	$pdf->GuardaXY();
	$pdf->Ln(1); // agrega un punto al espaciado
    $pdf->SetWidths($aAnchos); // Ancho de las columnas
    $pdf->SetFont(Letra, 'B', 9);
    $pdf->SetAligns($aJusTit); // Alineación de las columnas
	// _________________________________________________
    foreach ($aCabeza as $row) {
        $pdf->Row(($row),null);
    }
    // _________________________________________________ Cuadro de toda la página
    $y = $pdf->GetY();
    $pdf->Rect(2, $y, $pageWidth, $pdf->h - $y - 2, 'D');

    // _________________________________________________ Líneas verticales de separación
    if ($lLinSepa){
	    $x = 0;
	    for ($i=0;$i<count($aAnchos);$i++){
	        $x = $x + $aAnchos[$i] ;
	        $pdf->Rect(2+$x,$y,0,$pdf->h - $y - 2, 'D');
	    }
	}
    // _________________________________________________
    $pdf->SetFont(Letra, '', 7);
    $pdf->SetAligns( $aJusDeta ); // Con setaligns No se puede usar un array global o privado???
    $pdf->SetWidths($aAnchos);
    
}
// _________________________________________________________________________________
function cerrarPdf($pdf,&$r,$cNombre=""){
	ob_end_clean();
    $cArch 		  = ipRepo($cNombre);
	$tempFilename = '../pdfs/' . trim($cArch) ;
	$pdf->Output( $tempFilename , 'F');
	$pdf->Close();
    $r["mensaje"] 	= "";
    $r["success"] 	= true;
    $r["archivo"] 	= 'pdfs/' . trim($cArch) ;
}
// _________________________________________________________________________________
function traeCuenta($cCta,$aCtas){
	foreach ($aCtas as $cta ) {
		if (isset($cta["idcuentabancaria"])){
			if ($cCta==$cta["idcuentabancaria"]){
				return $cta;
			}
		}else if (isset($cta["cuenta"])){
			if ($cCta==$cta["cuenta"]){
				return $cta;
			}
		}
	}
	return ["idcuentabancaria"=>$cCta,"nombre"=>"S.N.","saldoinicial"=>"0.00"];
}
// _________________________________________________________________________________
function sFecha($cFecha){ // de YYYY-MM-DD a dd/smes/YYYY
	if (empty($cFecha)) {
        return '';
    }

    try {
        $date = new DateTime($cFecha);

        // Abreviaturas en español
        $meses = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        $dia = $date->format('d');
        $mes = (int)$date->format('m');
        $anio = $date->format('Y');

        return "$dia/{$meses[$mes]}/$anio";

    } catch (Exception $e) {
        // Fecha inválida
        return '';
    }
}
// _________________________________________________________________________________
function datosJefatura(&$respuesta){
	$cId = '10';
	$sql = "select descripcion, valor from configuracion where idconfiguracion=:id";
	$aVal= ejecutaSQL_($sql,[":id"=>$cId]);
	//$respuesta["borrame"] = $aVal;

	//	Si le dejaba el utf8 se perdía el regreso a JS por que el header trae UTF8, le di unset($r["datos"]) y se resolvió
	if ($aVal!==null){
		$respuesta["datos"]["depto"]  = utf8($aVal[0]["descripcion"]);
		//$respuesta["datos"]["depto"]  	= ($aVal[0]["descripcion"]);
		$respuesta["datos"]["empleado"] = utf8($aVal[0]["valor"]);
		//$respuesta["datos"]["empleado"] = ($aVal[0]["valor"]);
	}else{
		$respuesta["datos"]["depto"] 	= utf8("DEPARTAMENTO DE TESORERÍA");
		$respuesta["datos"]["empleado"] = "Falta definir firma";
	}
}

// _________________________________________________________________________________
function cierra_Pdf($pdf,&$res,$cSiglas){
    ob_end_clean();
    $res["opcion"]["reporte"] = str_replace("R_", $cSiglas, $res["opcion"]["reporte"]);
    $tempFilename			 = '../pdfs/' . trim($res["opcion"]["reporte"]) ;

    $pdf->Output( $tempFilename , 'F');
    $pdf->Close();
    $res["mensaje"] = "";
	$res["success"] = true;
	$res["archivo"] = 'pdfs/' . trim($res["opcion"]["reporte"]) ;
}
// _________________________________________________________________________________
function recortarResultadoPorcentaje($resultados, $porcentaje = 5, $minimoRegistros = 200) {
    $total = count($resultados);

    // Verificar si el número de registros es mayor que el mínimo
    if ($total > $minimoRegistros) {
        // Calcular el porcentaje de registros a recortar
        $cantidad = ceil($total * ($porcentaje / 100)); // Calcular el 5%

        // Obtener solo el 5% del arreglo
        return array_slice($resultados, 0, $cantidad);
    }

    // Si el número de registros es menor o igual a 200, no recortar
    return $resultados;
}
// _________________________________________________________________________________
function buscaNombreUr($cUr,$ctas){
	$cNombre = ""; 
	foreach ($ctas as $cta) {
	    if ($cta['idunidad'] === $cUr) {
	        $cNombre = strtoupper(($cta["nombreunidad"]));
	         break;  // Salir del bucle una vez encontrado el valor
	    }
	}
	return $cNombre;
}
// _________________________________________________________________________________
function correoJunta($cUr){
	$sql	= "select correos as salida from public.estados where idunidad=:idUr ";
	$cMail	= getCampo($sql,[":idUr"=>$cUr]); 
	return $cMail;
}
// _________________________________________________________________________________
function reemplazarVariables($plantilla, $valores) {
    foreach ($valores as $clave => $valor) {
        $plantilla = str_replace('{' . $clave . '}', $valor, $plantilla);
    }
    return $plantilla;
}
// _________________________________________________________________________________
function calcula_Ip(){
	$ip = $_SERVER['REMOTE_ADDR'];
	if ($ip === '::1') { // Si se ejecuta dede xamp
	    $ip = '127.0.0.1';
	}
	return $ip;
}
// _________________________________________________________________________________
function regresa_Ip(){
	$ip = calcula_Ip();
	str_replace(".", "", $ip);
}
// _________________________________________________________________________________
function convertirUtf8Recursivo($data) { // En Xamp al tratar de abrir desde JS un archivo PDF con acentos, perdía los valores de la variable $respuesta
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = convertirUtf8Recursivo($value);
        }
        return $data;
    } elseif (is_string($data)) {
        // Convierte solo si no está en UTF-8
        if (!mb_check_encoding($data, 'UTF-8')) {
            return mb_convert_encoding($data, 'UTF-8', 'ISO-8859-1');
        } else {
            return $data;
        }
    } else {
        return $data;
    }
}
// _________________________________________________________________________________
function siguienteMes($mesActual) {
    // Arreglo con los meses en español
    $meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];

    // Buscar el índice del mes actual
    $indice = array_search(ucfirst(strtolower($mesActual)), $meses);

    // Si no se encuentra, retornar null o un mensaje de error
    if ($indice === false) {
        return "..."; // o return "Mes inválido";
    }

    // Calcular el índice del siguiente mes
    $indiceSiguiente = ($indice + 1) % 12;

    // Retornar el siguiente mes
    return strtoupper($meses[$indiceSiguiente]);
}
// _________________________________________________________________________________
function diaAnterior($cFecha){
	$date = new DateTime($cFecha);
	$date->modify("-1 day");
	return $date->format("Y-m-d"); 
}
// _________________________________________________________________________________
function construye_where(string $campo, array $aCampos, array $tipos): string {
    $where = "";
    $cont = count($aCampos);

    // Escapa comillas simples para evitar errores en SQL
    $campo = str_replace("'", "''", $campo);

    for ($i = 0; $i < $cont; $i++) {
        $tipo = $tipos[$i];
        $columna = $aCampos[$i];

        if ($tipo === "C") { // Caracter o String
            $where = $where === "" ? "WHERE (" : $where;
            $where .= "$columna LIKE '%$campo%' OR ";

        } elseif ($tipo === "N" || $tipo === "NF") { // Números
            // Elimina comas por si el número viene como "1,000"
            $campoNum = str_replace(',', '', $campo);
            if (is_numeric($campoNum)) {
                $where = $where === "" ? "WHERE (" : $where;
                // ::text es útil para búsquedas flexibles en PostgreSQL
                $where .= "$columna::text ILIKE '%$campoNum%' OR ";
            }

        } elseif ($tipo === "D") { // Fecha
            // Validar que sea una fecha válida
            if (strtotime($campo) !== false) {
                // Solo si viene una fecha completa tipo "2025-10-10"
                if (strlen($campo) === 10) {
                    $where = $where === "" ? "WHERE (" : $where;
                    $where .= "$columna = '$campo' OR ";
                }
            }
        }
    }

    // Cierra el WHERE si se armó algo
    if ($where !== "") {
        $where = substr($where, 0, -3); // Quita último " OR "
        $where .= ")";
    }

    return $where;
}

// _________________________________________________________________________________
function validarCamposSeguros($cCampos) { // Se incluyo para evitar que veracode marque vulnerabilidades
    $campos = explode(',', $cCampos);
    foreach ($campos as $campo) {
        $campo = trim($campo);
        // Solo letras, números, punto, paréntesis, guión bajo y espacios
        if (!preg_match('/^[a-zA-Z0-9_.() ]+$/', $campo)) {
            throw new Exception("Campo inválido: " . $campo);
        }
        // Evitar frases sospechosas
        if (preg_match('/(;|--|\/\*|\*\/|DROP|INSERT|UPDATE|DELETE|CREATE|--|;)/i', $campo)) {
            throw new Exception("Campo con contenido malicioso: " . $campo);
        }
    }
    return implode(', ', $campos);
}
// _________________________________________________________________________________
function validarCamposSegurosImportes($cCampos) {
    $campos = explode(',', $cCampos);
    $camposValidos = [];

    foreach ($campos as $campo) {
        $campoOriginal = $campo; // Para mensajes de error
        $campo = trim($campo);

        // 1. Rechazar instrucciones peligrosas
        if (preg_match('/(;|--|\/\*|\*\/|\b(DROP|INSERT|UPDATE|DELETE|CREATE)\b)/i', $campo)) {
            throw new Exception("Campo con contenido malicioso: " . $campoOriginal);
        }

        // 2. Validar con expresión más permisiva (funciones, alias, operaciones)
        // Permitimos letras, números, puntos, comillas simples, paréntesis, coma, operadores y espacio
        if (!preg_match('/^[a-zA-Z0-9_().,+\-*\/\'\s]+( as [a-zA-Z0-9_]+)?$/i', $campo)) {
            throw new Exception("Campo con caracteres no permitidos: " . $campoOriginal);
        }

        $camposValidos[] = $campo;
    }

    return implode(', ', $camposValidos);
}

// _________________________________________________________________________________
function validaOrder($order) { // Se incluyo para evitar que veracode marque vulnerabilidades
    // Quita "order by" al inicio (opcionalmente con espacios antes o después), insensible a mayúsculas
    $order = preg_replace('/^\s*order\s+by\s+/i', '', $order);
    if (trim($order) === '') {
        return true; // No hay nada que validar
    }
    // Divide por coma si se pasan múltiples campos ordenados
    $campos = explode(',', $order);
    foreach ($campos as $campo) {
        $campo = trim($campo);
        // Valida campos como: a.campo, campo DESC, a.campo ASC, etc.
        if (!preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*\.)?[a-zA-Z_][a-zA-Z0-9_]*(\s+(ASC|DESC))?$/i', $campo)) {
        	throw new Exception("Ordenamiento inválido: [$order]");
            return false;
        }
    }
    return true;
}
// _________________________________________________________________________________
function validaTablas(string $cTabla): bool {
    // Divide por coma (una o más tablas)
    $tablas = explode(',', $cTabla);

    foreach ($tablas as $tabla) {
        $tabla = trim($tabla);

        // Expresión que permite:
        // esquema.tabla alias
        // tabla alias
        // esquema.tabla
        // tabla
        if (!preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*\.)?[a-zA-Z_][a-zA-Z0-9_]*(\s+[a-zA-Z_][a-zA-Z0-9_]*)?$/', $tabla)) {
        	throw new Exception("Nombre de tabla inválido: [$cTabla]");
            return false;
        }
    }

    return true;
}

// _________________________________________________________________________________
function validaWhereSecMal(string $whereSec): bool {
    $whereSec = trim($whereSec); // No hay que convertirlo a mayusculas

    // Si está vacío, es válido
    if ($whereSec === '') {
        return true;
    }

    // Divide por AND/OR (insensible a mayúsculas, con espacios) /i hace que el regex ignore si es AND, and, And, etc.
    $condiciones = preg_split('/\s+(AND|OR)\s+/i', $whereSec);
	$regex 		 = '/^([a-zA-Z_][a-zA-Z0-9_]*\.)?[a-zA-Z_][a-zA-Z0-9_]*\s*' .
	               '(=|!=|<|<=|>|>=|LIKE|IS)\s*' .
	               '(NULL|\'[^\']*\'|\d+|\?[0-9]*|:[a-zA-Z_][a-zA-Z0-9_]*)$/i';
    // Ejemplo válido: b.tipo = 'C', a.id = b.id, campo IS NULL, campo LIKE 'X%'	               
    foreach ($condiciones as $condicion) {
        $condicion = trim($condicion);

        if (!preg_match($regex, $condicion)) {
            throw new Exception("Condición inválida en WHERE secundario: [$condicion]");
            return false;
        }
    }

    return true;
}
// _________________________________________________________________________________
function validaWhereSec(string $whereSec): bool {
    $whereSec = trim($whereSec);

    // Si está vacío, es válido
    if ($whereSec === '') {
        return true;
    }

    // Para validar, removemos los AND/OR al principio y usamos preg_split para separar condiciones
    $whereSinConector = preg_replace('/^(AND|OR)\s+/i', '', $whereSec);

    // Dividimos las condiciones por AND u OR (con espacios)
    $condiciones = preg_split('/\s+(AND|OR)\s+/i', $whereSinConector);

    // Regex que permite campo operador valor (valor puede ser campo, número, string, NULL, marcador)
    $regex = '/^([a-zA-Z_][a-zA-Z0-9_]*\.)?[a-zA-Z_][a-zA-Z0-9_]*\s*' .
             '(=|!=|<|<=|>|>=|LIKE|IS)\s*' .
             '(' .
                'NULL|' .
                '\'[^\']*\'|' .
                '\d+(\.\d+)?|' .
                '\?[0-9]*|' .
                ':[a-zA-Z_][a-zA-Z0-9_]*|' .
                '([a-zA-Z_][a-zA-Z0-9_]*\.)?[a-zA-Z_][a-zA-Z0-9_]*' .
             ')$/i';

    foreach ($condiciones as $condicion) {
        $condicion = trim($condicion);

        if ($condicion === '') {
            // Puede pasar si hay AND/OR múltiples seguidos, se puede ignorar o lanzar error según política
            continue;
        }

        if (!preg_match($regex, $condicion)) {
            throw new Exception("Condición inválida en WHERE secundario: [$condicion]");
        }
    }

    return true;
}

// _________________________________________________________________________________
function validaCampo($cCampo){
	if ( trim($cCampo)=="" ){
		return true;
	}
	if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',$cCampo)) { // id es un campo, solo letras, números, y guión bajo, No puede iniciar con número
		throw new Exception("Campo inválido: [$cCampo]");
	    return false;
	}
	return true;
}
// _________________________________________________________________________________
function sanitizaCapturaUsuario(string $campo): string {
   // Escapar comillas simples duplicándolas (prevención básica)
    $campo = str_replace("'", "''", $campo);

    // Permitir letras, números, tildes, ñ, espacios y caracteres comunes en fechas o montos
    $campo = preg_replace('/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\/:\.]/u', '', $campo);

    // Eliminar palabras reservadas peligrosas (DROP, DELETE, etc.)
    $palabras_prohibidas = ['DROP', 'DELETE', 'INSERT', 'UPDATE', 'SELECT', 'TRUNCATE', 'ALTER','--'];
    foreach ($palabras_prohibidas as $palabra) {
        $campo = preg_replace('/\b' . $palabra . '\b/i', '', $campo);
    }

    // Limpieza de espacios múltiples
    $campo = preg_replace('/\s+/', ' ', $campo);
    $campo = strtoupper($campo);

    return trim($campo);
}
// _________________________________________________________________________________
function validaJoinMal(string $join): bool {
	if (trim($join)==""){
		return true;
	}
    // 1. Rechaza si contiene palabras peligrosas
    $prohibidas = ['--', ';', 'DROP', 'DELETE', 'INSERT', 'UPDATE', 'TRUNCATE'];
    foreach ($prohibidas as $palabra) {
        if (stripos($join, $palabra) !== false) {
            throw new Exception("JOIN inválido: contiene '$palabra'");
            return false; // En teoría esta linea ya no se ejecutaría
        }
    }

    // 2. Solo permite caracteres seguros
    // Letras, números, puntos, guión bajo, espacios, operadores, paréntesis, comillas
    if (!preg_match('/^[a-zA-Z0-9_\.=><!\s\'"\(\)\-]+(AND|OR)?[a-zA-Z0-9_\.=><!\s\'"\(\)\-]*$/i', $join)) {
        throw new Exception("JOIN inválido: contiene caracteres no permitidos $join");
        return false;
    }

    // 3. Validación básica pasada
    return true;
}
// _________________________________________________________________________________
function validaJoin(string $join): bool {
	if (trim($join) === "") {
		return true; // Cadena vacía es válida
	}

    // 1. Palabras SQL peligrosas que no deberían aparecer en un WHERE
    $prohibidas = [
        '--', ';', '/*', '*/', 'DROP', 'DELETE', 'INSERT',
        'UPDATE', 'TRUNCATE', 'EXEC', 'CREATE', 'REPLACE',
        'MERGE', 'ALTER', 'GRANT', 'REVOKE'
    ];
    foreach ($prohibidas as $palabra) {
        if (stripos($join, $palabra) !== false) {
            throw new Exception("JOIN inválido: contiene '$palabra'");
        }
    }

    // 2. Validación de caracteres seguros
    // Permitimos letras, números, operadores lógicos, %, comillas, punto decimal, paréntesis y espacios
	if (!preg_match('/^[a-zA-Z0-9_\.%\s\'"=\>\<!\(\)\-\+\*\/]+$/', $join)) {
	    throw new Exception("JOIN inválido: contiene caracteres no permitidos [$join]");
	}

    // 3. Opcional: validación contra patrones permitidos
    // Puedes agregar una lista blanca de palabras clave seguras si querés

    return true;
}

// _________________________________________________________________________________
function ejecutaDDL_($sql, $params = [], &$falla="", &$numreg = null) {
    global $conn_pdo;

    try {
        $stmt = $conn_pdo->prepare($sql);
        $stmt->execute($params);

        // Solo si $numreg fue pasado como referencia lo actualiza
        if (func_num_args() >= 4) {
            $numreg = $stmt->rowCount();
        }

        return true;
    } catch (PDOException $e) {
        $falla = $e->getMessage();
        return false;
    }
}

// _________________________________________________________________________________
function safeTabla($tabla) {
    if (!validaTablas($tabla)) {
        throw new Exception("Tabla inválida: $tabla");
    }
    return $tabla;
}
// _________________________________________________________________________________
/**
 * Ejecuta de forma segura una consulta SELECT en PostgreSQL usando parámetros.
 * 
 * Esta función utiliza pg_query_params(), lo que evita inyección SQL siempre que
 * se usen placeholders ($1, $2, ...) en la sentencia y los valores se pasen en $params.
 *
 * Reglas de seguridad:
 *  - Solo permite sentencias SELECT.
 *  - No permite comandos de modificación (INSERT, UPDATE, DELETE, DROP, etc.).
 *  - Valida que la consulta sea estática o provenga de código controlado.
 * 
 * @param resource $conn_pg  Conexión válida a PostgreSQL.
 * @param string   $sql      Sentencia SQL con placeholders ($1, $2, ...).
 * @param array    $params   Parámetros a sustituir en la consulta.
 * 
 * @return array|null        Arreglo asociativo con los resultados, o null si no hay filas.
 * @throws Exception         Si hay un error en la consulta o si se detecta una sentencia no permitida.
 * 
 * @veracode_secure This function uses parameterized queries via pg_query_params(), 
 *                  which mitigates SQL injection vulnerabilities.
 */
function ejecutaSQL_conn_pg($conn_pg, $sql, $params = []) {

    // --- Validaciones preventivas para análisis estático ---
    if (!is_string($sql)) {
        throw new Exception("SQL inválido: no es una cadena de texto.");
    }

    // Solo se permite SELECT (evita posibles modificaciones o DDL)
    if (!preg_match('/^\s*SELECT\s/i', $sql)) {
        throw new Exception("Solo se permiten sentencias SELECT en esta función.");
    }

    // Bloquear comandos peligrosos (por redundancia y claridad ante auditoría)
    if (preg_match('/\b(INSERT|UPDATE|DELETE|DROP|ALTER|TRUNCATE|EXEC|MERGE)\b/i', $sql)) {
        throw new Exception("Comando SQL no permitido en esta función segura.");
    }

    // --- Ejecución segura con parámetros ---
    // SAFE: pg_query_params() utiliza parámetros bind, previniendo inyección SQL
    $result = pg_query_params($conn_pg, $sql, $params);

    if (!$result) {
        $error = pg_last_error($conn_pg);
        throw new Exception("Error en consulta SQL:\n{$error}\nSQL: {$sql}");
    }

    // --- Procesar resultados ---
    $rows = [];
    if (pg_num_rows($result) > 0) {
        $rows = [];
        while ($row = pg_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    return $rows;
}
// _________________________________________________________________________________
function numeroString($cNumero,$nR=2){
	return round(floatval($cNumero),$nR);
}
// _________________________________________________________________________________
function fechaHoraActual(): string {
    $fecha = new DateTime('now', new DateTimeZone('UTC')); // UTC o la zona que necesites
    return $fecha->format('Y-m-d H:i:s');
}
// _________________________________________________________________________________

?>