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

    /* =========================
       CONFIGURACIÓN BASE
       ========================= */

    const dtBaseConfig = {
        processing      : true,
        serverSide      : true,
        deferLoading    : 0,
        pageLength      : 25,
        scrollY         : '420px',
        scrollCollapse  : true,
        paging          : true,
        fixedHeader     : true,
        autoWidth       : false,
        dom             : '<"top-controls"lpf>rt<"bottom"i>',
        columnDefs      : [ { targets: '_all', className: 'dt-left' } ]
    };

    /* =========================
       AJAX REUTILIZABLE
       ========================= */

    function ajaxDatatable(tablaBackend) {
        return {
            url: 'backP/api_datatables.php',
            type: 'POST',
            data: function (d) {
                d.tabla     = tablaBackend;
                d.filtro    = filtrosActuales;
                d.url       = gUrlCtas;
            },
            dataSrc: function (json) {

                // 👇 Error que viene del catch en PHP
                if (json.error) {
                    console.error(`Error PHP (${tablaBackend}):`, json.error);
                    mandaMensaje("Error en el servidor");
                    return [];
                }

                return json.data;
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                mandaMensaje("Error de comunicación con el servidor");
            }
        };
    }

    /* =========================
       FUNCIÓN FÁBRICA DE TABLAS
       ========================= */

    function crearTabla(selector, tablaBackend, nombreTabla) {

        const tabla = $(selector).DataTable({
            ...dtBaseConfig,
            ajax: ajaxDatatable(tablaBackend)
        });

        // Manejo de errores internos de DataTables
        tabla.on('error.dt', function (e, settings, techNote, message) {
            mandaMensaje(`Error en tabla <b>${nombreTabla}</b>:<br>${message}`);
        });

        return tabla;
    }

    /* =========================
       CREACIÓN DE TABLAS
       ========================= */

    window.tablaValidas = crearTabla(
        '#tblEstruValidas',
        'epvalidas',
        'Válidas'
    );

    window.tablaRevisar = crearTabla(
        '#tblEstruRevisar',
        'epinvalidas',
        'a Revisar'
    );

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
             gUrlCtas = vRes.urlCtas;
		break;
        // _______________________________
        case "actualizaEstado":
            ConsultaEstructuras(true);
        break;
        // _______________________________
        // _______________________________
        // _______________________________
        // _______________________________
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
function ConsultaEstructuras(cDataTables=true,lCorreo=false) {

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
        if (filtro==="P"){
            filtrosActuales.tipo = 'pendientes';
        }
    }
    if (cDataTables){
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
        // El boton de reenvio detecta que las tablas HTML estan vacías
        // if(lCorreo){
        //     reenviarCorreo(1);// podría entrar en un loop si los filtros no generar información
        // }
    }else{
        aParametros ={
            opcion  :"actualizaEstado",
            url     : gUrlCtas,
            filtros : filtrosActuales
        }
        loader('block');
        conectayEjecutaPost(aParametros,cPhp);
    }
}
// ________________________________________________________________________
function filtroOpciones(cOpc){
    //console.log("cOpc",cOpc);
    document.getElementById('filEnvio').classList.add('oculto');
    document.getElementById('filUrI').classList.add('oculto');
    document.getElementById('filUrF').classList.add('oculto');
//  document.getElementById('divReEnvio').classList.add('oculto');
    switch(cOpc){
        case 'N': // Numero de Envio
            document.getElementById('filEnvio').classList.remove('oculto');
//          document.getElementById('divReEnvio').classList.remove('oculto');
            //console.log("cOpc",cOpc);
        break;
        case 'U':
        case 'P':
            document.getElementById('filUrI').classList.remove('oculto');
            document.getElementById('filUrF').classList.remove('oculto');
            //console.log("cOpc",cOpc);
        break;
    }
}
// ________________________________________________________________________
function ActualizarEstado(){
    ConsultaEstructuras(false);
}
// ________________________________________________________________________
// function reenviarCorreo(nUnaVez=0){
//     tabla   = $('#tblEstruValidas').DataTable(); // obtiene instancia existente
//     nRenVal = tabla.rows().count();
//     tabla   = $('#tblEstruRevisar').DataTable();
//     nRenVal+=tabla.rows().count();
//     if ( nRenVal> 0) {
//         enviarCorreo(nUnaVez);
//     }else{
//         if (nUnaVez===0){
//             ConsultaEstructuras(true,true); //podría entrar en un bucle si al ir a PHP no hay registros que cumplan la condición
//         }else{
//             mandaMensaje("No hay información a reenviar. Revise si los filtros de información son correctos")
//         }
//     }

// }
// // ________________________________________________________________________
// function enviarCorreo(nUnaVez){
//     console.log("nUnaVez",nUnaVez);
// }
// ________________________________________________________________________