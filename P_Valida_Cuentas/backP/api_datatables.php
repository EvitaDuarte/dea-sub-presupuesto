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
	       VALIDAR TABLA
	       ========================= */
	    $mapaTablas = [
	        'epvalidas'   => 'epvalidas',
	        'epinvalidas' => 'epinvalidas'
	    ];

	    $tablaReq = $_POST['tabla'] ?? '';
	    if (!isset($mapaTablas[$tablaReq])) {
	        throw new Exception('Tabla no válida');
	    }

	    $tabla = $mapaTablas[$tablaReq];

	    /* =========================
	       PARAMETROS DATATABLES
	       ========================= */
	    $draw   = intval($_POST['draw'] ?? 0); // Estas variables se ven en Network/Payload del navegador
	    $start  = intval($_POST['start'] ?? 0);
	    $length = intval($_POST['length'] ?? 25);
	    $url	= $_POST['url'];
	    // Variables de depuracion
    	$nRen	= 0;
//    	$aSql	= [];

	    /* =========================
	       FILTROS
	       ========================= */
	    $filtro		= $_POST['filtro'] ?? [];
	    $where		= '1=1';
	    $params		= [];
	    $cFilTipo	= $filtro['tipo'];

	    if (!empty($cFilTipo)) {
	        switch ($cFilTipo) {
	            case 'envio':
	                $where .= " AND noenvio = :envio";
	                $params[':envio'] = $filtro['numEnvio'];
	                break;

	            case 'ur':
	                $where .= " AND clvcos BETWEEN :urI AND :urF";
	                $params[':urI'] = $filtro['urI'];
	                $params[':urF'] = $filtro['urF'];
	                break;

	            case 'pendientes':
	                $where .= " AND clvcos BETWEEN :urI AND :urF ";
	                $where .= " and estado like 'Estructura%' ";
	                $params[':urI'] = $filtro['urI'];
	                $params[':urF'] = $filtro['urF'];
	            break;

	            case 'todas':
	            break;

	            default:
	                break;
	        }
	    }

	    /* =========================
	       TOTAL SIN FILTROS
	       ========================= */
	    $total = $conn_pdo
	        ->query("SELECT COUNT(*) FROM $tabla")
	        ->fetchColumn();

	    /* =========================
	       TOTAL CON FILTROS
	       ========================= */
	    $sqlCount = "SELECT COUNT(*) FROM $tabla WHERE $where";
	    $stmt = $conn_pdo->prepare($sqlCount);
	    foreach ($params as $k => $v) {
	        $stmt->bindValue($k, $v);
	    }
	    $stmt->execute();
	    $filtered = $stmt->fetchColumn();

	    /* =========================
	       DATOS PAGINADOS
	       ========================= */
	    $sql = "
	        SELECT ine, clvcos, mayor, subcuenta, clvai, clvpp, clvspg, clvpy,
	               clvpar, estado, noenvio, procesado, numproceso, consecutivo,
	               'S' AS sele, fechahora
	        FROM $tabla
	        WHERE $where
	        ORDER BY fechahora DESC
	        LIMIT :length OFFSET :start
	    ";

	    $stmt = $conn_pdo->prepare($sql);
	    foreach ($params as $k => $v) {
	        $stmt->bindValue($k, $v);
	    }
	    $stmt->bindValue(':length', $length, PDO::PARAM_INT);
	    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
	    $stmt->execute();

	    $data = $stmt->fetchAll(PDO::FETCH_NUM);
	    /* ================================
	       Revisar si ya existe cada combinación de este bloque (limit, offset ) en SIGA 
		   ================================ */
	    if (count($data)>0){
	    	$oEstruc = new Estructura();
	    	$soap	 = metodos::nuevaSopita($url);
	    	foreach ($data as &$estru) {
	    		list($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda,$cEdo) = $estru;
				if ($cEdo==VALIDA || $cEdo==XREVISAR){

					$params = Array("segment10" => $cIne	, "segment1" => $cUr, "segment2" => $cCta,
					    			 "segment3"	=> $cSubCta	, "segment5" => $ai , "segment6" => $pp,
					    			 "segment7"	=> $spg		, "segment8" => $py , "segment9" => $ptda );
					$ctaSiga = json_decode(json_encode($soap->consultaCuentas($params)),true); 
					$lNoEsta = true;

					if ( isset($ctaSiga["cuentas"]) ){
						$cCta1	 = $cIne . "-" . $cUr . "-" . $cCta . "-" . $cSubCta . "-" . $ai . "-" . $pp . "-" . $spg . "-" . $py . "-" . $ptda;
						if ($cCta1===$ctaSiga["cuentas"][0]["concatenatedSegment"]){
							$lNoEsta		= false;
							$cEdo			= YAEXISTE;
							$estru[IDXEDO]	= $cEdo;
							 

							//$nRen += $oEstruc->modificaEstado($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda,$cEdo,$tabla,$aSql);
							$nRen += $oEstruc->modificaEstado($cIne, $cUr, $cCta, $cSubCta, $ai, $pp, $spg, $py, $ptda,$cEdo,$tabla);
						}
					}
				}
	    	}
	    }

	    /* =========================
	       RESPUESTA DATATABLES
	       ========================= */
	    echo json_encode([
	        'draw'            => $draw,
	        'recordsTotal'    => $total,
	        'recordsFiltered' => $filtered,
	        'data'            => $data,
	        'nRen'			  => $nRen
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
?>