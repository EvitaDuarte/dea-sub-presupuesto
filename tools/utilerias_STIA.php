<?php


// Utilerías para el correo electrónico
//require 'PHPMailer_6/src/Exception.php';
//require 'PHPMailer_6/src/PHPMailer.php';
//require 'PHPMailer_6/src/SMTP.php';

// ====================================================================================================

// function getSqlTrue($sql){
// 	//Ejecuta el sql recibido
// //	echo $sql;
// 	$salida = false;
// 	global $conn_pg;
// 	$rst_sql = pg_query($conn_pg, $sql) or die ("U-A No se logro ejecutar la consulta, rep&oacute;rtelo al &aacute;rea de soporte t&eacute;cnico. <br>");

// 	while ($row = pg_fetch_row($rst_sql, NULL, PGSQL_ASSOC)) {
// 		$salida = true;
// 	}

// 	pg_free_result($rst_sql);
	
// 	return $salida;
// }

// // ! ====================================================================================================
// // ! Nueva función para obtener campos de la base de datos

// function takeCampo($campo, $tabla, $where = null, $valores = null, $conection = null){

//     global $conn_pg; // * Conexión a la base de datos
    
//     // ? Declaramos las variables
// 	$salida   	  = "";
//     $whereSql 	  = "";
//     $and      	  = "";
// 	$where_output = "";

// 	// ? Si la conexión viene llena se usa esa, si no, se usa la global
// 	if( ! $conection )
// 		$conection = $conn_pg;

// 	// ? Validamos si el where viene vacío
// 	if( $where == "" ){
// 		$valores = array();

// 		$where_output = "";
// 	}else{
// 		// ? Recorremos el where para agregar los valores a la consulta
// 		foreach ($where as $key => $value) {
// 			$whereSql .= $and . $value . " = $" . ($key + 1); // * Ejemplo: campo1 = $1 AND campo2 = $2
	
// 			$and   = " AND ";
// 		}
// 		$where_output = "WHERE " . $whereSql;
// 	}

// 	// ? Generamos un ID único para la consulta
// 	$uniqID = uniqid();
    
//     // ? Preparamos la consulta
//     $rst = pg_prepare($conection, $uniqID, "SELECT {$campo} FROM {$tabla} {$where_output};");     // * Recibimos los campos
//     $rst = pg_execute($conection, $uniqID, $valores );                                              // * Ejecutamos la consulta
//     $row = pg_fetch_row($rst, NULL);                                                              // * Obtenemos el resultado
    
//     // ? Validamos si hay un resultado
//     if ( $row ) 
//         $salida = $row[0]; // ! Asignamos el resultado a la variable de salida

// 	pg_free_result($rst); // * Liberamos los resultados
	
// 	return $salida;
// }

// // ====================================================================================================
// // Función para conectarse a otras bases de datos

// function getCampodl($sql, $conection){
// 	$salida = "";
// 	global $conn_pg, $conn_pg_cat;

// 	// Se cambia el nombre de la conexión para no afectar a las demás funciones que la usen
// 	if($conection!=''){
// 		$conn_pgCons = $conection;
// 	}else{
// 		$conn_pgCons = $conn_pg;
// 	}
// 	$rst_sql = pg_query($conn_pgCons, $sql) or die ("U-BNo se logro ejecutar la consulta, rep&oacute;rtelo al &aacute;rea de soporte t&eacute;cnico. <br> $sql");

// 	while ($row = pg_fetch_row($rst_sql, NULL, PGSQL_ASSOC)) {
// 		$salida = $row['salida'];
// 	}

// 	pg_free_result($rst_sql);
	
// 	return $salida;
// }

// // ====================================================================================================
// // Función para conectarse a la base de datos propia

// function getCampo($sql){
// 	$salida = "";
// 	global $conn_pg;
// 	$rst_sql = pg_query($conn_pg, $sql) or die ("U-BNo se logro ejecutar la consulta, rep&oacute;rtelo al &aacute;rea de soporte t&eacute;cnico. <br> $sql");

// 	while ($row = pg_fetch_row($rst_sql, NULL, PGSQL_ASSOC)) {
// 		$salida = $row['salida'];
// 	}

// 	pg_free_result($rst_sql);
	
// 	return $salida;
// }

// // ====================================================================================================
// // Valida que las cadenas de texto enviadas no contengan caracteres que puedan afectar la base de datos

// function val_string($v_String){

// 	// Variable de salida
// 	$v_Salida = "";

// 	// Aquí agregamos todas las palabras que vamos a restringir

// 	// Palabras que puedan afectar la base de datos
// 	$v_Searchwords  = '(and|or|drop|delete|where|alter|add|all|between|case|call|collate|column|commit|convert|constraint|count|create|createview|database|';
// 	$v_Searchwords .= 'declare|deny|distinct|do|else|each|end|execute|exists|fetch|for|from|function|group|grant|handler|having|if|in|index|inner|insert|';
// 	$v_Searchwords .= 'into|join|key|left|like|login|loop|max|min|modifiable|modify|new|next|not|offset|on|only|open|order|out|outer|owner|password|';
// 	$v_Searchwords .= 'primary|print|public|read|rename|replace|restrict|return|returns|reverse|revoke|right|rollback|row|select|set|size|sqlstate|start|';
// 	$v_Searchwords .= 'sum|table|then|to|trigger|uncommitted|union|unique|update|user|using|values|view|when|while|with|work|write|truncate';

// 	// Palabras que puedan afectar el funcionamiento del sistema
// 	$v_Searchwords .= 'script|php)';

// 	// Arreglo que guardará las coincidencias
// 	$v_Coincidencias_Arr = array();
	
// 	// Recorremos las cadenas de texto y buscamos las palabras claves
// 	foreach($v_String as $k=>$v){
// 		if(preg_match_all("/\b$v_Searchwords\b/i", $v, $v_Coincidencias)){
// 			$v_Coincidencias_Arr[$k] = $v_Coincidencias;
// 		}
// 	}
	
// 	// Contamos todos los objetos que se envían por medio de la función
// 	$v_CountS = count($v_String);
	
