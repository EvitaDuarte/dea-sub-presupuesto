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
        <form name="frmCatUsu" id="frmCatUsu" method="post" enctype="multipart/form-data">
            <div id="main_container">    
                <?php include('P_Cuentas00_MenuPrincipal.php'); // Incluye el menú principal?>
                <section class="datos-personales2">
                    <h2 class="titleM">Catálogo Usuarios</h2>
                    <div class="container-data">
                        <div class="data-form">
                            <div class="wrapper">
                                <section class="seccion_caja" id="secCaptura">
                                    <div class="caja_captura0">
                                        <label for="usuario_id" class="lbl_txt">Usuario</label>
                                        <input type="text" name="usuario_id" id="usuario_id" maxlength="60"  
                                        onkeyup="this.value = this.value.toLowerCase();" title="Clave de Usuario" onblur="validaValor(this);validaLdap(this);" data-exp='soloDominio' data-valida="false">
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="nombre_completo" class="lbl_txt">Nombre</label>
                                        <input type="text" name="nombre_completo" id="nombre_completo" maxlength="200"  disabled 
                                        onkeyup="this.value = this.value.toUpperCase();" title="Nombre Completo" onblur="validaValor(this);" data-exp='soloLetras' data-valida="false">
                                    </div>
                                    <div class="caja_captura0">
                                        <label for="correo" class="lbl_txt">Correo</label>
                                        <input type="email" name="correo" id="correo" maxlength="200"   disabled 
                                        onkeyup="this.value = this.value.toLowerCase();" title="Correo" onblur="validaValor(this);" data-exp='soloCorreoIne' data-valida="false">
                                    </div>
                                    <div class="caja_captura">
                                        <label for="unidad_id" class="lbl_txt">Unidad</label> 
                                        <select name="unidad_id" id="unidad_id"  data-field="unidad_id" title="Ur" ></select>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="secCaptura">
                                    <div class="caja_captura">
                                        <label for="unidad_inicio" class="lbl_txt">Ur Inicial</label>
                                        <select name="unidad_inicio" id="unidad_inicio" data-field="unidad_inicio" title="Ur Inicial"></select>
                                    </div>
                                    <div class="caja_captura">
                                        <label for="unidad_fin" class="lbl_txt">Ur Final</label>
                                        <select name="unidad_fin" id="unidad_fin"  data-field="unidad_fin" title="Ur Final"></select>
                                    </div>
                                    <div class="caja_captura0">
                                        <label for="listaurs" class="lbl_txt">Lista URs</label>
                                        <input type="text" name="listaurs" id="listaurs" maxlength="200"   
                                        onkeyup="this.value = this.value.toUpperCase();" title="Lista Urs" onblur="validaValor(this);" data-exp='soloLetrasNumerosComasEspa' data-valida="false">
                                    </div>
                                    <div class="caja_captura">
                                        <label for="estatus" class="lbl_txt">Estatus</label>
                                        <select name="estatus" id="estatus"  data-field="estatus" title="Estatus">
                                            <option value="">Seleccione</option>
                                            <option value="ACTIVO">ACTIVO</option>
                                            <option value="INACTIVO">INACTIVO</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura">
                                        <label for="esquema_id" class="lbl_txt">Esquema</label>
                                        <select name="esquema_id" id="esquema_id"  data-field="esquema_id" title="Esquema"></select>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="sec2">

                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="agregaUsuario()" id="btnAgregaUsuario"> 
                                            <span>Grabar usuario</span>
                                        </a>
                                    </div>
                                    <div class="caja_captura">
                                    </div>
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="inactivaUsuario()" id="btnEstatusUsuario"> 
                                            <span>Cambia Estatus</span>
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
                                        <select id="selOpe" name="selOpe" title="Operación" onchange="Salida_Usuarios();">
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
                                            <option data-max = "40" value="">-- Todos --</option>
                                            <option data-tipo="C"  data-max = "30" value="a.usuario_id">Usuario</option>
                                            <option data-tipo="C"  data-max = "50" value="a.nombre_completo">Nombre</option>
                                            <option data-tipo="C"  data-max = "30" value="a.correo">Correo</option>
                                            <option data-tipo="C"  data-max = "8" value="a.estatus">Estatus</option>
                                            <option data-tipo="C"  data-max = "20" value="b.esquema">Esquema</option>
                                            <option data-tipo="C4" data-max = "4" value="a.unidad_id">UR</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="txtBuscar" class="lbl_txt">Valor</label>
                                        <!-- Segun la IA colocando un diferente name asegura que no compartan valores previamente capturados en diferentes pantallas -->
                                        <input type="text" id="txtBuscar" name="txtBuscarUsu"  maxlength="40" 
                                        title="Valor a Buscar" onblur="validaValor(this);" data-exp='letrasNumerosSeparadores' data-valida="false">
                                    </div>
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="cargarUsuarios(1);"> 
                                            <span>Buscar</span>
                                        </a>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="paginas">
                                    <div id="paginador"></div>
                                </section>
                                <div class="tabla-con-cuadricula">
                                    <table class="tablex" id="tblCatUsu">
                                        <thead>
                                            <tr>
                                                <th>Usuario</th>
                                                <th>Nombre</th>
                                                <th>Correo</th>
                                                <th>Estatus</th>
                                                <th>Esquema</th>
                                                <th>Ur</th>
                                                <th>UrIni</th>
                                                <th>UrFin</th>
                                                <th>Urs</th>
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
        <script src="jsP/P_CatUsu_.js?v=<?php echo filemtime('jsP/P_CatUsu_.js'); ?>"></script>
    </body>
</html>