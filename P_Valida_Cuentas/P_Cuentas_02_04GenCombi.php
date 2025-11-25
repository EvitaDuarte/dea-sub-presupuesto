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
        <form name="frmGenCombi" id="frmGenCombi" method="post" enctype="multipart/form-data">
            <div id="main_container">    
                <?php include('P_Cuentas00_MenuPrincipal.php'); // Incluye el menú principal?>
                <section class="datos-personales2">
                    <h2 class="titleM">Pre Combinaciones</h2>
                    <div class="container-data">
                        <div class="data-form">
                            <div class="wrapper">
                                <section class="seccion_caja" id="sec1">
                                    <div class="caja_captura">
                                        <label for="UrIni" class="lbl_txt">Ur Ini</label>
                                        <select name="UrIni" id="UrIni"  onchange="light_Title('UrIni');" data-field="unidad_id" title="Ur inicial"></select>
                                    </div>
                                    <div class="caja_captura">
                                        <label for="UrFin" class="lbl_txt">Ur Fin</label>
                                        <select name="UrFin" id="UrFin"  onchange="light_Title('UrFin');" data-field="unidad_id" title="Ur Final"></select>
                                    </div>
                                    <div class="caja_captura">
                                    </div>
                                    <div class="caja_captura0">
                                    </div>
                                    <div class="form-field-button_" id="btnGenCom">
                                        <a class="btn_1 efecto" onclick="generaUrCombi()" id="btnGenCombi"> 
                                            <span>Genera Combinaciones</span>
                                        </a>
                                    </div>

                                </section>
                                <section class="seccion_caja" id="sec2">
                                    <div class="caja_captura">
                                        <label for="tipoUr" class="lbl_txt">Tipo Ur</label>
                                        <select name="tipoUr" id="tipoUr"  onchange="light_Title('tipoUr');" data-campo="tipour" title="Tipo Ur"></select>
                                    </div>
                                    <div class="caja_captura">
                                        <label for="cveAi" class="lbl_txt">AI</label>
                                        <select name="cveAi" id="cveAi"  onchange="light_Title('cveAi');" data-campo="clvai" title="Actividad Institucional"></select>
                                    </div>
                                    <div class="caja_captura">
                                        <label for="cveScta" class="lbl_txt">SubCta</label>
                                        <select name="cveScta" id="cveScta" onchange="light_Title('cveScta');" data-campo="clvscta" title="Subcuenta"></select>
                                    </div>
                                    <div class="caja_captura">
                                        <label for="cvePp" class="lbl_txt">PP</label>
                                        <select name="cvePp" id="cvePp"  onchange="light_Title('cvePp');" data-campo="clvpp" title="Programa presupuestario"></select>
                                    </div>
                                    <div class="caja_captura">
                                        <label for="cveSpg" class="lbl_txt">Subprograma</label>
                                        <select name="cveSpg" id="cveSpg"  onchange="light_Title('cveSpg');" data-campo="clvspg" title="Subprograma"></select>
                                    </div>
                                    <div class="caja_captura">
                                        <label for="cvePy" class="lbl_txt">Proyecto</label>
                                        <select name="cvePy" id="cvePy"  onchange="light_Title('cvePy');" data-campo="clvpy" title="Proyecto"></select>
                                    </div>
                                    <div class="caja_captura">
                                        <label for="geografico" class="lbl_txt">Geográfico</label>
                                        <select id="geografico" name="geografico"  title="Geográfico">
                                            <option value=''>Seleccione</option>
                                            <option value='SI'>SI</option>
                                            <option value='NO'>NO</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura">
                                    </div>
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="agregarCombi()" id="btnAgregaCombi"> 
                                            <span>Agregar Combinación...</span>
                                        </a>
                                    </div>
                                </section>
                                <!--
                                <section class="seccion_caja" id="botones">
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="cargaCombinaciones(1);"> 
                                            <span>Consultar</span>
                                        </a>
                                        <a class="btn_1 efecto" onclick="cargarPtoAuto();"> 
                                            <span>Cargar Autorizado</span>
                                        </a>
                                    </div>
                                </section> -->
                                <section class="seccion_caja" id="busqueda">
                                    <div class="caja_captura0">
                                        <label for="selOpe" class="lbl_txt">Operación</label>
                                        <select id="selOpe" name="selOpe" title="Operación" onchange="Salida_preCombi();">
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
                                            <option data-max = "5" value="a.tipour">Tipo</option>
                                            <option data-max = "3" value="a.clvai">AI</option>
                                            <option data-max = "5" value="a.clvscta">Scta</option>
                                            <option data-max = "4" value="a.clvpp">PP</option>
                                            <option data-max = "3" value="a.clvspg">Spg</option>
                                            <option data-max = "7" value="a.clvpy">Py</option>
                                            <option data-max = "2" value="a.activo">Geográfico</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="txtBuscar" class="lbl_txt">Valor</label>
                                        <!-- Segun la IA colocando un diferente name asegura que no compartan valores previamente capturados en diferentes pantallas -->
                                        <input type="text" id="txtBuscar" name="txtBuscar_"  maxlength="7" 
                                        onkeyup="this.value = this.value.toUpperCase();" title="Valor a Buscar" onblur="validaValor(this);" data-exp='soloLetrasNumerosDiagoSinEsp' data-valida="false">
                                    </div>
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="cargaPreCombi(1);"> 
                                            <span>Buscar</span>
                                        </a>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="paginas">
                                    <div id="paginador"></div>
                                </section>
                                <div class="tabla-con-cuadricula">
                                    <table class="tablex" id="tblGenCombi">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>AI</th>
                                                <th>Subcuenta</th>
                                                <th>PP</th>
                                                <th>SPG</th>
                                                <th>PY</th>
                                                <th>Geo</th>
                                                <th>Procesar</th>
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
        <script src="jsP/P_GenCombi_.js?v=<?php echo filemtime('jsP/P_GenCombi_.js'); ?>"></script>
    </body>
</html>