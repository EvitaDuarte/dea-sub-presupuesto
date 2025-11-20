<?php
    header_remove('x-powered-by');
    session_start();

    
    $vinactivo = 1500; // 25 minutos en segundos 900; // 120; // 900;

    if(isset($_SESSION['tiempo'])){
        $vida_session = time() - $_SESSION['tiempo'];
        if($vida_session > $vinactivo){
            header("Location: P_Cuentas00_home.php");exit;
        }else{
            $_SESSION['tiempo'] = time();
        }
    }else{
        header("Location: P_Cuenta00_home.php");exit;
    }

    

    if(!isset($_SESSION['ValCtasClave'])){
        header("Location: P_Cuentas00_Salir.php"); //exit;
    }else{
        // Se recuperan variables de sesion
        $usrClave     = $_SESSION['ValCtasClave'];
        $usrApellidos = $_SESSION['ValCtasApellidos'];
        $usrNombres   = $_SESSION['ValCtasNombres'];
        $usrCurp      = $_SESSION['ValCtasCurp'];
        $usrNombreC   = $_SESSION['ValCtasNC'];
        $usrPuesto    = $_SESSION["ValCtasPuesto"];
        $usrEsquema   = $_SESSION['ValCtasEsquema'];
        $v_TituloS    = $_SESSION['ValCtasTituloS'];
        $v_Error      = $_SESSION['ValCtasError'];
        $v_Alias      = $_SESSION['alias'];
    }
?>
