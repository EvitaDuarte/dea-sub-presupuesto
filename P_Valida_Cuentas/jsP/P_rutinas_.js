// * * * * * * * * * * * * * * * * * * * * * * * * * 
// * Autor   : Miguel Ángel Bolaños Guillén        *
// * Sistema : Sistema de Operación Bancaria Web   *
// * Fecha   : Noviembre 2025                      *
// * Descripción : Rutinas de propósito general    * 
// *                                               *
// * * * * * * * * * * * * * * * * * * * * * * * * *

var conexion1;           // Variable global para la conexión
var una_vez   = true;

// Adiciono evento click a las X de los cuadros de dialogo, para que las cierre, no se puede repetir los ids
const botones = [
    { xClose: document.querySelector("#dialogo_close"),  dialogo: document.querySelector("#cajaMensaje") },
    { xClose: document.querySelector("#dialogo_close1"), dialogo: document.querySelector("#cajaRespuesta") },
    { xClose: document.querySelector("#dialogo_close2"), dialogo: document.querySelector("#cajaCancela") },
    { xClose: document.querySelector("#dialogo_close3"), dialogo: document.querySelector("#cajaCancelaLayOut") },
    { xClose: document.querySelector("#dialogo_close4"), dialogo: document.querySelector("#cajaImprimeCheque") },
    { xClose: document.querySelector("#dialogo_close5"), dialogo: document.querySelector("#cajaReporteReintegros") }
];

botones.forEach((item) => {
    if (item.xClose) { // Solo si encuentra la X 
        item.xClose.addEventListener("click", () => {
            item.dialogo.close();
        });
    }
});
// ______________________________________________________________________________
const expresiones = {
    soloLetras                  : { regex: /^[A-Za-záéíñóúÁÉÍÓÚüÜÑ\s\.]+$/                              , mensaje : "solo puede llevar letras y espacios" },
    letrasNumerosSeparadores    : { regex: /^[A-Za-záéíóúñÑÁÉÍÓÚ0-9.\-_,\/\s]+$/                        , mensaje : "solo puede llevar letras, números, punto, -, _, / y espacios" },
    soloLetrasNumerosEspacios   : { regex: /^[a-zA-ZÀ-ÖØ-öø-ÿ][a-zA-ZÀ-ÖØ-öø-ÿ0-9\s]*$/                 , mensaje : "solo puede llevar letras, números y espacios"},
    soloDominio                 : { regex: /^[a-z]+\.[a-z]+$/                                           , mensaje : "solo debe ser el formato nombre.apellido en minúsculas"}, 
    soloNumeros                 : { regex: /^[0-9]+$/                                                   , mensaje : "solo puede llevar números"},
    soloUr                      : { regex: /^[a-zA-Z]{2}[a-zA-Z0-9]{2}$/                                , mensaje : "solo puede tener dos letras iniciales y letras y números en los dos últimos caracteres"},
    soloImportes                : { regex: /^-?\d{1,3}(?:,\d{3})*(?:\.\d{1,2})?$|^\d+(?:\.\d{1,2})?$/   , mensaje : "solo puede llevar números,comas,punto decimal"},
    soloImportesPositivos       : { regex: /^\d{1,3}(?:,\d{3})*(?:\.\d{1,2})?$|^\d+(?:\.\d{1,2})?$/     , mensaje : "solo números, comas(3), punto decimal, sin signo, dos decimales"},
    soloCuenta                  : { regex: /^[a-zA-ZñÑ0-9-() ]+$/                                       , mensaje : "solo puede llevar Letras - Numeros ()"},
    soloSiglas                  : { regex: /^[a-zA-Z]+[0-9]+-$/                                         , mensaje : "solo puede llevar Letras seguido de Números y Guion final"},
    soloNumerosGuion            : { regex: /^[0-9-]+$/                                                  , mensaje : "solo puede llevar Números y guion"},
    soloLetrasNumerosGuion      : { regex: /^[A-Za-z0-9\- ]+$/                                          , mensaje : "solo puede llevar Letras, Números y guion"},
    soloLetrasNumeros           : { regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ ]+$/                                , mensaje : "solo puede llevar Letras, Números y espacios"},
    soloLetrasNumerosSinEspacios: { regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ]+$/                                 , mensaje : "solo puede llevar Letras, Números sin espacios"},
    exclusivoLetras             : { regex: /^[a-zA-Z]+$/                                                , mensaje : "solo puede llevar Letras, sin espacios"},
    exclusivoUR                 : { regex: /^[A-Za-z]{2}\d{2}$/                                         , mensaje : "Por favor, ingresa dos letras seguidas de dos números. para UR"}
};
// ______________________________________________________________________________
function validaValor(inputHTML) {

    const idExpresion = inputHTML.dataset.exp;
    const lValida     = inputHTML.dataset.valida === "true"; // <-- conversión a booleano

    const expresion = expresiones[idExpresion];

    let cVar    = inputHTML.value.trim();
    let cCampo  = inputHTML.title;

    // Validar campo vacío
    if (cVar === "") {
        if (lValida) {
            FocoEnObjeto(inputHTML);
            mandaMensaje(cCampo + ": debe de llevar información");
            return false;
        }
        return true;
    }

    // Validar con regex
    if (expresion.regex.test(cVar)) {
        return true;
    } else {
        FocoEnObjeto(inputHTML);
        mandaMensaje(`${cCampo}: ${expresion.mensaje}`)
        limpiaObjeto(inputHTML.id);
        return false;
    }
}

// ______________________________________________________________________________

// Verifica que cVar solo contenga letras mayusculas, minusculas, acentos y espacios
// _________________________________
// _________________________________
// function sololetras(idHtml,lValida=false){
// 	let expresionRegular = /^[A-Za-záéíñóúÁÉÍÓÚüÜÑ\s\.]+$/;
//     let cVar    = idHtml.value.trim();
//     let cCampo  = idHtml.title;
//     if (cVar===""){
//         if (lValida){
//             FocoEn(idHtml);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(idHtml);
//             return false;
//         }
//         return true;
//     }
// 	if (expresionRegular.test(cVar)) {
//     	return true;
//   	} else {
//         FocoEn(idHtml);
//     	mandaMensaje(cCampo +": solo puede llevar letras y espacios");
//         FocoEn(idHtml);
//     	return false;
//   	}
// }
// _____________________________________________________________________________________________
// function soloLetrasNumerosSeparadores(cVar,cCampo,cHTML,lValida=false){
//     var expresionRegular = /^[[a-zA-ZáéíóúñÑÁÉÍÓÚ][a-zA-ZáéíóúñÑÁÉÍÓÚ0-9.\-\,\_\/\s]*$/;
//     if (cVar.trim()===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     if (expresionRegular.test(cVar)) {
//         return true;
//     } else {
//         FocoEn(cHTML);
//         mandaMensaje(cCampo +": solo puede llevar letras, números, punto, -, _, / y espacios");
//         FocoEn(cHTML)
//         return false;
//     }

// }
// _____________________________________________________________________________________________
// function soloLetrasNumerosEspacios(cVar,cCampo,cHTML, lValida=false){
//     var expresionRegular = /^[a-zA-ZÀ-ÖØ-öø-ÿ][a-zA-ZÀ-ÖØ-öø-ÿ0-9\s]*$/;
//     // cVar ya debe de venir con trim, uppercase, etc por eso no se puede obtener usando cHTML
//     if (cVar.trim()===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     if (expresionRegular.test(cVar)) {
//         //con sole.log("La cadena contiene solo letras mayúsculas y minúsculas.");
//         return true;
//     } else {
//         FocoEn(cHTML);
//         mandaMensaje(cCampo +": solo puede llevar letras, números y espacios");
//         FocoEn(cHTML);
//         return false;
//     }

// }
// _________________________________
// function soloDominio(cVar,cCampo,cHTML,lValida=false){ 
// 	// Verifica si cVar solo tenga letras minusculas y un punto decimal 
// 	var regex =  /^[a-z]+\.[a-z]+$/;  // /^[a-z]+\.$/;
//     if (cVar.trim()===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     // Verificar si la variable cumple con la expresión regular && texto.trim() !== ""
//     if (regex.test(cVar) && cVar.trim()!="" ) {
//         //con sole.log("La variable cumple con los requisitos.");
//         return true;
//     } else {
//         //con sole.log("La variable no cumple con los requisitos.");
//         FocoEn(cHTML);
//         mandaMensaje(cCampo+ ": debe ser del formato nombre.apellido en minúsculas");
//         FocoEn(cHTML);
//         return false;
//     }
// }
// _________________________________
// function soloNumeros(cVar,cCampo,cHTML,lValida=false){
//     var patron = /^[0-9]+$/;
//     if (cVar.trim()===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     if (patron.test(cVar)){
//         return true;
//     }else{
//         FocoEn(cHTML);
//         mandaMensaje(cCampo+": solo puede llevar números");
//         FocoEn(cHTML);
//         return false;
//     }
// }
// _________________________________
// function soloUR(cCampo,cHTML,lValida=false){
//     // Expresión regular para verificar el formato
//     var patron = /^[a-zA-Z]{2}[a-zA-Z0-9]{2}$/;
//     cObj = document.getElementById(cHTML);
//     cVar = cObj.value.trim();
//     if (cVar.trim()===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     // Verificar si el valor cumple con la expresión regular
//     if (patron.test(cVar)) {
//         return true
//     } else {
//         cObj.focus();
//         cObj.click();
//         mandaMensaje(cCampo+": solo puede tener dos letras iniciales y letras y números en los dos últimos caracteres");
//         return false;
//     }
// }
// _________________________________
// function soloImportes(cCampo,cHTML,lValida=false){
//     //var patron = /^-?\d{1,3}(\d{3})*(,\d+)?(\.\d+)?$/;
//     var patron =  /^-?\d{1,3}(?:,\d{3})*(?:\.\d{1,2})?$|^\d+(?:\.\d{1,2})?$/;
//     cObj = document.getElementById(cHTML);
//     cVar = cObj.value.trim();
//     if (cVar.trim()===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     if (patron.test(cVar)){
//         return true;
//     }else{
//         cObj.focus();
//         cObj.click();
//         mandaMensaje(cCampo+": solo puede llevar números,comas,punto decimal");
//         return false;
//     }
// }
// _________________________________
// _________________________________
// function soloImportesPositivos(input, lValida = false) {