// 	// Recorremos todos los objetos uno por uno para meter en otro arreglo las palabras encontradas
// 	for($i = 0; $i < $v_CountS; $i++){
// 		for($c = 0; $c < count($v_Coincidencias_Arr[$i][0]); $c++){

// 			$v_Restricted .= $v_Pipe . $v_Coincidencias_Arr[$i][0][$c];

// 			$v_Pipe = "|";

// 		}
// 	}

// 	// Convertimos en un arreglo las palabras encontradas
// 	$v_Return = explode("|", $v_Restricted);

// 	// Contamos todos arreglos que se encuentren
// 	$v_CountR = count($v_Return);

// 	// Removemos las palabras duplicadas dentro del arreglo
// 	$v_Return = array_unique($v_Return);
	
// 	// Recorremos el arreglo de los arreglos encontrados para meter en una cadena de texto las palabras encontradas
// 	for($r = 0; $r <= $v_CountR; $r++){
// 		if($v_Return[$r] != ""){
// 			$v_Salida .= $v_Coma.'"'.$v_Return[$r].'"';

// 			$v_Coma = ", ";
// 		}
// 	}

// 	// Sí existen palabras restringidas devolvemos la variable llena y impide la inserción a la base de datos
// 	if($v_Salida != ""){
// 		$v_Salida = "No puedes ingresar palabras como ".$v_Salida;
// 	}

// 	// Retornamos la variable de salida
// 	return $v_Salida;

// }

// // ====================================================================================================

// function setSql($sql){
// 	//Ejecuta el sql recibido
// //	echo $sql;
// 	$salida = false;
// 	global $conn_pg;
// 	$rst_sql = pg_query($conn_pg, $sql) or die ("U-C No se logro ejecutar la consulta, rep&oacute;rtelo al &aacute;rea de soporte t&eacute;cnico. <br> " . $sql);

// 	if($rst_sql){
// 		$salida = true;
// 	}

// 	pg_free_result($rst_sql);
	
// 	return $salida;
// }

// // ====================================================================================================

// function getFormatoFechaPaginas($fecha){
// 	if(strlen($fecha) != 0){
// 		return substr($fecha, 8, 2) ."/". substr($fecha, 5, 2) ."/". substr($fecha, 0, 4);
// 	}else{
// 		return "";
// 	}
// }

// // ====================================================================================================

// function getFormatoFechaPostgres($fecha){
// 	if(strlen($fecha) != 0){
// 		return substr($fecha, 6, 4) ."-". substr($fecha, 3, 2) ."-". substr($fecha, 0, 2);
// 	}else{
// 		return "";
// 	}
// }

// function getFormatoRFCPag($rfc){
// 	$salida=str_replace("-","",$rfc); 	
// 	return $salida;
// }

// function getFormatoRFCPostgres($rfc){
// 	if(strlen($rfc) != 0){
// 		return substr($rfc, 0, 4) ."-". substr($rfc, 4, 6) ."-". substr($rfc, 10, 3);
// 	}else{
// 		return "";
// 	}		
// }

// function valida(&$var){
// 	if(!isset($var)){
// 		return "";
// 	}else{
// 		return htmlspecialchars($var, ENT_QUOTES);
// 	}
// }

// function validaPass(&$var){
// 	if(!isset($var)){
// 		return "";
// 	}else{
// 		return htmlspecialchars($var, ENT_IGNORE);
// 	}
// }

// function dias_transcurridos($fecha_i,$fecha_f)
// {
// 	$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
// 	$dias 	= abs($dias); $dias = floor($dias);		
// 	return $dias;
// }

// //Función para calcular los días hábiles

// function diasHabilesFCG($fecha1, $fecha2){
// 	$vdiash = 0;
// //	for($fecha1;$fecha1<=$fecha2;$fecha1=strtotime('+1 day ' . date('Y-m-d',$fecha1))){ 
// 	for($i=$fecha1;$i<=$fecha2;$i = date("Y-m-d", strtotime($i ."+ 1 days"))){
// 		$vsabdom = date('w',strtotime($i));
// 		$fechaEval = date('Y-m-d',strtotime($i));
// 		$diafestivo = getCampo("select fecha as salida from dias_festivos where fecha = '" . $fechaEval ."'");
// //		echo $fechaEval." - ". $diafestivo;
// 		if(($vsabdom != 0) and ($vsabdom != 6)){
// 			if ($diafestivo == ""){
// 				$vdiash ++;
// 			}
// 		}
// 	}  	
// 	return $vdiash;
// }

// // ================================================================
// //Regresa el nombre del mes

// function getMes($vformato){
// 	$vmes = date("n");
// 	$salida = "";
// 	if ($vformato=='M'){
// 		switch ($vmes){
// 		   case  1: $salida = "ENERO"; break;
// 		   case  2: $salida = "FEBRERO"; break;
// 		   case  3: $salida = "MARZO"; break;
// 		   case  4: $salida = "ABRIL"; break;
// 		   case  5: $salida = "MAYO"; break;
// 		   case 6: $salida = "JUNIO"; break;
// 		   case 7: $salida = "JULIO"; break;
// 		   case 8: $salida = "AGOSTO"; break;
// 		   case 9: $salida = "SEPTIEMBRE"; break;
// 		   case 10: $salida = "OCTUBRE"; break;
// 		   case 11: $salida = "NOVIEMBRE"; break;
// 		   case 12: $salida = "DICIEMBRE"; break;
// 		   default : $salida = "";
// 		}
// 	}else if ($vformato=='m'){
// 		switch ($vmes){
// 		   case  1: $salida = "Enero"; break;
// 		   case  2: $salida = "Febrero"; break;
// 		   case  3: $salida = "Marzo"; break;
// 		   case  4: $salida = "Abril"; break;
// 		   case  5: $salida = "Mayo"; break;
// 		   case 6: $salida = "Junio"; break;
// 		   case 7: $salida = "Julio"; break;
// 		   case 8: $salida = "Agosto"; break;
// 		   case 9: $salida = "Septiembre"; break;
// 		   case 10: $salida = "Octubre"; break;
// 		   case 11: $salida = "Noviembre"; break;
// 		   case 12: $salida = "Diciembre"; break;
// 		   default : $salida = "";
// 		}
// 	}	
// 	return $salida;
// }

