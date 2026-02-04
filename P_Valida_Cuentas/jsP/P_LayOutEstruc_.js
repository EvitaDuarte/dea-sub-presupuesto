/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Autor        : Miguel Ángel Bolaños Guillén
 * Sistema      : Sistema de Validación de cuentas
 * Fecha        : Noviembre 2025
 * Descripción  : Captura manual estructuras 
 *                Paso a PHP 8.03
 *                  
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*/
var cPhp        = "P_CargaEstru_.php";          // En este php estarán las funciones que se invocaran desde este JS
var cPhpBusca   = "P_Busca_Pagina_.php";
var gTabla      = "tblLayOut";                  // Tabla HTML que se esta visualizando
var gForma      = "frmLayOut";
var gPagina     = 1; 
var gConfigura  = null;                         // select id,nombre,valor,tipo from configuracion
var gUrIni      = "";                           // Urs permitidas 
var gUrFin      = "";
var gUrLis      = "";
var gUrUsu      = "";
var gUrlCtas    = "";                           // url para validar via soap una estructura
var gUrlPys     = "";                           // url para validar via sopa un proyecto
var gValidaPY   = "";                           // 'S' si se requiere validar el proyecto( o si es geográfico)??
var gEstructura = "";
var gEstructuras= null;                         // Para guardar las estructuras que se enviarán al SIGA
var gaCorreo    = null;                         // Guardara Correo de envio a presupuesto, a contabilidad, usuario del correo genérico, contraseña del correo genérico
var gTabla      = "";
var tablaEnvio  = "";
var filtrosActuales = {};
//window.onload = function () {  
document.addEventListener('DOMContentLoaded', function () { 
    const params = new URLSearchParams(window.location.search);
    const cTabla = params.get('tabla');
    gTabla = cTabla;
    //console.log("Tabla",gTabla);

    document.getElementById('divValidas').classList.add('oculto');
    document.getElementById('divInValidas').classList.add('oculto');
    if (gTabla==="epvalidas"){
         document.getElementById('divValidas').classList.remove('oculto');
    }else if(gTabla==="epinvalidas"){
        document.getElementById('divInValidas').classList.remove('oculto');
    }

    traeCataUrs();

});
// ________________________________________________________________________
async function procesarRespuesta__(vRes) {
    loader('none');
    cOpcion = vRes.parametros.opcion;
    switch(cOpcion){
        // _______________________________
        case "trae_CatUrs":
             llenaComboCveDes(document.getElementById("cveUrI"), vRes.urs , false);
             llenaComboCveDes(document.getElementById("cveUrF"), vRes.urs , false);
             gUrlCtas = vRes.urlCtas;
        break;
        // _______________________________
        case "generaLayOut":
            //con sole.log(vRes);
            if (vRes.archivot) {
                // 1. Corregimos la ruta para el cliente (index.html)
                // Quitamos el "../" inicial para que sea relativa a la raíz
                const urlPublica = vRes.archivo.replace('../', ''); 

                // 2. Creamos el enlace de descarga
                const link = document.createElement('a');
                link.href = urlPublica;

                // 3. Forzamos el nombre del archivo (opcional)
                link.download = "layOutEstructuras.txt"; 

                // 4. Ejecutamos la descarga
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
            if (vRes.archivo){
                descargarConSelector(vRes.archivo);
            }
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
        // ______________________________
        // ______________________________
        // ______________________________
        default:
        break;
    }
}
// ________________________________________________________________________
function traeCataUrs(){
    aParametros = {
        opcion: "trae_CatUrs"
    }
    conectayEjecutaPost(aParametros,cPhp);
}
// ________________________________________________________________________
function filtro_Opciones(cOpc){
    document.getElementById('filEnvio').classList.add('oculto');
    document.getElementById('filUrI').classList.add('oculto');
    document.getElementById('filUrF').classList.add('oculto');
    switch(cOpc){
        case 'N': // Numero de Envio
            document.getElementById('filEnvio').classList.remove('oculto');
        break;
        case 'U':
        case 'P':
            document.getElementById('filUrI').classList.remove('oculto');
            document.getElementById('filUrF').classList.remove('oculto');
        break;
    }
}
// ________________________________________________________________________
function ConsultaLyEstructuras(lLayOut=false) {

    if (!revisaFiltros()){
        return false;
    }

    // Inicializar tabla si aún no existe
    if (!tablaEnvio) {
        inicializarTablaVacia();
    }

    // 👉 ASIGNAR AJAX DINÁMICAMENTE
    tablaEnvio.settings()[0].ajax = {
        url  : 'backP/api_layout.php',
        type : 'POST',
        data : function (d) {
                d.tabla     = gTabla;
                d.filtro    = filtrosActuales;
                d.url       = gUrlCtas;
        },
        dataSrc: function (json) {
            if (json.error) {
                console.error(json.error);
                mandaMensaje("Error en el servidor");
                return [];
            }
            if (lLayOut){
                aParametros = {
                    opcion  : "reEnviarCorreo",
                    datos   : json.data,
                    url     : gUrlCtas
                }
                conectayEjecutaPost(aParametros,cPhp);
            }
            return json.data;
        }
    };

    tablaEnvio.ajax.reload();
}
// ________________________________________________________________________
function inicializarTablaVacia() {

    // 👇 1. Desactivar alert nativo (por si este JS se carga varias veces)
    $.fn.dataTable.ext.errMode = 'none';

    tablaEnvio = $('#tblLayOut').DataTable({
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
function revisaFiltros(){
    filtrosActuales = {}; // reset

    const filtro = valorDeObjeto("filtro");

    if (!filtro) {
        mandaMensaje('Seleccione un filtro');
        return false;
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
            return false;
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
            return false;
        }
        if (urI>urF){
            urT = urI;
            urI = urF;
            urF = urT;
        }

        filtrosActuales.tipo = 'ur';
        filtrosActuales.urI  = urI;
        filtrosActuales.urF  = urF;
        if (filtro==="P"){
            filtrosActuales.tipo = 'pendientes';
        }
    }
    filtrosActuales.area = document.querySelector('input[name="tipo"]:checked').value;
    return true;
}
// ________________________________________________________________________
function GenerarLayOut(){

    if (!revisaFiltros()){
        return false;
    }
    aParametros = {
        opcion  : "generaLayOut",
        filtros : filtrosActuales,
        tabla   : gTabla,
        urlCtas : gUrlCtas
    }
    loader('flex');
    conectayEjecutaPost(aParametros,cPhp);
}
// ________________________________________________________________________
async function descargarConSelector(urlRelativa) {
    try {
        const urlPublica = urlRelativa.replace('../', '');
        
        // Obtenemos los datos del servidor
        const response = await fetch(urlPublica);
        const blob = await response.blob();

        // Abrimos el cuadro de diálogo del Sistema Operativo
        const handle = await window.showSaveFilePicker({
            suggestedName: 'layOut.txt',
            types: [{
                description: 'Archivos de Texto',
                accept: {'text/plain': ['.txt']},
            }],
        });

        // Escribimos el archivo en la ruta elegida por el usuario
        const writable = await handle.createWritable();
        await writable.write(blob);
        await writable.close();

    } catch (err) {
        // El usuario canceló o el navegador no es compatible
        console.log("Descarga cancelada o error de API");
    }
}
// ________________________________________________________________________