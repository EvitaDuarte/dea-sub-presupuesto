/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Autor   		: Miguel Ángel Bolaños Guillén
 * Sistema 		: Sistema de Validación de cuentas
 * Fecha   		: Noviembre 2025
 * Descripción 	: Catalogo de Configuración
 *                Paso a PHP 8.03
 *                	
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*/
var cPhp      	= "P_Configura_.php";			// En este php estarán las funciones que se invocaran desde este JS
var cPhpBusca	= "P_Busca_Pagina_.php";
var gTabla	  	= "tblConfigura";				// Tabla HTML que se esta visualizando
var gForma	  	= "frmConfigura";
var gPagina		= 1; 
var gConfigura  = null;							// select id,nombre,valor,tipo from configuracion
// ________________________________________________________________________
window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a este JS
	// Se obtiene el nombre del archivo que lo invoca
	var loc     = window.location;
    var cHtml 	= loc.pathname.substring(loc.pathname.lastIndexOf('/') + 1);

	switch(cHtml){
		case "P_Cuentas_02_08Configura.php":
			creaCampoTipo();
			cargarExpresiones();
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
			aIds = ["id","nombre","valor","tipo"]; // Para hacer el llenado de los HTML al dar clic en la tabla HTML
			pintarTablaHTMLEscucha(gTabla,vRes.registros,vRes.parametros,aIds,cambiaTipoConfigura);
			generarPaginador(vRes.totalPaginas, vRes.parametros);
		break;
		// ______________________________
		// case "traeConfigura":
		// 	cargaConfigura();
		// break;
		// ______________________________
		case "CrearCampoTipo":
			gConfigura = vRes.configura;
			cargaCataConfigura();

		break;
		// ______________________________
		case "Configura_Salida":
			limpiarValorObjetoxId("selOpe");
			cSalida = vRes.salida;
			abrePdf("salidas/"+cSalida);
		break;
		// ______________________________
		// ______________________________
		// case "eliminaConfigura":
		// 	cargaCataConfigura();
		// break;
		// ______________________________
		case "ConfigurarActualizar":
			cargaCataConfigura();
		break;
		// ______________________________
		case "ConfigurarAdicionar":
			cargaCataConfigura();
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
		case 'Configura_Salida':
			limpiarValorObjetoxId("selOpe");
		break;
		// ______________________________
		// ______________________________
		// ______________________________
		default:
		break;
	}
}
// _______________________FUNCIONES GENERALES _____________________________
function creaCampoTipo(){
	aParametros = {
		opcion : "CrearCampoTipo"
	};
	conectayEjecutaPost(aParametros,cPhp);
}
// _________________________________________
function cargaCataConfigura(cPagina=1){
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
    	tablaPos    : "configuracion a ",
	    campos		: [
				        { nombre: "a.id"		, tipo: "C" },
				        { nombre: "a.nombre"	, tipo: "C" },
				        { nombre: "a.valor"		, tipo: "C" },
				        { nombre: "a.tipo"		, tipo: "C" },
				        { nombre: "a.fecha"		, tipo: "C" }
		    		  ],
    	scroll      : [],
    	maxscroll   : 90,
    	orden       : ["ORDER BY id ASC  "],
    	registros   : cRegistros,              	// número de registros por página
    	pagina      : cPagina,               	// página seleccionada
    	campoSel    : campoBus,        			// opción seleccionada del select
    	valSel      : valSel,          			// texto del input
    	join        : ""						// construir_Where1() 			// "a.id=b.id" si es necesario
	};
	conectayEjecutaPost(aParametros,cPhpBusca);
}
// _________________________________________
function cambiaTipoConfigura(aFila,nId){
	//console.log ("Fila",aFila,nId);
	const cTipo = aFila.tipo;
	const inputElement = document.getElementById('valor');
	// 2. Usar setAttribute para cambiar el valor de 'data-exp'
	if (inputElement) {
	    inputElement.setAttribute('data-exp', cTipo);
	    // Para verificar, podrías imprimir el nuevo valor:
	    //console.log(inputElement.getAttribute('data-exp'));
	}
}
// _________________________________________
function cargarExpresiones(){
	aPropiedades = regresaExpresiones();
	llenaComboPropiedades("tipo",aPropiedades);
}
// _________________________________________
function ConfiguraActualiza(){
	cId = valorDeObjeto("id"); cValor = valorDeObjeto("valor")
	if (cId===null){
		mandaMensaje("Debe seleccionar un elemento a actualizar");
		return false;
	}
	if (cValor===null){
		mandaMensaje("Se debe de capturar el valor a actualizar;")
		FocoEnObjeto("valor");
		return false;
	}
	aParametros = {
		opcion	: "ConfigurarActualizar",
		id		: cId,
		valor	: cValor
	}
	conectayEjecutaPost(aParametros,cPhp);

}
// _________________________________________
function ConfiguraNuevo(){
	aIds = ["id","nombre"];
	aDiv = ["divActualiza","divAdiciona"];

	aIds.forEach((cId) => {
		alternaHabilitado(cId);
		limpiaObjeto(cId);
	});
	limpiaObjeto("valor");limpiaObjeto("tipo");

	aDiv.forEach((cId)=> {
		alternaVisibilidad(cId);
	});

	if ( !document.getElementById("divAdiciona").disabled ){
		FocoEnObjeto(document.getElementById("id"));
		document.getElementById("valor").setAttribute('data-exp', "");
	}
}
// _________________________________________
function ConfiguraAdicionar(){
	aIds = ["id","nombre","valor","tipo"];
	lOk  = true;

	aIds.forEach((cId)=>{
		cValor = valorDeObjeto(cId);
		if (cValor===null){
			lOk = false;
		}
	});
	if (lOk){
		aParametros = {
			opcion	: "ConfigurarAdicionar",
			id 		: valorDeObjeto("id"),
			nombre	: valorDeObjeto("nombre"),
			valor	: valorDeObjeto("valor"),
			tipo	: valorDeObjeto("tipo")
		};
		conectayEjecutaPost(aParametros,cPhp);
	}
}
// _________________________________________
// _________________________________________
// _________________________________________