// // ================================================================
// //Regresa el nombre del mes dado

// function giveMes($vmes, $vformato){
// 	$salida = "";
	
// 	if ($vformato=='M'){
// 		switch ($vmes){
// 		   case  1: $salida = "ENERO"; break;
// 		   case  2: $salida = "FEBRERO"; break;
// 		   case  3: $salida = "MARZO"; break;
// 		   case  4: $salida = "ABRIL"; break;
// 		   case  5: $salida = "MAYO"; break;
// 		   case 6: $salida = "JUNIO"; break;
// 		   case 7: $salida = "JULIO"; break;
// 		   case 8: $salida = "AGOSTO"; break;
// 		   case 9: $salida = "SEPTIEMBRE"; break;
// 		   case 10: $salida = "OCTUBRE"; break;
// 		   case 11: $salida = "NOVIEMBRE"; break;
// 		   case 12: $salida = "DICIEMBRE"; break;
// 		   default : $salida = "";
// 		}
// 	}else if ($vformato=='m'){
// 		switch ($vmes){
// 		   case  1: $salida = "Enero"; break;
// 		   case  2: $salida = "Febrero"; break;
// 		   case  3: $salida = "Marzo"; break;
// 		   case  4: $salida = "Abril"; break;
// 		   case  5: $salida = "Mayo"; break;
// 		   case 6: $salida = "Junio"; break;
// 		   case 7: $salida = "Julio"; break;
// 		   case 8: $salida = "Agosto"; break;
// 		   case 9: $salida = "Septiembre"; break;
// 		   case 10: $salida = "Octubre"; break;
// 		   case 11: $salida = "Noviembre"; break;
// 		   case 12: $salida = "Diciembre"; break;
// 		   default : $salida = "";
// 		}
// 	}	
// 	return $salida;
// }

// // FUNCIONES DE CONVERSION DE NUMEROS A LETRAS.
 
// function centimos(){
// 	global $importe_parcial;
// 	if($importe_parcial!=""){
// 		$importe_parcial = number_format($importe_parcial, 2, ".", "") * 100;
// 		if ($importe_parcial > 0)
// 			if ($importe_parcial > 9)
// 				$num_letra = " PESOS ".$importe_parcial."/100 M.N.";
// 			else
// 				$num_letra = " PESOS 0".$importe_parcial."/100 M.N.";
// 		else
// 			$num_letra = " PESOS 00"."/100 M.N.";
// 	}else{
// 		$num_letra = " PESOS 00"."/100 M.N.";
// 	}
// /*	if ($importe_parcial > 0)
// 		$num_letra = " con ".decena_centimos($importe_parcial);
// 	else
// 		$num_letra = "";
// */		
// 	return $num_letra;
// }
 
// function unidad_centimos($numero){
// 	switch ($numero){
// 		case 9:	$num_letra = "nueve c�ntimos";	break;
// 		case 8:	$num_letra = "ocho c�ntimos";	break;
// 		case 7:	$num_letra = "siete c�ntimos";	break;
// 		case 6: $num_letra = "seis c�ntimos";	break;
// 		case 5:	$num_letra = "cinco c�ntimos";	break;
// 		case 4:	$num_letra = "cuatro c�ntimos";	break;
// 		case 3:	$num_letra = "tres c�ntimos";	break;
// 		case 2:	$num_letra = "dos c�ntimos";	break;
// 		case 1: $num_letra = "un c�ntimo";		break;
// 	}
// 	return $num_letra;
// }
 
// function decena_centimos($numero){
// 	if ($numero >= 10){
// 		if ($numero >= 90 && $numero <= 99){
// 			  if ($numero == 90)
// 				  return "noventa c�ntimos";
// 			  else if ($numero == 91)
// 				  return "noventa y un c�ntimos";
// 			  else
// 				  return "noventa y ".unidad_centimos($numero - 90);
// 		}
// 		if ($numero >= 80 && $numero <= 89){
// 			if ($numero == 80)
// 				return "ochenta c�ntimos";
// 			else if ($numero == 81)
// 				return "ochenta y un c�ntimos";
// 			else
// 				return "ochenta y ".unidad_centimos($numero - 80);
// 		}
// 		if ($numero >= 70 && $numero <= 79){
// 			if ($numero == 70)
// 				return "setenta c�ntimos";
// 			else if ($numero == 71)
// 				return "setenta y un c�ntimos";
// 			else
// 				return "setenta y ".unidad_centimos($numero - 70);
// 		}
// 		if ($numero >= 60 && $numero <= 69){
// 			if ($numero == 60)
// 				return "sesenta c�ntimos";
// 			else if ($numero == 61)
// 				return "sesenta y un c�ntimos";
// 			else
// 				return "sesenta y ".unidad_centimos($numero - 60);
// 		}
// 		if ($numero >= 50 && $numero <= 59){
// 			if ($numero == 50)
// 				return "cincuenta c�ntimos";
// 			else if ($numero == 51)
// 				return "cincuenta y un c�ntimos";
// 			else
// 				return "cincuenta y ".unidad_centimos($numero - 50);
// 		}
// 		if ($numero >= 40 && $numero <= 49){
// 			if ($numero == 40)
// 				return "cuarenta c�ntimos";
// 			else if ($numero == 41)
// 				return "cuarenta y un c�ntimos";
// 			else
// 				return "cuarenta y ".unidad_centimos($numero - 40);
// 		}
// 		if ($numero >= 30 && $numero <= 39){
// 			if ($numero == 30)
// 				return "treinta c�ntimos";
// 			else if ($numero == 91)
// 				return "treinta y un c�ntimos";
// 			else
// 				return "treinta y ".unidad_centimos($numero - 30);
// 		}
// 		if ($numero >= 20 && $numero <= 29){
// 			if ($numero == 20)
// 				return "veinte c�ntimos";
// 			else if ($numero == 21)
// 				return "veintiun c�ntimos";
// 			else
// 				return "veinti".unidad_centimos($numero - 20);
// 		}
// 		if ($numero >= 10 && $numero <= 19){
// 			if ($numero == 10)
// 				return "diez c�ntimos";
// 			else if ($numero == 11)
// 				return "once c�ntimos";
// 			else if ($numero == 11)
// 				return "doce c�ntimos";
// 			else if ($numero == 11)
// 				return "trece c�ntimos";
// 			else if ($numero == 11)
// 				return "catorce c�ntimos";
// 			else if ($numero == 11)
// 				return "quince c�ntimos";
// 			else if ($numero == 11)
// 				return "dieciseis c�ntimos";
// 			else if ($numero == 11)
// 				return "diecisiete c�ntimos";
// 			else if ($numero == 11)
// 				return "dieciocho c�ntimos";
// 			else if ($numero == 11)
// 				return "diecinueve c�ntimos";
// 		}
// 	}else{
// 		return unidad_centimos($numero);
// 	}
// }
 
