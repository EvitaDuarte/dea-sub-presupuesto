<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            require_once("P_Cuentas00_00VarSesion.php"); // Pone disponible las variables de sesión
        ?>
        <meta charset="uft-8" />
        <title><?=$v_TituloS?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="script-principal" content="Usuarios_.js">
        <!-------------General Style's--------------->
        <link rel="stylesheet" href="assetsF/css/panel_style.css">
        <link rel="stylesheet" href="assetsF/css/seccion.css">
    </head>
    <body>
        <form name="frmUsuario" id="frmUsuario" method="post" enctype="multipart/form-data">
            <div id="main_container">    
                <?php include('OpeFin00_MenuPrincipal.php'); // Incluye el menú principal?>
                <section class="datos-personales2">
                    <h2 class="titleM">Catálogo de Partidas</h2>
                    <div class="container-data">
                        <div class="data-form">
                            <div class="wrapper">
                                <section class="seccion_caja_despliegue" id="secCap">
                                    <div class="caja_captura">
                                        <label for="partida" class="lbl_txt">Partida</label>
                                        <input type="text" name="partida" id="partida"  disabled 
                                        onkeyup="this.title=this.value;">
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="compraMenor" class="lbl_txt">Compra Menor</label>
                                        <input type="text" name="compraMenor" id="compraMenor" disabled  
                                        onkeyup="this.title=this.value;">
                                    </div>
                                    <div class="caja_captura">
                                        <label for="ordenCompra" class="lbl_txt">Orden Compra</label>
                                        <input type="text" name="ordenCompra" id="ordenCompra" disabled 
                                        onkeyup="this.title=this.value;">
                                    </div>
                                    <div class="caja_captura">
                                        <label for="contactamay" class="lbl_txt">Contabilidad</label>
                                        <input type="text" id="contactamay" name="contactamay" disabled
                                        onkeyup="this.title=this.value;">
                                    </div>
                                    <div class="caja_captura">
                                        <label for="nombre" class="lbl_txt">Nombre</label>
                                        <input type="checkbox" id="nombre" name="nombre"  disabled
                                        onkeyup="this.title=this.value;">>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="botones">
                                </section>
                                <hr>
                                <div class="tabla-con-cuadricula">
                                    <table class="tablex" id="partidas">
                                        <caption class="captionTable">Catálogo de Partidas</caption>
                                        <thead>
                                            <tr>
                                                <th>Partida</th>
                                                <th>CompraMenor</th>
                                                <th>OrdenCompra</th>
                                                <th>Contabilidad</th>
                                                <th>Nombre</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cuerpo">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
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
        <script src="jsP/P_cerrarSesion_.js?v=<?php  echo filemtime('jsP/P_cerrarSesion_.js'); ?>"></script>
        <script src="jsP/P_rutinas_.js?v=<?php  echo filemtime('jsP/P_rutinas_.js'); ?>"></script>
        <script src="jsP/P_partidas_.js?v=<?php echo filemtime('jsP/P_partidas_.js'); ?>"></script>
    </body>
</html>