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
		case "Usuarios_Salida":
			limpiarValorObjetoxId("selOpe");
			cSalida = vRes.salida;
			abrePdf("salidas/"+cSalida);
		break;
		// ______________________________
		case "validaLdapUsu":
			refrescaDatosUsuario(vRes.lDap);
		break;
		// ______________________________
		case "actualizaUsuario":
			cargarUsuarios();
		break;
		// ______________________________
		case "cambiaStatusUsuario":
			asignaValorXId("estatus",vRes.estatus);
			cargarUsuarios();
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
		case "Usuarios_Salida":
			limpiarValorObjetoxId("selOpe");
		break;
		// ______________________________
		case "validaLdapUsu":
			refrescaDatosUsuario(vRes.lDap);
		break;
		// ______________________________
		default:
		break;
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
function Salida_Usuarios(){
	let cWhere	= whereCampoBusqueda("aCamSel","txtBuscar");
	let cSalida	= valorDeObjeto("selOpe",false);

	aParametros = {
			opcion	: "Usuarios_Salida",
			salida	: cSalida,
			where	: cWhere
	}
	conectayEjecutaPost(aParametros,cPhp);
}
// ________________________________________________________________________
function validaLdap(oUsu) {
	cUsu = oUsu.value.trim();
	if (cUsu!==""){
		aParametros ={
			opcion	: "validaLdapUsu",
			idUsu	: cUsu
		}
		conectayEjecutaPost(aParametros,cPhp);
	}else{
		// Nada que hacer, igual el osoario solo anda navegando
	}
}
// ________________________________________________________________________
function refrescaDatosUsuario(aLDap){
	asignaValorXId("nombre_completo",aLDap.nombre);
	asignaValorXId("correo"			,aLDap.mail);
	asignaValorXId("unidad_id"		,aLDap.ur);
	asignaValorXId("unidad_inicio"	,aLDap.ur);
	asignaValorXId("unidad_fin"		,aLDap.ur);
	asignaValorXId("estatus"		,aLDap.estatus);
	asignaValorXId("esquema_id"		,aLDap.esquema_id);
	asignaValorXId("listaurs"		,aLDap.listaurs);


}
// ________________________________________________________________________
function agregaUsuario(){
	cNombre = valorDeObjeto("nombre_completo",false);
	
	if (cNombre===null){
		mandaMensaje("Se requiere datos del usuario a actualizar");
		return false;
	}
	aIdHtml = ["correo","unidad_id","unidad_inicio","unidad_fin","estatus","esquema_id"];
	for(i=0;i<aIdHtml.length;i++){ // el foreach no se detiene con un return
		cVal = valorDeObjeto(aIdHtml[i]);
		if (cVal==null){
			return false; // Valor de Objeto manda mensaje si no hay captura
		}
	}
	cUsuId  = valorDeObjeto("usuario_id",false);
	esperaRespuesta(`Desea actualizar la información del usuario ${cUsuId} `).then((respuesta) => {
		if(respuesta){
			aParametros ={
				opcion			: "actualizaUsuario", 
				usuario_id		: valorDeObjeto("usuario_id",false),
				nombre_completo	: valorDeObjeto("nombre_completo",false),
				correo			: valorDeObjeto("correo",false),
				unidad_id		: valorDeObjeto("unidad_id",false),
				unidad_inicio	: valorDeObjeto("unidad_inicio",false),
				unidad_fin		: valorDeObjeto("unidad_fin",false),
				estatus			: valorDeObjeto("estatus",false),
				esquema_id		: valorDeObjeto("esquema_id",false),
				listaurs		: valorDeObjeto("listaurs",false)
			};
			conectayEjecutaPost(aParametros,cPhp);
		}
	});
}
// ________________________________________________________________________
function inactivaUsuario(){
	cNombre = valorDeObjeto("nombre_completo",false);
	cUsuId  = valorDeObjeto("usuario_id",false);
	cActivo = valorDeObjeto("estatus",false)
	if (cNombre===null || cActivo===null || cActivo===null){
		mandaMensaje("Se requiere datos del usuario para cambiar su estatus");
		return false;
	}
	if (cActivo=="ACTIVO"){
		cActivo = "INACTIVO";
	}else{
		cActivo = "ACTIVO";
	}
	cMen = `¿Desea pasar al estado ${cActivo} al usuario ${cUsuId}?`;
	esperaRespuesta(cMen).then((respuesta) => {
		if(respuesta){
			aParametros = {
				opcion		: "cambiaStatusUsuario",
				usuario_id	: cUsuId,
				estatus		: cActivo
			};
			conectayEjecutaPost(aParametros,cPhp);
		}
	});
}
// ________________________________________________________________________