// function unidad($numero){
// 	switch ($numero){
// 		case 9:	$num = "NUEVE";	break;
// 		case 8:	$num = "OCHO";	break;
// 		case 7:	$num = "SIETE";	break;
// 		case 6:	$num = "SEIS";	break;
// 		case 5:	$num = "CINCO";	break;
// 		case 4:	$num = "CUATRO"; break;
// 		case 3:	$num = "TRES";	break;
// 		case 2:	$num = "DOS";	break;
// 		case 1:	$num = "UNO";	break;
// 		case 0: $num = ""; break;
// 	}
// 	return $num;
// }
 
// function decena($numero){
// 	if ($numero >= 90 && $numero <= 99){
// 		$num_letra = "NOVENTA ";
// 		if ($numero > 90)
// 			$num_letra = $num_letra."y ".unidad($numero - 90);
// 	}else if ($numero >= 80 && $numero <= 89){
// 		$num_letra = "OCHENTA ";
// 		if ($numero > 80)
// 			$num_letra = $num_letra."y ".unidad($numero - 80);
// 	}else if ($numero >= 70 && $numero <= 79){
// 		$num_letra = "SETENTA ";
// 		if ($numero > 70)
// 			$num_letra = $num_letra."y ".unidad($numero - 70);
// 	}else if ($numero >= 60 && $numero <= 69){
// 		$num_letra = "SESENTA ";
// 		if ($numero > 60)
// 			$num_letra = $num_letra."y ".unidad($numero - 60);
// 	}else if ($numero >= 50 && $numero <= 59){
// 		$num_letra = "CINCUENTA ";
// 		if ($numero > 50)
// 			$num_letra = $num_letra."y ".unidad($numero - 50);
// 	}else if ($numero >= 40 && $numero <= 49){
// 		$num_letra = "CUARENTA ";
// 		if ($numero > 40)
// 			$num_letra = $num_letra."y ".unidad($numero - 40);
// 	}else if ($numero >= 30 && $numero <= 39){
// 		$num_letra = "TREINTA ";
// 		if ($numero > 30)
// 			$num_letra = $num_letra."y ".unidad($numero - 30);
// 	}else if ($numero >= 20 && $numero <= 29){
// 		if ($numero == 20)
// 			$num_letra = "VEINTE ";
// 		else
// 			$num_letra = "VEINTI".unidad($numero - 20);
// 	}else if ($numero >= 10 && $numero <= 19){
// 		switch ($numero){
// 			case 10: $num_letra = "DIEZ ";	break;
// 			case 11: $num_letra = "ONCE ";	break;
// 			case 12: $num_letra = "DOCE ";	break;
// 			case 13: $num_letra = "TRECE ";	break;
// 			case 14: $num_letra = "CATORCE "; break;
// 			case 15: $num_letra = "QUINCE "; break;
// 			case 16: $num_letra = "DIECISEIS ";	break;
// 			case 17: $num_letra = "DIECISIETE "; break;
// 			case 18: $num_letra = "DIECIOCHO ";	break;
// 			case 19: $num_letra = "DIECINUEVE "; break;
// 		}
// 	}else{
// 		$num_letra = unidad($numero);
// 	}
// 	return $num_letra;
// }
 
// function centena($numero){
// 	if ($numero >= 100)	{
// 		if ($numero >= 900 & $numero <= 999){
// 			$num_letra = "NOVECIENTOS ";
// 			if ($numero > 900)
// 				$num_letra = $num_letra.decena($numero - 900);
// 		}else if ($numero >= 800 && $numero <= 899){
// 			$num_letra = "OCHOCIENTOS ";
// 			if ($numero > 800)
// 				$num_letra = $num_letra.decena($numero - 800);
// 		}else if ($numero >= 700 && $numero <= 799){
// 			$num_letra = "SETECIENTOS ";
// 			if ($numero > 700)
// 				$num_letra = $num_letra.decena($numero - 700);
// 		}else if ($numero >= 600 && $numero <= 699){
// 			$num_letra = "SEISCIENTOS ";
// 			if ($numero > 600)
// 				$num_letra = $num_letra.decena($numero - 600);
// 		}else if ($numero >= 500 && $numero <= 599){
// 			$num_letra = "QUINIENTOS ";
// 			if ($numero > 500)
// 				$num_letra = $num_letra.decena($numero - 500);
// 		}else if ($numero >= 400 && $numero <= 499){
// 			$num_letra = "CUATROCIENTOS ";
//  			if ($numero > 400)
// 				$num_letra = $num_letra.decena($numero - 400);
// 		}else if ($numero >= 300 && $numero <= 399){
// 			$num_letra = "TRESCIENTOS ";
// 			if ($numero > 300)
// 				$num_letra = $num_letra.decena($numero - 300);
// 		}else if ($numero >= 200 && $numero <= 299){
// 			$num_letra = "DOSCIENTOS ";
// 			if ($numero > 200)
// 				$num_letra = $num_letra.decena($numero - 200);
// 		}else if ($numero >= 100 && $numero <= 199){
// 			if ($numero == 100)
// 				$num_letra = "CIEN ";
// 			else
// 				$num_letra = "CIENTO ".decena($numero - 100);
// 		}
// 	}else{
// 		$num_letra = decena($numero);
// 	}
// 	return $num_letra;
// }
 
