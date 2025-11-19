<?php

	try{
		$validator 	 = array('success' => false , 'messages' => array()  , 'cuentas' => array() , 'jujuy' => array() , 
	                     'Ws' => '' , 'Envio' => '' , 'paso' => '', 'datos'=>array()); 
		$url	= "https://soa-oci.ine.mx/soa-infra/services/default/AppSegmentoProyectos/MediatorProyectos_ep?wsdl";
		$soap	= new SoapClient($url, aOpcionesWs($url));	
		$params	= Array("PROYECTO"=> "B00CA01");		// PROYECTO se definio en el WS
		//$aPy	= json_decode(json_encode($soap->consultaProyectos($params)),true); 
		$aPy	= $soap->consultaProyectos($params); 
		$validator["pys"] = $aPy;



	}catch(SoapFault $fault){
		$validator["messages"]	= "No se logró la conexión con el SIGA";
		trigger_error("Falla Conexión SIGA: (Código: {$fault->faultcode}, Descripción: {$fault->faultstring})", E_USER_ERROR);
		return;
	}
	header_remove('x-powered-by');
	header('Content-type: application/json; charset=utf-8');
	echo json_encode($validator);


function aOpcionesWs($url){
	return     Array(
		"uri"					=> $url,			"style"					=> SOAP_RPC,
		"use"					=> SOAP_ENCODED, 	"soap_version"			=> SOAP_1_1,
		"cache_wsdl"			=> WSDL_CACHE_BOTH, "connection_timeout" 	=> 15,
		"trace" 				=> true, 			"encoding" 				=> "UTF-8",
		"exceptions" 			=> true, 			"features" 				=> SOAP_SINGLE_ELEMENT_ARRAYS
	);
}


?>