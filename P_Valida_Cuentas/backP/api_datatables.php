<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

	session_start();
	try {
	    require_once($_SESSION['conW']);
	    require_once("P_rutinas_.php");

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
	    $draw   = intval($_POST['draw'] ?? 0);
	    $start  = intval($_POST['start'] ?? 0);
	    $length = intval($_POST['length'] ?? 25);

	    /* =========================
	       FILTROS
	       ========================= */
	    $filtro = $_POST['filtro'] ?? [];
	    $where  = '1=1';
	    $params = [];

	    if (!empty($filtro['tipo'])) {
	        switch ($filtro['tipo']) {
	            case 'envio':
	                $where .= " AND noenvio = :envio";
	                $params[':envio'] = $filtro['numEnvio'];
	                break;

	            case 'ur':
	                $where .= " AND clvcos BETWEEN :urI AND :urF";
	                $params[':urI'] = $filtro['urI'];
	                $params[':urF'] = $filtro['urF'];
	                break;

	            case 'todas':
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

	    /* =========================
	       RESPUESTA DATATABLES
	       ========================= */
	    echo json_encode([
	        'draw'            => $draw,
	        'recordsTotal'    => $total,
	        'recordsFiltered' => $filtered,
	        'data'            => $data
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