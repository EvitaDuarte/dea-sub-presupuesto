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
        <?php $version = filemtime('assetsF/css/seccion.css'); ?>
        <link rel="stylesheet" href="assetsF/css/seccion.css?v=<?=$version?>">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    </head>
    <body>
        <form name="frmConsuEnvios" id="frmConsuEnvios" method="post" enctype="multipart/form-data">
            <div id="main_container">    
                <?php include('P_Cuentas00_MenuPrincipal.php'); // Incluye el menú principal?>
                <section class="datos-personales2">
                    <h2 class="titleM">Captura Estructuras a Validar y Enviar</h2>
                    <div class="container-data">
                        <div class="data-form">
                            <div class="wrapper">
                                <section class="seccion_caja_Izquierda" id="sec1">
                                    <div class="caja_captura">
                                        <label for="filtro" class="lbl_txt">Filtro</label>
                                        <select name="filtro" id="filtro"  onchange="light_Title('filtro');" onchange="filtroOpciones(this) data-field="filtro" title="Filtro">
                                            <option value="">Seleccione</option>
                                            <option value="T">Todos</option>
                                            <option value="N">Número Envio</option>
                                            <option value="U">Por Ur</option>
                                            <option value="P">Pendientes</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura_chk">
                                        <label for="filVal">Válidas</label>
                                        <input type="checkbox" id="filVal" name="filVal" value="V">
                                    </div>
                                    <div class="caja_captura0"></div><div class="caja_captura0"></div>
                                    <div class="caja_captura_chk">
                                        <label for="filRevisar" class="lbl_txt">a Revisar</label>
                                        <input type="checkbox" id="filRevisar" name="filRevisa" value="R">
                                    </div>
                                    <div class="caja_captura0"></div><div class="caja_captura0"></div><div class="caja_captura0"></div>
                                    <div class="caja_captura0" id=filEnvio>
                                        <label for="numEnvio" class="lbl_txt">Número Envío</label>
                                        <input type="text" name="numEnvio" id="numEnvio" maxlength="50" size="28" 
                                        onkeyup="this.value = this.value.toUpperCase();" title="Número Envío" data-field="noenvio" onblur="validaValor(this);" data-exp='soloLetrasNumeros' data-valida="false" >
                                    </div>
                                    <div class="caja_captura" id="filUrI">
                                        <label for="cveUrI" class="lbl_txt">Ur Inicial</label>
                                        <select name="cveUrI" id="cveUrI"  onchange="light_Title('cveUrI');" data-field="clvcos" title="Ur Inicial"></select>
                                    </div>
                                    <div class="caja_captura" id="filUrF">
                                        <label for="cveUrF" class="lbl_txt">Ur Final</label>
                                        <select name="cveUrF" id="cveUrF"  onchange="light_Title('cveUrF');" data-field="clvcos" title="Ur Final"></select>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="botones">
                                    <div class="caja_captura0"></div>
                                    <div class="caja_captura" id="divActualiza">
                                        <div class="form-field-button_" >
                                            <a class="btn_1 efecto" onclick="ConsultaEstructuras();"> 
                                                <span>Consultar</span>
                                            </a>
                                        </div>
                                    </div>
                                     <div class="caja_captura0"></div>
                                    <div class="caja_captura" id="divAdiciona">
                                        <div class="form-field-button_">
                                            <a class="btn_1 efecto" onclick="ActualizarEstado();"> 
                                                <span>Actualizar Estado</span>
                                            </a>
                                        </div>
                                    </div>
                                </section> 
<!--
                                <section class="seccion_caja" id="busqueda">
                                    <div class="caja_captura0">
                                        <label for="selOpe" class="lbl_txt">Operación</label>
                                        <select id="selOpe" name="selOpe" title="Operación" onchange="Salida_UrPpSpg();">
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
                                        </select>
                                    </div>
                                    <div class="caja_captura0">
                                        <label for="aCamSel" class="lbl_txt">Celda</label>
                                        <select id="aCamSel" name="aCamSel" onchange="cambiaMaxLength('txtBuscar',this);">
                                            <option data-max = "200"    value="">-- Todos --</option>
                                            <option data-max = "4"      value="a.id">Id</option>
                                            <option data-max = "40"     value="a.nombre">Nombre</option>
                                            <option data-max = "200"    value="a.valor">Valor</option>
                                            <option data-max = "40"     value="a.tipo">Tipo</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="txtBuscar" class="lbl_txt">Valor</label>
                                        <input type="text" id="txtBuscar" name="txtConfigura1"  maxlength="4" 
                                        title="Valor a Buscar" onblur="validaValor(this);" data-exp='soloLetrasNumerosDiagoSinEsp' data-valida="false">
                                    </div>
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="ConfiguraCarga(1);"> 
                                            <span>Buscar</span>
                                        </a>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="paginas">
                                    <div id="paginador"></div>
                                </section>
-->
                                <div class="tabla-con-cuadricula">
                                    <table class="tablex" id="tblEstruValidas">
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
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="tabla-con-cuadricula">
                                    <table class="tablex" id="tblEstruRevisar">
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
                                            </tr>
                                        </thead>
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
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
        <script src="jsP/P_ConsultaEstruc_.js?v=<?php echo filemtime('jsP/P_ConsultaEstruc_.js'); ?>"></script>

    </body>
</html>