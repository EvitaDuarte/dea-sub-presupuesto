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
        <form name="frmProyectos" id="frmProyectos" method="post" enctype="multipart/form-data">
            <div id="main_container">    
                <?php include('P_Cuentas00_MenuPrincipal.php'); // Incluye el menú principal?>
                <section class="datos-personales2">
                    <h2 class="titleM">Catálogo de Proyectos</h2>
                    <div class="container-data">
                        <div class="data-form">
                            <div class="wrapper">
                                <!-- <section class="seccion_caja_despliegueflex" id="secCap"> -->
                                <section class="seccion_caja" id="botones">
                                    <div class="caja_captura0">
                                        <label for="clvpy" class="lbl_txt">Proyecto</label>
                                        <input type="text" name="clvpy" id="clvpy"   
                                        onkeyup="this.value = this.value.toUpperCase();" title="Clave de Proyecto" onblur="validaValor(this);" data-exp='soloLetrasNumeros' data-valida="false">
                                    </div>
                                    <div class="caja_captura">
                                        <label for="despy" class="lbl_txt">Nombre</label>
                                        <input type="text" name="despy" id="despy"   
                                        onkeyup="this.value = this.value.toUpperCase();" title="Nombre Proyecto" onblur="validaValor(this);" data-exp="soloLetras">
                                    </div>
                                    <div class="caja_captura0">
                                        <label for="geografico" class="lbl_txt">Geográfico</label>
                                        <select id="geografico" name="geografico" title="Geográfico">
                                            <option value=''>Seleccione</option>
                                            <option value='SI'>SI</option>
                                            <option value='NO'>NO</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura0">
                                        <label for="activo" class="lbl_txt">Estatus</label>
                                        <select id="activo" name="activo" title="Estatus">
                                            <option value=''>Seleccione</option>
                                            <option value=true>ACTIVO</option>
                                            <option value=false>INACTIVO</option>
                                        </select>
                                    </div>
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="actualizaProyecto();"> 
                                            <span>ActualizaProyecto</span>
                                        </a>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="busqueda">
                                    <div class="caja_captura0">
                                        <label for="selOpe" class="lbl_txt">Operación</label>
                                        <select id="selOpe" name="selOpe" title="Operación" onchange="Salida_Proyectos();">
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
                                            <option value="">-- Todos --</option>
                                            <option data-tipo="C" data-max = "7"  value="a.clvpy"       >Proyecto</option>
                                            <option data-tipo="C" data-max = "99" value="a.despy"       >Nombre</option>
                                            <option data-tipo="C" data-max = "2"  value="a.geografico"  >Geografico</option>
                                            <option data-tipo="B" data-max = "2"  value="a.activo"      >Activo</option>
                                        </select>
                                    </div>
                                    <div class="caja_captura3">
                                        <label for="txtBuscar" class="lbl_txt">Valor</label>
                                        <input type="text" id="txtBuscar" name="txtBuscarPy"  
                                        onkeyup="this.title=this.value;" title="Valor a Buscar">
                                    </div>
                                    <div class="form-field-button_" id="grpBotones">
                                        <a class="btn_1 efecto" onclick="cargaCatalogoProyectos(1);"> 
                                            <span>Buscar</span>
                                        </a>
                                    </div>
                                </section>
                                <section class="seccion_caja" id="paginas">
                                    <div id="paginador"></div>
                                </section>
                                <div class="tabla-con-cuadricula">
                                    <table class="tablex" id="tblProyectos">
                                        <thead>
                                            <tr>
                                                <th>Proyecto</th>
                                                <th>Geografico</th>
                                                <th>Activo</th>
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
        <script src="jsP/P_cerrar_sesion_.js?v=<?php  echo filemtime('jsP/P_cerrar_sesion_.js'); ?>"></script>
        <script src="jsP/P_rutinas_.js?v=<?php  echo filemtime('jsP/P_rutinas_.js'); ?>"></script>
        <script src="jsP/P_proyectos_.js?v=<?php echo filemtime('jsP/P_proyectos_.js'); ?>"></script>
    </body>
</html>