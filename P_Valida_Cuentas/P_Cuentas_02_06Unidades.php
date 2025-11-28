<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            require_once("P_Cuentas00_00VarSesion.php"); // Pone disponible las variables de sesión
        ?>
        <meta charset="uft-8" />
        <title><?=$v_TituloS?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-------------General Style's--------------->
        <link rel="stylesheet" href="assetsF/css/panel_style.css">
        <?php $version = filemtime('assetsF/css/seccion.css'); ?>
        <link rel="stylesheet" href="assetsF/css/seccion.css?v=<?=$version?>">
    </head>
    <body>
        <form name="frmCatUrs" id="frmCatUrs" method="post" enctype="multipart/form-data">
            <div id="main_container">    
                <?php include('P_Cuentas00_MenuPrincipal.php'); // Incluye el menú principal?>
                <section class="datos-personales2">
                    <h2 class="titleM">Catálogo Unidades</h2>
                    <div class="container-data">
                        <div class="data-form">
                            <div class="wrapper">
                                <section class="seccion_caja" id="secCaptura">
                                    <div class="caja_captura0">
                                        <label for="unidad_id" class="lbl_txt">Centro de Costo</label>
                                        <input type="text" name="unidad_id" id="unidad_id" maxlength="64"  
                                        title="Clave Centro de Costo" disabled data-exp='soloLetrasNumeros' data-valida="false">
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="unidad_desc" class="lbl_txt">Nombre</label>
                                        <input type="text" name="unidad_desc" id="unidad_desc" maxlength="200"  disabled 
                                        title="Nombre Centro de Costo" data-exp='soloLetrasNumeros' data-valida="false">
                                    </div>
                                    <div class="caja_captura0">
                                        <label for="unidad_digito" class="lbl_txt">Digito Py</label>
                                        <input type="text" name="unidad_digito" id="unidad_digito" maxlength="1"   disabled 
                                        onkeyup="this.value = this.value.toUpperCase();" title="Digito PY" onblur="validaValor(this);" data-exp='soloLetrasNumeros' data-valida="false">
                                    </div>
                                    <div class="caja_captura0">
                                        <label for="activo" class="lbl_txt">ACTIVO</label>
                                        <input type="text" name="activo" id="activo" maxlength="8"   disabled 
                                        onkeyup="this.value = this.value.toUpperCase();" title="Estatus" onblur="validaValor(this);" data-exp='soloLetras' data-valida="false">
                                    </div>
                                </section>
                                <section class="seccion_caja" id="sec2"> <!--
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="agregaUsuario()" id="btnAgregaUsuario"> 
                                            <span>Grabar usuario</span>
                                        </a>
                                    </div> -->
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="cambiaEstatusUR()" id="btnEstatusUsuario"> 
                                            <span>Cambia Estatus</span>
                                        </a>
                                    </div> 
                                </section>

                                <section class="seccion_caja" id="busqueda">
                                    <div class="caja_captura0">
                                        <label for="selOpe" class="lbl_txt">Operación</label>
                                        <select id="selOpe" name="selOpe" title="Operación" onchange="Salida_Unidades();">
                                            <option value="">Seleccione</option>
                                            <option value="Excel">Excel</option>
                                            <option value="Pdf">PDF</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura0">
                                        <label for="selNumReg" class="lbl_txt">No.Reg</label>
                                        <select id="selNumReg" name="selNumReg" title="No Reg">
                                            <option value=15>15</option>
                                            <option value=30>30</option>
                                            <option value=60>60</option>
                                            <option value=100>100</option>
                                            <option value=200>200</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura0">
                                        <label for="aCamSel" class="lbl_txt">Celda</label>
                                        <select id="aCamSel" name="aCamSel" onchange="cambiaMaxLength('txtBuscar',this);">
                                            <option data-tipo="C"  data-max = "50"  value="">-- Todos --</option>
                                            <option data-tipo="C"  data-max = "4"   value="a.unidad_id">UR</option>
                                            <option data-tipo="C"  data-max = "50"  value="a.unidad_desc">Nombre</option>
                                            <option data-tipo="C"  data-max = "30"  value="a.unidad_digito">Digito</option>
                                            <option data-tipo="*"  data-max = "1"   value="a.activo">Activo</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="txtBuscar" class="lbl_txt">Valor</label>
                                        <!-- Segun la IA colocando un diferente name asegura que no compartan valores previamente capturados en diferentes pantallas -->
                                        <input type="text" id="txtBuscar" name="txtBuscarUr"  maxlength="50" 
                                        title="Valor a Buscar" onblur="validaValor(this);" data-exp='soloLetrasNumeros' data-valida="false">
                                    </div>
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="cargarCentrosCosto(1);"> 
                                            <span>Buscar</span>
                                        </a>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="paginas">
                                    <div id="paginador"></div>
                                </section>
                                <div class="tabla-con-cuadricula">
                                    <table class="tablex" id="tblCatUrs">
                                        <thead>
                                            <tr>
                                                <th>Centro Costo</th>
                                                <th>Nombre</th>
                                                <th>Digito</th>
                                                <th>Activo?</th>
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
        <script src="jsP/P_CatUrs_.js?v=<?php echo filemtime('jsP/P_CatUrs_.js'); ?>"></script>
    </body>
</html>