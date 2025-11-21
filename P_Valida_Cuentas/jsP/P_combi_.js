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
var cPhp      	= "P_Combi_.php";			// En este php estarán las funciones que se invocaran desde este JS
var cPhpBusca	= "P_Busca_Pagina_.php";
var gTabla	  	= "tblCombiVal";			// Tabla HTML que se esta visualizando
var gForma	  	= "frmCombiVal";
var gPagina		= 1; 
// ________________________________________________________________________
window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a este JS
	// Se obtiene el nombre del archivo que lo invoca
	var loc     = window.location;
    var cHtml 	= loc.pathname.substring(loc.pathname.lastIndexOf('/') + 1);

	switch(cHtml){
		case "P_Cuentas_02_03CombiM.php":
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
			aIds = ["UrIni","AiIni","SctaIni","PpIni","SpgIni","PyIni"]
			pintarTablaHTMLEscucha("tblCombiVal",vRes.registros,vRes.parametros,aIds);
			generarPaginador(vRes.totalPaginas, vRes.parametros);
		break;
		// ______________________________
		case "cargaCatalogos":
			llenaCombos(vRes);
			cargaCombinaciones(); // llama a buscaYPagina
		break;
		// ______________________________
		case "cargar_PtoAuto":
			cargaCombinaciones(); // llama a buscaYPagina
		break;
		// ______________________________
		case "genera_Salida":
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
		break;
		// ______________________________
	}
}
// _______________________FUNCIONES GENERALES ____________________________________
// ________________________________________________________________________
// ________________________________________________________________________
const cargaCatalogos=()=>{
	aParametros ={
		opcion:"cargaCatalogos"
	}
	conectayEjecutaPost(aParametros,"U_Cargas_.php");

}
// ________________________________________________________________________
const cargaCombinaciones=(cPagina=1)=>{
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
    	tablaPos    : "combinaciones a",
    	//campos      : ["a.clvpy","a.geografico","a.activo","a.despy"],
	    campos		: [
				        { nombre: "a.clvcos"	, tipo: "C" },
				        { nombre: "a.clvai"		, tipo: "C" },
				        { nombre: "a.clvscta"	, tipo: "C" },
				        { nombre: "a.clvpp"  	, tipo: "C" },
				        { nombre: "a.clvspg"  	, tipo: "C" },
				        { nombre: "a.clvpy"  	, tipo: "C" },
				        { nombre: "a.activo"  	, tipo: "C" },
				        { nombre: "a.usuario_id", tipo: "C" },
				        { nombre: "a.horafecha" , tipo: "C" }
		    		  ],
    	scroll      : [],
    	maxscroll   : 90,
    	orden       : ["ORDER BY a.clvcos ASC, a.clvai ASC, a.clvscta ASC, a.clvpp ASC, a.clvspg ASC, a.clvpy ASC "],
    	registros   : cRegistros,              	// número de registros por página
    	pagina      : cPagina,               	// página seleccionada
    	campoSel    : campoBus,        			// opción seleccionada del select
    	valSel      : valSel,          			// texto del input
    	join        : construirWhere() 			// "a.id=b.id" si es necesario
	};
	conectayEjecutaPost(aParametros,cPhpBusca);
}
// ________________________________________________________________________
function llenaCombos(vRes){
	oCata = vRes.cata
	llenaComboCveDes(document.getElementById("UrIni"),oCata.urs);
	llenaComboCveDes(document.getElementById("UrFin"),oCata.urs);

	llenaComboCveDes(document.getElementById("AiIni"),oCata.ais);
	llenaComboCveDes(document.getElementById("AiFin"),oCata.ais);

	llenaComboCveDes(document.getElementById("SctaIni"),oCata.sctas);
	llenaComboCveDes(document.getElementById("SctaFin"),oCata.sctas);

	llenaComboCveDes(document.getElementById("PpIni"),oCata.pps);
	llenaComboCveDes(document.getElementById("PpFin"),oCata.pps);

	llenaComboCveDes(document.getElementById("SpgIni"),oCata.spgs);
	llenaComboCveDes(document.getElementById("SpgFin"),oCata.spgs);

	llenaComboCveDes(document.getElementById("PyIni"),oCata.pys);
	llenaComboCveDes(document.getElementById("PyFin"),oCata.pys);

}
// ________________________________________________________________________
function construirWhere() {
    // Tomamos todos los selects que tienen data-campo
    const selects = Array.from(document.querySelectorAll("select[data-campo]"));

    // Agrupamos por campo (clvcos, clvai, clvscta, etc.)
    const grupos = {}; 

    selects.forEach(sel => {
        const campo = sel.dataset.campo;
        if (!grupos[campo]) grupos[campo] = { ini: null, fin: null };

        if (sel.id.endsWith("Ini")) grupos[campo].ini = sel.value;
        else if (sel.id.endsWith("Fin")) grupos[campo].fin = sel.value;
    });

    // Construimos el WHERE
    let where = "";
    let esPrimero = true;

    for (const campo in grupos) {
        const { ini, fin } = grupos[campo];
        where += filtro(campo, ini, fin, esPrimero);
        if (ini || fin) esPrimero = false;
    }

    return where;
}
// ________________________________________________________________________
function filtro(campo, vIni, vFin, esPrimero) {
    let cAnd = esPrimero ? "" : " AND ";

    if (!vIni && !vFin) return "";

    if (vIni && !vFin) return `${cAnd}${campo}='${vIni}'`;
    if (!vIni && vFin) return `${cAnd}${campo}='${vFin}'`;

    if (vIni === vFin) return `${cAnd}${campo}='${vIni}'`;

    return (vIni < vFin)
        ? `${cAnd}${campo}>='${vIni}' AND ${campo}<='${vFin}'`
        : `${cAnd}${campo}>='${vFin}' AND ${campo}<='${vIni}'`;
}
// ________________________________________________________________________
function cargarPtoAuto(){
	esperaRespuesta(`¿Desea integrar Ur-Scta-Ai-Pp-Spg-Py del Autorizado a las combinaciones válidas?`).then((respuesta) => {
		aParametros = {
			opcion		: "cargar_PtoAuto",
		};
		conectayEjecutaPost(aParametros,cPhp);

	});
}
// ________________________________________________________________________
const generaSalida=()=>{
	let cVal		= valorDeObjeto("selOpe",false);
	let campoBus	= valorDeObjeto("aCamSel",false);
	let valSel 		= valorDeObjeto("txtBuscar",false);

	if (cVal && cVal!==""){
		let cWhere = construirWhere(); // Darle mayor peso a la selección de combos
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
