/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Autor   		: Miguel Ángel Bolaños Guillén
 * Sistema 		: Sistema de Validación de cuentas
 * Fecha   		: Noviembre 2025
 * Descripción 	: Captura manual estructuras 
 *                Paso a PHP 8.03
 *                	
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*/
var cPhp      	= "P_CargaEstru_.php";			// En este php estarán las funciones que se invocaran desde este JS
var cPhpBusca	= "P_Busca_Pagina_.php";
var gTabla	  	= "tblEstruValidas";				// Tabla HTML que se esta visualizando
var gForma	  	= "frmConsuEnvios";
var gPagina		= 1; 
var gConfigura  = null;							// select id,nombre,valor,tipo from configuracion
var gUrIni		= "";							// Urs permitidas 
var gUrFin		= "";
var gUrLis		= "";
var gUrUsu		= "";
var gUrlCtas	= "";							// url para validar via soap una estructura
var gUrlPys		= "";							// url para validar via sopa un proyecto
var gValidaPY	= "";							// 'S' si se requiere validar el proyecto( o si es geográfico)??
var gEstructura = "";
var gEstructuras= null;							// Para guardar las estructuras que se enviarán al SIGA
var gaCorreo	= null;							// Guardara Correo de envio a presupuesto, a contabilidad, usuario del correo genérico, contraseña del correo genérico
var tablaValidas= "";
var tablaRevisar= "";
var filtrosActuales = {};
// ________________________________________________________________________
window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a este JS
	traeCatUrs();

}
// ________________________________________________________________________


/* ==========================
   CARGA DEL DOM (DataTables)
   ========================== */
$(document).ready(function () {

    tablaValidas = $('#tblEstruValidas').DataTable({
        processing: true,
        serverSide: true,
        deferLoading: 0,
        ajax: {
            url: 'backP/api_datatables.php',
            type: 'POST',
            data: function (d) {
                d.tabla = 'epvalidas';
                d.filtro = filtrosActuales;
            },
		    dataSrc: function (json) {

		        // 👇 AQUÍ CAE EL ERROR DEL CATCH DE PHP
		        if (json.error) {

		            // Mostrar en consola
		            console.error('Error PHP:', json.error);

		            // Mostrar al usuario
		            mandaMensaje("Error en el servidor");

		            // Evitar que DataTables intente renderizar
		            return [];
		        }

		        return json.data;
		    }
        },
        pageLength: 25,
        dom: '<"top"lpf>t<"bottom"i>'
    });

    tablaRevisar = $('#tblEstruRevisar').DataTable({
        processing: true,
        serverSide: true,
        deferLoading: 0,
        ajax: {
            url: 'backP/api_datatables.php',
            type: 'POST',
            data: function (d) {
                d.tabla = 'epinvalidas';
                d.filtro = filtrosActuales;
            },
		    dataSrc: function (json) {

		        // 👇 AQUÍ CAE EL ERROR DEL CATCH DE PHP
		        if (json.error) {

		            // Mostrar en consola
		            console.error('Error PHP:', json.error);

		            // Mostrar al usuario
		            mandaMensaje("Error en el servidor");

		            // Evitar que DataTables intente renderizar
		            return [];
		        }

		        return json.data;
		    }
        },
        pageLength: 25,
        dom: '<"top"lpf>t<"bottom"i>'
    });

    /* ======================
       MANEJO DE ERRORES
       ====================== */

    tablaValidas.on('error.dt', function (e, settings, techNote, message) {
        mandaMensaje('Error en tabla <b>Válidas</b>:<br>' + message);
    });

    tablaRevisar.on('error.dt', function (e, settings, techNote, message) {
        mandaMensaje('Error en tabla <b>a Revisar</b>:<br>' + message);
    });

});
// ________________________________________________________________________
async function procesarRespuesta__(vRes) {
	loader('none');
	cOpcion = vRes.parametros.opcion;
	switch(cOpcion){
		// ______________________________
		case "trae_CatUrs":
			 llenaComboCveDes(document.getElementById("cveUrI"), vRes.urs , false);
			 llenaComboCveDes(document.getElementById("cveUrF"), vRes.urs , false);
		break;
	}
}
// __________________________REGRESOS DE PHP _____________________________________
async function procesarError__(vRes) {		
	loader('none');
	cOpcion = vRes.parametros.opcion;
	switch(cOpcion){
		// ______________________________
		case "buscaYPagina":
		break;
		// ______________________________
		// ______________________________
		// ______________________________
		default:
		break;
	}
}
// ________________________________________________________________________
function traeCatUrs(){
	aParametros = {
		opcion: "trae_CatUrs"
	}
	conectayEjecutaPost(aParametros,cPhp);
}
// ________________________________________________________________________
function ConsultaEstructuras() {

    filtrosActuales = {}; // reset

    const filtro = valorDeObjeto("filtro");

    if (!filtro) {
        mandaMensaje('Seleccione un filtro');
        return;
    }

    // ✔ Todas
    if (filtro === 'T') {
        filtrosActuales.tipo = 'todas';
    }

    // ✔ Número de envío
    if (filtro === 'N') {
        const numEnvio = valorDeObjeto("numEnvio");
        if (!numEnvio) {
            mandaMensaje('Capture el número de envío');
            return;
        }
        filtrosActuales.tipo = 'envio';
        filtrosActuales.numEnvio = numEnvio.trim();
    }

    // ✔ Rango de UR
    if (filtro==='U' || filtro==="P") {
        const urI = valorDeObjeto("cveUrI");
        const urF = valorDeObjeto('cveUrF');

        if (!urI || !urF) {
            mandaMensaje('Seleccione UR inicial y final');
            return;
        }
        if (urI>urF){
        	urT = urI;
        	urI = urF;
        	urF = urT;
        }

        filtrosActuales.tipo = 'ur';
        filtrosActuales.urI	 = urI;
        filtrosActuales.urF	 = urF;
    }

    // ✔ Checkboxes
    filtrosActuales.validas   = $('#filVal').is(':checked');
    filtrosActuales.revisar   = $('#filRevisar').is(':checked');

    // 🔥 Recargar tablas
    if (filtrosActuales.validas) {
        tablaValidas.ajax.reload();
    } else {
        tablaValidas.clear().draw();
    }

    if (filtrosActuales.revisar) {
        tablaRevisar.ajax.reload();
    } else {
        tablaRevisar.clear().draw();
    }
}
// ________________________________________________________________________
// ________________________________________________________________________
// ________________________________________________________________________
// ________________________________________________________________________
// ________________________________________________________________________