/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Autor   		: Miguel Ángel Bolaños Guillén
 * Sistema 		: Sistema de Validación de cuentas
 * Fecha   		: Noviembre 2025
 * Descripción 	: Catálogo de partidas
 *                Paso a PHP 8.03
 *                	
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*/
var cPhp      	 = "P_Partidas_.php";	// En este php estarán las funciones que se invocaran desde este JS
var cPhpBusca	 = "P_Busca_Pagina_.php";
var gTabla	  	 = "";					// Tabla HTML que se esta visualizando
var gForma	  	 = "";

window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a P_partidas_.js
	// Se obtiene el nombre del archivo que lo invoca
	var loc     = window.location;
    var cHtml 	= loc.pathname.substring(loc.pathname.lastIndexOf('/') + 1);

	switch(cHtml){
		case "P_Cuentas_02_02Ptda.php":
			cargaCatalogoPartidas();
		break;
	}
}
// _________________________________________________________________________
const cargaCatalogoPartidas=(cPagina=1)=>{
	valSel	 = ""; 
	campoBus = valorDeObjeto("aCamSel",false);
	//campoBus = campoBus.trim();
	//console.log("campoBus",campoBus);
	if (campoBus!==""){
		valSel = valorDeObjeto("txtBuscar",false);
		if (valSel===""){
			return false;
		}
	}
	cRegistros = valorDeObjeto("selNumReg");
	const aParametros = {
    	opcion      : "buscaYPagina",
    	tablaPos    : "partidas a",
    	//campos      : ["a.partida","a.compramenor","a.ordencompra","a.contactamay","a.nombre"],
	    campos		: [
				        { nombre: "a.partida",      tipo: "C" },
				        { nombre: "a.compramenor",  tipo: "C" },
				        { nombre: "a.ordencompra",  tipo: "C" },
				        { nombre: "a.contactamay",  tipo: "C" },
				        { nombre: "a.nombre",       tipo: "C" }
		    		  ],
    	scroll      : ["nombre"],
    	maxscroll   : 90,
    	orden       : ["ORDER BY a.partida ASC"],
    	registros   : cRegistros,              	// número de registros por página
    	pagina      : cPagina,               	// página seleccionada
    	campoSel    : campoBus,        			// opción seleccionada del select
    	valSel      : valSel,          			// texto del input
    	join        : ""               			// "a.id=b.id" si es necesario
	};
	conectayEjecutaPost(aParametros,cPhpBusca);
}
// __________________________REGRESOS DE PHP _____________________________________
async function procesarRespuesta__(vRes) {
	loader('none');
	cOpcion = vRes.parametros.opcion;
	switch(cOpcion){
		case "buscaYPagina":
			aIds = ["partida","compraMenor","ordenCompra","contactamay","nombre"]
			pintarTablaHTMLEscucha("tblPartidas",vRes.registros,vRes.parametros,aIds);
			generarPaginador(vRes.totalPaginas, vRes.parametros);
		break;
	}

}
// __________________________REGRESOS DE PHP _____________________________________
async function procesarError__(vRes) {		
	loader('none');
	cOpcion = vRes.parametros.opcion;
	switch(cOpcion){
		case "buscaYPagina":
			
		break;
	}
}
// _______________________FUNCIONES GENERALES ____________________________________
// _______________________________________________________________________________
