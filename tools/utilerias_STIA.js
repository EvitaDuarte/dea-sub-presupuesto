// //! utilerias_STIA.js
// //! version : 1.0.0
// //! authors : Nancy Gonazález, José Juan Gutierrez, Miguel Bolaños, Anselmo Cortes, Francisco J. Chablé


// //Limita el uso de caracteres a los objetos de texto asociados
// function permite (elEvento, permitidos) {
// 	// Variables que definen los caracteres permitidos
// 	var numeros = "0123456789";
// 	var caracteres = "abcdefghijklmnopqrstuvwxyzñABCDEFGHIJKLMNOPQRSTUVWXYZÑ";
// 	var formato_fecha = "0123456789/";
// 	var espacio = " ";
// 	var numeros_caracteres = espacio + numeros + caracteres;
// 	var espacio_caracteres = espacio + caracteres;
// 	var esp_car_punto = espacio + caracteres + ".";
// 	var correo = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@_-.";
// 	var rfc_curp = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
// 	var colonia = "0123456789abcdefghijklmnopqrstuvwxyzñABCDEFGHIJKLMNOPQRSTUVWXYZÑ-. ";
// 	var simbolos = "/-_";
// 	var documento = caracteres + numeros + simbolos;
// 	var puntuacion = ".";
// 	var usuario = caracteres + numeros + puntuacion;
// 	var telefono = espacio + numeros + "-()";
// 	var importe = numeros + ".";
// 	var oficios = simbolos + numeros + caracteres;
	
// 	var imgcaptcha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
// 	var acentos = "áéíóúÁÉÍÓÚ";
// 	var tecla = (document.all) ? e.keyCode : e.which;
// 	var campo_memo = numeros + caracteres + espacio + acentos + puntuacion + simbolos + ",()$:%"+ tecla;
// 	var vhoras = numeros + ":";
// 	var espacio_caracteres2 = espacio + caracteres + acentos;
// 	// Seleccionar los caracteres a partir del parámetro de la función
// 	switch(permitidos) {
// 		case 'rfc_curp':
// 			permitidos = rfc_curp;
// 			break;			
// 		case 'num':
// 			permitidos = numeros;
// 			break;
// 		case 'car':
// 			permitidos = caracteres;
// 			break;
// 		case 'num_car':
// 			permitidos = numeros_caracteres;
// 			break;
// 		case 'fecha':
// 			permitidos = formato_fecha;
// 			break;
// 		case 'doc_sim':
// 			permitidos = documento;
// 			break;
// 		case 'user':
// 			permitidos = usuario;
// 			break;			
// 		case 'car_esp':
// 			permitidos = espacio_caracteres;
// 			break;		
// 		case 'car_esp2':
// 			permitidos = espacio_caracteres2;
// 			break;		
// 		case 'car_esp_pto':
// 			permitidos = esp_car_punto;
// 			break;			
// 		case 'colonia':
// 			permitidos = colonia;
// 			break;			
// 		case 'correo':
// 			permitidos = correo;
// 			break;			
// 		case 'tel':
// 			permitidos = telefono;
// 			break;	
// 		case 'imp':
// 			permitidos = importe;
// 			break;
// 		case 'icaptcha':
// 			permitidos = imgcaptcha;
// 			break;
// 		case 'car_acen_pto':
// 			permitidos = esp_car_punto + acentos;
// 			break;
// 		case 'ctexto':
// 			permitidos = campo_memo;
// 			break;	
// 		case 'choras':
// 			permitidos = vhoras;
// 			break;
// 		case 'oficios':
// 			permitidos = oficios;
// 			break;
// 	}
// 	cadena = navigator.userAgent;
// 	if(/Firefox/.test(cadena)){
// 		teclas_especiales = [8,9,37,39,46];
// 	}else{
// 		teclas_especiales = [8];
// 	}
// 	// Obtener la tecla pulsada 
// 	var evento = elEvento || window.event;
// 	var codigoCaracter = evento.charCode || evento.keyCode;
// 	var caracter = String.fromCharCode(codigoCaracter);
// 	// Comprobar si la tecla pulsada es alguna de las teclas especiales
// 	// (teclas de borrado y flechas horizontales)
// 	var tecla_especial = false;
// 	for(var i in teclas_especiales) {
// 		if(codigoCaracter == teclas_especiales[i]) {
// 			if ((evento.keyCode==8) || (evento.keyCode==9) || (evento.keyCode==37) || (evento.keyCode==39) || (evento.keyCode==46)){
// //			if ((evento.keyCode==8) || (evento.keyCode==9)){
// 				tecla_especial = true;
// 			}
// 		    break;
// 		}
// 	}
// 	// Comprobar si la tecla pulsada se encuentra en los caracteres permitidos
// 	// o si es una tecla especial
// 	return permitidos.indexOf(caracter) != -1 || tecla_especial;
// }

