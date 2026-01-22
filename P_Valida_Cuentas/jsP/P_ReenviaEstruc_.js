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
var gaCorreo	= null;							// Guardara Correo de envio a presupuesto, a contabilidad, usu
var tablaEnvio	= "";
var filtrosActuales	= {};
// ________________________________________________________________________
window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a este JS
	traeUrlCtas();
}
// ________________________________________________________________________
async function procesarRespuesta__(vRes) {
	loader('none');
	cOpcion = vRes.parametros.opcion;
	switch(cOpcion){
		// _______________________________
		case "traeUrlCtas":
			gUrlCtas = vRes.urlCtas;
		break;
        // _______________________________
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
		// ______________________________
		// ______________________________
		// ______________________________
		default:
		break;
	}
}
// ________________________________________________________________________
// ===========================

// Función que inicializa la tabla vacía
function inicializarTablaVaciaMal() {

    const dtBaseConfig = {
        processing      : true,
        serverSide      : false,
        deferLoading    : 0,          // Evita carga inicial
        pageLength      : 25,
        scrollY         : '420px',
        scrollCollapse  : true,
        paging          : true,
        fixedHeader     : true,
        autoWidth       : false,
        dom             : '<"top-controls"lpf>rt<"bottom"i>',
        columnDefs      : [ { targets: '_all', className: 'dt-left' } ],
        data            : [],          // Inicialmente vacía

        // Configuración AJAX
        ajax: {
            url  : 'backP/api_reenviar.php',
            type : 'POST',
            data : function(d) {
                d.filtro = filtrosActuales;
                d.url    = gUrlCtas;
            },
            dataSrc: function(json) {
                if (json.error) {
                    console.error("Error PHP:", json.error);
                    mandaMensaje("Error en el servidor");
                    return [];
                }
                return json.data;
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                mandaMensaje("Error de comunicación con el servidor");
            }
        }
    };

    tablaEnvio = $('#tblReenvio').DataTable(dtBaseConfig);

    // Manejo de errores internos de DataTables
    tablaEnvio.on('error.dt', function (e, settings, techNote, message) {
        mandaMensaje(`Error en tabla <b>Reenvío</b>:<br>${message}`);
    });
}
// ________________________________________________________________________
function inicializarTablaVacia() {

    // 👇 1. Desactivar alert nativo (por si este JS se carga varias veces)
    $.fn.dataTable.ext.errMode = 'none';

    tablaEnvio = $('#tblReenvio').DataTable({
        processing      : true,
        serverSide      : false,
        pageLength      : 25,
        scrollY         : '420px',
        scrollCollapse  : true,
        paging          : true,
        fixedHeader     : true,
        autoWidth       : false,
        dom             : '<"top-controls"lpf>rt<"bottom"i>',
        columnDefs      : [{ targets: '_all', className: 'dt-left' }],
        data            : [],
		language: {
		    processing: "Procesando...",
		    search: "Buscar:",
		    lengthMenu: "Mostrar _MENU_ registros",
		    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
		    infoEmpty: "Mostrando 0 a 0 de 0 registros",
		    infoFiltered: "(filtrado de _MAX_ registros totales)",
		    loadingRecords: "Cargando...",
		    zeroRecords: "No se encontraron registros",
		    emptyTable: "No hay información disponible",
		    paginate: {
		        first: "Primero",
		        previous: "Anterior",
		        next: "Siguiente",
		        last: "Último"
		    }
		}

    });

    // 👇 2. Error de servidor / negocio (PHP, SQL, filtros)
    $('#tblReenvio').on('xhr.dt', function (e, settings, json, xhr) {

        if (json && json.error) {
            mandaMensaje(`Error en Reenvío:<br>${json.error}`);
        }

        if (xhr.status !== 200) {
            mandaMensaje(`Error HTTP ${xhr.status} en Reenvío`);
        }
    });

    // 👇 3. Error interno DataTables (columnas, JSON inválido, etc.)
    $('#tblReenvio').on('error.dt', function (e, settings, techNote, message) {
        mandaMensaje(`Error interno DataTables:<br>${message}`);
    });
}


// ________________________________________________________________________
// ===========================
// Función que se ejecuta al pulsar "Consultar"
// ===========================
function ConsultaEstructuras() {

    filtrosActuales = {};

    let numEnvio = valorDeObjeto("numEnvio");
    if (!numEnvio) {
        mandaMensaje('Capture el número de envío');
        return;
    }

    filtrosActuales.tipo     = 'envio';
    filtrosActuales.numEnvio = numEnvio.trim();

    // Inicializar tabla si aún no existe
    if (!tablaEnvio) {
        inicializarTablaVacia();
    }

    // 👉 ASIGNAR AJAX DINÁMICAMENTE
    tablaEnvio.settings()[0].ajax = {
        url  : 'backP/api_reenviar.php',
        type : 'POST',
        data : function (d) {
            d.filtro = filtrosActuales;
            d.url    = gUrlCtas;
        },
        dataSrc: function (json) {
            if (json.error) {
                console.error(json.error);
                mandaMensaje("Error en el servidor");
                return [];
            }
            return json.data;
        }
    };

    tablaEnvio.ajax.reload();
}

// ________________________________________________________________________
function traeUrlCtas(){
	aParametros = {
		opcion: "traeUrlCtas"
	}
	conectayEjecutaPost(aParametros,cPhp);
}
// ________________________________________________________________________
// ________________________________________________________________________