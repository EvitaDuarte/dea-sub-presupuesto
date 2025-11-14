<?php
    global $conn_pdo;
    $host       = '10.0.15.106';
    $port       = '5432';
    $dbname     = 'valida_cuentas';
    $user       = 'wwwdea';
    $password   = '3wdeamz56GA$#6';
    try{
        $conn_pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");
        $conn_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(Exception $e){
        die ("An error occurred while trying to access the database [ " . $e->getMessage() . "]" );
    } 


?>
