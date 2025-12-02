/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Autor   		: Miguel Ángel Bolaños Guillén
 * Sistema 		: Sistema de Validación de cuentas
 * Fecha   		: Noviembre 2025
 * Descripción 	: Catalogo de UrPpSpg Válidas
 *                Paso a PHP 8.03
 *                	
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*/
var cPhp      	= "P_UrPpSpg_.php";			// En este php estarán las funciones que se invocaran desde este JS
var cPhpBusca	= "P_Busca_Pagina_.php";
var gTabla	  	= "tblUrPpSpg";				// Tabla HTML que se esta visualizando
var gForma	  	= "frmUrPpSpg";
var gPagina		= 1; 
// ________________________________________________________________________
window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a este JS
	// Se obtiene el nombre del archivo que lo invoca
	var loc     = window.location;
    var cHtml 	= loc.pathname.substring(loc.pathname.lastIndexOf('/') + 1);

	switch(cHtml){
		case "P_Cuentas_02_07UrPpSpg.php":
			//cargaUrPpSpg();
			cargaCataUrPpSpg();
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
			aIds = ["tur","pp","spg","activo"]; // Para hacer el llenado de los HTML al dar clic en la tabla HTML
			pintarTablaHTMLEscucha(gTabla,vRes.registros,vRes.parametros,aIds);
			generarPaginador(vRes.totalPaginas, vRes.parametros);
		break;
		// ______________________________
		case "traeUrPpSpg":
			llenaComboCveDes(document.getElementById("tur")	, vRes.urs);
			llenaComboCveDes(document.getElementById("pp")	, vRes.pps);
			llenaComboCveDes(document.getElementById("spg")	, vRes.spgs);
			cargaUrPpSpg();
		break;
		// ______________________________

		// ______________________________
		case "UrPpSpg_Salida":
			limpiarValorObjetoxId("selOpe");
			cSalida = vRes.salida;
			abrePdf("salidas/"+cSalida);
		break;
		// ______________________________
		case "actualizaUrPpSpg":
			cargaUrPpSpg();
		break;
		// ______________________________
		case "eliminaUrPpSpg":
			cargaUrPpSpg();
		break;
		// ______________________________
		case "cargaDelAutorizado":
			cargaUrPpSpg();
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
		case 'UrPpSpg_Salida':
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
function cargaUrPpSpg(cPagina=1){
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
    	tablaPos    : "v_ur_pp_spg a ",
	    campos		: [
				        { nombre: "a.tur"		, tipo: "C" },
				        { nombre: "a.pp"		, tipo: "C" },
				        { nombre: "a.spg"		, tipo: "C" },
				        { nombre: "a.activo"	, tipo: "C" },
				        { nombre: "a.fecha"		, tipo: "C" }
		    		  ],
    	scroll      : [],
    	maxscroll   : 90,
    	orden       : ["ORDER BY tur ASC, pp asc, spg asc  "],
    	registros   : cRegistros,              	// número de registros por página
    	pagina      : cPagina,               	// página seleccionada
    	campoSel    : campoBus,        			// opción seleccionada del select
    	valSel      : valSel,          			// texto del input
    	join        : ""						// construir_Where1() 			// "a.id=b.id" si es necesario
	};
	conectayEjecutaPost(aParametros,cPhpBusca);
}
// ____________________________________________________
function cargaCataUrPpSpg(){
	aParametros ={
		opcion: "traeUrPpSpg"
	}
	conectayEjecutaPost(aParametros,cPhp);
}
// ____________________________________________________
function Salida_UrPpSpg(){
let cWhere	= whereCampoBusqueda("aCamSel","txtBuscar");
let cSalida	= valorDeObjeto("selOpe",false);

	aParametros = {
			opcion	: "UrPpSpg_Salida",
			salida	: cSalida,
			where	: cWhere
	}
	conectayEjecutaPost(aParametros,cPhp);
}
// ____________________________________________________
function adiciona_UrPpSpg(){
	vUr  = valorDeObjeto("tur");
	vPp	 = valorDeObjeto("pp");
	vSpg = valorDeObjeto("spg");
	vSta = valorDeObjeto("activo");
	if( vUr==null || vPp==null || vSpg==null || vSta==null){
		mandaMensaje("Se requiere los valores de Ur, PP, Spg y Activo");
		return false;
	}
	esperaRespuesta(`¿Desea actualizar la información de ${vUr}-${vPp}-${vSpg}-${vSta} ?`).then((respuesta) => {
		if(respuesta){
			aParametros ={
				opcion	: "actualizaUrPpSpg",
				tur		: vUr,
				pp		: vPp,
				spg		: vSpg,
				activo	: vSta
			}
			conectayEjecutaPost(aParametros,cPhp);
		}else{

		}
	});
}
// ____________________________________________________
function eliminar_UrPpSpg(){
	vUr  = valorDeObjeto("tur");
	vPp	 = valorDeObjeto("pp");
	vSpg = valorDeObjeto("spg");
	vSta = valorDeObjeto("activo");
	if( vUr==null || vPp==null || vSpg==null || vSta==null){
		mandaMensaje("Se requiere los valores de Ur, PP, Spg");
		return false;
	}
	esperaRespuesta(`¿Desea eliminar la combinación ${vUr}-${vPp}-${vSpg}-${vSta} ?`).then((respuesta) => {
		if(respuesta){
			aParametros ={
				opcion	: "eliminaUrPpSpg",
				tur		: vUr,
				pp		: vPp,
				spg		: vSpg,
				activo	: vSta
			}
			conectayEjecutaPost(aParametros,cPhp);
		}else{

		}
	});
}
// ____________________________________________________
function autorizado_UrPpSpg(){
	esperaRespuesta('¿Desea integrar Ur-Pp-Spg del Autorizado a las Ur-Pp-Spg válidas?').then((respuesta) => {
		if (respuesta){
			loader('flex');
			aParametros = {
				opcion: "cargaDelAutorizado"
			}
			conectayEjecutaPost(aParametros,cPhp);
		}
	});
}
// ____________________________________________________




/*
	    text 	: "¿Estás seguro de integrar la combinación " + vUr+"-"+vPp+"-"+vSpg,
	    text	: "¿Desea integrar Ur-Pp-Spg del Autorizado a las Ur-Pp-Spg válidos? " */