/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Autor   		: Miguel Ángel Bolaños Guillén
 * Sistema 		: Sistema de Validación de cuentas
 * Fecha   		: Noviembre 2025
 * Descripción 	: Carga estructuras de archivo XLS
 *                Paso a PHP 8.03
 *                	
 * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*/
var cPhp      	= "P_CargaEstru_.php";			// En este php estarán las funciones que se invocaran desde este JS
var cPhpBusca	= "P_Busca_Pagina_.php";
var gTabla	  	= "tblCargaEstru";				// Tabla HTML que se esta visualizando
var gForma	  	= "frmCargaEstru";
var gPagina		= 1; 
var gConfigura  = null;							// select id,nombre,valor,tipo from configuracion
var gUrIni		= "";							// Urs permitidas 
var gUrFin		= "";
var gUrLis		= "";
var gUrUsu		= "";
var gUrlCtas	= "";							// url para validar via soap una estructura
var gUrlPys		= "";							// url para validar via sopa un proyecto
var gValidaPY	= "";							// 'S' si se requiere validar el proyecto( o si es geográfico)??
var gEstructuras= null;							// Para guardar las estructuras que se enviarán al SIGA
var gaCorreo	= null;							// Guardara Correo de envio a presupuesto, a contabilidad, usuario del correo genérico, contraseña del correo genérico
// ________________________________________________________________________
window.onload = function () {		// Función que se ejecuta al cargar la página HTML que invoca a este JS
	// Se obtiene el nombre del archivo que lo invoca
	var loc     = window.location;
    var cHtml 	= loc.pathname.substring(loc.pathname.lastIndexOf('/') + 1);
	switch(cHtml){
		case "P_Cuentas_04_01CtasCarga.php":
			efectoBotones(() => CargaValidar("NO")); // agrega un escucha al input file para que ponga el nombre del archivo que se cargo y le paso la funció a ejecutar cuando ubique el archivo de carga
			traeUrIniUrFin();
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
		case "traer_UrIni_UrFin":
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
		break;
		// ______________________________
		case "validarCarga":
			poblarTablaCargaEstructuras(vRes.aXls,"NP");
		break;
		// ______________________________
		case "EnviarEstructuras":
			limpiar_Boton_Input();
		break;
		// ______________________________
		case "ReEnviaEstructuras":
			limpiar_Boton_Input();
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
		case 'Configura_Salida':
		break;
		// ______________________________
		case "EnviarEstructuras":
			limpiar_Boton_Input();
		break;
		// ______________________________
		case "ReEnviaEstructuras":
			limpiar_Boton_Input();
		break;
		// ______________________________
		// ______________________________
		// ______________________________
		default:
		break;
	}
}
// _______________________FUNCIONES GENERALES _____________________________
async function CargaValidar(cWs){
	// Leer y guardar en un arreglo el contenido del archivo XLS
	loader('flex');
	lColExtra	= true;
	numColumnas = 9; // No se incluye la Columna extra para el Estado
	aEnca   	= ["INE","UR","CUENTA"]; // alguno de los posibles encabezados
	//              INE    UR     CUENTA  SCTA    AI     PP    SPG    PY      PTDA
	aReglas 	= ["SL" , "SLN" , "SN" , "SLN" , "SN" , "SLN", "SN" , "SLN" , "SN" ]; // SoloLetras, SoloLetrasNumeros, SoloNumeros

	aXlsOri		= await leerExcelDesdeInput("ArchivoCarga_file", aEnca, aReglas , lColExtra,numColumnas);
	if (aXlsOri==null){
		return false;
	}
	// Quitar duplicados
	const aXls = Array.from(
	  new Set(aXlsOri.map(row => JSON.stringify(row)))
	).map(row => JSON.parse(row));
	// - Verificar rango de URS permitidos
	aXls.forEach((xls) => {
		cUr	 = xls[1]; // la Ur
		$lOk = false;
		if ( (cUr>=gUrIni && cUr<=gUrFin) || ( gUrLis && gUrLis.split(",").includes(cUr) ) ){

		}else{
			xls[9] = `NP Rango de Urs no permitido ${cUr} -> { [${gUrIni}, ${gUrFin}]  ${gUrLis} } `;
		}
	});
	//console.log("Arreglo XLS",aXls);
	poblarTablaCargaEstructuras(aXls,"NP");

	aParametros = {
		opcion	: "validarCarga",
		aXls	: aXls,
		urlCtas : gUrlCtas,
		urlPys	: gUrlPys,
		validaPY: gValidaPY,
		cWs		: cWs
	}
	//console.log(aParametros);
	conectayEjecutaPost(aParametros,cPhp)
}
// ________________________________________________________________________
function traeUrIniUrFin(){
	aParametros = {
		opcion : "traer_UrIni_UrFin"
	}
	conectayEjecutaPost(aParametros,cPhp);
}
// ________________________________________________________________________
function CargaEnviar(){
	if (SiNoSeCargoArchivoXls()){
		return false;
	}
	if (SiNoHayDatosaEnviar()){
		mandaMensaje("No hay estructuras válidas o a revisar para enviar.");// llena gEstructuras con Estructuras validas o Estructuras a Revisar
		limpiarInputFile();
		return false
	}
	loader('flex');
	aParametros = {
		opcion : "EnviarEstructuras",
		correo : gaCorreo,
		datos  : gEstructuras,
		urUsu  : gUrUsu

	}
	conectayEjecutaPost(aParametros,cPhp);
}
// ________________________________________________________________________
function ReEnviar(){
	if (SiNoSeCargoArchivoXls()){
		return false;
	}
	if (SiNoHayDatosaEnviar("NP Ya fue solicitada anteriormente")){ // llena gEstructuras con Estructuras con la leyenda "NP Ya fur soli...."
		mandaMensaje("No hay estructuras enviadas anteriormente.");
		return false
	}
	loader('flex');
	aParametros = {
		opcion : "ReEnviaEstructuras",
		correo : gaCorreo,
		datos  : gEstructuras,
		urUsu  : gUrUsu

	}
	conectayEjecutaPost(aParametros,cPhp);
}
// ________________________________________________________________________
function SiNoSeCargoArchivoXls(){
	
	const vArchivo 	= document.getElementById("ArchivoCarga_file").files[0];
	if (vArchivo==undefined){
		mandaMensaje("Se requiere haber procesado primero un archivo XLS ");
		return true;
	}
	const filas  = document.getElementById("cuerpo").rows;
	if (filas.length==0){ // ya se cargo el archivo XLS, pero no se ha dado clic en Revisar (aunque al cargarlo se revisa en automático)
		mandaMensaje("De clic en el botón de VALIDAR ");
		return true;
	}
	return false;
}
// ________________________________________________________________________
function SiNoHayDatosaEnviar(cStatus="Estructura") {

	gEstructuras	= [];
	const aCol 		= ["ine", "clvcos", "mayor", "subcuenta", "clvai", "clvpp", "clvspg", "clvpy", "clvpar", "estado"];
    let lNohayDatos = true;
    const filas		= document.getElementById("cuerpo").rows; // Body de la tabla HTML

    for (const fila of filas) {
        const cEstado = fila.cells[9];  // celda 10

        if (!cEstado) continue;

        const texto = cEstado.textContent.trim();

        if (texto.startsWith(cStatus)) {
            lNohayDatos = false;
            // Convertimos la fila en un objeto con propiedades definidas en aCol
            const filaObj = {};
            Array.from(fila.cells).forEach((celda, idx) => {
                const nombreProp = aCol[idx] || `columna${idx}`;
                filaObj[nombreProp] = celda.textContent.trim();
            });

            gEstructuras.push(filaObj);
            //  break;   // ← rompe el bucle inmediatamente
        }
    }

    return lNohayDatos;
}
// ________________________________________________________________________
function limpiar_Boton_Input(){
	limpiarInputFile();
	efectoBotones(() => CargaValidar("NO"));
}
// ________________________________________________________________________