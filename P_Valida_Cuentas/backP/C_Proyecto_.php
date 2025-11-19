<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
//	__________________________________________________________________________________________
class Proyecto{
private $clvPy;
private $desPy;
private $geografico;
private $activo;
private $conexion;
//	__________________________________________________________________________________________
public function __construct($conexion) {
	if ($conexion==null){
		// Solo construye el objeto
	}else{
		$this->conexion	= $conexion;
	}
}
//	__________________________________________________________________________________________
public function cargaDatos($aDatos){
	$this->clvPy		= ($aDatos["clvpy"]); 
	$this->desPy		= ($aDatos["despy"]); 
	$this->geografico	= ($aDatos["geografico"]); 
	$this->activo		= ($aDatos["activo"]); 
}
//	__________________________________________________________________________________________
public function actualizaPy($lAdiciona){
    try {

        // 1. Construcción del SQL
        if ($lAdiciona) {
            $sql = "INSERT INTO proyectos ( clvpy, despy, geografico, activo ) VALUES ( :clvpy, :despy, :geografico, :activo )";
        } else {
            $sql = "UPDATE proyectos SET despy = :despy, geografico = :geografico,  activo = :activo WHERE clvpy = :clvpy";
        }

        // 2. Preparar sentencia
        $stmt = $this->conexion->prepare($sql);

        // 3. Enlazar parámetros
        $stmt->bindParam(':clvpy',      $this->clvPy,      PDO::PARAM_STR);
        $stmt->bindParam(':despy',      $this->desPy,      PDO::PARAM_STR);
        $stmt->bindParam(':geografico', $this->geografico, PDO::PARAM_STR);
        $stmt->bindParam(':activo',     $this->activo,     PDO::PARAM_BOOL);

        // 4. Ejecutar
        $ok = $stmt->execute(); // si falla → cae en catch

        // 5. Retornar éxito
        return [
            "ok"        => true,
            "filas"     => $stmt->rowCount(),
            "error"     => null,
            "sql"       => $sql,
            "accion"    => $lAdiciona ? "INSERT" : "UPDATE"
        ];

    } catch (PDOException $e) {

        // 6. Cualquier error se captura aquí
        return [
            "ok"        => false,
            "filas"     => 0,
            "error"     => $e->getMessage(),
            "sql"       => isset($sql) ? $sql : null,
            "accion"    => $lAdiciona ? "INSERT" : "UPDATE"
        ];
    }
}
//	__________________________________________________________________________________________


}
?>