// //Devuelve una cadena si el valor del objeto esta vacio o contiene un 0
// function esta_vacio(vObjeto){
// 	var objetoValor;
// 	objetoValor = vObjeto;
// 	if ((objetoValor == null) || (objetoValor.length == 0) || ( /^\s+$/.test(objetoValor))){
// 		return true;
// 	}else{
// 		return false;
// 	}
// }

// //Verifica que el valor del objeto no sea nulo
// function noNulo(cNombre_Objeto){
// 	vObj = document.getElementById(cNombre_Objeto);
// 	if ( vObj != null ){
// 		return true;
// 	}
// 	return false;
// }

// //Verifica que el valor del objeto no sea vacio
// function noVacio(cNombre_Objeto){
// 	vObj = document.getElementById(cNombre_Objeto);
// 	if ( vObj != null ){
// //		alert(typeof(vObj.value));
// 		if ( vObj.value != "" ){
// //			alert("["+vObj.value.trim()+"]");
// 			return vObj.value;
// 		}
// 	}
// 	return "";
// }

// //Elimina comas y signos de pesos para dejar un importe en su formato númerico
// function formatNumber(number)
// {
//     var number = number.toFixed(2) + '';
//     var x = number.split('.');
//     var x1 = x[0];
//     var x2 = x.length > 1 ? '.' + x[1] : '';
//     var rgx = /(\d+)(\d{3})/;
//     while (rgx.test(x1)) {
//         x1 = x1.replace(rgx, '$1' + ',' + '$2');
//     }
//     return x1 + x2;
// }


// //Valida que el formato de la fecha capturada sea valido
// function validaFecha(recibeObj, chkEdad){
// 	var vObj = recibeObj;
// 	var mensaje="";
// 	var tipo_Err="";
	
// 	if ((vObj.length<10) || (vObj.charAt(2)!='/') || (vObj.charAt(5)!='/')){
// 		tipo_Err = tipo_Err + mensaje+ " - Formato de fecha no valido."
	
// 	}else{
// 		var dia = vObj.substr(0,2);
// 		var mes = vObj.substr(3,2);
// 		var anyo = vObj.substr(6,4);
		
// 		if ((mes < 1 ) || (mes > 12)) {
// 				tipo_Err = tipo_Err + mensaje+ " - El mes no es válido."
// 			}else{
// 				if ((mes == "01") || (mes == "03") || (mes == "05") || (mes == "07") || (mes == "08") || (mes == "10") || (mes == "12")){
// 					if ((dia < 1 ) || (dia <= 31)){
// 						mensaje = "";
// 						tipo_Err = tipo_Err + mensaje
// 					}else{	
// 						tipo_Err = tipo_Err + mensaje+ " - El día no es válido."
// 					}
// 				}	
				
// 				if ((mes == "04") || (mes == "06") || (mes == "09") || (mes == "11")){
// 					if ((dia < 1 ) || (dia <= 30)){
// 						mensaje = "";
// 						tipo_Err = tipo_Err + mensaje
// 					}else{	
// 						tipo_Err = tipo_Err + mensaje+ " - El día no es válido."
// 					}
// 				}
				
// 				if ((mes == "02")){
// 					if (((anyo % 4 == 0) && (anyo % 100!= 0))|| (anyo % 400 == 0)){
// 						if ((dia < 1 ) || (dia <= 29)){
// 							mensaje = "";
// 							tipo_Err = tipo_Err + mensaje
// 						}else{	
// 							tipo_Err = tipo_Err + mensaje+ " - El día no es válido."
// 						}
// 					}else{
// 						if ((dia < 1 ) || (dia <= 28)){
// 							mensaje = "";
// 							tipo_Err = tipo_Err + mensaje
// 						}else{	
// 							tipo_Err = tipo_Err + mensaje+ " - El día no es válido."
// 						}
// 					}
// 				}		
// 			}
		
// 	}
// 	return tipo_Err;
// }