//     //var patron = /^(?!0\d)(\d{1,3}(,\d{3})*|\d+)(\.\d{1,2})?$/; // Expresión regular
//     //var patron = /^(?!0\d)(\d{1,3}(,\d{3})*|\d+)(\.\d{1,2})?$/;
//     //var numero = input.value.trim().replace(/,/g, ''); // Eliminar comas
//     // Funciona tanto si el valor trae comas o no , tambien valida que las comas esten bien posicionadas
//     var patron =  /^\d{1,3}(?:,\d{3})*(?:\.\d{1,2})?$|^\d+(?:\.\d{1,2})?$/;
//     var numero = input.value.trim(); 
//     // Verificar si el campo está vacío
//     if (numero === "") {
//         if (lValida) {
//             FocoEnObjeto(input);
//             mandaMensaje(input.getAttribute("data-info") + ": debe de llevar información");
//             FocoEnObjeto(input);
//             return false;
//         }
//         return true;
//     }

//     // Comprobar que el número cumple con el patrón
//     if (!patron.test(numero)) {
//         FocoEnObjeto(input);
//         mandaMensaje(input.getAttribute("data-info") + ": solo números, comas(3), punto decimal, sin signo, dos decimales");
//         FocoEnObjeto(input);
//         return false;
//     }

//     // Si no trae comas, agregarlas
//     // Paso 1: quitar comas para convertir a número
//     var sinComas = numero.replace(/,/g, '');

//     // Paso 2: convertir a número flotante y luego formatear con comas
//     var numeroFloat = parseFloat(sinComas);

//     // Verifica que no sea NaN
//     if (!isNaN(numeroFloat)) {
//         // Formatea con separadores de miles y máximo 2 decimales
//         input.value = numeroFloat.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
//     }

//     return true;
// }

// _________________________________
// function soloCuenta(cTitulo,cHTML,lValida=false){
//     //var patron = /^[a-zA-ZñÑ\s]+(?:\s-\s(?:\d{4}|\(\d{4}-\d{4}\)))?$/;
//     var patron = /^[a-zA-ZñÑ0-9-() ]+$/;
//     cObj = document.getElementById(cHTML);
//     cVar = cObj.value.trim();
//     if (cVar===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cTitulo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     if (patron.test(cVar)){
//         return true;
//     }else{
//         cObj.focus();
//         cObj.click();
//         mandaMensaje(cTitulo+": solo puede llevar Letras - Numeros ()");
//         return false;
//     }
// }
// _________________________________
// function soloSiglas(cCampo,cHTML,lValida=false){
//     var patron = /^[a-zA-Z]+[0-9]+-$/;
//     cObj = document.getElementById(cHTML);
//     cVar = cObj.value.trim();
//     if (cVar===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     if (patron.test(cVar)){
//         return true;
//     }else{
//         cObj.focus();
//         cObj.click();
//         mandaMensaje(cCampo+": solo puede llevar Letras seguido de Números y Guion final");
//         return false;
//     }
// }
// _________________________________
function anioValido(cAnio){
    var fechaActual = new Date();
    var añoActual = fechaActual.getFullYear();
    return (cAnio<=añoActual && (añoActual-cAnio)<6 );
}
// _________________________________
// function soloNumerosGuion(cVar,cCampo,cHTML,lValida=false){
//   // Expresión regular que verifica que la variable tenga al menos un número o guion y no esté vacía
//     var patron = /^[0-9-]+$/;
//     if (cVar.trim()===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     if (patron.test(cVar.trim())){
//         return true
//     }else{
//         document.getElementById(cHTML).focus();
//         document.getElementById(cHTML).click();
//         mandaMensaje(cCampo+": solo puede llevar Números y guion");
//     }
//     return false;
// }
// _________________________________
// function soloLetrasNumerosGuion(cVar,cCampo,cHTML,lValida=false){
//   // Expresión regular que verifica que la variable tenga al menos un número o guion y no esté vacía
//   var patron = /^[A-Za-z0-9\- ]+$/;
//     if (cVar.trim()===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     if (patron.test(cVar.trim())){
//         return true;
//     }else{
//         document.getElementById(cHTML).focus();
//         document.getElementById(cHTML).click();
//         mandaMensaje(cCampo+": solo puede llevar Letras Números y guion");
//     }
//   return false;
// }
// _________________________________
// function function soloLetrasNumeros(idHtml,lValida=false){
//     // Expresión regular que verifica que la variable tenga al menos un número o guion y no esté vacía
//     let patron  = /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ ]+$/;
//     let cVar    = idHtml.value;
//     if (cVar.trim()===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     if (patron.test(cVar.trim())){
//         return true;
//     }else{
//         document.getElementById(cHTML).focus();
//         document.getElementById(cHTML).click();
//         mandaMensaje(cCampo+": solo puede llevar Letras Números y espacios");
//     }
//     return false;
// }
// _________________________________
// function exclusivoLetras(cCampo,cHTML,lValida=false){
//     var patron = /^[a-zA-Z]+$/
//     cObj = document.getElementById(cHTML);
//     cVar = cObj.value.trim();
//     if (cVar===""){
//         if (lValida){
//             FocoEn(cHTML);
//             mandaMensaje(cCampo+": debe de llevar información");
//             FocoEn(cHTML);
//             return false; 
//         }
//         return true;
//     }
//     if (patron.test(cVar)){
//         return true;
//     }else{
//         cObj.focus();
//         cObj.click();
//         mandaMensaje(cCampo+": solo puede llevar Letras");
//         return false;
//     }
// }
// _________________________________
// function exclusivoUR(input) {
//     var entrada = input.value.trim();

//     // Expresión regular que verifica dos letras seguidas de dos números
//     var patron = /^[A-Za-z]{2}\d{2}$/;

//     if (!patron.test(entrada)) {
//         input.focus();
//         input.click();
//         mandaMensaje("Por favor, ingresa dos letras seguidas de dos números. para UR");
//         // Limpiar el campo o realizar otras acciones según tus necesidades
//         //input.value = "";
//         // Devolver el enfoque al elemento input
//         input.focus();
//         input.click();
//     }
// }
// _________________________________
// function tieneValor(cVar,cCampo,cHTML){ // Pregunta si la variable tiene información
//     // Revisar si se usa para la validación en los botones
//     regreso = false;
//     if (cVar===null || cVar === undefined){           // Detecta nulo
//         document.getElementById(cHTML).focus();
//         document.getElementById(cHTML).click();
//         mandaMensaje("Se require un valor para "+cCampo)

//     }else if( cVar.trim()==="" ){                     // Detecta vacía
//         document.getElementById(cHTML).focus();
//         document.getElementById(cHTML).click();
//         mandaMensaje("Ingrese valor para "+cCampo)

