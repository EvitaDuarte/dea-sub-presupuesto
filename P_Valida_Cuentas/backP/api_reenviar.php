<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

define("VALIDA"		, "Estructura Válida");
define("XREVISAR"	, "Estructura a revisar");
define("YAEXISTE"	, "Ya existe combinación en siga -----");
define("IDXEDO"		, 9);
session_start();

	try {
	    require_once($_SESSION['conW']);
	    require_once("P_rutinas_.php");
    	require_once("M_Catalogos_.php");
    	require_once("Pdo/C_Estructuras_.php"); 


	    /* =========================
	       PARAMETROS DATATABLES
	       ========================= */
	    $draw   = intval($_POST['draw'] ?? 0); // Estas variables se ven en Network/Payload del navegador
	    $start  = intval($_POST['start'] ?? 0);
	    $length = intval($_POST['length'] ?? 25);
	    $url	= $_POST['url'];
	    $r		= array('success' => false , 'mensaje' => '');
	    // Variables de depuracion
    	$nRen	= 0;

	    /* =========================
	       FILTROS
	       ========================= */
	    $params			  = [];
	    $filtro			  = $_POST['filtro'] ?? [];
        $params[':envio'] = $filtro['numEnvio'];

	    /* =========================
	       TOTAL SIN FILTROS
	       ========================= */
		$total =  $conn_pdo->query("SELECT COUNT(*) FROM epinvalidas ")->fetchColumn();
		$total += $conn_pdo->query("SELECT COUNT(*) FROM epvalidas   ")->fetchColumn();

	    /* =========================
	       TOTAL CON FILTROS
	       ========================= */
	    $filtered  = filtroValidas(true ,$params,$conn_pdo);
	    $filtered += filtroValidas(false,$params,$conn_pdo);

	    /* =========================
	       DATOS PAGINADOS
	       ========================= */
	    $params[':length'] = $length;
	    $params[':start']  = $start;
		$data = [
		    ...queryValidas(true , $params, $conn_pdo),
		    ...queryValidas(false, $params, $conn_pdo)
		];

	    /* ================================
	       Revisar si ya existe cada combinación de este bloque (limit, offset ) en SIGA 
		   ================================ */
	    if (count($data)>0){
	    	
			metodos::revisaSiga($data,$url,"",$r);
	    }

	    /* =========================
	       RESPUESTA DATATABLES
	       ========================= */
	    echo json_encode([
	        'draw'            => $draw,
	        'recordsTotal'    => $total,
	        'recordsFiltered' => $filtered,
	        'data'            => $data,
	        'nRen'			  => $nRen,
	        'r'				  => $r
//	        'aSql'			  => $aSql
	    ]);

	} catch (Exception $e) {

	    echo json_encode([
	        'draw' => intval($_POST['draw'] ?? 0),
	        'recordsTotal' => 0,
	        'recordsFiltered' => 0,
	        'data' => [],
	        'error' => $e->getMessage()
	    ]);
	}
// ____________________________________________________
function filtroValidas($lValidas,$params,$conn_pdo){
	$sqlCount = "";
	if ($lValidas){
		$sqlCount = "SELECT COUNT(*) FROM epvalidas WHERE noenvio = :envio and estado like'Estructura%' ";
	}else{
		$sqlCount = "SELECT COUNT(*) FROM epinvalidas WHERE noenvio = :envio and estado like'Estructura%' ";
	}
    
    $stmt = $conn_pdo->prepare($sqlCount);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->execute();
    $filtered = $stmt->fetchColumn();
    return $filtered;
}
// ___________________________________________________
function queryValidas($lValidas,$params,$conn_pdo){
    $sql = "SELECT ine, clvcos, mayor, subcuenta, clvai, clvpp, clvspg, clvpy, " .
           "clvpar, estado, noenvio, procesado, numproceso, consecutivo, '' AS sele, fechahora, ";

    if ($lValidas){
        $sql .= " 'epvalidas' as tabla FROM epvalidas ";
    } else {
        $sql .= " 'epinvalidas' as tabla FROM epinvalidas ";
    }

    // Aquí usamos placeholders solo para valores válidos
    $sql .= "WHERE noenvio=:envio AND estado LIKE 'Estructura%' " .
            "ORDER BY fechahora DESC " .
            "LIMIT " . intval($params[':length']) . " OFFSET " . intval($params[':start']);

    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':envio', $params[':envio']);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_NUM);
}

?>