// function calculaDias(fechaini, fechafin){
// 	var diaini = fechaini.substr(0,2);
// 	var mesini = fechaini.substr(3,2);
// 	var anioini = fechaini.substr(6,4);

// 	var diafin = fechafin.substr(0,2);
// 	var mesfin = fechafin.substr(3,2);
// 	var aniofin = fechafin.substr(6,4);

// 	anioini = parseInt(anioini);
// 	mesini = parseInt(mesini);
// 	diaini = parseInt(diaini);

// 	aniofin = parseInt(aniofin);
// 	mesfin = parseInt(mesfin);
// 	diafin = parseInt(diafin);

// 	var a = moment([aniofin, mesfin, diafin]);
// 	var b = moment([anioini, mesini, diaini]);
// 	diferencia = a.diff(b, 'days');       // 1
// 	return diferencia;	
// }

// function checaCamposHome() {
// //alert("checa campos");
// 	var vuser = document.form1.txtUser.value.trim();
// 	var vpws = document.form1.txtPws.value.trim();
// 	var vcaptcha = document.form1.tmptxt.value.trim();

// 	missinginfo = "";
	
// 	if (esta_vacio(vuser)){
// 		//alert("Entró en validación");
// 		missinginfo += "\n El USUARIO no puede ir vacio \n";
// 	}else{
// 		if (palabrasReservadas(vuser)){
// 			missinginfo += "\n El valor del campo USUARIO contiene palabras reservadas \n";
// 		}
// 	}
// 	if (esta_vacio(vpws)){
// 		//alert("Entró en validación");
// 		missinginfo += "\n La CONTRASEÑA no puede ir vacia \n";
// 	}else{
// 		if (palabrasReservadas(vpws)){
// 			missinginfo += "\n El valor del campo CONTRASEÑA contiene palabras reservadas \n";
// 		}	
// 	}
// 	if (esta_vacio(vcaptcha)){
// 		//alert("Entró en validación");
// 		missinginfo += "\n El CAPTCHA no puede ir vacio \n";
// 	}else{
// 		if (palabrasReservadas(vcaptcha)){
// 			missinginfo += "\n El valor del campo CAPTCHA contiene palabras reservadas \n";
// 		}	
// 	}
	
// 	if (missinginfo != "") {
// 		missinginfo ="_____________________________\n" +
// 		missinginfo + "\n_____________________________" +
// 		"\nPor favor ingresa un valor";
		
// 		alert(missinginfo);
// 		return false;
// 	}
// 	else return true;
// }

// function palabrasReservadas(vcadena){
// 	var esReservada = false;
// 	var palabras = 	["ZAP","zap","TIME","time","SLEEP","sleep","INSERT","insert","select","SELECT","update","UPDATE", "ZAP;start-sleep -s 5", "zap;START-SLEEP -S 5", "ZAP;START-sleep -s 5","ZAP;start-SLEEP -s 5"];
// //	vcadena += ","; 
// //	alert(vcadena);
// //	for(i=0; i<12; i++){
//       if (palabras.indexOf(vcadena)){
//          esReservada = true;
//       }
//   // }
// 	return esReservada;
// }

// function slider(){
// 	var input = document.querySelector("input[type=range]");
// 	   actualizarInput(input) 
	
	
// 	input.addEventListener("input", function(evt) {
// 	   actualizarInput(input)
// 	});
	
// 	function actualizarInput(input){
// 	   var label = input.parentElement.querySelector("label");
// 	   label.innerHTML = input.value;
// 	   var inputMin = input.getAttribute("min");
// 	   var inputMax = input.getAttribute("max");
// 	   var unidad = (inputMax - inputMin) / 100;
// 	   input.style.setProperty("--value", (input.value - inputMin)/unidad);  
// 	}
// }


// // =============================================================================
// // Función para sacar hora actual de cualquier sitio

// // function getRealDate(zona_horaria, es_fronterizo){
// function getRealDate(zona_horaria, es_fronterizo, horas_afectadas){
// 	var date = new Date;
          
// 	// var url_string = "http://www.example.com/t.html?a=1&b=3&c=m2-m3-m4-m5"; 
// 	// var url = new URL(url_string);
// 	// var c = url.searchParams.get("c");
	
// 	if(zona_horaria == null){
// 		var resolvedOptions = Intl.DateTimeFormat().resolvedOptions()
// 		zona_horaria        = resolvedOptions.timeZone
// 	}else{