//     }else{
//         regreso = true;
//     }
//     return regreso;
// }
// _________________________________
function seCapturo(oHTML){ // Pregunta si la variable tiene información
    regreso = false;
    try {
        const selectElement = document.getElementById(oHTML.id);
        cVar = selectElement.value.trim();
        if (cVar===""){
            selectElement.focus();
            selectElement.click();
            mandaMensaje("Se requiere capturar "+oHTML.nombre);
        }else{
            oHTML.valor = cVar
            regreso     = true;
        }
    } catch (error) {
        mandaMensaje("No se encuentra el elemento HTML ["+oHTML.id+"]");
    }
    return regreso;
}
// _________________________________________________________
function revisaFecha(oFecha){
    if (CadenaToFecha(oFecha.value)==null){
        FocoEn(oFecha.id)
        return false;
    }
    return true;
}
// _________________________________________________________
function verificaFecha(oFecha){
    cMensa = "";
    if( revisaFecha(oFecha) ){
        // console.log(dHoy); // dHoy debe ser una variable global que viene desde la fecha del servidor
        cFechaCap = fyyyyddmm(oFecha.value,'/');
        cFechaHoy = dHoy.replace(/-/g, '');
        if (cFechaCap>cFechaHoy){
            cMensa = "No se pueden solicitar fechas mayores a " + dHoy;
        }else{
            cAnio = cFechaCap.substring(0,4);
            if (cAnio<"2000"){
                cMensa = "No se tiene información del año " + cAnio
            }else{
                return true
            }
        }
        //oFecha.blur();
        //oFecha.focus();
        //oFecha.click();
        mandaMensaje(cMensa );
    }
    FocoEn(oFecha.id);
    oFecha.value = fddmmyyyy(dHoy,"-");
    return false;
}
// _________________________________________________________
function valoresPermitidos(cBusco,cValores,cCampo,cHTML){
    //con sole.log("["+cBusco+"]["+cValores+"]["+cValores.includes(cBusco)+"]");
    if ( tieneValor(cBusco,cCampo,cHTML) ){
        if (cValores.includes(cBusco.trim())) {
            return true
        } else {
            document.getElementById(cHTML).focus();
            document.getElementById(cHTML).click();
            mandaMensaje(cCampo + ": solo puede tener los valores "+cValores);

        }
    }
    return false;
}
// ____________________________________________________________________________
function NumerosComasDecimales(cId) {
    var inputElement = document.getElementById(cId);

    inputElement.addEventListener("blur", function() {
        validarContenido(this);
    });

    inputElement.addEventListener("input", function() {
        formatearContenido(this);
    });
}
// ____________________________________________________________________________
function validarContenido(inputElement) {
    var valor = inputElement.value;

    // Eliminar caracteres no deseados
    valor = valor.replace(/[^\d.,-]/g, '');

    // Verificar si hay un signo negativo al principio
    if (valor.indexOf('-') === 0) {
        valor = '-' + valor.replace(/-/g, ''); // Eliminar signos negativos adicionales
    }

    // Eliminar comas existentes
    valor = valor.replace(/,/g, '');

    // Agregar comas cada tres posiciones desde el final
    var partes = valor.split('.');
    partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    // Limitar a dos decimales
    if (partes.length > 1) {
        valor = partes.join('.');
        valor = valor.substring(0, valor.indexOf('.') + 3);
    }

    inputElement.value = valor;
}
// ____________________________________________________________________________
function formatearContenido(inputElement) {
    // Verificar el formato mientras se está escribiendo
    var valor = inputElement.value;

    // Eliminar caracteres no deseados
    valor = valor.replace(/[^\d.,-]/g, '');

    // Verificar si hay un signo negativo al principio
    if (valor.indexOf('-') === 0) {
        valor = '-' + valor.replace(/-/g, ''); // Eliminar signos negativos adicionales
    }

    // Eliminar comas existentes
    valor = valor.replace(/,/g, '');

    // Agregar comas cada tres posiciones desde el final
    var partes = valor.split('.');
    partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    // Limitar a dos decimales
    if (partes.length > 1) {
        valor = partes.join('.');
        valor = valor.substring(0, valor.indexOf('.') + 3);
    }

    inputElement.value = valor;
}
// ____________________________________________________________________________
// _________________________________
function conectayEjecuta__(aParametros,cPhp,aTabla){
    conexion1                    = new XMLHttpRequest();                 // Prepara conexión http
    conexion1.onreadystatechange = respuesta__;                          // La función JS que se invocara al terminar de ejecutar el php
    aParametros                  = JSON.stringify(aParametros);          // Convierte los datos a JSON
    // En el php realizara las operaciones solicitadas (Consultar,Adicionar,Modificar,Borrar)
    // De acuerdo a los parámetros contenidos en aParametros
    // En aParametros.opcion cPhp sabra que instrucciones ejecutara
    if (aTabla==null || aTabla==undefined){
        conexion1.open('PUT',"backP/" + cPhp + "?aDatos="+aParametros,true);  // Prepara llamada al archivo PHP
    }else{
        aTabla = JSON.stringify(aTabla);
        conexion1.open('PUT',"backP/" + cPhp + "?aDatos="+aParametros+"&aTabla="+aTabla,true); 
    }
    conexion1.send();                                                        // Envía datos al servidor
}
// ______________________________________________
function conectayEjecutaPost(aParametros, cPhp) {
    conexion1                    = new XMLHttpRequest();        // Prepara conexión http
    conexion1.onreadystatechange = respuesta__;                 // La función JS que se invocará al terminar de ejecutar el php
    aParametros                  = JSON.stringify(aParametros); // Convierte los datos a JSON

    conexion1.open('POST', "backP/" + cPhp, true); // Cambia PUT a POST y elimina los parámetros de la URL

    // Establece el encabezado para indicar que se envían datos JSON
    conexion1.setRequestHeader('Content-Type', 'application/json;charset=UTF-8;');

    conexion1.send(aParametros); // Envía datos al servidor en el cuerpo de la solicitud POST
}
// ______________________________________________ el api fetch es más moderno que XMLHttpRequest, en teoría no habría que hacer cambios
async function conectayEjecutaPostModerna(aParametros, cPhp, aTabla = null) {
    try {
        const payload = aParametros;

        const response = await fetch("backP/" + cPhp, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const text = await response.text();
        //console.debug("Respuesta",sanitizeLogOutput(text));

        // Luego, si quieres, intenta parsearlo:
        const data = JSON.parse(text); // Esto lanzará si no es JSON válido

        //const data = await response.json();

        // Mantienes la llamada a tu función que ya existe
        respuesta__(data);
    }catch (error) {
        loader('none');
        console.error("[Error de red o al procesar JSON:"  + "]", error);
    }
}
// ______________________________________________]
function conectayEjecutaPostMasModerna(aParametros, cPhp) {
    let payload = aParametros;

    fetch("backP/" + cPhp, {
        method: "POST",
        headers: {
            "Content-Type": "application/json;charset=UTF-8"
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error HTTP: " + response.status);
        }
        return response.json(); // Asumes que PHP devuelve JSON
    })
    .then(data => {
        respuesta__(data); // Igual que antes
    })
    .catch(error => {
        console.error("Error en la petición:", error);
    });
}

// ______________________________________________
function respuesta__(data){                                     // Respuesta del servidor, de ConectayEjecuta
    if (conexion1.readyState==4){     
        //console.log("Respuesta cruda[", conexion1.responseText,"]");                       // Indica si llego respuesta del servidor
        if (conexion1.responseText!==""){
            try{
                vRespuesta = JSON.parse(conexion1.responseText);
            }catch(error){
                mandaMensaje("Php Error "+conexion1.responseText);
                loader('none');
                return false;
            }
            if (vRespuesta.success==true){                      // EL Servidor ejecuto exitosamente la operación Solicitada
                cOpcion = vRespuesta.opcion;
                // actualiza información en el cliente
                procesarRespuesta__(vRespuesta);               // Esta rutina debe estar en el JS que invoca conectayEjecuta__
                setTimeout(function () {                       // Espera que se termine procesarRespuesta
                    if (vRespuesta.mensaje.trim() !== "") {    // Y ya despues lanza el al ert
                        mandaMensaje("("+vRespuesta.mensaje+") ....");
                    }
                }, 0);
            }else{                                                  // Se detectaron inconsistencias
                mandaMensaje("{"+vRespuesta.mensaje+"} ....");
                try{
                    procesarError__(vRespuesta);
                } catch (error){
                    mandaMensaje("No esta definida la funcion procesarError_ en el JS que llama a conectayEjecutaPost");
                }
            }
        }else{
            //console.log("La respuesta de PHP esta vacía", "[" + sanitizeLogOutput(conexion1.responseText) + "]");
            loader('none');
            mandaMensaje("Problemas en la codificación");
            return false;
        }
    }else{
        // mandaMensaje("Problemas en la conexión JS/Php"); // se hacen reintentos no debe llevar mandamensaje
    }
}
// ______________________________________________
function llenaCombo(select,aCombo,cValor=""){ // llena un HTML Select (combobox) a partir del arreglo aCombo
    // Limpiar opciones previas si las hay
    select.innerHTML = "";

    // Recorre el arreglo y crea opciones
    for (var i = 0; i < aCombo.length; i++) {
        aVal         = aCombo[i].split(",");
        var opcion   = document.createElement("option");
        opcion.text  = aVal[1].trim();  // Texto visible de la opción
        opcion.value = aVal[0].trim(); // Valor de la opción (puede ser diferente del texto visible)
        select.appendChild(opcion); // Agrega la opción al select
    }
    // Selecciona el valor según cValor
    if (cValor === "1" && select.options.length > 0) {
        select.value = select.options[1].value; // Primer valor
    } else if (cValor === "U" && select.options.length > 0) {
        select.value = select.options[select.options.length - 1].value; // Último valor
    }
}
// ______________________________________________
function llenaComboCveDes(select, aCombo) {
    // Limpiar el select
    select.innerHTML = "";

    // Opción por defecto
    const opcionDefault     = document.createElement("option");
    opcionDefault.text      = "Seleccione ...";
    opcionDefault.value     = "";
    select.appendChild(opcionDefault);

    // Llenar opciones
    for (let i = 0; i < aCombo.length; i++) {
        const item      = aCombo[i];
        const opcion    = document.createElement("option");

        if (item.descripcion) {
            // Si existe descripción, mostrar "clave - descripción"
            opcion.text = `${item.clave} - ${item.descripcion.trim()}`;
        } else {
            // Si no existe, solo la clave
            opcion.text = String(item.clave);
        }

        // Valor siempre es clave (o vacío si no existe)
        opcion.value = item.clave != null ? String(item.clave).trim() : "";

        select.appendChild(opcion);
    }
}

// ______________________________________________
function llenaComboCveNombre(select,aCombo){ // llena un HTML Select (combobox) a partir del arreglo aCombo
    // Recorre el arreglo y crea opciones
    select.innerHTML = "";
    var opcion   = document.createElement("option");
    opcion.text  = "Seleccione ..."  // Texto visible de la opción
    opcion.value = ""; // Valor de la opción (puede ser diferente del texto visible)
    select.appendChild(opcion);

    for (var i = 0; i < aCombo.length; i++) {
        var opcion   = document.createElement("option");
        opcion.text  = aCombo[i]["clave"] + " - " + aCombo[i]["descripcion"];  // Texto visible de la opción
        opcion.value = aCombo[i]["clave"].trim(); // Valor de la opción (puede ser diferente del texto visible)
        select.appendChild(opcion); // Agrega la opción al select
    }
}
// ______________________________________________
function limpiaTabla(table){
    if (table) {// Se elimina cualquier carga anterior
        // Elimina todas las filas (tr) del tbody
        while (table.firstChild) {
            table.removeChild(table.firstChild);
        }
    }
}
// ______________________________________________
Paginador = function(divPaginador, tabla, tamPagina){
    this.miDiv      = divPaginador;     //un DIV donde irán controles de paginación
    this.tabla      = tabla;            //la tabla a paginar
    this.tamPagina  = tamPagina;        //el tamaño de la página (filas por página)
    this.pagActual  = 1;                //asumiendo que se parte en página 1
    this.paginas    = Math.floor((this.tabla.rows.length - 1) / this.tamPagina); //¿?
 
    this.SetPagina = function(num){ // Establece el número de página
        if (num < 0 || num > this.paginas)
            return;
 
        this.pagActual = num;
        var min = 1 + (this.pagActual - 1) * this.tamPagina;
        var max = min + this.tamPagina - 1;
 
        for(var i = 1; i < this.tabla.rows.length; i++){
            if (i < min || i > max)
                this.tabla.rows[i].style.display = 'none';
            else
                this.tabla.rows[i].style.display = '';
        }
        this.miDiv.firstChild.rows[0].cells[1].innerHTML = this.pagActual;
    }
 
    this.Mostrar = function(){

        var divPadre = document.getElementById('tuDiv'); // Obtén el div padre por su ID

        eliminarHijosYContinuar(this.miDiv); // Llama a la función para ejecutarla


        //Crear la tabla
        var tblPaginador = document.createElement('table');
 
        //Agregar una fila a la tabla, al principio
        var fil = tblPaginador.insertRow(tblPaginador.rows.length);
        //Ahora, agregar a la fila las celdas que serán los controles
        // Boton Anterior
        var ant = fil.insertCell(fil.cells.length);
        ant.innerHTML = 'Anterior';
        ant.className = 'pag_btn'; //con eso le asigno un estilo
        var self = this;
        ant.onclick = function(){
            if (self.pagActual == 1)
                return;
            self.SetPagina(self.pagActual - 1);
        }
     
        // Número de Página
        var num = fil.insertCell(fil.cells.length);
        num.innerHTML = ''; //en rigor, aún no se el número de la página
        num.className = 'pag_num';
 
        // Botón Siguiente
        var sig = fil.insertCell(fil.cells.length);
        sig.innerHTML = 'Siguiente';
        sig.className = 'pag_btn';
        sig.onclick = function() {
            if (self.pagActual == self.paginas)
                return;
            self.SetPagina(self.pagActual + 1);
        }

        //Como ya tengo mi tabla, puedo agregarla al DIV de los controles
        this.miDiv.appendChild(tblPaginador);
        this.SetPagina(1); // Continúa con la siguiente instrucción después de que el bucle haya terminado
        //¿y esto por qué?
        if (this.tabla.rows.length - 1 > this.paginas * this.tamPagina)
            this.paginas = this.paginas + 1;
 
        this.SetPagina(this.pagActual);
    }
}
// ______________________________________________
async function eliminarHijosYContinuar(oDiv) {
  while (oDiv.firstChild) {
    oDiv.removeChild(oDiv.firstChild);
  }
  await new Promise(resolve => setTimeout(resolve, 0)); // Espera a que se ejecute la cola de tareas
}
// ______________________________________________
function CadenaToFecha(cCadena){
    const regexFecha = /^(\d{2})\/(\d{2})\/(\d{4})$/;
    const matches    = cCadena.match(regexFecha);
    let mensaje      = "La fecha no tiene el formato correcto DD/MM/YYYY ["+cCadena+"]";

    if (matches) {
        const dia = parseInt(matches[1], 10);  // Extraer el día como número
        const mes = parseInt(matches[2], 10);  // Extraer el mes como número
        const año = parseInt(matches[3], 10);  // Extraer el año como número

        // con sole.log("Dia["+dia+"] Mes["+mes+"] Año["+año+"]");
        if(mes>=1 && mes<=12){
            nDf = 0;
            switch(mes){
                case 1:
                case 3:
                case 5:
                case 7:
                case 8:
                case 10:
                case 12:
                   nDf = 31;
                break;
                case 2:
                    // Año bisiesto
                    if ( (año % 4 === 0 && año % 100 !== 0) || (año % 400 === 0) ){
                        nDf = 29;
                    }else{
                        nDf = 28;
                    }
                break;
                case 4:
                case 6:
                case 9:
                case 11:
                    nDf= 30;
                break
            }
            if (dia>=1 && dia <=nDf){
                const fecha = new Date(año, mes - 1, dia);
                //con sole.log(fecha);
                return fecha;
            }else{
                mensaje = "El valor del día ["+dia+"] es incorrecto para el mes ["+mes+"] del año ["+año+"]";
            }
        }else{
            mensaje = "El valor del mes ["+mes+"] es incorrecto";
        }
    } 
    mandaMensaje(mensaje);
    return null;
}
// ______________________________________________
function fechasLetras(fechaIni, fechaFin) {
    // Definir el array de traducción de meses
    const meses = [
        'Enero', 'Febrero'  , 'Marzo'       , 'Abril'   , 'Mayo'     , 'Junio',
        'julio', 'Agosto'   , 'Septiembre'  , 'Octubre' , 'Noviembre', 'Diciembre'
    ];

    // Crear objetos Date para las fechas inicial y final
    const dateIni = new Date(fechaIni + 'T00:00:00-06:00' );
    const dateFin = new Date(fechaFin + 'T00:00:00-06:00' );

    // Obtener el día, mes y año de cada fecha
    const diaIni = dateIni.getDate();
    const mesIni = dateIni.getMonth(); // getMonth() devuelve 0 para enero, 1 para febrero, etc.
    const anioIni = dateIni.getFullYear();

    const diaFin = dateFin.getDate();
    const mesFin = dateFin.getMonth();
    const anioFin = dateFin.getFullYear();

    // Determinar el formato de año a mostrar
    const anioMostrar = (anioIni === anioFin) ? anioIni : `${anioIni} al ${anioFin}`;

    // Construir la cadena de resultado
    if (anioIni === anioFin) {
        return `Del ${diaIni} de ${meses[mesIni]} al ${diaFin} de ${meses[mesFin]} del ${anioIni}`;
    } else {
        return `Del ${diaIni} de ${meses[mesIni]} del ${anioIni} AL ${diaFin} de ${meses[mesFin]} del ${anioFin}`;
    }
}
// ______________________________________________
function fyyyyddmm(cFecha,cSeparador){
    // cFecha en formato dd/mm/yy
    const partes = cFecha.split(cSeparador);
    return ( partes[2]+partes[1]+partes[0] );
}
// ______________________________________________
function fddmmyyyy(cFecha,cSeparador){ // yyyy-mm-dd -> dd/mm/yyyy ó dd-mm-yyy -> yyyy-mm-dd
    const partes = cFecha.split(cSeparador);
    //con sole.log(partes + "cFEcha:" + cFecha + " Separador : "+cSeparador);
    return ( partes[2]+"/"+partes[1]+"/"+partes[0] );
}
// ______________________________________________
function fgyyyyddmm(cFecha,cSeparador){
    const partes = cFecha.split(cSeparador);
    return ( partes[2]+"-"+partes[1]+"-"+partes[0] );
}
// ______________________________________________
function BusquedaTabla(cTabla,cTxtBusqueda){

    const tabla   = document.getElementById(cTabla);
    const cBuscar = document.getElementById(cTxtBusqueda).value.toLowerCase();
    let total     = 0; // Contador de coincidencias

    // Recorremos todas las filas con contenido de la tabla
    for (let i = 1; i < tabla.rows.length; i++) {
        // Si el td tiene la clase "noSearch" no se busca en su cntenido
        if (tabla.rows[i].classList.contains("noSearch")) {
            continue;
        }
        let lEncontro = false;
        const celdasR = tabla.rows[i].getElementsByTagName('td');

        // Recorremos todas las celdas
        for (let j = 0; j < celdasR.length && !lEncontro; j++) {
            const comparaCon = celdasR[j].innerHTML.toLowerCase();
            // Buscamos el texto en el contenido de la celda
            if (cBuscar.length == 0 || comparaCon.indexOf(cBuscar) > -1) {
                lEncontro = true;
                total++;
            }
        }
        if (lEncontro) {
            tabla.rows[i].style.display = '';
        } else {
            // si no ha encontrado ninguna coincidencia, se esconde renglón de la tabla
            tabla.rows[i].style.display = 'none';
        }
    }
}
// ______________________________________________
function obtenerFechaHoy() {
  const fecha   = new Date();
  const dia     = fecha.getDate().toString().padStart(2, '0');
  const mes     = (fecha.getMonth() + 1).toString().padStart(2, '0');
  const anio    = fecha.getFullYear();
  return `${dia}/${mes}/${anio}`;
}
// ______________________________________________________________________
function filtrarSelect(cValor,cSele,lUltimo) {
    const select    = document.getElementById(cSele);
    const opciones  = select.options;
    const filtro    = cValor; // El valor por el cual deseas filtrar

    for (let i = 0; i < opciones.length; i++) {
        const opcion      = opciones[i];
        const valorOpcion = opcion.value;
        if (lUltimo){ // Si lo que busco esta al final
            if (valorOpcion.endsWith(filtro) || valorOpcion==="") {
                opcion.style.display = 'block'; // Mostrar opción
            } else {
                opcion.style.display = 'none'; // Ocultar opción
            }
        }
    }
    select.value = "";
}
// ______________________________________________________________________
function quitarFiltroSelect(cSele) {
  const select      = document.getElementById(cSele);
  const opciones    = select.getElementsByTagName("option");

  for (let i = 0; i < opciones.length; i++) {
    opciones[i].style.display = "block"; // Restablecer todas las opciones a visible
  }
}
// ______________________________________________________________________
// ______________________________________________________________________
function esperaRespuesta(cMensaje,lSalto=false) {
    return new Promise((resolve) => {
        const dialogo   = document.querySelector("#cajaRespuesta");
        const btnSi     = document.querySelector('#btnSi');
        const btnNo     = document.querySelector('#btnNo');
        const btnCerrar = document.querySelector('#dialogo_close1');

        const dialogRespuesta       = document.querySelector('#dialogRespuesta');
        if (lSalto){
            dialogRespuesta.innerHTML   = escapeHTML(cMensaje);
        }else{
            dialogRespuesta.innerHTML   = escapeHTML(cMensaje).replace(/\n/g, '<br>');
        }

        // Evitar múltiples escuchas si la función se llama más de una vez
        const limpiarEventos = () => {
            btnSi.removeEventListener('click', onSi);
            btnNo.removeEventListener('click', onNo);
            btnCerrar.removeEventListener('click', onCerrar);
        };

        const onSi = (e) => {
            e.preventDefault();
            limpiarEventos();
            dialogo.close();
            resolve(true);
        };

        const onNo = (e) => {
            e.preventDefault();
            limpiarEventos();
            dialogo.close();
            resolve(false);
        };

        const onCerrar = () => {
            limpiarEventos();
            dialogo.close();
            resolve(false); // o false, según tu lógica
        };

        btnSi.addEventListener('click', onSi);
        btnNo.addEventListener('click', onNo);
        btnCerrar.addEventListener('click', onCerrar);

        dialogo.showModal();
    });
}

// ______________________________________________________________________
// ______________________________________________________________________
function capturaFechaModal() {
    return new Promise((resolve) => {
        const dialogo    = document.querySelector("#cajaCancela");
        const btnRegresa = document.querySelector('#btnRegresa');

       // const dialogRespuesta = document.querySelector('#dialogRespuesta'); // Párrafo de texto
       // dialogRespuesta.textContent = cMensaje; // Cambia el mensaje

        btnRegresa.addEventListener('click', (e) => { 
            e.preventDefault(); // Evitar el envío del formulario
            dialogo.close();
            resolve(true);
        });

        dialogo.showModal();
    });
}
// ______________________________________________________________________
function capturaNumeroChequeModal() {
    return new Promise((resolve) => {
        const dialogo    = document.querySelector("#cajaImprimeCheque");
        const btnImprime = document.querySelector('#btnImprime');

       // const dialogRespuesta = document.querySelector('#dialogRespuesta'); // Párrafo de texto
       // dialogRespuesta.textContent = cMensaje; // Cambia el mensaje

        btnImprime.addEventListener('click', (e) => { 
            e.preventDefault(); // Evitar el envío del formulario
            dialogo.close();
            resolve(true);
        });

        dialogo.showModal();
    });
}
// ______________________________________________________________________
function solicitaArchivoLayOut(){
    return new Promise((resolve) => {
        const dialogo    = document.querySelector("#cajaCancelaLayOut");
        const btnRegresa = document.querySelector('#btnCancelaLayOut');

       // const dialogRespuesta = document.querySelector('#dialogRespuesta'); // Párrafo de texto
       // dialogRespuesta.textContent = cMensaje; // Cambia el mensaje

        btnRegresa.addEventListener('click', (e) => { 
            e.preventDefault(); // Evitar el envío del formulario
            dialogo.close();
            resolve(true);
        });

        dialogo.showModal();
    });
}
// ______________________________________________________________________
function FocoEnObjeto(input){
    input.click();
    input.focus();
}
// ______________________________________________________________________
function FocoEn(cIdCampo){
    //document.getElementById(cIdCampo).blur();
    document.getElementById(cIdCampo).click();
    document.getElementById(cIdCampo).focus();
}
// ______________________________________________________________________
function guardarValorOriginal(objHtml){
    objHtml.dataset.originalValue = objHtml.value;
}
// ______________________________________________________________________
// ______________________________________________________________________
function recuperaValorOriginal(cIdCampo){
    cObjHtml        = document.getElementById(cIdCampo);
    cObjHtml.value  = cObjHtml.dataset.originalValue;
}
// ______________________________________________________________________
function escuchaFoco(aSelect){
    aSelect.forEach(idCombo => {
        combo = document.getElementById(idCombo)
        combo.addEventListener('focus', function() {
            combo.click();
        });
    });
}
// ______________________________________________________________________
function valorDeObjeto(idHtml,lMensaje=true){
    var elemento = document.getElementById(idHtml);

    if (elemento && elemento.value !== null && elemento.value !== undefined) {
        if (elemento.value!==""){
            return elemento.value;
        }else{
            FocoEn(idHtml);
            if (lMensaje){
                mandaMensaje("Se requiere valor de "+ elemento.title);
            }
            return null;
        }
        //con sole.log("Valor válido:", valor);
    } else {
        mandaMensaje("No se encuentra definido el objeto HTML "+idHtml);
        return null;
    }
}
// ______________________________________________________________________
function abrePdf(cArchivo){
    var parametroUnico = Date.now();

    var rutaPDF = cArchivo;

    // Agregar el parámetro único a la URL del PDF
    var urlPDF = rutaPDF + '?nocache=' + parametroUnico;
    //con sole.log("urlPdf=",urlPDF);
    // Abrir el PDF en una nueva ventana o pestaña
    window.open(urlPDF, '_blank');
   // window.open(cArchivo, '_blank');
}
// ______________________________________________________________________
function abrePdf1(cArchivo) { // Para archivos grandes creo que simula una clic en un elemento <a>
    var parametroUnico = Date.now();
    var urlPDF = cArchivo + '?nocache=' + parametroUnico;

    // Crear un enlace dinámico
    const link = document.createElement('a');
    link.href = urlPDF;
    link.target = '_blank'; // aún se abre en nueva pestaña si es posible
    link.rel = 'noopener';
    link.download = ''; // intenta forzar descarga, aunque depende del servidor
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
// ______________________________________________________________________ Esta función fue de prueba con Xamp por que no abria el archivo XLS , noi se esta usando
function abrePdfXls(cArchivo){ // Usar con Xamp y verificar si en Linux con versión PHP 8 se puede usar abrePdf y que muestre abrir, guardar, guardar Como
    var parametroUnico = Date.now();
    var url = cArchivo + '?nocache=' + parametroUnico;
    //console.log(cArchivo);
    var extension = cArchivo.split('.').pop().toLowerCase();

    if (extension === 'pdf') {
        window.open(url, '_blank');
    } else if (extension === 'xls' || extension === 'xlsx') {
        var downloadUrl = 'backP/G_descargar_xls.php?file=' + encodeURIComponent(cArchivo); // Esta comentada todo el php
        window.open(downloadUrl + '&nocache=' + parametroUnico, '_blank');
    } else {
        window.open(url, '_blank');
    }
}
// ______________________________________________________________________
function formatoMes(nMes){
    return nMes < 10 ? '0' + nMes : nMes;  
}
// ______________________________________________________________________
function obtenerDiasEnMes(anio, mes) {
    var ultimoDia = new Date(anio, mes, 0).getDate();
    return ultimoDia;
}
// ______________________________________________________________________
function textoSelect(idSelect){
    select = document.getElementById(idSelect);
    return select.options[select.selectedIndex].text.toUpperCase();
}
// ______________________________________________________________________
// Debe ser por el atributo name del objeto HTML que debe definir el atributo value que va a regresar
function valorRadio(cName,cDes){
    // Obtener todos los elementos radio con el nombre 'filCta'
    var opcionesRadio = document.getElementsByName(cName);

    // Inicializar una variable para almacenar el valor seleccionado
    var valorSeleccionado = null;

    // Iterar sobre los elementos radio para encontrar el seleccionado
    for (var i = 0; i < opcionesRadio.length; i++) {
        if (opcionesRadio[i].checked) {
            valorSeleccionado = opcionesRadio[i].value;
            break; // Rompemos el bucle si encontramos el seleccionado
        }
    }
    if (valorSeleccionado==null){
        mandaMensaje("No se ha seleccionado una opción en ",cDes)
    }
    return valorSeleccionado;
}
// ______________________________________________________________________
function formatearFecha(cadenaFecha) {
    // Dividir la cadena de fecha en día, mes y año
    var partesFecha = cadenaFecha.split('-');
    
    // Obtener el día, mes y año
    var dia = parseInt(partesFecha[2], 10);
    var mes = parseInt(partesFecha[1], 10);
    var año = parseInt(partesFecha[0], 10);

    // Obtener el nombre del mes
    var nombreMes = new Date(año, mes - 1, 1).toLocaleString('es-ES', { month: 'long' });

    // Construir la cadena formateada
    var fechaFormateada = `AL ${dia} DE ${nombreMes.toUpperCase()} DEL ${año}`;

    return fechaFormateada;
}
// ______________________________________________________________________
function validarSeleccion(select,lRequerido=false) {
    var selectedValue = select.value;

    if (selectedValue === "") {
        // Devolver el enfoque al elemento select
        if (lRequerido){
            FocoEn(select.id);
            info = select.getAttribute("data-info");
            mandaMensaje("Se requiere seleccionar un valor de la lista para " + info);
            FocoEn(select.id);
            return false;   // Validar que se haya capturado
        }
        return true; // No se capturo valor pero se validará despues
    }
    return true; // Se capturo valor
}
// ______________________________________________________________________
function quitaSubmit(gForma){
    // Evitar que al dar enter en un input vacío se recargue la página
    document.getElementById(gForma).addEventListener('submit', function(event) {
        event.preventDefault();
        //console.log('Se presionó Enter, pero no se recargará la página.');
    });
}
// __________________________________________________________________________________
function revisaFechas(oInputIni,oInputFin=null){
    cFechaI = oInputIni.value;
    if (cFechaI==="" || cFechaI===null){
        FocoEnObjeto(oInputIni)
        mandaMensaje("Fecha inválida o vacía");
        return false;
    }
    if (oInputFin!==null){
        cFechaF = oInputFin.value;
        if (cFechaF==="" || cFechaF===null){
            FocoEnObjeto(oInputFin)
            mandaMensaje("Fecha inválida o vacía");
            return false;
        }
        if (cFechaI > cFechaF){
            oInputIni.value = cFechaF;
            oInputFin.value = cFechaI;
        }
    }
    return true;
}
// __________________________________________________________________________________
function validaFecha1(input){
    cFecha = input.value;
    if (cFecha==="" || cFecha===null){
        FocoEn(input.id)
        mandaMensaje("Fecha inválida o vacía");
        FocoEn(input.id);
        return false;
    }
    cFecha = fddmmyyyy(cFecha,"-");
    validaFecha(cFecha,input.id,false);
}
// __________________________________________________________________________________
// __________________________________________________________________________________
function validaFecha(dFecha,cIdFecha,lHoy){
    var elemento = document.getElementById('idMovimiento');

    if ( CadenaToFecha(dFecha)==null){
        if (lHoy){ // En modificaciones habria que ver como re_cuperar la fecha del movimiento y no la de hoy
            document.getElementById(cIdFecha).value = obtenerFechaHoy();
        }
        return false
    }
    cFechaCap = fyyyyddmm(dFecha,'/');
    cFechaHoy = dHoy.replace(/-/g, ''); // fyyyyddmm(dHoy,'-');
    cAnio     = cFechaCap.substring(0,4)
    if (elemento){
        cIdMov    = document.getElementById("idMovimiento").value.trim(); 
    }
    //con sole.log("FechaCap["+cFechaCap+"] FechaHoy["+cFechaHoy+"]["+dHoy+"]");
    cObj = document.getElementById(cIdFecha);
    if ( cFechaCap < cFechaHoy ){
        if ( !( gEsquema=="Administrador" || gEsquema=="Captura") ){
            //cObj.value = fddmmyyyy(dHoy,'-');
            //re cuperaValorOriginal("idFecha");
            //cObj.blur();
            //cObj.focus();
            //cObj.click();
            if (elemento){
                if (cIdMov===""){ // EN modificaciones si se debe permitir salir del input de fecha
                    FocoEn(cIdFecha);
                    mandaMensaje("No se pueden capturar fechas menores a " + dHoy ); // +" ["+cFechaCap+"-"+cFechaHoy+"]");
                    FocoEn(cIdFecha);
                    return false;
                }
            }
        }
        if ( (cYear - cAnio)> 4 ){
            FocoEn(cIdFecha);
            mandaMensaje("El año de la fecha no debe ser menor a "+(cYear-4));
            FocoEn(cIdFecha);
            return false;
        }

    }else{
        if (cFechaCap>cFechaHoy){
            //cObj.value = fddmmyyyy(dHoy,'-');;
            //re cuperaValorOriginal("idFecha");
            FocoEn(cIdFecha);
            mandaMensaje("No se pueden capturar fechas mayores a " + dHoy + "["+dFecha+"]" ); // +" ["+cFechaCap+"-"+cFechaHoy+"]");
            FocoEn(cIdFecha);
            return false;
        }
    }
    return true
}
// _______________________________________________
function efectoBotones(cOpc){
    /* Se agrega un escucha para cuando se carga el archivo CSV */
    document.addEventListener('DOMContentLoaded', function () {
        var input1_file = document.getElementById('ArchivoCarga_file');
        var input_icon  = document.getElementById('input_icon');     // Para cambiar el icono
        var input_text  = document.getElementById('input_text');     // Para cambiar el texto del boton
        var inputLabel  = document.getElementById('lblCarga');   

        input1_file.addEventListener('change', function (e) {       // Escucha el evento change
            name_file = input1_file.files[0].name;
            var full_name = "";

            if (name_file.length >= 12) {                           // Nombres muy largos
                full_name = name_file;
                name_file = name_file.substring(0, 14) + ".."; 
            } else {
                full_name = "";
            }

            if (name_file !== "") {
                input_icon.innerHTML = "done_all";                  // Cambia el icono
                input_text.innerHTML = name_file;                   // Guarda el nombre
                inputLabel.setAttribute('title', full_name);        // Guarda toda la ruta ?
                if (cOpc=="Buzon"){
                    cargaArchivoCsv()
                }else if(cOpc=="BuzonXls"){
                    cargaArchivoXls()
                }else if(cOpc==""){

                }
            }
        });
    });
}
// __________________________________________________________________________________________________________
function ponArchivoCarga(){
    var input_icon  = document.getElementById('input_icon');     // Para cambiar el icono
    var input_text  = document.getElementById('input_text');     // Para cambiar el texto del boton
    var input1_file = document.getElementById('ArchivoCarga_file');
    var inputLabel  = document.getElementById('lblCarga'); 
    var full_name   = "";

    name_file = input1_file.files[0].name;
    if (name_file.length >= 40) {  // Nombres muy largos
        full_name = name_file;
        name_file = name_file.substring(name_file.length - 40) + ".."; 
    } else {
        full_name = "";
    }

    if (name_file !== "") {
        input_icon.innerHTML = "done_all";                  // Cambia el icono
        input_text.innerHTML = name_file;                   // Guarda el nombre
        inputLabel.setAttribute('title', full_name);        // Guarda toda la ruta ?
    }
}
// __________________________________________________________________________________________________________
// __________________________________________________________________________________________________________
// __________________________________________________________________________________________________________
function primerUltimoValor(oSelect ){
    primerValor = oSelect.options[1].value.split("|")[0].trim(); // Es la 1 porque la 0 es Seleccione
    aOpciones   = oSelect.options;
    ultimoValor = aOpciones[aOpciones.length - 1].value.split("|")[0].trim();
    return [primerValor,ultimoValor];
}
// __________________________________________________________________________________
function limpiaPantalla(gForma){
    document.getElementById(gForma).reset();
}
// __________________________________________________________________________________
const validaPatron = (oInput) => {

    // Obtener el valor del input
    var valor = oInput.value.trim();

    if (valor===""){ // No validar nada
        return true;
    }
    
    // Obtener el patrón establecido en la propiedad pattern
    var patron = oInput.getAttribute("pattern");

    // Crear una expresión regular con el patrón
    var regex = new RegExp("^" + patron + "$");
    
    // Verificar si el valor coincide con el patrón
    if (!regex.test(valor)) {
        var titulo = oInput.getAttribute("title");
        // Mostrar un mensaje de error
        FocoEnObjeto(oInput);
        mandaMensaje(`El valor no coincide con el patrón establecido. ${titulo}`);
        // Limpiar el valor del input
        oInput.value = "";
        // Devolver el foco al input
        FocoEnObjeto(oInput);
        return false;
    }else{
        return true;
    }
}
// __________________________________________________________________________________
function cerosIzquierda(valor, numCeros) {
    let longitudActual = valor.toString().length;
    let cerosFaltantes = numCeros - longitudActual;
    if (cerosFaltantes > 0) {
        return '0'.repeat(cerosFaltantes) + valor;
    } else {
        return valor.toString();
    }
}
// __________________________________________________________________________________
function escuchaCampoFecha(cIdFecha){
    // Recuperar el valor almacenado del input date
    var dateInput = document.getElementById(cIdFecha);
    if (dateInput) { // Si existe ?
        // Escucha en change, Guardar el valor en localStorage cuando cambie
        dateInput.addEventListener('change', function() {
            localStorage.setItem('storedDate', dateInput.value);
        });

        // Escucha en focus, Recuperar el valor almacenado cuando haga foco en el input date
        dateInput.addEventListener('focus', function() {
            var storedDate = localStorage.getItem('storedDate');
            if (storedDate) {
                dateInput.value = storedDate;
            }
        });
    }
}
// __________________________________________________________________________________
function valoresUnicos(oInput){
    cValor  = oInput.value.trim();
    cUnicos = oInput.getAttribute('pattern'); 
    cInfo   = oInput.getAttribute('"data-info"');
    cInfo   = cInfo===null?"":"para "+cInfo+" ";
    if (cUnicos === null) { 
        mandaMensaje("No se han definido en HTML los valores únicos");
        return false;
    }else{
        if (cValor!==""){
            // Crear una expresión regular a partir del patrón
            const regex = new RegExp(cUnicos);

            // Verificar si el valor cumple con el patrón
            if (regex.test(cValor)) {
                return true;
            }else {
                FocoEnObjeto(oInput);
                mandaMensaje(`Los valores permitidos ${cInfo}son ${cUnicos}`);
                //FocoEnObjeto(oInput);
                return false;
            }
        }// Se deja pasar valores en blanco por si quier regresar a otro input
    }
}
// __________________________________________________________________________________
const seleccionaTexto = (event) => {
    event.target.select();
}
// __________________________________________________________________________________
const validaAnio =(inputAnio) =>{
    cAnio    = inputAnio.value.trim();
    cAnioHoy = dHoy.substring(0,4)
    if (cAnio==""){
        return true;
    }
    if ( cAnio > cAnioHoy ){
        FocoEnObjeto(inputAnio);
        mandaMensaje(`No se pueden procesar años posteriores al actual ${cAnioHoy}`);
        return false;
    }
    if ( (cAnioHoy - cAnio)> 4 ){
        FocoEnObjeto(inputAnio);
        mandaMensaje(`El año de captura no puede ser menor a  ${cAnioHoy-4}`);
        return false;
    }
    return true;
}
// __________________________________________________________________________________
const CambiaLetrero = () =>{
    const seleccion         = document.getElementById('opcionesFiltro');
    const etiqueta          = document.getElementById('labelFiltro');
    const inputBusqueda     = document.getElementById("campo")
    const opcionElegida     = seleccion.options[seleccion.selectedIndex].text;
    etiqueta.textContent    = opcionElegida;

    if (opcionElegida=="Fecha"){
        inputBusqueda.type = "date";
        inputBusqueda.value = "";
    }else{
        inputBusqueda.type  = "text";
        inputBusqueda.value = "";
    }
    inputBusqueda.focus();
}
// __________________________________________________________________________________
const ModificaLetrero = (cSele,cLabel,cInput) =>{
    const seleccion         = document.getElementById(cSele);
    const etiqueta          = document.getElementById(cLabel);
    const inputBusqueda     = document.getElementById("campo")
    const opcionElegida     = seleccion.options[seleccion.selectedIndex].text;
    etiqueta.textContent    = opcionElegida;
    if (opcionElegida!=""){
        inputBusqueda.type  = "text";
        inputBusqueda.value = "";
        if (opcionElegida=="Fecha"){
            inputBusqueda.style.width   = '300px';
            inputBusqueda.type          = "date";
        }else if(opcionElegida=="Beneficiario" || opcionElegida=="Concepto"){
            inputBusqueda.style.width   = '600px';
        }else if(opcionElegida=="Importe"){
            inputBusqueda.style.width   = '300px';
        }else{
            inputBusqueda.style.width   = '150px';
        } 
        inputBusqueda.focus();
    }else{
        etiqueta.textContent = "Buscar"
    }
}
// __________________________________________________________________________________
const llenaFechaHoy = (cIdHtml,vHoy,lDiaIni=false) =>{
    oFechaHoy = document.getElementById(cIdHtml);
    if (oFechaHoy){
        if (lDiaIni){// Día Inicial del Mes
            vHoy1 = vHoy.substring(0,8)+"01";
            oFechaHoy.value = vHoy1;
        }else{
            oFechaHoy.value = vHoy;
        }
        oFechaHoy.setAttribute('max', vHoy);
    }
}
// __________________________________________________________________________________
const buscaxNombre = (cIdHtml,cNombre) =>{
    var select = document.getElementById(cIdHtml);
    cNombre    = cNombre.trim();

    // Recorre las opciones para encontrar el texto que coincide con el nombre
    for (var i = 0; i < select.options.length; i++) {
        if (select.options[i].text.trim() === cNombre) {
          // Cambia la opción seleccionada por su índice
          select.selectedIndex = i;
          return select.options[i].value;
        }
    }
    return "";
}
// __________________________________________________________________________________
const fechaXLS = (cFechaNumero,cSeparador="-") =>{ // Regresa la fecha, de acuerdo con un valor numérico

    if (cFechaNumero && !isNaN(cFechaNumero)) {
        // Convertir el número de Excel (por ejemplo, 45643) a una fecha en formato dd/mm/yyyy
        let fechaExcel = new Date(1900, 0, cFechaNumero - 1);   // 1 de enero de 1900 es la fecha base de Excel
        fechaExcel.setDate(fechaExcel.getDate() );              // tenia un +1 pero adelantaba un día Ajustar debido a que Excel tiene un error con el 1900

        // Obtener el día, mes y año con formato dd/mm/yyyy
        let dia = String(fechaExcel.getDate()).padStart(2, '0');
        let mes = String(fechaExcel.getMonth() + 1).padStart(2, '0'); // Los meses en JavaScript son 0-indexados
        let anio = fechaExcel.getFullYear();

        let fechaFormateada = `${anio}${cSeparador}${mes}${cSeparador}${dia}`;
        // console.log("Fecha Formateada: ",cFechaNumero,"->" ,fechaFormateada);

        // Asigna la fecha formateada a la variable cFecha
        cFechaNumero = fechaFormateada;
        return cFechaNumero;
    }else{
        return "";
    }    

}
// __________________________________________________________________________________
function agregarComas(numero) {
  // Convertir el número a un string (si no es ya un string)
  let numStr = String(numero);
  
  // Usamos una expresión regular para insertar las comas
  return numStr.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
// __________________________________________________________________________________
// Función para obtener el nombre del mes
const getMonthName = (month) => {
    const months = [
        "ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", 
        "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE"
    ];
    return months[parseInt(month) - 1];
}
// __________________________________________________________________________________
const parseImporte = (importeStr) => {
    // Eliminar las comas y convertir a número
    return parseFloat(importeStr.replace(/,/g, ''));
}
// __________________________________________________________________________________
const regresaYMDHM=()=>{
    const now = new Date();

    // Formatea la fecha y hora como 'YYYYMMDDHHmm'
    const formattedDate = now.getFullYear() +
        ("0" + (now.getMonth() + 1)).slice(-2) + // Mes (asegurándote de que tenga dos dígitos, + 1 x que empieza en 0)
        ("0" + now.getDate()).slice(-2) + // Día (asegurándote de que tenga dos dígitos)
        ("0" + now.getHours()).slice(-2) + // Hora (asegurándote de que tenga dos dígitos)
        ("0" + now.getMinutes()).slice(-2); 
    return formattedDate;
}
// __________________________________________________________________________________
function verificaImporte(elemento) {
    let valor = elemento.value;

    // Quitar todo lo que no sea dígito o punto
    valor = valor.replace(/[^0-9.]/g, '');

    // Permitir solo un punto decimal
    const partes = valor.split('.');
    if (partes.length > 2) {
        valor = partes[0] + '.' + partes[1];
    }

    // Limitar a 2 decimales si los hay
    if (partes.length === 2) {
        partes[1] = partes[1].slice(0, 2);
        valor = partes[0] + '.' + partes[1];
    }

    elemento.value = valor;
}
// __________________________________________________________________________________
function formatoImporte(elemento) {
    let valor    = elemento.value.replace(/,/g, '');
    let numero   = parseFloat(valor);
    let etiqueta = elemento.dataset.info || "importe"; // Valor por defecto

    if (!isNaN(numero)) {
        // Quitar clase de error si existía
        elemento.classList.remove('input-error');
        elemento.value = numero.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
        });
    }else {
        // Si NO es un número válido, lo limpia
        // Valor no válido → limpiar, enfocar y opcionalmente alertar
        //elemento.value = '';
        elemento.classList.add('input-error');
        elemento.focus();
        elemento.setSelectionRange(0, 0); // Coloca el cursor al inicio
        mandaMensaje(`Por favor ingrese un valor válido para ${etiqueta}, con hasta dos decimales.`);
    }
}
// __________________________________________________________________________________
const FinSemana = (oFecha) =>{
    const valorFecha = oFecha.value;
    const cTitulo    = oFecha.getAttribute("data-info"); 
    if (!valorFecha) {
        mandaMensaje(`No se ha seleccionado ${cTitulo}`);
        FocoEnObjeto(oFecha);
        return;
    }

    // Parsear la fecha como local (evitando el constructor con cadena ISO)
    const [year, month, day] = valorFecha.split("-").map(Number);
    const fecha = new Date(year, month - 1, day); // mes: 0 = enero, 11 = diciembre

    const diaSemana = fecha.getDay(); // 0 = Domingo, 6 = Sábado

    if (diaSemana === 0 || diaSemana === 6) {
        mandaMensaje(`La ${cTitulo} es de fin de semana`);
        FocoEnObjeto(oFecha);
        return;
    } 
}
// __________________________________________________________________________________
const campoTabla = (cOpc) =>{
    cOpc = cOpc.toLowerCase();
    switch(cOpc){
        case "beneficiario":
            cCampo = "beneficiario";
        break;
        // ____________________________
        case "concepto":
            cCampo = "concepto";
        break
        // ____________________________
        case "importe":
            cCampo = "importeoperacion"
        break;
        // ____________________________
        case "fecha":
            cCampo = "fechaoperacion"
        break;
        // ____________________________
        case "control":
            cCampo = "idcontrol"
        break;
        // ____________________________
        case "operacion":
            cCampo = "importeoperacion"
        break;
        // ____________________________

    }
}
// __________________________________________________________________________________
function visible(cId, visible) {
    const oHtml = document.getElementById(cId);
    if (oHtml) {
        if (visible) {
            oHtml.classList.remove('oculto');
        } else {
            oHtml.classList.add('oculto');
        }
    } else {
        mandaMensaje(`Elemento con id "${cId}" no encontrado.`);
    }
}
// __________________________________________________________________________________
const tituloSelect = (cId)=>{
    cSele = document.getElementById(cId);            // Debe ser un HTML select
    cTxt  = cSele.options[cSele.selectedIndex].text; // Regresa el texto desplegado en el select
    return cTxt;
}
// __________________________________________________________________________________
function limpiaObjeto(cId){
    document.getElementById(cId).value = '';
}
// __________________________________________________________________________________
function con_Comas(importe) {
    return parseFloat(importe).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
// __________________________________________________________________________________
function MarcaDesmarca(cTabla) {
    const tabla = document.getElementById(cTabla);
    if (!tabla) {
        mandaMensaje("No se encuentra el objeto HTML: " + cTabla);
        return;
    }

    const filas = tabla.getElementsByTagName('tr');

    for (let i = 0; i < filas.length; i++) {
        const checkbox = filas[i].querySelector('input[type="checkbox"]');
        if (checkbox) {
            checkbox.checked = !checkbox.checked;  // Alternar estado
        }
    }
}
// __________________________________________________________________________________
const cerosCheque=(objHtml,nCeros=8,cRelleno='0')=>{
    cChe            = document.getElementById(objHtml.id).value.trim();
    cChe            = cChe.padStart(nCeros,cRelleno);
    objHtml.value   = cChe; 
}
// __________________________________________________________________________________
const guardaValorObjeto=(cId,cVal)=>{
    document.getElementById(cId).value = cVal;
}
// __________________________________________________________________________________
// Función para formatear una cadena a una longitud fija
function padLeft(str, length, char = '0') {
  str = String(str);
  return str.length >= length ? str : char.repeat(length - str.length) + str;
}
// __________________________________________________________________________________
function padRight(str, length, char = ' ') {
  str = String(str);
  return str.length >= length ? str.slice(0, length) : str + char.repeat(length - str.length);
}
// __________________________________________________________________________________
function validaCorreo(oMail,lVacio=false){
    cInfo   = oMail.getAttribute("data-info");
    cVal    = oMail.value.trim();
    if (cVal===""){
        if (lVacio){
            return true;
        }
        mandaMensaje("Se requiere valor para " + cInfo);
        FocoEnObjeto(oMail);
        return false;
    }
    if ( !oMail.checkValidity() ){
        mandaMensaje("Correo Inválido para "+ cInfo);
        FocoEnObjeto(oMail);
        return false;
    }
    return true;
}
// __________________________________________________________________________________
// const soloNumeros_ = (oHtml, lValida = false) => { // Se tiene soloNumeros, pero lleva mas parámetros
//     const cVar = oHtml.value.trim();
//     const patron = /^[0-9]+$/;

//     if (cVar === "") {
//         if (lValida) {
//             mandaMensaje(oHtml.title + ": debe contener información");
//             FocoEnObjeto(oHtml);
//             return false;
//         }
//         return true; // vacío, pero no obligatorio
//     }

//     if (!patron.test(cVar)) {
//         mandaMensaje(oHtml.title + ": solo puede llevar números");
//         FocoEnObjeto(oHtml);
//         return false;
//     }

//     return true;
// };
// __________________________________________________________________________________
const tieneValorObjeto = (oHtml) => {
    try {
        const cVal = oHtml.value?.trim() ?? ""; // Maneja null/undefined y remueve espacios
        const tipo = oHtml.type || "text";
        
        if (cVal === "") {
            mandaMensaje("Se requiere la captura de " + (oHtml.title || oHtml.name || oHtml.id));
            FocoEnObjeto(oHtml);
            return false;
        }

        // Validación adicional para fechas
        if (tipo === "date") {
            const fecha = Date.parse(cVal);
            if (isNaN(fecha)) {
                mandaMensaje("La fecha capturada no es válida en " + (oHtml.title || oHtml.name || oHtml.id));
                FocoEnObjeto(oHtml);
                return false;
            }
        }

        return true;

    } catch (error) {
        mandaMensaje("No se encuentra el elemento HTML [" + (oHtml?.id || "desconocido") + "]");
        return false;
    }
};
// __________________________________________________________________________________
const validaHHMM = (oHtml) => {
    const cVal = oHtml.value.trim();

    if (cVal === "") {
        mandaMensaje("Se requiere valor para " + oHtml.title);
        FocoEnObjeto(oHtml);
        return false;
    }

    if (cVal.length !== 4) {
        mandaMensaje("Se requieren 4 dígitos en formato HHMM");
        FocoEnObjeto(oHtml);
        return false;
    }

    const cHora = parseInt(cVal.substring(0, 2), 10);
    const cMin  = parseInt(cVal.slice(-2), 10);

    if (isNaN(cHora) || isNaN(cMin)) {
        mandaMensaje("La hora debe contener solo números");
        FocoEnObjeto(oHtml);
        return false;
    }

    if (cHora < 0 || cHora > 23) {
        mandaMensaje("Valor inválido para la hora (debe ser entre 00 y 23)");
        FocoEnObjeto(oHtml);
        return false;
    }

    if (cMin < 0 || cMin > 59) {
        mandaMensaje("Valor inválido para los minutos (debe ser entre 00 y 59)");
        FocoEnObjeto(oHtml);
        return false;
    }

    return true;
};
// __________________________________________________________________________________
const fechaDDMMYY = (cId)=>{
    const input = document.getElementById(cId);
    const valor = input.value; // valor en formato "YYYY-MM-DD"
    //console.log("Input date valor=",valor);

    if (valor === "") {
        mandaMensaje("No se ha seleccionado una fecha.");
        FocoEnObjeto(input);
        return "";
    }

    const [yyyy, mm, dd] = valor.split("-");
    const yy = yyyy.slice(-2); // obtiene los últimos dos dígitos del año

    const formatoFinal = `${dd}${mm}${yy}`;
    return formatoFinal;
}
// __________________________________________________________________________________
function escapeHTML(str) {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}
// __________________________________________________________________________________
function limpiaInputFile(cId) {
    const el = document.getElementById(cId);
    document.getElementById(cId).value = ""
    const nuevo = el.cloneNode(true); // clona sin archivos
    el.parentNode.replaceChild(nuevo, el); // reemplaza el input
}
// __________________________________________________________________________________
function ocultaObjeto(cId){
    oHtml               = document.getElementById(cId);
    oHtml.style.display = 'none';
}
// __________________________________________________________________________________
function visibleObjeto(cId){
    oHtml               = document.getElementById(cId);
    oHtml.style.display = 'block';
}
// __________________________________________________________________________________
const llenaFechaMesAnterior = (cIdHtml, vHoy, lDiaIni = false) => {
    const oFechaInput = document.getElementById(cIdHtml);
    if (!oFechaInput) return;

    const fechaHoy = new Date(vHoy);
    
    // Restar un mes
    const primerDiaMesAnterior = new Date(fechaHoy.getFullYear(), fechaHoy.getMonth() - 1, 1);
    const ultimoDiaMesAnterior = new Date(fechaHoy.getFullYear(), fechaHoy.getMonth(), 0); // día 0 del mes actual es el último del anterior

    const formateaFecha = (fecha) => {
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const dia = String(fecha.getDate()).padStart(2, '0');
        return `${fecha.getFullYear()}-${mes}-${dia}`;
    };

    const valorFecha = lDiaIni ? formateaFecha(primerDiaMesAnterior) : formateaFecha(ultimoDiaMesAnterior);

    oFechaInput.value = valorFecha;
    oFechaInput.setAttribute('max', formateaFecha(fechaHoy)); // aún limita al día de hoy
};
// __________________________________________________________________________________
function sanitizeLogOutput(text) {
    return text.replace(/[\r\n]/g, "\\n");
}
// __________________________________________________________________________________
// __________________________________________________________________________________
function mandaMensaje(cMensaje) {
    dialogo = document.querySelector("#cajaMensaje");
    dialogMessage = document.querySelector('#dialogMessage');

    // Escapa el HTML malicioso
    let mensajeSeguro = escapeHTML(cMensaje);

    // Si deseas permitir <br>, puedes convertir '\n' a '<br>' aquí
    mensajeSeguro = mensajeSeguro.replace(/\n/g, "<br>");

    dialogMessage.textContent = mensajeSeguro; // tenía innerHTML pero veracode lo reporto aún con el escapeHTML
    dialogo.showModal();
}
// __________________________________________________________________________________
function validaListaCorreos(oCorreo,lVacio=true) {
    cLista = oCorreo.value.trim();
    cInfo  = oCorreo.getAttribute("data-info");
    // Si está vacío, válido
    if (cLista === '') {
        if (lVacio){
            return true;
        }
        mandaMensaje("Se requiere valor para " + cInfo);
        FocoEnObjeto(oCorreo);
        return false;
    }

    // Expresión regular simple para validar correo
    const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Separar por ; y validar cada correo
    const correos = cLista.split(';').map(c => c.trim()).filter(c => c !== '');

    if (correos.length === 0) {
        // Solo capturo ;
        mandaMensaje("Correos incorrectos en " + cInfo);
        FocoEnObjeto(oCorreo);
        return false;
    }

    // Validar cada correo
    for (const correo of correos) {
        if (!regexCorreo.test(correo)) {
            mandaMensaje("Correo incorrecto ["+ correo +"] en "+ cInfo);
            FocoEnObjeto(oCorreo);
            return false;
        }
    }

    return true;
}
// __________________________________________________________________________________
// __________________________________________________________________________________
function quitaTabulaciones(cTexto){
    return cTexto.replace(/^[ \t]+/gm, '').trim();
}
// __________________________________________________________________________________
// __________________________________________________________________________________
/**
 * Extrae el contenido de una tabla HTML en un arreglo de objetos 🧠
 * @param {string} idTabla - ID de la tabla
 * @param {string[]} [nombres] - Arreglo de nombres de columnas. Poner "NO" o "IGNORAR" para excluir columna. Si no se pasa, se toma de la segunda fila del thead.
 * @returns {Object[]} - Arreglo de filas como objetos {columna1: valor1, columna2: valor2, ...}
 */
function contenidoTabla(idTabla, nombres) {
    const tabla = document.getElementById(idTabla);
    if (!tabla) return [];

    // Si no se envía nombres, tomarlos del <thead> segunda fila
    if (!Array.isArray(nombres) || !nombres.length) {
        const ths = tabla.querySelectorAll("thead tr:nth-child(2) th");
        nombres = Array.from(ths).map(th => th.textContent.trim());
    }

    const filas = Array.from(tabla.querySelectorAll("tbody tr"));
    if (!filas.length) return [];

    // Prepara los índices de columnas válidas
    const indicesValidos = nombres
        .map((nombre, i) => ({ nombre, i }))
        .filter(n => n.nombre && !["NO", "IGNORAR"].includes(n.nombre.toUpperCase()));

    const resultado = filas.map(fila => {
        const celdas = fila.querySelectorAll("td");
        const filaObj = {};

        indicesValidos.forEach(({ nombre, i }) => {
            const celda = celdas[i];
            if (!celda) {
                filaObj[nombre] = null;
                return;
            }

            // Detecta checkbox dentro de la celda
            const chk = celda.querySelector('input[type="checkbox"]');
            if (chk) {
                filaObj[nombre] = chk.checked;
                return;
            }

            // Si hay div u otros contenedores
            const div = celda.querySelector('div');
            filaObj[nombre] = div ? div.textContent.trim() : celda.textContent.trim();
        });

        return filaObj;
    });

    return resultado;
}

// __________________________________________________________________________________
function restableceEtiquetaSpan(cId='input_text',cEtiqueta='Seleccione Archivo de ......') {
    const el    = document.getElementById(cId);
    const nuevo = el.cloneNode(true); // clona sin archivos
    el.parentNode.replaceChild(nuevo, el); // reemplaza el input (se limpia)

    // --- Restaura el estado visual del label ---
    const input_icon = document.getElementById('input_icon');
    const input_text = document.getElementById('input_text');
    const inputLabel = document.getElementById('lblCarga');

    if (input_icon) input_icon.innerHTML = "&#10697;"; // icono original (flecha hacia arriba)
    if (input_text) input_text.innerHTML = cEtiqueta; // texto original
    if (inputLabel) inputLabel.removeAttribute('title'); // limpia tooltip

    const spanText = document.getElementById(cId);
    if (spanText) {
        // Establece el texto original
        spanText.textContent = "Seleccione Archivo de ......"; 
    }
}

// __________________________________________________________________________________
const loader=(cStatus)=>{
    var loader = document.getElementById('loader-container');
    if (loader) {
        loader.style.display = cStatus; // 'none'; // Contenedor que bloquea la pantalla mientras se genera un proceso largo
    }
}
// __________________________________________________________________________________
// __________________________________________________________________________________
function pintarTablaHTML(idTabla, registros, aParametros) {

    cTabla = document.getElementById(idTabla).getElementsByTagName('tbody')[0];
    limpiaTabla(cTabla);

    if (registros.length === 0) {
        document.getElementById(cTabla).innerHTML = "<b>No hay registros.</b>";
        return;
    }

    let html = "";

    registros.forEach(row => {
        html += "<tr>";

        aParametros.campos.forEach((c,i) => {

            let nom = c.nombre.split(".")[1];
            let val = row[nom] ?? "";

            // Campo con scroll (OPCIÓN: todo en la misma celda)
            if (aParametros.scroll.includes(nom) && val.length > aParametros.maxscroll) {

                html += `
                    <td>
                        <div class="celda-scroll">

                            <div class="texto-visible" data-fulltext="${val}">
                                ${val}
                            </div>

                            <div class="scroll-container">
                                <div class="scroll-interno">${val}</div>
                            </div>

                        </div>
                    </td>`;
            
            } else {

                html += `<td>${val}</td>`;
            }

        });

        html += "</tr>";
    });

    html += "</table>";
    cTabla.innerHTML = html;

    document.querySelectorAll(".celda-scroll").forEach(celda => {
        let visible = celda.querySelector(".texto-visible");
        let scroll = celda.querySelector(".scroll-container");

        scroll.addEventListener("scroll", () => {
            visible.scrollLeft = scroll.scrollLeft;
        });
    });
}
// __________________________________________________________________________________
function pintarTablaHTMLEscucha(idTabla, registros, aParametros, aIdsHtml = []) {
    const cTabla = document.getElementById(idTabla).getElementsByTagName('tbody')[0];
    limpiaTabla(cTabla);

    if (registros.length === 0) {
        cTabla.innerHTML = "<b>No hay registros.</b>";
        return;
    }

    let html = "";

    registros.forEach((row, rowIndex) => {
        html += "<tr data-index='" + rowIndex + "'>";

        aParametros.campos.forEach((c, i) => {
            let nom = c.nombre.split(".")[1];
            let val = row[nom] ?? "";

            // Campo con scroll
            if (aParametros.scroll.includes(nom) && val.length > aParametros.maxscroll) {
                html += `
                    <td>
                        <div class="celda-scroll">
                            <div class="texto-visible" data-fulltext="${val}">${val}</div>
                            <div class="scroll-container">
                                <div class="scroll-interno">${val}</div>
                            </div>
                        </div>
                    </td>`;
            } else {
                html += `<td>${val}</td>`;
            }
        });

        html += "</tr>";
    });

    cTabla.innerHTML = html;

    // Sincronización de scroll en celdas
    document.querySelectorAll(".celda-scroll").forEach(celda => {
        let visible = celda.querySelector(".texto-visible");
        let scroll = celda.querySelector(".scroll-container");

        scroll.addEventListener("scroll", () => {
            visible.scrollLeft = scroll.scrollLeft;
        });
    });

    // --- Agregar listener a cada fila ---
    cTabla.querySelectorAll("tr").forEach((tr, rowIndex) => {
        tr.addEventListener("click", () => {
            aParametros.campos.forEach((c, colIndex) => {
                if (!aIdsHtml[colIndex]) return; // si no hay ID correspondiente, saltar

                const input = document.getElementById(aIdsHtml[colIndex]);
                if (!input) return;

                const nom = c.nombre.split(".")[1];
                const valor = registros[rowIndex][nom] ?? "";

                if (input.tagName === "INPUT") {
                    if (input.type === "checkbox") {
                        input.checked = Boolean(valor);
                    } else {
                        input.value = valor;
                    }
                } else if (input.tagName === "SELECT" || input.tagName === "TEXTAREA") {
                    input.value = valor;
                }
            });
        });
    });
}

// __________________________________________________________________________________
function generarPaginador(totalPaginas, aParametros) {
    const cont = document.getElementById("paginador");
    cont.innerHTML = "";

    const paginaActual = aParametros.pagina || 1;
    const rango = 10; // máximo botones visibles
    const mostrarNav = totalPaginas > rango;

    // Función auxiliar para crear botones
    const crearBoton = (texto, page, claseExtra = "") => {
        const a = document.createElement("a");
        a.innerText = texto;
        a.href = "#";
        a.className = "pagina-btn " + claseExtra;
        a.onclick = () => {
            aParametros.pagina  = page;
            gPagina             = page; // Debe ser una variable global definida en el JS principal
            conectayEjecutaPost(aParametros, cPhpBusca);
        };
        cont.appendChild(a);
    };

    // Botones "Primero" y "Anterior"
    if (mostrarNav) {
        crearBoton("Primero", 1);
        crearBoton("Anterior", Math.max(1, paginaActual - 1));
    }

    let inicio = Math.max(1, paginaActual - Math.floor(rango / 2));
    let fin = inicio + rango - 1;
    if (fin > totalPaginas) {
        fin = totalPaginas;
        inicio = Math.max(1, fin - rango + 1);
    }

    // Si el rango no incluye la primera página, mostrar "1" y "…"
    if (inicio > 1) {
        crearBoton("1", 1);
        const span = document.createElement("span");
        span.innerText = "…";
        span.style.padding = "0 5px";
        cont.appendChild(span);
    }

    // Botones de páginas dentro del rango
    for (let i = inicio; i <= fin; i++) {
        crearBoton(i, i, i === paginaActual ? "activo" : "");
    }

    // Si el rango no incluye la última página, mostrar "…" y la última página
    if (fin < totalPaginas) {
        const span = document.createElement("span");
        span.innerText = "…";
        span.style.padding = "0 5px";
        cont.appendChild(span);
        crearBoton(totalPaginas, totalPaginas);
    }

    // Botones "Siguiente" y "Último"
    if (mostrarNav) {
        crearBoton("Siguiente", Math.min(totalPaginas, paginaActual + 1));
        crearBoton("Último", totalPaginas);
    }
}
// __________________________________________________________________________________
function light_Title(id) { // Cambia la clase de la etiqueta, si el value del objeto id tiene valor,  utilizado en el onchange 
    const input = document.getElementById(id);
    const p     = document.querySelector(`label[for="${id}"]`);

    if (!input || !p) return;

    if (input.value !== "") {
        p.classList.add("titles-filled");
    } else {
        p.classList.remove("titles-filled");
    }
}
// __________________________________________________________________________________
function cambiaMaxLength(cIdHtml, objDetona) {
    const objHtml = document.getElementById(cIdHtml);

    if (objHtml && objDetona) {
        const max = objDetona.selectedOptions[0].dataset.max; // ← aquí
        objHtml.maxLength = max; // si lo quieres usar así
    }
}
// __________________________________________________________________________________