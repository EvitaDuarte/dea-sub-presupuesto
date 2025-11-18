<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 
require_once($_SESSION['conW']);
global $conn_pdo;
try {

    // ──────────────────────────────────────────
    // 1. Obtener la data enviada desde JS
    // ──────────────────────────────────────────
    $data = json_decode(file_get_contents("php://input"), true);

    $tabla      = $data["tablaPos"];
    $join       = $data["join"] ?? "";
    $orden      = $data["orden"][0] ?? "";
    $regPag     = intval($data["registros"]);
    $pagina     = intval($data["pagina"]);
    $offset     = ($pagina - 1) * $regPag;

    // Campos con nombre y tipo
    $campos = $data["campos"]; // arreglo de objetos { nombre, tipo }

    // Convertimos a lista separada por comas para SELECT
    $listaCampos = implode(",", array_map(fn($c) => $c["nombre"], $campos));

    // Parámetros de búsqueda
    $campoSel = $data["campoSel"] ?? "";
    $valSel   = $data["valSel"]   ?? "";

    // ──────────────────────────────────────────
    // 2. Determinar tipo del campo de búsqueda
    // ──────────────────────────────────────────
    $tipoCampoSel = null;

    if (!empty($campoSel)) {
        foreach ($campos as $c) {
            if ($c["nombre"] === $campoSel) {
                $tipoCampoSel = $c["tipo"];   // "C", "N", "D"
                break;
            }
        }

        if ($tipoCampoSel === null) {
            throw new Exception("El campo '$campoSel' no existe en 'campos'.");
        }
    }

    // ──────────────────────────────────────────
    // 3. Construcción del WHERE
    // ──────────────────────────────────────────
    $where = [];
    $params = [];

    if (!empty($join)) {
        $where[] = $join;
    }

    if (!empty($campoSel) && !empty($valSel)) {

        switch ($tipoCampoSel) {
            case "C":   // VARCHAR / CHAR → LIKE
                $where[] = "$campoSel LIKE :valLike";
                $params[":valLike"] = "%$valSel%";
                break;

            case "N":   // NUMÉRICO → =
                $where[] = "$campoSel = :valSel";
                $params[":valSel"] = $valSel;
                break;

            case "D":   // FECHA → =
                $where[] = "$campoSel = :valSel";
                $params[":valSel"] = $valSel;
                break;
        }
    }

    $sqlWhere = count($where) ? "WHERE " . implode(" AND ", $where) : "";

    // ──────────────────────────────────────────
    // 4. Conexión PDO PostgreSQL
    // ──────────────────────────────────────────
    // $conn_pdo = new PDO("pgsql:host=localhost;dbname=miBD","usuario","pwd");

    // ──────────────────────────────────────────
    // 5. CONTAR TOTAL
    // ──────────────────────────────────────────
    $sqlCount = "SELECT COUNT(*) FROM " . $tabla. " " . $sqlWhere;
/*    echo json_encode([
        "sqlCount" => $sqlCount,
        "tabla"     => $tabla,
        "sqlwhere"  => $sqlWhere
    ]);
    return;*/
    $stmt = $conn_pdo->prepare($sqlCount);

    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }

    $stmt->execute();
    $totalReg = $stmt->fetchColumn();
    $totalPag = ceil($totalReg / $regPag);

    // ──────────────────────────────────────────
    // 6. CONSULTA PRINCIPAL (SELECT)
    // ──────────────────────────────────────────
    $sql = "SELECT $listaCampos FROM $tabla $sqlWhere $orden LIMIT $regPag OFFSET $offset";

    $stmt = $conn_pdo->prepare($sql);

    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }

    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ──────────────────────────────────────────
    // 7. RESPUESTA JSON
    // ──────────────────────────────────────────
    header_remove('x-powered-by');
    header('Content-type: application/json; charset=utf-8');
    echo json_encode([
        "success"        => true,
        "mensaje"        => "",
        "totalRegistros" => $totalReg,
        "totalPaginas"   => $totalPag,
        "registros"      => $registros,
        "parametros"     => $data
    ]);
    return true;

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>