// 		// Comentar lo relacionado al fronterizo
// 		// if(es_fronterizo == "SI"){
// 		// 	es_fronterizo = true;
// 		// }else if(es_fronterizo == "NO"){
// 		// 	es_fronterizo = false;
// 		// }
		
// 		var date = new Date();
	
// 		usDate = date.toLocaleString("en-US", {timeZone: zona_horaria});
	
// 		ArrayF = usDate.split(",")
// 		Fecha  = ArrayF[0].trim()
// 		Hora_c = ArrayF[1].trim()
	
// 		ArrayFC = Fecha.split("/")
// 		// Comentar
// 		// if(es_fronterizo){
// 		// 	ArrayFront  = Hora_c.split(":")
// 		// 	horaFr  = ArrayFront[0]
// 		// 	segundosFr  = ArrayFront[2]
// 		// 	ArrayisPMFR = segundosFr.split(" ")
// 		// 	is_pmFront  = ArrayisPMFR[1]

// 		// 	diaArr = parseInt(ArrayFC[1])
// 		// 	if(horaFr == '12' && is_pmFront == 'PM'){
// 		// 		diaArr = (diaArr+1)
// 		// 	}
// 		// }else{
// 		// 	diaArr = ArrayFC[1]
// 		// }
// 		dia  = parseInt(ArrayFC[1])
// 		mes = ('0'+ArrayFC[0]).slice(-2);
// 		año = ArrayFC[2]
	
// 		ArrayH = Hora_c.split(":")
// 		hora = ArrayH[0];
		
// 		//Validación para agregar o restar horas a usuarios 
// 		if(horas_afectadas != ""){
// 			HorasAgrRes = horas_afectadas.substr(0,1)
// 			VarHorasA = parseInt(horas_afectadas.substr(1,1))

// 			if(HorasAgrRes == "+"){
// 				hora = (parseInt(hora)+VarHorasA)
// 			}else if(HorasAgrRes == "-"){
// 				hora = (parseInt(hora)-VarHorasA)
// 			}
// 		}

// 		// Comentar y realizar funcionalidad parseando a entero la hora
// 		// if(horas_afectadas){
// 		// 	hora = parseInt(ArrayH[0])
// 		// 	if(hora != 12){
// 		// 		hora = (hora+1)
// 		// 	}else{
// 		// 		hora = 1
// 		// 	}
// 		// }else{
// 			// hora = ArrayH[0];
// 		// }
		
		
// 		minutos   = ArrayH[1]
// 		segundos = ArrayH[2]
	
// 		ArrayisPM = segundos.split(" ")
	
// 		segundos  = ArrayisPM[0]
// 		is_pm     = ArrayisPM[1]
// 		if(is_pm == "PM"){
// 			is_pm = "p.m."
// 		}else if(is_pm == "AM"){
// 			is_pm = "a.m."
// 		}

// 		if(horas_afectadas != ""){
// 			// hora = 12
// 			// is_pm = "p.m."
// 			// dia   = 17
// 			hora_nueva = calculaHoras(hora, is_pm, dia);
// 			ArrayHoraNueva = hora_nueva.split("|")
// 			hora   = ArrayHoraNueva[0]
// 			is_pm  = ArrayHoraNueva[1]
// 			dia    = ArrayHoraNueva[2]
// 		}

// 		dia   = ('0'+dia).slice(-2);
// 		hora = ('0'+hora).slice(-2);

// 		fecha_c = dia+"/"+mes+"/"+año 
// 		hora_f   = hora+":"+minutos+":"+segundos+" "+is_pm;
// 		DateHour = año+mes+dia+hora+minutos+segundos

// 		mens_ext = ""

// 		if(((hora == "10" || hora == "11") && is_pm == "p.m.") || ((hora <= "04" || hora == "12") && is_pm == "a.m.")){
// 			if(is_pm == "p.m."){
// 				Texto = "para el día siguiente." 
// 			}else{
// 				Texto = "para el dia de hoy."
// 			}
				
// 			mens_ext = "Todos los registros realizados después de las 23:59 de la noche, serán guardados "+Texto 
		
// 		} 
// 		// Todos los valores de variables se devuelven globalmente
// 	}

// }

// // =============================================================================
// // Función para calcular horas modificadas
// function calculaHoras(hora, is_pm, dia){
// 	new_hora = ""
// 	new_ispm = ""
// 	new_dia  = dia
	