// function cien(){
// 	global $importe_parcial;
 
// 	$parcial = 0; $car = 0;
// 	while ((substr($importe_parcial, 0, 1) == 0) && (strlen($importe_parcial)>1))
// 		$importe_parcial = substr($importe_parcial, 1, strlen($importe_parcial) - 1);
// 	if ($importe_parcial > 0){
// 	if ($importe_parcial >= 1 && $importe_parcial <= 9.99)
// 		$car = 1;
// 	else if ($importe_parcial >= 10 && $importe_parcial <= 99.99)
// 		$car = 2;
// 	else if ($importe_parcial >= 100 && $importe_parcial <= 999.99)
// 		$car = 3;
 	
// 	$parcial = substr($importe_parcial, 0, $car);
// 	$importe_parcial = substr($importe_parcial, $car);
// // 	echo $parcial."<br>";
// //	echo $importe_parcial."<br>";

// 	$num_letra = centena($parcial).centimos();
// 	}else{
// 		$num_letra = centimos();	
// 	} 
// 	return $num_letra;
// }


// function cien_mil(){
// 	global $importe_parcial;
 
// 	$parcial = 0; $car = 0;
 
// 	while ((substr($importe_parcial, 0, 1) == 0) && (strlen($importe_parcial)>1))
// 		$importe_parcial = substr($importe_parcial, 1, strlen($importe_parcial) - 1);
 
// 	if ($importe_parcial >= 1000 && $importe_parcial <= 9999.99)
// 		$car = 1;
// 	else if ($importe_parcial >= 10000 && $importe_parcial <= 99999.99)
// 		$car = 2;
// 	else if ($importe_parcial >= 100000 && $importe_parcial <= 999999.99)
// 		$car = 3;
 
// 	$parcial = substr($importe_parcial, 0, $car);
// 	$importe_parcial = substr($importe_parcial, $car);
// //	echo $parcial."<br>";
// //	echo $importe_parcial."<br>";
// 	if ($parcial > 0){
// 		if ($parcial == 1){
// 			$num_letra = "UN MIL ";
// 		}else
// 			$num_letra = centena($parcial)." MIL ";
// 	}
// 	return $num_letra;
// }
 
 
// function millon(){
// 	global $importe_parcial;
 
// 	$parcial = 0; $car = 0;
 
// 	while ((substr($importe_parcial, 0, 1) == 0) && (strlen($importe_parcial)>1))
// 		$importe_parcial = substr($importe_parcial, 1, strlen($importe_parcial) - 1);
 
// 	if ($importe_parcial >= 1000000 && $importe_parcial <= 9999999.99)
// 		$car = 1;
// 	else if ($importe_parcial >= 10000000 && $importe_parcial <= 99999999.99)
// 		$car = 2;
// 	else if ($importe_parcial >= 100000000 && $importe_parcial <= 999999999.99)
// 		$car = 3;
 
// 	$parcial = substr($importe_parcial, 0, $car);
// 	$importe_parcial = substr($importe_parcial, $car);
 
// 	if ($parcial == 1)
// 		$num_letras = "UN MILL�N ";
// 	else
// 		$num_letras = centena($parcial)." MILLONES ";
 
// 	return $num_letras;
// }
 
// function convertir_a_letras($numero){
// 	global $importe_parcial;
 
// 	$importe_parcial = $numero;
 
// 	if ($numero < 1000000000)
// 	{
// 		if ($numero >= 1000000 && $numero <= 999999999.99)
// 			$num_letras = millon().cien_mil().cien();
// 		else if ($numero >= 1000 && $numero <= 999999.99)
// //			$num_letras = cien_mil();
// 			$num_letras = cien_mil().cien();
// 		else if ($numero >= 1 && $numero <= 999.99)
// 			$num_letras = cien();
// 		else if ($numero >= 0.01 && $numero <= 0.99)
// 		{
// 			if ($numero == 0.01)
// 				$num_letras = "UN CENTAVO";
// 			else
// 				$num_letras = convertir_a_letras(($numero * 100)."/100")." M.N.";
// 		}
// 	}
// 	return $num_letras;
// }


// function QuitarArticulos($palabra){ 
// 	$palabra=str_replace("DEL ","",$palabra); 
// 	$palabra=str_replace("LAS ","",$palabra); 
// 	$palabra=str_replace("DE ","",$palabra); 
// 	$palabra=str_replace("LA ","",$palabra); 
// 	$palabra=str_replace("Y ","",$palabra); 
// 	$palabra=str_replace("A ","",$palabra); 
// 	return $palabra; 
// } 

// function EsVocal($letra){ 
// 	if ($letra == 'A' || $letra == 'E' || $letra == 'I' || $letra == 'O' || $letra == 'U' || 
// 		$letra == 'a' || $letra == 'e' || $letra == 'i' || $letra == 'o' || $letra == 'u') 
// 		return 1; 
// 	else 
// 		return 0; 
// } 

// function CalcularRFC($nombre,$apellidoPaterno,$apellidoMaterno,$fecha){ 
// 	/*Cambiamos todo a mayúsculas. 
// 	Quitamos los espacios al principio y final del nombre y apellidos*/ 
// 	$nombre =strtoupper(trim($nombre)); 
// 	$apellidoPaterno =strtoupper(trim($apellidoPaterno)); 
// 	$apellidoMaterno =strtoupper(trim($apellidoMaterno)); 

// 	//RFC que se regresará 
// 	$rfc=""; 

// 	//Quitamos los artículos de los apellidos 
// 	$apellidoPaterno = QuitarArticulos($apellidoPaterno); 
// 	$apellidoMaterno = QuitarArticulos($apellidoMaterno); 

// 	//Agregamos el primer caracter del apellido paterno 
// 	$rfc = substr($apellidoPaterno,0, 1); 
	
