function validaCredenciales(cUsu,cPass){

	cUsu  = cUsu.trim();
	cPass = cPass.trim();

	if ( cUsu==="" || cPass===""){
		mandaMensaje("Credenciales incorrectas");
		return false;
	}
	aDatos = {
		opcion		: "validaLdap",
		Usuario		: cUsu,
		Contra		: cPass,
		regreso 	: false
	}
	conectayEjecutaPost(aDatos,"P_login_.php")
}
// ____________________________________________ Regreso
async function procesarRespuesta__(vRes){
	//console.log(vRes);
	cOpcion = vRes.parametros.opcion
	switch(cOpcion){
		case "validaLdap":
			if (vRes.success){// 🟢 Credenciales correctas
				limpiaObjeto("password_login");
				limpiaObjeto("user_login");
				window.location.href = "P_Cuentas00_00.php";
				
				// Nota: Para asegurar una redirección limpia,
				// puedes añadir un 'return' para detener cualquier 
				// ejecución de código adicional en esta función.
				return; 
			}else{ // 🔴 Credenciales incorrectas No llega aquí 
				mandaMensaje("Credenciales incorrectas");
				limpiaObjeto("password_login");
				limpiaObjeto("user_login");
				FocoEnObjeto("user_login");

			}
		break;
	}
}
// _________________________________________________________
async function procesarError__(vRes){
	// utilizar para casos especiales, normalmente esta vacía
	cOpcion = vRes.parametros.opcion
	switch(cOpcion){
		case "validaLdap":
			mandaMensaje("Credenciales incorrectas");
			limpiaObjeto("password_login");
			limpiaObjeto("user_login");
			FocoEnObjeto("user_login");
		break;
	}
}
// _________________________________________________________
