<?php
    session_start();
    session_unset();
    session_destroy();
    header("Location: P_Cuentas00_home.php");
?>