// 	//Buscamos y agregamos al rfc la primera vocal del primer apellido 
// 	$len_apellidoPaterno=strlen($apellidoPaterno); 
// 	for($x=1;$x<$len_apellidoPaterno;$x++){ 
// 		$c=substr($apellidoPaterno,$x,1); 
// 		if (EsVocal($c)){ 
// 			$rfc .= $c; 
// 			break; 
// 		} 
// 	} 

// 	//Agregamos el primer caracter del apellido materno 
// 	$rfc .= substr($apellidoMaterno,0, 1); 

// 	//Agregamos el primer caracter del primer nombre 
// 	$rfc .= substr($nombre,0, 1); 

// 	//agregamos la fecha ddmmyyyy
// 	$rfc .= substr($fecha,6, 2).substr($fecha,2, 2).substr($fecha,0, 2); 

// 	//Le agregamos la homoclave al rfc 
// 	CalcularHomoclave($apellidoPaterno." ".$apellidoMaterno." ".$nombre, $fecha,$rfc); 
// 	return $rfc; 
// } 

// function CalcularHomoclave($nombreCompleto,$fecha, &$rfc){ 
// 	//Guardara el nombre en su correspondiente numérico 
// 	//agregamos un cero al inicio de la representación númerica del nombre 
// 	$nombreEnNumero="0"; 
// 	//La suma de la secuencia de números de nombreEnNumero 
// 	$valorSuma = 0; 

// 	#region Tablas para calcular la homoclave 
// 	//Estas tablas realmente no se porque son como son 
// 	//solo las copie de lo que encontré en internet 

// 	$tablaRFC1['&']='10'; 
// 	$tablaRFC1['Ñ']='10'; 
// 	$tablaRFC1['A']='11'; 
// 	$tablaRFC1['B']='12'; 
// 	$tablaRFC1['C']='13'; 
// 	$tablaRFC1['D']='14'; 
// 	$tablaRFC1['E']='15'; 
// 	$tablaRFC1['F']='16'; 
// 	$tablaRFC1['G']='17'; 
// 	$tablaRFC1['H']='18'; 
// 	$tablaRFC1['I']='19'; 
// 	$tablaRFC1['J']='21'; 
// 	$tablaRFC1['K']='22'; 
// 	$tablaRFC1['L']='23'; 
// 	$tablaRFC1['M']='24'; 
// 	$tablaRFC1['N']='25'; 
// 	$tablaRFC1['O']='26'; 
// 	$tablaRFC1['P']='27'; 
// 	$tablaRFC1['Q']='28'; 
// 	$tablaRFC1['R']='29'; 
// 	$tablaRFC1['S']='32'; 
// 	$tablaRFC1['T']='33'; 
// 	$tablaRFC1['U']='34'; 
// 	$tablaRFC1['V']='35'; 
// 	$tablaRFC1['W']='36'; 
// 	$tablaRFC1['X']='37'; 
// 	$tablaRFC1['Y']='38'; 
// 	$tablaRFC1['Z']='39'; 
// 	$tablaRFC1['0']='00'; 
// 	$tablaRFC1['1']='01'; 
// 	$tablaRFC1['2']='02'; 
// 	$tablaRFC1['3']='03'; 
// 	$tablaRFC1['4']='04'; 
// 	$tablaRFC1['5']='05'; 
// 	$tablaRFC1['6']='06'; 
// 	$tablaRFC1['7']='07'; 
// 	$tablaRFC1['8']='08'; 
// 	$tablaRFC1['9']='09'; 
	
// 	$tablaRFC2[0]="1"; 
// 	$tablaRFC2[1]="2"; 
// 	$tablaRFC2[2]="3"; 
// 	$tablaRFC2[3]="4"; 
// 	$tablaRFC2[4]="5"; 
// 	$tablaRFC2[5]="6"; 
// 	$tablaRFC2[6]="7"; 
// 	$tablaRFC2[7]="8"; 
// 	$tablaRFC2[8]="9"; 
// 	$tablaRFC2[9]="A"; 
// 	$tablaRFC2[10]="B"; 
// 	$tablaRFC2[11]="C"; 
// 	$tablaRFC2[12]="D"; 
// 	$tablaRFC2[13]="E"; 
// 	$tablaRFC2[14]="F"; 
// 	$tablaRFC2[15]="G"; 
// 	$tablaRFC2[16]="H"; 
// 	$tablaRFC2[17]="I"; 
// 	$tablaRFC2[18]="J"; 
// 	$tablaRFC2[19]="K"; 
// 	$tablaRFC2[20]="L"; 
// 	$tablaRFC2[21]="M"; 
// 	$tablaRFC2[22]="N"; 
// 	$tablaRFC2[23]="P"; 
// 	$tablaRFC2[24]="Q"; 
// 	$tablaRFC2[25]="R"; 
// 	$tablaRFC2[26]="S"; 
// 	$tablaRFC2[27]="T"; 
// 	$tablaRFC2[28]="U"; 
// 	$tablaRFC2[29]="V"; 
// 	$tablaRFC2[30]="W"; 
// 	$tablaRFC2[31]="X"; 
// 	$tablaRFC2[32]="Y"; 
// 	$tablaRFC2[33]="Z"; 
	
// 	$tablaRFC3['A']=10; 
// 	$tablaRFC3['B']=11; 
// 	$tablaRFC3['C']=12; 
// 	$tablaRFC3['D']=13; 
// 	$tablaRFC3['E']=14; 
// 	$tablaRFC3['F']=15; 
// 	$tablaRFC3['G']=16; 
// 	$tablaRFC3['H']=17; 
// 	$tablaRFC3['I']=18; 
// 	$tablaRFC3['J']=19; 
// 	$tablaRFC3['K']=20; 
// 	$tablaRFC3['L']=21; 
// 	$tablaRFC3['M']=22; 
// 	$tablaRFC3['N']=23; 
// 	$tablaRFC3['O']=25; 
// 	$tablaRFC3['P']=26; 
// 	$tablaRFC3['Q']=27; 
// 	$tablaRFC3['R']=28; 
// 	$tablaRFC3['S']=29; 
// 	$tablaRFC3['T']=30; 
// 	$tablaRFC3['U']=31; 
// 	$tablaRFC3['V']=32; 
// 	$tablaRFC3['W']=33; 
// 	$tablaRFC3['X']=34; 
// 	$tablaRFC3['Y']=35; 
// 	$tablaRFC3['Z']=36; 
// 	$tablaRFC3['0']=0; 
// 	$tablaRFC3['1']=1; 
// 	$tablaRFC3['2']=2; 
// 	$tablaRFC3['3']=3; 
// 	$tablaRFC3['4']=4; 
// 	$tablaRFC3['5']=5; 
// 	$tablaRFC3['6']=6; 
// 	$tablaRFC3['7']=7; 
// 	$tablaRFC3['8']=8; 
// 	$tablaRFC3['9']=9; 
// 	$tablaRFC3['']=24; 
// 	$tablaRFC3[' ']=37; 
	
