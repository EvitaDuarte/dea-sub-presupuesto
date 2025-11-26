/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Autor   		: Miguel Ángel Bolaños Guillén
 * Sistema 		: Sistema de Validación de cuentas
 * Fecha   		: Noviembre 2025
 * Descripción 	: Catalogo de Usuarios
 *                Paso a PHP 8.03
 *                	
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*/
var cPhp      	= "P_CatUsu_.php";			// En este php estarán las funciones que se invocaran desde este JS
var cPhpBusca	= "P_Busca_Pagina_.php";
var gTabla	  	= "tblCatUsu";			// Tabla HTML que se esta visualizando
var gForma	  	= "frmCatUsu";
var gPagina		= 1; 
// ________________________________________________________________________
window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a este JS
	// Se obtiene el nombre del archivo que lo invoca
	var loc     = window.location;
    var cHtml 	= loc.pathname.substring(loc.pathname.lastIndexOf('/') + 1);

	switch(cHtml){
		case "P_Cuentas_02_05Usuarios.php":
			pantaUsuCata();
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
			aIds = ["usuario_id","nombre_completo","correo","estatus","esquema_id","unidad_id","unidad_inicio","unidad_fin","listaurs"]; // Para hacer el llenado de los HTML al dar clic en la tabla HTML
			pintarTablaHTMLEscucha(gTabla,vRes.registros,vRes.parametros,aIds);
			generarPaginador(vRes.totalPaginas, vRes.parametros);
		break;
		// ______________________________
		case "CatalogosUsuarios":
			llenaCombosUsuarios(vRes);
			cargarUsuarios(); // llama a buscaYPagina
		break;
		// ______________________________
		case "agregarCombi":
			cargarUsuarios(); // llama a buscaYPagina
		break;
		// ______________________________
		case "genera_Salida":
			limpiarValorObjetoxId("selOpe");
			cSalida = vRes.salida;
			abrePdf("salidas/"+cSalida);
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
		case 'CatalogosUsuarios':
		break;
		// ______________________________
		case "genera_Salida":
			limpiarValorObjetoxId("selOpe");
		break;
		// ______________________________
	}
}
// _______________________FUNCIONES GENERALES _____________________________
function pantaUsuCata() {
	aParametros = {
		opcion: "CatalogosUsuarios"
	}
	conectayEjecutaPost(aParametros,cPhp);
}
// ________________________________________________________________________
function cargarUsuarios(cPagina=1){
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
    	tablaPos    : "usuarios a ",
	    campos		: [
				        { nombre: "a.usuario_id"		, tipo: "C" },
				        { nombre: "a.nombre_completo"	, tipo: "C" },
				        { nombre: "a.correo"			, tipo: "C" },
				        { nombre: "a.estatus"			, tipo: "C" },
				        { nombre: "a.esquema_id"  		, tipo: "C" },
				        { nombre: "a.unidad_id"  		, tipo: "C" },
				        { nombre: "a.unidad_inicio"  	, tipo: "C" },
				        { nombre: "a.unidad_fin"  		, tipo: "C" },
				        { nombre: "a.listaurs"  		, tipo: "C" }
		    		  ],
    	scroll      : [],
    	maxscroll   : 90,
    	orden       : ["ORDER BY unidad_id ASC, a.usuario_id ASC "],
    	registros   : cRegistros,              	// número de registros por página
    	pagina      : cPagina,               	// página seleccionada
    	campoSel    : campoBus,        			// opción seleccionada del select
    	valSel      : valSel,          			// texto del input
    	join        : ""						// construir_Where1() 			// "a.id=b.id" si es necesario
	};
	conectayEjecutaPost(aParametros,cPhpBusca);
}
// ________________________________________________________________________
function llenaCombosUsuarios(vRes){
	llenaComboCveDes(document.getElementById("unidad_inicio"),vRes.unidades);
	llenaComboCveDes(document.getElementById("unidad_fin"),vRes.unidades);
	llenaComboCveDes(document.getElementById("unidad_id"),vRes.unidades);
	llenaComboCveDes(document.getElementById("esquema_id"),vRes.esquemas);
}
// ________________________________________________________________________
// ________________________________________________________________________
// ________________________________________________________________________
// ________________________________________________________________________