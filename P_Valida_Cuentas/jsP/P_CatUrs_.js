/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Autor   		: Miguel Ángel Bolaños Guillén
 * Sistema 		: Sistema de Validación de cuentas
 * Fecha   		: Noviembre 2025
 * Descripción 	: Catalogo de Unidades
 *                Paso a PHP 8.03
 *                	
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*/
var cPhp      	= "P_CatUrs_.php";			// En este php estarán las funciones que se invocaran desde este JS
var cPhpBusca	= "P_Busca_Pagina_.php";
var gTabla	  	= "tblCatUrs";			// Tabla HTML que se esta visualizando
var gForma	  	= "frmCatUrs";
var gPagina		= 1; 
// ________________________________________________________________________
window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a este JS
	// Se obtiene el nombre del archivo que lo invoca
	var loc     = window.location;
    var cHtml 	= loc.pathname.substring(loc.pathname.lastIndexOf('/') + 1);

	switch(cHtml){
		case "P_Cuentas_02_06Unidades.php":
			cargarCentrosCosto();
		break;
	}
}
// __________________________REGRESOS DE PHP _____________________________________
async function procesarRespuesta__(vRes) {
	loader('none');
	cOpcion = vRes.parametros.opcion;
	switch(cOpcion){
		// ______________________________
		case "buscaYPagina":
			aIdsHtml = ["unidad_id","unidad_desc","unidad_digito","activo"]; // Para hacer el llenado de los input HTML, al dar clic en la tabla HTML
			pintarTablaHTMLEscucha(gTabla,vRes.registros,vRes.parametros,aIdsHtml);
			generarPaginador(vRes.totalPaginas, vRes.parametros);
		break;
		// ______________________________
		//case "CatalogosCentrosCostos":
		//	cargarCentrosCosto(vRes); // llama a buscaYPagina , lo comente porque mejor usar buscaYPagina cuando se carge lapágina HTML en el window.onload
		//break;
		// ______________________________
		// ______________________________
		case "Unidades_Salida":
			limpiarValorObjetoxId("selOpe");
			cSalida = vRes.salida;
			abrePdf("salidas/"+cSalida);
		break;
		// ______________________________
		// ______________________________
		case "cambiaStatusCentroCosto":
			asignaValorXId("activo",vRes.parametros.activo);
			cargarCentrosCosto();
		break;
		// ______________________________
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
		case "Unidades_Salida":
			limpiarValorObjetoxId("selOpe");
		break;
		// ______________________________

		// ______________________________
		default:
		break;
	}
}

// _______________________FUNCIONES GENERALES _____________________________
function pantaUrsCata() {
	aParametros = {
		opcion: "CatalogosCentrosCostos"
	}
	conectayEjecutaPost(aParametros,cPhp);
}
// _________________________________________________________________
function Salida_Unidades(){
let cWhere	= whereCampoBusqueda("aCamSel","txtBuscar");
let cSalida	= valorDeObjeto("selOpe",false);

	aParametros = {
			opcion	: "Unidades_Salida",
			salida	: cSalida,
			where	: cWhere
	}
	conectayEjecutaPost(aParametros,cPhp);

}
// _________________________________________________________________
function cargarCentrosCosto(cPagina=1) {
	let valSel	 = ""; 
	let campoBus = valorDeObjeto("aCamSel",false);
	if (campoBus!==""){
		valSel = valorDeObjeto("txtBuscar",false);
		if (valSel===""){
			return false;
		}
	}
	cRegistros = valorDeObjeto("selNumReg");
	const aParametros = {
    	opcion      : "buscaYPagina",
    	tablaPos    : "unidades a ",
	    campos		: [
				        { nombre: "a.unidad_id"		, tipo: "C" },
				        { nombre: "a.unidad_desc"	, tipo: "C" },
				        { nombre: "a.unidad_digito"	, tipo: "C" },
				        { nombre: "a.activo"		, tipo: "C" }
		    		  ],
    	scroll      : [],
    	maxscroll   : 90,
    	orden       : ["ORDER BY unidad_id ASC "],
    	registros   : cRegistros,              	// número de registros por página
    	pagina      : cPagina,               	// página seleccionada
    	campoSel    : campoBus,        			// opción seleccionada del select
    	valSel      : valSel,          			// texto del input
    	join        : ""						// construir_Where1() 			// "a.id=b.id" si es necesario
	};
	conectayEjecutaPost(aParametros,cPhpBusca);
}
// _________________________________________________________________
function cambiaEstatusUR(){
	cUr		= valorDeObjeto("unidad_id",false);
	cActivo = valorDeObjeto("activo");

	if (cUr===null || cActivo===null){
		mandaMensaje("Se requiere seleccionar una UR ");
		return false;
	}

	if (cActivo=="S"){
		cActivo = "N";
	}else{
		cActivo = "S";
	}
	cMen = `¿Desea pasar al estaus de activo a  '${cActivo}' al centro de costo ${cUr}?`;
	esperaRespuesta(cMen).then((respuesta) => {
		if (respuesta){
			aParametros = {
				opcion		: "cambiaStatusCentroCosto",
				unidad_id	: cUr,
				activo		: cActivo
			};
			conectayEjecutaPost(aParametros,cPhp);
		}
	});  

}
// _________________________________________________________________