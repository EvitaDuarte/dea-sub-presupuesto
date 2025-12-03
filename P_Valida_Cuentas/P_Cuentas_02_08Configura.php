<!DOCTYPE html>
<html lang="es">
    <head>
        <?php
            require_once("P_Cuentas00_00VarSesion.php"); // Pone disponible las variables de sesión
        ?>
        <meta charset="UTF-8" />
        <title><?=$v_TituloS?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="script-principal" content="Usuarios_.js">
        <!-------------General Style's--------------->
        <link rel="stylesheet" href="assetsF/css/panel_style.css">
        <?php $version = filemtime('assetsF/css/seccion.css'); ?>
        <link rel="stylesheet" href="assetsF/css/seccion.css?v=<?=$version?>">
    </head>
    <body>
        <form name="frmConfigura" id="frmConfigura" method="post" enctype="multipart/form-data">
            <div id="main_container">    
                <?php include('P_Cuentas00_MenuPrincipal.php'); // Incluye el menú principal?>
                <section class="datos-personales2">
                    <h2 class="titleM">Configuración</h2>
                    <div class="container-data">
                        <div class="data-form">
                            <div class="wrapper">
                                <section class="seccion_caja" id="sec1">
                                    <div class="caja_captura0">
                                        <label for="id" class="lbl_txt">Id</label>
                                        <!-- Segun la IA colocando un diferente name asegura que no compartan valores previamente capturados en diferentes pantallas -->
                                        <input type="text" id="id" name="idConfigura"  maxlength="3" disabled 
                                        title="Consecutivo" onblur="validaValor(this);" data-exp='soloNumeros' data-valida="false">
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="nombre" class="lbl_txt">Nombre</label> 
                                        <input type="text" id="nombre" name="nombreConfigura"  maxlength="200"  disabled 
                                        title="Nombre" onblur="validaValor(this);" data-exp='soloLetras' data-valida="false">
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="valor" class="lbl_txt">Valor</label>
                                        <!-- Segun la IA colocando un diferente name asegura que no compartan valores previamente capturados en diferentes pantallas -->
                                        <input type="text" id="valor" name="valorConfigura"  maxlength="200" 
                                        title="Valor de Nombre" onblur="validaValor(this);" data-exp='soloLetrasNumerosDiagoSinEsp' data-valida="false">
                                    </div>
                                    <div class="caja_captura">
                                        <label for="tipo" class="lbl_txt">Tipo</label>
                                        <select id="tipo" name="tipo" data-field="tipo" title="Tipos Válidos"></select>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="botones">
                                    <div class="caja_captura0"></div>
                                    <div class="caja_captura" id="divActualiza">
                                        <div class="form-field-button_" >
                                            <a class="btn_1 efecto" onclick="ConfiguraActualiza();"> 
                                                <span>Actualizar</span>
                                            </a>
                                        </div>
                                    </div>
                                     <div class="caja_captura0"></div>
                                    <div class="caja_captura" id="divAdiciona" style="display: none;">
                                        <div class="form-field-button_">
                                            <a class="btn_1 efecto" onclick="ConfiguraAdicionar();"> 
                                                <span>Adicionar</span>
                                            </a>
                                        </div>
                                    </div>
                                   <div class="caja_captura0"></div>
                                    <div class="caja_captura" id="divNuevo">
                                        <div class="form-field-button_" >
                                            <a class="btn_1 efecto" onclick="ConfiguraNuevo();"> 
                                                <span>Nuevo</span>
                                            </a>
                                        </div>
                                    </div>
                                </section> 
                                <section class="seccion_caja" id="busqueda">
                            <!--        <div class="caja_captura0">
                                        <label for="selOpe" class="lbl_txt">Operación</label>
                                        <select id="selOpe" name="selOpe" title="Operación" onchange="Salida_UrPpSpg();">
                                            <option value="">Seleccione</option>
                                            <option value="Excel">Excel</option>
                                            <option value="Pdf">PDF</option>
                                        </select>
                                    </div> -->
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
                                        <!-- Segun la IA colocando un diferente name asegura que no compartan valores previamente capturados en diferentes pantallas -->
                                        <input type="text" id="txtBuscar" name="txtConfigura"  maxlength="4" 
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
                                <div class="tabla-con-cuadricula">
                                    <table class="tablex" id="tblConfigura">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Nombre</th>
                                                <th>Valor</th>
                                                <th>Tipo</th>
                                                <th>Fecha</th>
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
        <script src="jsP/P_Configura_.js?v=<?php echo filemtime('jsP/P_Configura_.js'); ?>"></script>
    </body>
</html>