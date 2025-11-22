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
        <?php $version = filemtime('assetsF/css/seccion.css'); ?>
        <link rel="stylesheet" href="assetsF/css/seccion.css?v=<?=$version?>">
    </head>
    <body>
        <form name="frmCombiVal" id="frmCombiVal" method="post" enctype="multipart/form-data">
            <div id="main_container">    
                <?php include('P_Cuentas00_MenuPrincipal.php'); // Incluye el menú principal?>
                <section class="datos-personales2">
                    <h2 class="titleM">Combinaciones Válidas</h2>
                    <div class="container-data">
                        <div class="data-form">
                            <div class="wrapper">
                                <section class="seccion_caja_despliegue" id="despliegue">
                                    <div>
                                        <div class="caja_captura">
                                            <label for="UrIni" class="lbl_txt">Ur Ini</label>
                                            <select name="UrIni" id="UrIni" class="select-input" onchange="light_Title('UrIni');" data-campo="clvcos"></select>
                                        </div>
                                        <div class="caja_captura">
                                            <label for="UrFin" class="lbl_txt">Ur Fin</label>
                                            <select name="UrFin" id="UrFin" class="select-input" onchange="light_Title('UrFin');" data-campo="clvcos"></select>
                                        </div>
                                        <div class="caja_captura">
                                            <label for="AiIni" class="lbl_txt">Ai Ini</label>
                                            <select name="AiIni" id="AiIni" class="select-input" onchange="light_Title('AiIni');" data-campo="clvai"></select>
                                        </div>
                                        <div class="caja_captura">
                                            <label for="AiFin" class="lbl_txt">Ai Fin</label>
                                            <select name="AiFin" id="AiFin" class="select-input" onchange="light_Title('AiFin');" data-campo="clvai"></select>
                                        </div>

                                        <div class="caja_captura">
                                            <label for="SctaIni" class="lbl_txt">SCta Ini</label>
                                            <select name="SctaIni" id="SctaIni" class="select-input" onchange="light_Title('SctaIni');" data-campo="clvscta"></select>
                                        </div>
                                        <div class="caja_captura">
                                            <label for="SctaFin" class="lbl_txt">SCta Fin</label>
                                            <select name="SctaFin" id="SctaFin" class="select-input" onchange="light_Title('SctaFin');" data-campo="clvscta"></select>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="caja_captura">
                                            <label for="PpIni" class="lbl_txt">PP Ini</label>
                                            <select name="PpIni" id="PpIni" class="select-input" onchange="light_Title('PpIni');" data-campo="clvpp"></select>
                                        </div>
                                        <div class="caja_captura">
                                            <label for="PpFin" class="lbl_txt">PP Fin</label>
                                            <select name="PpFin" id="PpFin" class="select-input" onchange="light_Title('PpFin');" data-campo="clvpp"></select>
                                        </div>
                                        <div class="caja_captura">
                                            <label for="SpgIni" class="lbl_txt">Spg Ini</label>
                                            <select name="SpgIni" id="SpgIni" class="select-input" onchange="light_Title('SpgIni');" data-campo="clvspg"></select>
                                        </div>
                                        <div class="caja_captura">
                                            <label for="SpgFin" class="lbl_txt">Spg Fin</label>
                                            <select name="SpgFin" id="SpgFin" class="select-input" onchange="light_Title('SpgFin');" data-campo="clvspg"></select>
                                        </div>
                                        <div class="caja_captura">
                                            <label for="PyIni" class="lbl_txt">Py Ini</label>
                                            <select name="PyIni" id="PyIni" class="select-input" onchange="light_Title('PyIni');" data-campo="clvpy"></select>
                                        </div>
                                        <div class="caja_captura">
                                            <label for="PyFin" class="lbl_txt">Py Fin</label>
                                            <select name="PyFin" id="PyFin" class="select-input" onchange="light_Title('PyFin');" data-campo="clvpy"></select>
                                        </div>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="botones">
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="cargaCombinaciones(1);"> 
                                            <span>Consultar</span>
                                        </a>
                                        <a class="btn_1 efecto" onclick="cargarPtoAuto();"> 
                                            <span>Cargar Autorizado</span>
                                        </a>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="busqueda">
                                    <div class="caja_captura0">
                                        <label for="selOpe" class="lbl_txt">Operación</label>
                                        <select id="selOpe" name="selOpe" title="Operación" onchange="generaSalida();">
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
                                            <option data-max = "7" value="">-- Todos --</option>
                                            <option data-max = "4" value="a.clvcos">UR</option>
                                            <option data-max = "3" value="a.clvai">AI</option>
                                            <option data-max = "5" value="a.clvscta">Scta</option>
                                            <option data-max = "4" value="a.clvpp">PP</option>
                                            <option data-max = "3" value="a.clvspg">Spg</option>
                                            <option data-max = "7" value="a.clvpy">Py</option>
                                            <option data-max = "1" value="a.activo">Activo</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="txtBuscar" class="lbl_txt">Valor</label>
                                        <!-- Segun la IA colocando un diferente name asegura que no compartan valores previamente capturados en diferentes pantallas -->
                                        <input type="text" id="txtBuscar" name="txtBuscar_"  maxlength="7" 
                                        onkeyup="this.value = this.value.toUpperCase();" title="Valor a Buscar" onblur="validaValor(this);" data-exp='soloLetrasNumerosSinEspacios' data-valida="false">
                                    </div>
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="cargaCombinaciones(1);"> 
                                            <span>Buscar</span>
                                        </a>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="paginas">
                                    <div id="paginador"></div>
                                </section>
                                <div class="tabla-con-cuadricula">
                                    <table class="tablex" id="tblCombiVal">
                                        <thead>
                                            <tr>
                                                <th>UR</th>
                                                <th>AI</th>
                                                <th>Subcuenta</th>
                                                <th>PP</th>
                                                <th>SPG</th>
                                                <th>PY</th>
                                                <th>Activo</th>
                                                <th>Usuario</th>
                                                <th>fecha</th>
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
        <script src="jsP/P_cerrar_sesion_.js?v=<?php  echo filemtime('jsP/P_cerrar_sesion_.js'); ?>"></script>
        <script src="jsP/P_rutinas_.js?v=<?php  echo filemtime('jsP/P_rutinas_.js'); ?>"></script>
        <script src="jsP/P_combi_.js?v=<?php echo filemtime('jsP/P_combi_.js'); ?>"></script>
    </body>
</html>