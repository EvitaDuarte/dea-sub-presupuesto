<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            require_once("P_Cuentas00_00VarSesion.php"); // Pone disponible las variables de sesión
        ?>
        <meta charset="UTF-8">
        <title><?=$v_TituloS?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="script-principal" content="Usuarios_.js">
        <!-------------General Style's--------------->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="assetsF/css/panel_style.css">

        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
        <!-- Si seccion modifica css de datatables o bootstrap debe ir al final para que no se sobreescriba -->
        <?php $version = filemtime('assetsF/css/seccion.css'); ?>
        <link rel="stylesheet" href="assetsF/css/seccion.css?v=<?=$version?>">

    </head>
    <body>
        <form name="frmReEnvio" id="frmReEnvio" method="post" enctype="multipart/form-data">
            <div id="main_container">    
                <?php include('P_Cuentas00_MenuPrincipal.php'); // Incluye el menú principal?>
                <section class="datos-personales2">
                    <h2 class="titleM">ReEnvio de Correo solicitando estructuras</h2>
                    <div class="container-data">
                        <div class="data-form">
                            <div class="wrapper">
                                <section class="seccion_caja_Izquierda" id="sec1">
                                    <div class="caja_captura0" id=filEnvio>
                                        <label for="numEnvio" class="lbl_txt">Número Envío</label>
                                        <input type="text" name="numEnvio" id="numEnvio" maxlength="50" size="28" 
                                        onkeyup="this.value = this.value.toUpperCase();" title="Número Envío" data-field="noenvio" onblur="validaValor(this);" data-exp='soloLetrasNumeros' data-valida="false" >
                                    </div>
                                    <div class="caja_captura0"></div>
                                    <div class="caja_captura" id="divConsulta">
                                        <div class="form-field-button_" >
                                            <a class="btn_1 efecto" onclick="ConsultaEstructuras();"> 
                                                <span>Consultar</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="caja_captura0"></div>
                                    <div class="caja_captura" id="divActualiza">
                                        <div class="form-field-button_">
                                            <a class="btn_1 efecto" onclick="ReenviarCorreo();"> 
                                                <span>Actualizar Estado</span>
                                            </a>
                                        </div>
                                    </div>
                                </section>
                                <!--<section class="seccion_caja" id="botones">-->
                                <section class="sectiontCuadricula " id='divReEnvio'>
                                    <table class="tablex" id="tblReenvio">
                                        <thead>
                                            <tr>
                                                <th>INE</th>
                                                <th>UR</th>
                                                <th>CUENTA</th>
                                                <th>SUBCUENTA</th>
                                                <th>AI</th>
                                                <th>PP</th>
                                                <th>SPG</th>
                                                <th>PY</th>
                                                <th>PTDA</th>
                                                <th>EDO</th>
                                                <th>SOL</th>
                                                <th>PRO</th>
                                                <th>NOPRO</th>
                                                <th>CONSE</th>
                                                <th>SEL</th>
                                                <th>FECHA</th>
                                                <th>tabla</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </section>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div id="loader-container" style="display:none;">
                <div id="loader">Espere un momento ....</div>
            </div>
<!--        ______________________________________________________________________              -->
            <dialog id="cajaMensaje" class="dialogo">
                <div class="dialogo_header">
                    <div id="dialogo_close" class="claseX">&#8999;</div>
                </div>
                <hr>
                <div class="dialogo_body">
                    <p id="dialogMessage">Mensajes al usuario en lugar del alert</p>
                </div>
            </dialog>
<!--        ______________________________________________________________________              -->
            <dialog id="cajaRespuesta" class="dialogo">
                <div class="dialogo_header">
                    <div id="dialogo_close1" class="claseX">&#8999;</div>
                </div>
                <hr>
                <div class="dialogo_body">
                    <p id="dialogRespuesta">Mensajes al usuario en lugar del alert</p>
                </div>
                <div class="dialogo_botones">
                    <button id="btnSi" class="detalle_button1">Sí</button>
                    <button id="btnNo" class="detalle_button1">No</button>
                </div>
            </dialog>
<!--        ______________________________________________________________________              -->
        </form>
<!--    El filemtime asegura que se vuelva a cargar el JS solo si este se modifico, evitando tener que borrar cookies -->
        <script src="jsP/P_backspace_.js?v=<?php echo filemtime('jsP/P_backspace_.js'); ?>"></script>
        <script src="jsP/P_cerrar_sesion_.js?v=<?php  echo filemtime('jsP/P_cerrar_sesion_.js'); ?>"></script>
        <script src="jsP/P_rutinas_.js?v=<?php  echo filemtime('jsP/P_rutinas_.js'); ?>"></script> 
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
        <script src="jsP/P_ReenviaEstruc_.js?v=<?php echo filemtime('jsP/P_ReenviaEstruc_.js'); ?>"></script>

    </body>
</html>