// 	//Recorremos el nombre y vamos convirtiendo las letras en 
// 	//su valor numérico 
// 	$len_nombreCompleto=strlen($nombreCompleto); 
// 	for($x=0;$x<$len_nombreCompleto;$x++){ 
// 		$c=substr($nombreCompleto,$x,1); 
// 		if (isset($tablaRFC1[$c])) 
// 			$nombreEnNumero.=$tablaRFC1[$c]; 
// 		else 
// 		$nombreEnNumero.="00"; 
// 	} 
// 	//Calculamos la suma de la secuencia de números 
// 	//calculados anteriormente 
// 	//la formula es: 
// 	//( (el caracter actual multiplicado por diez) 
// 	//mas el valor del caracter siguiente ) 
// 	//(y lo anterior multiplicado por el valor del caracter siguiente) 
	
// 	$n=strlen($nombreEnNumero)-1; 
// 	for ($i = 0; $i < $n; $i++){ 
// 		$prod1 = substr($nombreEnNumero, $i, 2); 
// 		$prod2 = substr($nombreEnNumero, $i + 1, 1); 
// 		$valorSuma += $prod1 * $prod2; 
// 	} 
// 	//Lo siguiente no se porque se calcula así, es parte del algoritmo. 
// 	//Los magic numbers que aparecen por ahí deben tener algún origen matemático 
// 	//relacionado con el algoritmo al igual que el proceso mismo de calcular el 
// 	//digito verificador. 
// 	//Por esto no puedo añadir comentarios a lo que sigue, lo hice por acto de fe. 
// 	$div = 0; 
// 	$mod = 0; 
// 	$div = $valorSuma % 1000; 
// 	$mod = floor($div / 34);//cociente 
// 	$div = $div - $mod * 34;//residuo 
	
// 	$hc = $tablaRFC2[$mod]; 
// 	$hc.= $tablaRFC2[$div]; 
	
// 	$rfc .= $hc; 
	
// 	//Aqui empieza el calculo del digito verificador basado en lo que tenemos del RFC 
// 	//En esta parte tampoco conozco el origen matemático del algoritmo como para dar 
// 	//una explicación del proceso, así que ¡tengamos fe hermanos!. 
// 	$sumaParcial = 0; 
// 	$n=strlen($rfc); 
// 	for ($i = 0; $i < $n; $i++){ 
// 		$c=substr($rfc,$i,1); 
// 		if (isset($tablaRFC3[$c])){ 
// 			$sumaParcial += ($tablaRFC3[$c] * (14 - ($i + 1))); 
// 		} 
// 	} 
	
// 	$moduloVerificador = $sumaParcial % 11; 
// 	if ($moduloVerificador == 0){
// 		$rfc .= "0"; 
// 	}else{ 
// 		$sumaParcial = 11 - $moduloVerificador; 
// 		if ($sumaParcial == 10) 
// 			$rfc .= "A"; 
// 		else 
// 			$rfc .= $sumaParcial; 
// 	} 
// }

// //recibimos los datos..
// /*$nombre = $_POST['nombre'];
// $paterno = $_POST['paterno'];
// $materno = $_POST['materno'];
// $dia = (strlen($_POST['dia'])==1)? '0'.$_POST['dia'] : $_POST['dia'];
// $mes = (strlen($_POST['mes'])==1)? '0'.$_POST['mes'] : $_POST['mes'];
// $anio = $_POST['anio'];


// $fecha = $dia.$mes.$anio;
// echo CalcularRFC($nombre,$paterno,$materno,$fecha);
// */
// // ----------------------------------------------------
// function ejecutaSQL_cat($sql){
// 	global $conn_pg_cat;// Si no se lo pongo me manda error en el $sql 
// 	//echo $sql;
// 	$regreso = null;
// 	$rst1 = pg_query($conn_pg_cat, $sql) or die ("Error en " . "\n" . $sql);;
// 	if ($rst1){
// 		if (pg_num_rows($rst1)>0 ){
// 			$regreso = array();
// 			while ($row = pg_fetch_row($rst1, NULL, PGSQL_ASSOC)) {
// 				$regreso[] = $row;
// 			}
// 			//var_dump($regreso);
// 		}
// 	}
// 	return $regreso;
// }
// // ----------------------------------------------------
// function ejecutaSQL_($sql){
// 	global $conn_pg;// Si no se lo pongo me manda error en el $sql 
// 	//echo $sql;
// 	$regreso = null;
// 	$rst1 = pg_query($conn_pg, $sql) or die ("Error en " . "\n" . $sql);;
// 	if ($rst1){
// 		if (pg_num_rows($rst1)>0 ){
// 			$regreso = array();
// 			while ($row = pg_fetch_row($rst1, NULL, PGSQL_ASSOC)) {
// 				$regreso[] = $row;
// 			}
// 			//var_dump($regreso);
// 		}
// 	}
// 	return $regreso;
// }
// // ----------------------------------------------------
// function actualizaSql($sql){
// 	global $conn_pg;// Si no se lo pongo me manda error en el $sql 
// 	$regreso	= null;
// 	$rst1 	 	= pg_query($conn_pg, $sql) or die ("Error en " . "\n" . $sql);
// 	$regreso	= pg_affected_rows($rst1);
// 	return $regreso;
// }

