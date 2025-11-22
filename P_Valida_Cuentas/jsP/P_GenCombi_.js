/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Autor   		: Miguel Ángel Bolaños Guillén
 * Sistema 		: Sistema de Validación de cuentas
 * Fecha   		: Noviembre 2025
 * Descripción 	: Tabla precombi
 *                Paso a PHP 8.03
 *                	
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*/
var cPhp      	= "P_GenCombi_.php";			// En este php estarán las funciones que se invocaran desde este JS
var cPhpBusca	= "P_Busca_Pagina_.php";
var gTabla	  	= "tblGenCombi";			// Tabla HTML que se esta visualizando
var gForma	  	= "frmGenCombi";
var gPagina		= 1; 
// ________________________________________________________________________
window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a este JS
	// Se obtiene el nombre del archivo que lo invoca
	var loc     = window.location;
    var cHtml 	= loc.pathname.substring(loc.pathname.lastIndexOf('/') + 1);

	switch(cHtml){
		case "P_Cuentas_02_04GenCombi.php":
			cargaCatalogos();
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
			aIds = ["tipoUr","cveAi","cveScta","cvePp","cveSpg","cvePy"]
			pintarTablaHTMLEscucha(gTabla,vRes.registros,vRes.parametros,aIds);
			generarPaginador(vRes.totalPaginas, vRes.parametros);
		break;
		// ______________________________
		case "cargaCatalogos":
			llena_Combos1(vRes);
			cargaPreCombi(); // llama a buscaYPagina
		break;
		// ______________________________
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
		case "cargaCatalogos":
			
		break;
		// ______________________________
		case "genera_Salida":
			limpiarValorObjetoxId("selOpe");
		break;
		// ______________________________
	}
}
// _______________________FUNCIONES GENERALES _____________________________
function cargaPreCombi(cPagina=1){
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
    	tablaPos    : "precombi a",
	    campos		: [
				        { nombre: "a.tipour"	, tipo: "C" },
				        { nombre: "a.clvai"		, tipo: "C" },
				        { nombre: "a.clvscta"	, tipo: "C" },
				        { nombre: "a.clvpp"  	, tipo: "C" },
				        { nombre: "a.clvspg"  	, tipo: "C" },
				        { nombre: "a.clvpy"  	, tipo: "C" },
				        { nombre: "a.geografico", tipo: "C" }
		    		  ],
    	scroll      : [],
    	maxscroll   : 90,
    	orden       : ["ORDER BY a.tipour ASC, a.clvai ASC, a.clvscta ASC, a.clvpp ASC, a.clvspg ASC, a.clvpy ASC "],
    	registros   : cRegistros,              	// número de registros por página
    	pagina      : cPagina,               	// página seleccionada
    	campoSel    : campoBus,        			// opción seleccionada del select
    	valSel      : valSel,          			// texto del input
    	join        : ""						// construir_Where1() 			// "a.id=b.id" si es necesario
	};
	conectayEjecutaPost(aParametros,cPhpBusca);
}
// ________________________________________________________________________
// ________________________________________________________________________
const cargaCatalogos=()=>{
	aParametros ={
		opcion:"cargaCatalogos"
	}
	conectayEjecutaPost(aParametros,"U_Cargas_.php");

}
// ________________________________________________________________________
function llena_Combos1(vRes){
	oCata = vRes.cata
	llenaComboCveDes(document.getElementById("UrIni"),oCata.urs);
	llenaComboCveDes(document.getElementById("UrFin"),oCata.urs);

	llenaComboCveDes(document.getElementById("tipoUr"),oCata.tipos);

	llenaComboCveDes(document.getElementById("cveAi"),oCata.ais);

	llenaComboCveDes(document.getElementById("cveScta"),oCata.sctas);

	llenaComboCveDes(document.getElementById("cvePp"),oCata.pps);

	llenaComboCveDes(document.getElementById("cveSpg"),oCata.spgs);

	llenaComboCveDes(document.getElementById("cvePy"),oCata.pys);
}
// ________________________________________________________________________
function construir_Where1() { // Creo que no es necesario filtrar por los selects, solo por el input de búsqueda
    // Solo selects con data-campo
    const selects = Array.from(document.querySelectorAll("select[data-campo]"));

    let where = "";
    let esPrimero = true;

    selects.forEach(sel => {
        const campo = sel.dataset.campo;
        const valor = sel.value;

        if (valor) {
            const cAnd = esPrimero ? "" : " AND ";
            where += `${cAnd}${campo}='${valor}'`;
            esPrimero = false;
        }
    });

    return where;
}
// ________________________________________________________________________
const Salida_preCombi=()=>{
	let cVal		= valorDeObjeto("selOpe",false);
	let campoBus	= valorDeObjeto("aCamSel",false);
	let valSel 		= valorDeObjeto("txtBuscar",false);

	if (cVal && cVal!==""){
		let cWhere = construir_Where1(); // Darle mayor peso a la selección de combos
		if (cWhere===""){ // como pueden chocar ya que aCamSel tiene los mismos parametros de busqueda que los combos, 
			// solo ver si txtBuscar tiene información
			
			if (campoBus && campoBus!==""){
				
				if (valSel && valSel!==""){
					cWhere = " " + campoBus + " = '" + valSel + "' ";
				}
			}
		}else{
			if (campoBus==="a.activo"){ // es la única que no esta en los combos superiores
				if (valSel && valSel!=="" && (valSel==="S" || valSel==="N" ) ){
					cWhere = cWhere + " and " +campoBus + " = '" + valSel + "' ";
				}
			}

		}
		aParametros = {
			opcion	: "genera_Salida",
			salida	: cVal,
			where	: cWhere
		}
		conectayEjecutaPost(aParametros,cPhp);
	}
}
// ________________________________________________________________________