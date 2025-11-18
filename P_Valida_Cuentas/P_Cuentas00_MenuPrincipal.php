		<link rel="stylesheet" href="assetsF/cssF/menupt.css">
		<?php
			$v_Color= 'style="color: #ff41ae;"';
		?>

		<header>
			<img class="logoMenu" src="assetsF/img/logo_ine_completo_svg1200.svg" alt="INE Logo">
			<nav>
				<ul>
            		<li><a id="b_inicio"  onClick="Enviar('P_Cuentas00_00.php');">Inicio</a></li>
            		<li id="cata" data-esquema="ADM,CON,PTO,JLE"> 
                    	<a>Catálogos<b>▼</b></a>
                		<ul>
                			<li onClick="Enviar('P_Cuentas_02_02Ptda.php');"><a>Partidas</a></li>

                            <li id="li-drop" onClick="Enviar('P_Cuentas_02_01Py.php');" 		data-esquema="ADM,CON,PTO"><a>Proyectos</a></li>
                            <li id="li-drop" onClick="Enviar('P_Cuentas_02_03CombiM.php');" 	data-esquema="ADM,CON,PTO"><a>Combinaciones Válidas</a></li> 
                            <li id="li-drop" onClick="Enviar('P_Cuentas_02_04GenCombi.php');"	data-esquema="ADM,CON,PTO"><a>Generar Combinaciones</a></li> 

                        	<li id="li-drop" onClick="Enviar('Cuentas_02_05Usuarios.php');"		data-esquema="ADM"><a>Usuarios</a></li>
                        	<li id="li-drop" onClick="Enviar('Cuentas_02_06Unidades.php');"		data-esquema="ADM"><a>Unidades</a></li>
                        	<li id="li-drop" onClick="Enviar('Cuentas_02_07UrPpSpg.php') ;"		data-esquema="ADM"><a>Ur-PP-Spg</a></li>  
                        	<li id="li-drop" onClick="Enviar('Cuentas_02_08Configura.php') ;"	data-esquema="ADM"><a>Correos</a></li>
                    	</ul>
                	</li>





            		<li id="carga">
                    	<a>Validación<b>▼</b></a>
                        <ul>
                            <li onClick="Enviar('P_Cuentas_04_01CtasCarga.php');">		<a>Carga Estructuras</a></li>
                            <li onClick="Enviar('P_Cuentas_04_02CtasCaptura.php');">	<a>Captura Estructuras</a></li>
                            <li onClick="Enviar('P_Cuentas_04_03CtasConsulta.php');">	<a>Consulta Envíos</a></li>
                            <li onClick="Enviar('P_Cuentas_04_04CtasReEnvio.php');">	<a>Reenvío Correo</a></li>
                        </ul>
                	</li>



            		<li id="captura" data-esquema="ADM,CON,PTO,MABG">
                    	<a>LayOut<b>▼</b></a>
                        <ul>
                        	<hr>
                            <li onClick="Enviar1('Cuentas_05_01Validas.php','validas');"		data-esquema="ADM,CON,PTO">	<a>Estructuras Válidas</a></li>
                            <li onClick="Enviar1('Cuentas_05_01Validas.php','arevisar');"		data-esquema="ADM,CON,PTO">	<a>Estructuras a Revisar</a></li>
                            <li onClick="Enviar1('Cuentas_05_03InsertaSiga.php','validas');"	data-esquema="MABG">		<a>Válidas a SIGA</a></li>
                            <li onClick="Enviar1('Cuentas_05_03InsertaSiga.php','arevisar');"	data-esquema="MABG">		<a>A revisar a SIGA</a></li>
                        </ul>
                	</li>

                    <li>
                        <li><a data-after="Guía de usuario" href="MPTIC/Guia SISVAL.pdf" target="_blank">Guía de Usuario</a></li>
                    </li>



 					<li>
                        <a id="b_user" data-after="Mi cuenta" style="color: #ff41ae; text-transform: uppercase;">
                            <?= substr($usrNombreC,0,20) ?>
                            <ion-icon name="arrow-dropdown" size="small" 
                            class="arrow-dropdown" id="AD-Clases"></ion-icon>
                            <img src="assetsF/img/usuario.png" 
                            style="position: absolute; top: 8px; right: -30px; "//>
                        </a>
                        <ul class="sub-menu-dr">
                            <li id="li-drop"><a class="b_csesion" onclick="#"><?= $usrEsquema ?></a></li>
                            <li id="li-drop"><a class="b_csesion" onClick="Enviar('P_Cuentas00_Salir.php');">
                            Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
			</nav>
			<div class="menuToggle"></div>
		</header>