// // ===========================================================================
// // Altas y bajas

// function AltasBajas($text, $utf8 = null){
// 	if($utf8 == null){
// 		$text = mb_strtolower($text);
// 	}
// 	if($utf8){
// 		$text = mb_strtolower($text, "UTF-8");
// 	}
// 	$text = ucfirst($text);

// 	return $text;
// }

// // ===========================================================================
// // Asignar NULL a datos integer, dentro de un Query

// function setNULL($dato){
// 	if(empty($dato)){
// 		$dato = "NULL";
// 	}else{
// 		$dato = "'$dato'";
// 	}
// 	return $dato;
// }

// function setDateX($dato){
// 	if(strlen($dato) == 7 or empty($dato)){
// 		$dato = "NULL";
// 	}else{
// 		$dato = "'$dato'";
// 	}
// 	return $dato;
// }

// // ===========================================================================
// // Función para traer rango de días
// function createDateRangeArray($strDateFrom,$strDateTo){
// 	$aryRange = [];

// 	$iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
// 	$iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

// 	if ($iDateTo >= $iDateFrom) {
// 		array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
// 		while ($iDateFrom<$iDateTo) {
// 			$iDateFrom += 86400; // add 24 hours
// 			array_push($aryRange, date('Y-m-d', $iDateFrom));
// 		}
// 	}
// 	return $aryRange;
// }

// function getRecaptchaSecret(){
// 	$recaptcha_secret = '6LeGRc4UAAAAAOzWD1RFtia4_P0pw-BLYGPH3Vd9';

// 	return $recaptcha_secret;
// }

// /**
//  * ! ==============================================================================
//  * ! Función para enviar correos con PHPMailer
//  * 
//  * @param array $destinatarios Arreglo con los correos a los que se enviará el correo
//  * @param string $asunto Asunto del correo
//  * @param string $cuerpo Cuerpo del correo
//  * @param array $cc Arreglo con los correos a los que se enviará copia del correo
//  * @param string $mensaje Mensaje de respuesta retornada por la función
//  * 
//  * @return boolean Regresa true si el correo fue enviado con éxito, false en caso contrario
//  */


//  // Dependencias
//  //use PHPMailer\PHPMailer\Exception;
//  //use PHPMailer\PHPMailer\PHPMailer;

//  function sendMail($destinatarios, $asunto, $cuerpo, $cc = null, &$mensaje) {

//     global $conn_pg;
    
//     $mail     = new PHPMailer(true);
//     $mensaje  = '';
//     $response = false;
// 	$nombre_carpeta = takeCampo('nombre_carpeta', 'parametros');

//     // Obtener la URL del servidor
//     $protocol 	= (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
//     $host 		= $_SERVER['HTTP_HOST'];  // Dominio del servidor
// 	$base_path 	= "/$nombre_carpeta/";
//     $base_url 	= $protocol . $host . $base_path;
    
//     // URL personalizada que quieres incluir
//     $url_sistema = $base_url;  // Cambia "ruta_del_tramite" por la ruta correcta del sistema

//     try {
//         // ? Configuración del servidor SMTP
//         $mail->isSMTP();
//         $mail->Host       = 'correo.ife.org.mx'; 
//         $mail->SMTPAuth   = True;
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         $mail->Port       = 465; 
//         $mail->isHTML(true);
//         $mail->CharSet    = 'UTF-8';

// 		$correo_sistema  = takeCampo('correo_generico', 'parametros');
//         $nombre_sistema  = takeCampo('nombre_sistema', 'parametros');
// 		$password = takeCampo('correo_generico_pws', 'parametros');

// 		$usuario = takeCampo('correo_generico_user', 'parametros');
// 		$mail->Username   = $usuario;
// 		$mail->Password   = $password;

//         // $mail->isSMTP();
//         // $mail->Host       = 'correo.ife.org.mx'; 
//         // $mail->SMTPAuth   = false;
//         // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         // $mail->Port       = 465; 
//         // $mail->isHTML(true);
//         // $mail->CharSet    = 'UTF-8';

//         $directorio = (explode($nombre_carpeta, __DIR__)[0])."/../".$nombre_carpeta;

//         $mail->AddEmbeddedImage("$directorio/assets/img/banner_sistema_int.png",'correoTop','banner_sistema_int.png','base64','image/png');
//         $mail->AddEmbeddedImage("$directorio/assets/img/correoFooter.png",'correoFooter','correoFooter.png','base64','image/png');

//         // * Obtención de los parámetros del sistema
        
    
//         // ? Destinatarios
//         $mail->setFrom($correo_sistema, $nombre_sistema);
//         foreach ($destinatarios as $destinatario) {
//             $mail->addAddress($destinatario); 
//         }
        
//         // ? Copias
//         if ($cc != null) {
//             foreach ($cc as $con_copia) {
//                 $mail->addCC($con_copia);
//             }
//         }
    
//         // ? Contenido del correo
//         $mail->Subject = $asunto;
        
//         // ? Cuerpo del correo con URL incluida
//         $cuerpo = '
//         <img src="cid:banner_sistema_int.png"> <br><br>
//         <font face="arial" size=2>
//             Se le hace de su conocimiento lo siguiente: <br><br>

//             '.$cuerpo.'<br><br>
            
//             Puede validar este trámite ingresando al siguiente enlace:<br>
//             <a href="'.$url_sistema.'">'.$url_sistema.'</a><br><br>
			
//             Atentamente<br>
//             '.$nombre_sistema.' <br><br>

//             <strong>Este es un mensaje automático y no es necesario responderlo</strong><br><br>
//         </font>
//         <img src="cid:correoFooter.png">
//         ';
//         $mail->Body = $cuerpo;
    
//         $mail->send();
//         $mensaje = 'Mensaje enviado con éxito';
//         $response = true;
//     } catch (Exception $e) {
//         $mensaje = "El mensaje no pudo ser enviado. Error de PHPMailer: {$mail->ErrorInfo}";
//         $response = false;
//     }

//     return $response;
}

?>