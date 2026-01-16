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
var gTabla	  	= "tblCapturaEstru";				// Tabla HTML que se esta visualizando
var gForma	  	= "frmCapturaEstru";
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
var gaCorreo	= null;							// Guardara Correo de envio a presupuesto, a contabilidad, usuario del correo genérico, contraseña del correo genérico
// ________________________________________________________________________
window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a este JS
	// Se obtiene el nombre del archivo que lo invoca
	var loc     = window.location;
    var cHtml 	= loc.pathname.substring(loc.pathname.lastIndexOf('/') + 1);
	switch(cHtml){
		case "P_Cuentas_04_02CtasCaptura.php":
			//efectoBotones(() => CargaValidar("NO")); // agrega un escucha al input file para que ponga el nombre del archivo que se cargo y le paso la funció a ejecutar cuando ubique el archivo de carga
			traeCatUrCtas();
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
			aIds = []; // Para hacer el llenado de los HTML al dar clic en la tabla HTML
			pintarTablaHTMLEscucha(gTabla,vRes.registros,vRes.parametros);
			generarPaginador(vRes.totalPaginas, vRes.parametros);
		break;
		// ______________________________
		//case "trae Cat UrCtas":
		//	llenaDatosUrs(vRes);
		//break;
		// ______________________________
		case "validaEstructura":
			cEdo = vRes.aXls[0][9];
			if (cEdo.substring(0,10)==="Estructura"){
				gEstructura ={
					cIne 	: vRes.aXls[0][0],
					cUr		: vRes.aXls[0][1],
					cCta	: vRes.aXls[0][2],
					cScta	: vRes.aXls[0][3],
					cAi		: vRes.aXls[0][4],
					cPp		: vRes.aXls[0][5],
					cSpg	: vRes.aXls[0][6],
					cPy		: vRes.aXls[0][7],
					cPtda	: vRes.aXls[0][8],
					cEdo    : vRes.aXls[0][9]
				};
				agregarEstructuraATabla(gEstructura);
			}else{
				mandaMensaje(cEdo);
			}
		break;
		// ______________________________
		case "trae_CatUrCtas":
			llenaDatosUrs(vRes);
			llenaComboCveDes(document.getElementById("cveUr"),vRes.urs,false);
			llenaComboCveDes(document.getElementById("cveCta"),vRes.cuentas);

		break;
		// ______________________________
		// ______________________________

		// ______________________________

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
		case "validaEstructura":
		break;
		// ______________________________
		// ______________________________
		// ______________________________
		// ______________________________
		default:
		break;
	}
}
// _______________________________________________________________
function traeCatUrCtas(){
	aParametros ={
		opcion: "trae_CatUrCtas"
	};
	conectayEjecutaPost(aParametros,cPhp);
}
// _______________________________________________________________
function llenaDatosUrs(vRes){
	gUrIni		= vRes.urIni;
	gUrFin		= vRes.urFin;
	gUrLis		= vRes.urLis;
	gUrUsu		= vRes.urUsu;
	gUrlCtas	= vRes.urlCtas;
	gUrlPys		= vRes.urlPys;
	gValidaPY	= vRes.validaPy;
	gaCorreo    = {
		correoConta : vRes.CorreoConta,
		correoPto	: vRes.CorreoPto,
		correoUsu	: vRes.CorreoUsu,
		correoPass	: vRes.CorreoPass
	}
}
// _______________________________________________________________
function ValidarCaptura(cOpc='NO'){
	if (faltanDatos()){
		return false;
	}
	if (existeEstructuraEnTabla(gEstructura)) {
	    mandaMensaje("La estructura ya fue capturada");
	    return false;
	} 

	aParametros = {
		opcion		: "validaEstructura",
		urlCtas 	: gUrlCtas,
		urlPys		: gUrlPys,
		validaPY	: gValidaPY,
		estructura	: gEstructura
	};

	conectayEjecutaPost(aParametros,cPhp);


    // agregarEstructuraATabla(gEstructura);

}
// _______________________________________________________________
function faltanDatos(){
	gEstructura = null;
	aCampos = ["cveUr","cveCta","subcuenta","clvai","clvpp","clvspg","clvpy","clvpar"];
	aLong	= [4,5,5,3,4,3,7,5];
	aVal    = [];
	aVal.push("INE");
	i = 0 ;
	for (const campo of aCampos) {
		cVal = valorDeObjeto(campo);
		if (cVal==null){
			return true;
		}
		cVal = cVal.trim();
		if (cVal.length!=aLong[i]){
			FocoEn(campo);
			mandaMensaje(campo +" debe tener "+aLong[i]+ " caracteres");
			FocoEn(campo);
			return true;
		}
		aVal.push(cVal);i++;
	}
	gEstructura ={
		cIne 	: aVal[0],
		cUr		: aVal[1],
		cCta	: aVal[2],
		cScta	: aVal[3],
		cAi		: aVal[4],
		cPp		: aVal[5],
		cSpg	: aVal[6],
		cPy		: aVal[7],
		cPtda	: aVal[8],
		cEdo    : ""
	}
	return false;

}
// _______________________________________________________________
function existeEstructuraEnTabla(g) {
    const keys = ["cIne","cUr","cCta","cScta","cAi","cPp","cSpg","cPy","cPtda"];
    const filas = document.querySelectorAll("#cuerpo tr");

    return Array.from(filas).some(fila => {
        const celdas = fila.querySelectorAll("td");
        return keys.every((key, i) =>
            celdas[i].textContent.trim() === String(g[key])
        );
    });
}
// _______________________________________________________________
function agregarEstructuraATabla(g) {
    const tbody = document.getElementById("cuerpo");
    const tr = document.createElement("tr");

    tr.innerHTML = `
        <td>${g.cIne}</td>
        <td>${g.cUr}</td>
        <td>${g.cCta}</td>
        <td>${g.cScta}</td>
        <td>${g.cAi}</td>
        <td>${g.cPp}</td>
        <td>${g.cSpg}</td>
        <td>${g.cPy}</td>
        <td>${g.cPtda}</td>
        <td>${g.cEdo}</td>
    `;

    tbody.appendChild(tr);
}
// _______________________________________________________________
// _______________________________________________________________
// _______________________________________________________________
// _______________________________________________________________
// _______________________________________________________________
// _______________________________________________________________
// _______________________________________________________________