// 	if(hora == 0 || hora == -1 || hora == -2 || hora == 12 || hora == 13 || hora == 14 || hora == 15){
// 		switch(hora) {
// 			case 0:
// 				new_hora = 12
// 				if(is_pm == "a.m."){
// 					new_ispm = "a.m."
// 					new_dia  = dia-1
// 				}else if(is_pm == "p.m."){
// 					new_ispm = "p.m."
// 				}
// 			break;
// 			case -1: 
// 				new_hora = 11
// 				if(is_pm == "a.m."){
// 					new_ispm = "p.m."
// 					new_dia  = dia-1
// 				}else if(is_pm == "p.m."){
// 					new_ispm = "a.m."
// 				}
// 			break;
// 			case -2:
// 				new_hora = 10
// 				if(is_pm == "a.m."){
// 					new_ispm = "p.m."
// 					new_dia  = dia-1
// 				}else if(is_pm == "p.m."){
// 					new_ispm = "a.m."
// 				}
// 			break;

// 			case 12:
// 				new_hora = 12
// 				if(is_pm == "a.m."){
// 					new_ispm = "p.m."
// 				}else if(is_pm == "p.m."){
// 					new_ispm = "a.m."
// 					new_dia = dia+1
// 				}
// 			break;

// 			case 13:
// 				new_hora = 1
// 				if(is_pm == "a.m."){
// 					new_ispm = "p.m."
// 				}else if(is_pm == "p.m."){
// 					new_ispm = "a.m."
// 					new_dia = dia+1
// 				}
// 			break;

// 			case 14:
// 				new_hora = 2
// 				if(is_pm == "a.m."){
// 					new_ispm = "p.m."
// 				}else if(is_pm == "p.m."){
// 					new_ispm = "a.m."
// 					new_dia = dia+1
// 				}
// 			break;
// 			case 15:
// 				new_hora = 3
// 				if(is_pm == "a.m."){
// 					new_ispm = "p.m."
// 				}else if(is_pm == "p.m."){
// 					new_ispm = "a.m."
// 					new_dia = dia+1
// 				}
// 			break;
// 		}
// 	}else{
// 		new_hora = hora
// 		new_ispm = is_pm
// 		new_dia  = dia
// 	}
		
// 	return new_hora+"|"+new_ispm+"|"+new_dia
// }


// // =============================================================================
// // Función para formatear importes a 2 decimales

// function formatearImporte(objeto, lenguaje){
// 	if(lenguaje == "html"){
// 		numero = objeto.value;
// 	}
// 	if(lenguaje == "js"){
// 		numero = objeto;
// 	}

// 	if(numero != "" && numero != null){
// 		numero = parseFloat(numero).toFixed(2)
// 	}else{
// 		numero = ""
// 	}

// 	if(lenguaje == "html"){
// 		objeto.value = numero;
// 	}
// 	if(lenguaje == "js"){
// 		return numero;
// 	}	
// }

// // ===========================================================================
// // Valida Cifras

// function validaCifras(cifra_1, cifra_2, tipo){	
// 	var val1 = parseInt(cifra_1);
// 	var val2 = parseInt(cifra_2);
// 	var mensaje ="";
			
// 	if(tipo == "Mayor igual"){
// 		if (val2 >= val1) {
// 			mensaje = true;
// 		}else{
// 			mensaje = false;
// 		}
// 	}
// 	if(tipo == "Mayor que"){
// 		if (val2 > val1) {
// 			mensaje = true;
// 		}else{
// 			mensaje = false;
// 		}
// 	}
	
// 	return mensaje;
// }

// // ===========================================================================
// // Función para validar 2 fechas

// function validarFechas(date1, date2) {
// 	return date1.getTime() <= date2.getTime();
// }

// // ===========================================================================
// // Función para validar 2 fechas (Sistema boletos avion)

// function validarFechasBoletos(date1, date2) {
// 	return date1.getTime() < date2.getTime();
// }

// // ===========================================================================
// // Formato de fechas para PostgreSQL

// function getFormatoFechaPostgres(fecha) {
// 	return fecha.substr(6, 4) + "-" + fecha.substr(3, 2) + "-" + fecha.substr(0, 2)
// }

// function eliminarCaracteresNoNumericos(cadena) {
// 	return cadena.replace(/[^0-9.]/g, "");
// }