<!-- Menu -->

		<div id="data_content"></div>

<!-- Menu -->
		<div class="side-bar">
    		<div class="menu-list" id="close_side" style="-webkit-tap-highlight-color: transparent;">
        		<ion-icon name="close-circle-outline"></ion-icon>
    		</div>
    		<img class="go-home" id="go-home" src="assetsF/img/person-at-home.png"/>
		</div>
		<!-- Menu -->

		<!-- Marca de agua SC -->
		<section class="logo_SC" id="logo_SC">
		    <a id="b_sc_logo">
		        <h1>
		            <img alt="Sistemas complementarios Logo" class="logo_sc_bn">
		        </h1>
		    </a>
		</section>
		<!-- Marca de agua CTIA -->
		<section class="logo_CTIA" id="logo_CTIA">
		    <a id="b_ctia_logo">
		        <h1>
		            <img alt="CTIA Logo" class="logo_ctia_bn">
		        </h1>
		    </a>
		</section>
		<input type="hidden" id="nomEsquema" value="<?= $usrEsquema ?>">

<!--    El filemtime asegura que se vuelva a cargar el JS solo si este se modifico, evitando tener que borrar cookies -->
        <script src="jsP/P_backspace_.js?v=<?php echo filemtime('jsP/P_backspace_.js'); ?>"></script>		
		<script src="jsP/P_menu_principal_.js?v=<?php echo filemtime('jsP/P_menu_principal_.js'); ?>"></script>
		
<!-- 	<script type="module" src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons/ionicons.esm.js">
		</script>-->
<!--	<script nomodule="" src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons/ionicons.js">
		</script>--> 
		
		<script type="text/javascript">
			function actualizarMenus() {
				/*
			    vEsquema = document.getElementById("nomEsquema").value.toUpperCase().substring(0, 3);
			    con sole.log('Esquema actual:', vEsquema);

			    document.querySelectorAll('li').forEach(function(li, index) {
			        var esquemas = li.getAttribute('data-esquema');
			        var cId      = li.getAttribute('id');
			        con sole.log(`Elemento ${index}: data-esquema = ${esquemas}`,`Id=${cId}`);

			        if (esquemas != null) {
			            if (esquemas.includes(vEsquema)) {
			                con sole.log(`Elemento ${index} visible`,`Id=${cId}`);
			                li.style.display = 'block';
			            } else {
			                con sole.log(`Elemento ${index} oculto`,`Id=${cId}*****`);
			                li.style.display = 'none';
			            }
			        } else {
			            //con sole.log(`Elemento ${index} sin data-esquema, visible`);
			            li.style.display = 'block';
			        }
			    });
			    */
				// Oculta o Despliega los submenus de cada Opción principal, de acuerdo al ROL
				vEsquema = document.getElementById("nomEsquema").value.toUpperCase().substring(0,3);
				// con sole.log(`Esquema=${vEsquema}`);
				if (vEsquema!="ADM1"){ // El administrador puede ver todo
				    document.querySelectorAll('li').forEach(function(li) {
				        var esquemas = li.getAttribute('data-esquema');
				        if (esquemas!=null){// Algunos li no tienen data-esquema
					        //console.log(`Esquema=${esquemas}`);
					        if (esquemas.includes(vEsquema)) {
					            li.style.display = 'block';
					        } else {
					            li.style.display = 'none';
					        }
					    }
				    });
				}

			}

			document.addEventListener('DOMContentLoaded', function() {
			    actualizarMenus();
			});
				
		</script>